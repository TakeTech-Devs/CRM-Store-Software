/*
 * Session guard — loaded on every logged-in page.
 *
 *  1. Logout runs Sync Out first, so nothing is left on this PC when staff sign off.
 *  2. Closing the tab / browser without logging out shows the browser's "Leave site?"
 *     warning. (A web page can never forbid closing a browser, only ask first.)
 *     Moving around inside the app — links, buttons that redirect, reload — never asks.
 *  3. Every few minutes a quiet check asks the server whether an automatic backup is due.
 */
(function () {
    'use strict';

    var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';
    var storeId = (document.querySelector('meta[name="store-id"]') || {}).content || '';

    // ── 1. Leave guard ────────────────────────────────────────────────────────
    var leaving = false; // true while the app itself is navigating (so no prompt)
    var leavingTimer = null;

    function allowLeave(ms) {
        leaving = true;
        clearTimeout(leavingTimer);
        // Auto-reset: a click whose navigation got cancelled must not switch the guard off for good.
        leavingTimer = setTimeout(function () { leaving = false; }, ms || 2500);
    }

    window.addEventListener('beforeunload', function (e) {
        if (leaving) return;
        e.preventDefault();
        e.returnValue = ''; // required by Chrome / Edge to show the prompt
    });

    // Navigation API (Chrome / Edge): sees every in-app navigation, including
    // `location.href = …` and reloads, but not a tab / window close.
    if (window.navigation && typeof window.navigation.addEventListener === 'function') {
        window.navigation.addEventListener('navigate', function (e) {
            try {
                if (new URL(e.destination.url).origin === location.origin) allowLeave();
            } catch (err) { /* ignore */ }
        });
    }

    // Fallback for browsers without the Navigation API.
    document.addEventListener('click', function (e) {
        var a = e.target.closest && e.target.closest('a[href]');
        if (!a || a.target === '_blank' || a.hasAttribute('download')) return;
        var href = a.getAttribute('href');
        if (!href || href.charAt(0) === '#' || /^javascript:/i.test(href)) return;
        try {
            if (new URL(a.href, location.href).origin === location.origin) allowLeave();
        } catch (err) { /* ignore */ }
    }, true);
    document.addEventListener('submit', function () { allowLeave(); }, true);
    document.addEventListener('keydown', function (e) {
        if (e.key === 'F5' || ((e.ctrlKey || e.metaKey) && (e.key === 'r' || e.key === 'R'))) allowLeave();
    }, true);

    // ── 2. Logout = Sync Out, then sign out ───────────────────────────────────
    function goLogout() {
        allowLeave(10000);
        window.location.href = '/logout';
    }

    function syncOutThenLogout() {
        var hasSwal = typeof Swal !== 'undefined';

        // Without the popup library (e.g. no internet for its CDN) just sign out.
        if (!hasSwal) { goLogout(); return; }

        Swal.fire({
            title: 'Syncing before you log out…',
            html: 'Sending today\'s data to head office.<br><small>Please do not close the browser.</small>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            didOpen: function () { Swal.showLoading(); }
        });

        var controller = new AbortController();
        var timeout = setTimeout(function () { controller.abort(); }, 180000); // 3 minutes

        fetch('/sync/out/data/' + encodeURIComponent(storeId), {
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            credentials: 'same-origin',
            signal: controller.signal
        })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                clearTimeout(timeout);
                if (d && d.success) { goLogout(); } else { offerLogoutAnyway((d && d.message) || 'Sync Out did not complete.'); }
            })
            .catch(function () {
                clearTimeout(timeout);
                offerLogoutAnyway('Could not reach head office. Check the internet connection.');
            });
    }

    function offerLogoutAnyway(reason) {
        Swal.fire({
            icon: 'warning',
            title: 'Sync Out did not finish',
            html: reason + '<br><br>Nothing is lost — your data stays on this PC and goes out on the next Sync Out.',
            showCancelButton: true,
            confirmButtonText: 'Log out anyway',
            cancelButtonText: 'Stay logged in',
            confirmButtonColor: '#d33'
        }).then(function (res) {
            if (res.isConfirmed) goLogout();
        });
    }

    document.addEventListener('click', function (e) {
        var a = e.target.closest && e.target.closest('a[href]');
        if (!a) return;
        var path;
        try { path = new URL(a.href, location.href).pathname.replace(/\/+$/, ''); } catch (err) { return; }
        if (path !== '/logout') return;
        e.preventDefault();
        e.stopPropagation();
        syncOutThenLogout();
    }, true);

    // ── 3. Automatic backup heartbeat ─────────────────────────────────────────
    function backupCheck() {
        fetch('/store/backup/auto-check', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
            credentials: 'same-origin'
        }).catch(function () { /* silent: it simply tries again later */ });
    }
    setTimeout(backupCheck, 30000);            // shortly after the page opens
    setInterval(backupCheck, 15 * 60 * 1000);  // then every 15 minutes while it stays open

    window.RightAidGuard = { allowLeave: allowLeave };
})();
