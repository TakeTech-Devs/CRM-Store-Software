/*
 * ReportTable — client-side search + "show N rows" + compact pagination for the
 * simple report tables (Expiry, Stock).
 *
 *   const table = ReportTable({
 *       tbody:      '#exp_list',          // <tbody> to fill
 *       pager:      '#pagination-links',  // <ul class="pagination">
 *       info:       '#tableInfo',         // "Showing 1 to 25 of 140"
 *       search:     '#reportSearch',      // text input (optional)
 *       pageSize:   '#pageSizeSelect',    // <select> of row counts (optional)
 *       filter:     '#statusFilter',      // <select> (optional)
 *       colspan:    3,
 *       rowHtml:    (item) => '<tr>…</tr>',
 *       searchText: (item) => 'text that the search box matches against',
 *       filterFn:   (item, filterValue) => boolean   // only with `filter`
 *   });
 *   table.setData(rows);          // (re)load data and render page 1
 *   table.getFiltered();          // rows after search + filter (used for printing)
 *
 * The pager shows at most ~11 buttons (first, last, current ±2, ellipses, prev/next),
 * so it stays short however many pages there are.
 */
(function (global) {
    'use strict';

    function escapeHtml(v) {
        return String(v == null ? '' : v).replace(/[&<>"']/g, function (c) {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
        });
    }

    function pageList(current, total) {
        if (total <= 9) {
            return Array.from({ length: total }, function (_, i) { return i + 1; });
        }
        var pages = [1];
        var from = Math.max(2, current - 2);
        var to = Math.min(total - 1, current + 2);
        if (from > 2) pages.push('…');
        for (var p = from; p <= to; p++) pages.push(p);
        if (to < total - 1) pages.push('…');
        pages.push(total);
        return pages;
    }

    function ReportTable(opts) {
        var $tbody = $(opts.tbody);
        var $pager = $(opts.pager);
        var $info = opts.info ? $(opts.info) : null;
        var $search = opts.search ? $(opts.search) : null;
        var $size = opts.pageSize ? $(opts.pageSize) : null;
        var $filter = opts.filter ? $(opts.filter) : null;

        var all = [];
        var filtered = [];
        var page = 1;

        function pageSize() {
            return $size ? (parseInt($size.val(), 10) || 25) : 25;
        }

        function applyFilters() {
            var q = $search ? $.trim($search.val()).toLowerCase() : '';
            var f = $filter ? $filter.val() : '';
            filtered = all.filter(function (item) {
                if ($filter && opts.filterFn && !opts.filterFn(item, f)) return false;
                if (q && String(opts.searchText(item)).toLowerCase().indexOf(q) === -1) return false;
                return true;
            });
        }

        function render() {
            var size = pageSize();
            var totalPages = Math.max(1, Math.ceil(filtered.length / size));
            if (page > totalPages) page = totalPages;
            var start = (page - 1) * size;
            var slice = filtered.slice(start, start + size);

            if (!slice.length) {
                $tbody.html('<tr><td colspan="' + opts.colspan + '" class="text-center">No Data Available</td></tr>');
            } else {
                $tbody.html(slice.map(opts.rowHtml).join(''));
            }

            if ($info) {
                $info.text(filtered.length
                    ? 'Showing ' + (start + 1) + ' to ' + (start + slice.length) + ' of ' + filtered.length +
                      (filtered.length !== all.length ? ' (filtered from ' + all.length + ')' : '')
                    : 'No records');
            }

            $pager.empty();
            if (totalPages <= 1) return;
            $pager.append('<li class="page-item' + (page === 1 ? ' disabled' : '') +
                '"><a class="page-link" href="#" data-page="' + (page - 1) + '">&laquo;</a></li>');
            pageList(page, totalPages).forEach(function (p) {
                if (p === '…') {
                    $pager.append('<li class="page-item disabled"><span class="page-link">…</span></li>');
                } else {
                    $pager.append('<li class="page-item' + (p === page ? ' active' : '') +
                        '"><a class="page-link" href="#" data-page="' + p + '">' + p + '</a></li>');
                }
            });
            $pager.append('<li class="page-item' + (page === totalPages ? ' disabled' : '') +
                '"><a class="page-link" href="#" data-page="' + (page + 1) + '">&raquo;</a></li>');
        }

        function refresh() {
            applyFilters();
            page = 1;
            render();
        }

        $pager.on('click', 'a.page-link', function (e) {
            e.preventDefault();
            var p = parseInt($(this).data('page'), 10);
            if (!isNaN(p) && p >= 1) {
                page = p;
                render();
            }
        });
        if ($search) $search.on('input', refresh);
        if ($size) $size.on('change', refresh);
        if ($filter) $filter.on('change', refresh);

        return {
            setData: function (rows) { all = rows || []; refresh(); },
            getFiltered: function () { return filtered; },
            escapeHtml: escapeHtml
        };
    }

    ReportTable.escapeHtml = escapeHtml;
    global.ReportTable = ReportTable;
})(window);
