@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<style>
/* ── Reset layout ── */
.container-fluid { padding: 0 !important; }

/* ── Top Banner ── */
.db-banner {
    background: linear-gradient(120deg, #1e3a5f 0%, #2563eb 60%, #3b82f6 100%);
    padding: 1.1rem 1.8rem .9rem;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem;
}
.db-banner h4 { color:#fff; font-weight:700; font-size:1.1rem; margin:0 0 2px; }
.db-banner p  { color:rgba(255,255,255,.75); font-size:.8rem; margin:0; }
.db-date-chip { background:rgba(255,255,255,.15); color:#fff; border-radius:20px; padding:3px 12px; font-size:.75rem; font-weight:600; display:inline-block; margin-top:6px; border:1px solid rgba(255,255,255,.25); }
.db-banner-actions { display:flex; flex-direction:column; align-items:flex-end; gap:.35rem; }
.db-banner-actions .btn { font-size:.8rem; font-weight:600; border-radius:8px; padding:.35rem .9rem; }

/* ── KPI Strip ── */
.kpi-strip { background:#fff; border-bottom:1px solid #eef0f4; padding:.6rem 1.8rem; display:flex; gap:.75rem; overflow-x:auto; }
.kpi-strip::-webkit-scrollbar { height:4px; }
.kpi-strip::-webkit-scrollbar-thumb { background:#ddd; border-radius:4px; }
.kpi-card {
    min-width:140px; flex:1; background:#f8faff; border:1px solid #e8edf5;
    border-radius:10px; padding:.7rem 1rem; display:flex; align-items:center; gap:.7rem;
    transition: box-shadow .15s, transform .15s;
}
.kpi-card:hover { box-shadow:0 4px 14px rgba(0,0,0,.09); transform:translateY(-2px); }
.kpi-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
.kpi-label { font-size:.67rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#8896a9; margin-bottom:2px; white-space:nowrap; }
.kpi-value { font-size:1.05rem; font-weight:800; color:#0f172a; line-height:1; }
.kpi-value.warn  { color:#d97706; }
.kpi-value.danger{ color:#dc2626; }

/* ── Main body ── */
.db-body { display:flex; gap:1.1rem; padding:1.1rem 1.8rem; }
.db-main  { flex:1; min-width:0; display:flex; flex-direction:column; gap:1.1rem; }
.db-side  { width:280px; flex-shrink:0; display:flex; flex-direction:column; gap:1.1rem; }

/* ── Section card ── */
.db-card { background:#fff; border:1px solid #eef0f4; border-radius:12px; overflow:hidden; }
.db-card-head {
    padding:.7rem 1rem; border-bottom:1px solid #f0f2f6;
    display:flex; align-items:center; justify-content:space-between;
    background:#fafbfd;
}
.db-card-head h6 { margin:0; font-weight:700; font-size:.85rem; color:#1e293b; }
.db-card-body { padding:.9rem 1rem; }

/* ── Quick actions ── */
.qa-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.6rem; padding:.9rem 1rem; }
.qa-btn {
    display:flex; flex-direction:column; align-items:center; justify-content:center; gap:.4rem;
    padding:.75rem .5rem; border-radius:10px; border:1.5px solid #e8edf5;
    background:#fafbfd; text-decoration:none; transition:.15s;
    font-size:.72rem; font-weight:700; color:#374151; text-align:center;
}
.qa-btn:hover { background:#eff6ff; border-color:#2563eb; color:#2563eb; transform:translateY(-2px); box-shadow:0 4px 12px rgba(37,99,235,.12); text-decoration:none; }
.qa-btn .qa-icon { width:36px; height:36px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:.95rem; }

/* ── Recent bills table ── */
.db-table { width:100%; font-size:.8rem; }
.db-table thead th { background:#f8faff; color:#8896a9; font-weight:700; font-size:.68rem; text-transform:uppercase; letter-spacing:.05em; padding:.55rem .8rem; border-bottom:1px solid #eef0f4; white-space:nowrap; }
.db-table tbody td { padding:.55rem .8rem; border-bottom:1px solid #f5f6fa; vertical-align:middle; color:#374151; white-space:nowrap; }
.db-table tbody tr:last-child td { border:none; }
.db-table tbody tr:hover td { background:#f8faff; }
.type-badge { font-size:.65rem; font-weight:700; padding:2px 7px; border-radius:8px; }
.type-badge.cust { background:#eff6ff; color:#2563eb; }
.type-badge.staff{ background:#f0fdf4; color:#16a34a; }

/* ── Alert items ── */
.alert-row { display:flex; align-items:flex-start; gap:.65rem; padding:.6rem 0; border-bottom:1px solid #f3f4f6; }
.alert-row:last-child { border:none; }
.alert-dot { width:9px; height:9px; border-radius:50%; flex-shrink:0; margin-top:4px; }
.alert-text strong { font-size:.82rem; color:#1e293b; }
.alert-text small { display:block; color:#8896a9; font-size:.72rem; margin-top:1px; }

/* ── Sync strip ── */
.sync-strip { background:#fff; border:1px solid #eef0f4; border-radius:12px; padding:.6rem .9rem; display:flex; align-items:center; justify-content:space-between; gap:.5rem; flex-wrap:nowrap; white-space:nowrap; }
.sync-strip span { font-size:.78rem; color:#64748b; font-weight:600; flex-shrink:0; }
.sync-strip .sync-btns { display:flex; gap:.4rem; flex-shrink:0; }
.sync-strip .sync-btns .btn { font-size:.72rem; padding:.3rem .65rem; white-space:nowrap; }

/* ── Clock ── */
.db-clock { font-size:1.5rem; font-weight:800; color:#fff; letter-spacing:.04em; line-height:1; }
.db-clock-label { font-size:.7rem; color:rgba(255,255,255,.6); margin-top:2px; }
</style>

{{-- ── TOP BANNER ── --}}
<div class="db-banner">
    <div>
        <h4 id="greetingText">Good Evening! 👋</h4>
        <p>Here's what's happening at your store today.</p>
        <span class="db-date-chip"><i class="fa fa-calendar-day mr-1"></i><span id="todayDate"></span></span>
    </div>
    <div class="db-banner-actions" style="flex-direction:column; align-items:flex-end; gap:.35rem;">
        <div style="text-align:right; display:flex; align-items:baseline; gap:.3rem; justify-content:flex-end;">
            <div class="db-clock" id="liveClock">00:00:00</div>
            <div class="db-clock-label" id="liveAmPm">AM</div>
        </div>
        <a href="{{ url('store/sync/history') }}" class="btn btn-light btn-sm">
            <i class="fa fa-history mr-1"></i> Sync History
        </a>
    </div>
</div>

{{-- ── KPI STRIP ── --}}
<div class="kpi-strip">
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#eff6ff;color:#2563eb"><i class="fa fa-rupee-sign"></i></div>
        <div><div class="kpi-label">Today's Sale</div><div class="kpi-value" id="todaySale">…</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f0fdf4;color:#16a34a"><i class="fa fa-history"></i></div>
        <div><div class="kpi-label">Yesterday</div><div class="kpi-value" id="yesterdaySale">…</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#faf5ff;color:#7c3aed"><i class="fa fa-calendar-alt"></i></div>
        <div><div class="kpi-label">This Month</div><div class="kpi-value" id="monthlyEarnings">…</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fff7ed;color:#d97706"><i class="fa fa-exclamation-triangle"></i></div>
        <div><div class="kpi-label">Expiring Soon</div><div class="kpi-value warn" id="expiringSoon">…</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#fef2f2;color:#dc2626"><i class="fa fa-times-circle"></i></div>
        <div><div class="kpi-label">Expired</div><div class="kpi-value danger" id="expiredMedicines">…</div></div>
    </div>
    <div class="kpi-card">
        <div class="kpi-icon" style="background:#f0fdfa;color:#0d9488"><i class="fa fa-box-open"></i></div>
        <div><div class="kpi-label">Zero Stock</div><div class="kpi-value" id="zeroStockMedicine">…</div></div>
    </div>
</div>

{{-- ── MAIN BODY ── --}}
<div class="db-body">

    {{-- LEFT COLUMN --}}
    <div class="db-main">

        {{-- Quick Actions --}}
        <div class="db-card">
            <div class="db-card-head">
                <h6><i class="fa fa-bolt mr-2 text-warning"></i>Quick Actions</h6>
            </div>
            <div class="qa-grid">
                <a href="{{ url('store/customer/create/billing') }}" class="qa-btn">
                    <div class="qa-icon" style="background:#eff6ff;color:#2563eb"><i class="fa fa-file-invoice"></i></div>
                    Customer Billing
                </a>
                <a href="{{ url('store/staff/create/billing') }}" class="qa-btn">
                    <div class="qa-icon" style="background:#f0fdf4;color:#16a34a"><i class="fa fa-user-tie"></i></div>
                    Staff Billing
                </a>
                <a href="{{ url('store/create/stockTransfer') }}" class="qa-btn">
                    <div class="qa-icon" style="background:#faf5ff;color:#7c3aed"><i class="fa fa-exchange-alt"></i></div>
                    Stock Transfer
                </a>
                <a href="{{ url('store/stock/report') }}" class="qa-btn">
                    <div class="qa-icon" style="background:#f0fdfa;color:#0d9488"><i class="fa fa-warehouse"></i></div>
                    Stock Report
                </a>
                <a href="{{ url('store/cumulative/report') }}" class="qa-btn">
                    <div class="qa-icon" style="background:#fff7ed;color:#d97706"><i class="fa fa-chart-bar"></i></div>
                    Sales Report
                </a>
                <a href="{{ url('store/expiry/report') }}" class="qa-btn">
                    <div class="qa-icon" style="background:#fef2f2;color:#dc2626"><i class="fa fa-pills"></i></div>
                    Expiry Report
                </a>
                <a href="{{ url('store/gst/report') }}" class="qa-btn">
                    <div class="qa-icon" style="background:#fffbeb;color:#b45309"><i class="fa fa-percentage"></i></div>
                    GST Report
                </a>
                <a href="{{ url('store/backup') }}" class="qa-btn">
                    <div class="qa-icon" style="background:#f5f3ff;color:#6d28d9"><i class="fa fa-database"></i></div>
                    Backup
                </a>
            </div>
        </div>

        {{-- Recent Bills --}}
        <div class="db-card">
            <div class="db-card-head">
                <h6><i class="fa fa-receipt mr-2 text-primary"></i>Recent Bills</h6>
                <div>
                    <a href="{{ url('store/customer/billing') }}" class="btn btn-sm btn-outline-primary py-0 px-2 mr-1" style="font-size:.72rem">Customer</a>
                    <a href="{{ url('store/staff/billing') }}"   class="btn btn-sm btn-outline-success py-0 px-2" style="font-size:.72rem">Staff</a>
                </div>
            </div>
            <div style="overflow-x:auto">
                <table class="db-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice</th>
                            <th>Type</th>
                            <th>Name</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="recentBills">
                        <tr><td colspan="7" class="text-center text-muted py-3" style="font-size:.8rem">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="db-side">

        {{-- Sync Actions --}}
        <div class="sync-strip">
            <span><i class="fa fa-sync-alt mr-1 text-primary"></i> Data Sync</span>
            <div class="sync-btns">
                <button class="btn btn-primary btn-sm" id="sessionValue" value="{{ Session::get('storeId') }}">
                    <i class="fa fa-download mr-1"></i> Sync In
                </button>
                <button class="btn btn-warning btn-sm" id="syncOutBtn">
                    <i class="fa fa-upload mr-1"></i> Sync Out
                </button>
            </div>
        </div>

        {{-- Alerts --}}
        <div class="db-card" style="flex:1">
            <div class="db-card-head">
                <h6><i class="fa fa-bell mr-2 text-warning"></i>Alerts & Notices</h6>
            </div>
            <div class="db-card-body" id="alertsPanel">
                <div class="text-center text-muted py-3" style="font-size:.8rem"><i class="fa fa-spinner fa-spin"></i> Loading…</div>
            </div>
        </div>

        {{-- Last Sync Info --}}
        <div class="db-card">
            <div class="db-card-head">
                <h6><i class="fa fa-clock mr-2 text-secondary"></i>Last Sync</h6>
            </div>
            <div class="db-card-body" id="lastSyncPanel">
                <div class="text-center text-muted py-2" style="font-size:.8rem">Loading…</div>
            </div>
        </div>

    </div>
</div>

{{-- Loading Modal --}}
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-4">
            <div class="spinner-border text-primary mb-3 mx-auto" role="status"></div>
            <p class="mb-0 font-weight-bold">Syncing Data…</p>
            <small class="text-muted">Please wait, do not close the page.</small>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    // Live clock
    function tickClock() {
        const t   = new Date();
        let hh    = t.getHours();
        const mm  = String(t.getMinutes()).padStart(2,'0');
        const ss  = String(t.getSeconds()).padStart(2,'0');
        const ampm = hh >= 12 ? 'PM' : 'AM';
        hh = hh % 12 || 12;
        $('#liveClock').text(String(hh).padStart(2,'0') + ':' + mm + ':' + ss);
        $('#liveAmPm').text(ampm);
    }
    tickClock();
    setInterval(tickClock, 1000);

    // Greeting & date
    const now   = new Date();
    const h     = now.getHours();
    const greet = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
    $('#greetingText').text(greet + '! 👋');
    $('#todayDate').text(now.toLocaleDateString('en-IN', { weekday:'long', day:'numeric', month:'long', year:'numeric' }));

    // Store name passed directly from server — no AJAX needed
    const storeName = '{{ \Illuminate\Support\Facades\DB::table("store")->where("store_meta_id", session("storeId"))->value("name") ?? "" }}';
    if (storeName) $('#greetingText').text(greet + ', ' + storeName + '! 👋');

    const fmt = v => '₹' + Number(v || 0).toLocaleString('en-IN', { maximumFractionDigits: 0 });

    // KPI cards
    $.get('/api/today-sale',      r => $('#todaySale').text(r.status==='success' ? fmt(r.data) : '–'));
    $.get('/api/yesterday-sale',  r => $('#yesterdaySale').text(r.status==='success' ? fmt(r.data) : '–'));
    $.get('/api/monthly-earnings',r => $('#monthlyEarnings').text(r.status==='success' ? fmt(r.data) : '–'));
    $.get('/api/zero-stock-medicine', r => $('#zeroStockMedicine').text(r.status==='success' ? r.data.count : '–'));

    $.get('/api/expiry-report', function (r) {
        if (r.status !== 200) { $('#expiredMedicines').text('–'); $('#expiringSoon').text('–'); return; }
        const today = new Date(); today.setHours(0,0,0,0);
        const soon  = new Date(today); soon.setDate(today.getDate() + 30);
        let expired = 0, expiring = 0;
        (r.data || []).forEach(item => {
            const d = new Date(item.exp_date);
            if (d < today) expired++; else if (d <= soon) expiring++;
        });
        $('#expiredMedicines').text(expired);
        $('#expiringSoon').text(expiring);

        // Alerts panel
        let html = '';
        if (expired > 0)  html += alertRow('#dc2626', `<strong>${expired} batch${expired>1?'es':''} expired</strong><small>Remove from stock immediately</small>`);
        if (expiring > 0) html += alertRow('#d97706', `<strong>${expiring} batch${expiring>1?'es':''} expiring within 30 days</strong><small>Consider offering discounts</small>`);

        $.get('/api/zero-stock-medicine', function (zr) {
            const zc = zr.status==='success' ? zr.data.count : 0;
            if (zc > 0) html += alertRow('#7c3aed', `<strong>${zc} product${zc>1?'s':''} out of stock</strong><small>Request restock from admin</small>`);
            $('#alertsPanel').html(html || `<div class="text-center py-3"><i class="fa fa-check-circle fa-2x text-success mb-2 d-block"></i><span style="font-size:.8rem;color:#64748b">No alerts — store looks great!</span></div>`);
        });
    });

    function alertRow(color, content) {
        return `<div class="alert-row"><div class="alert-dot" style="background:${color}"></div><div class="alert-text">${content}</div></div>`;
    }

    // Recent bills
    $.get('/api/cumulative-report', function (r) {
        const bills = r?.data?.transactions || [];
        if (!bills.length) {
            $('#recentBills').html('<tr><td colspan="7" class="text-center text-muted py-3" style="font-size:.8rem">No recent bills.</td></tr>');
            return;
        }
        let html = '';
        bills.slice(0, 5).forEach((b, i) => {
            const tc = (b.type||'').toLowerCase()==='customer' ? 'cust' : 'staff';
            const payIco = b.payment_type==='cash' ? 'fa-money-bill-wave' : b.payment_type==='card' ? 'fa-credit-card' : 'fa-mobile-alt';
            const d = b.date ? new Date(b.date).toLocaleDateString('en-IN',{day:'2-digit',month:'short'}) : '–';
            html += `<tr>
                <td class="text-muted">${i+1}</td>
                <td><strong>${b.invoiceNo||'–'}</strong></td>
                <td><span class="type-badge ${tc}">${b.type||'–'}</span></td>
                <td>${b.name||'–'}</td>
                <td style="font-weight:700">₹${Number(b.sales_amount||0).toLocaleString('en-IN')}</td>
                <td><i class="fa ${payIco} text-muted mr-1"></i>${b.payment_type||'–'}</td>
                <td class="text-muted">${d}</td>
            </tr>`;
        });
        $('#recentBills').html(html);
    }).fail(() => $('#recentBills').html('<tr><td colspan="7" class="text-center text-muted py-3">Could not load.</td></tr>'));

    // Last sync
    $.get('/api/get/sync/history', function (r) {
        const rows = Array.isArray(r?.data) ? r.data : [];
        if (!rows.length) { $('#lastSyncPanel').html('<p class="text-muted mb-0" style="font-size:.8rem">No sync records.</p>'); return; }
        const last = rows[0];
        const isOk = (last.sync_status||'').toLowerCase()==='succeed';
        const d = last.sync_date ? new Date(last.sync_date).toLocaleDateString('en-IN',{day:'2-digit',month:'short',year:'numeric'}) : '–';
        $('#lastSyncPanel').html(`
            <div class="d-flex align-items-center justify-content-between">
                <span style="font-size:.8rem;color:#64748b">${last.sync_type||'Sync'} &nbsp;·&nbsp; ${d}</span>
                <span style="font-size:.72rem;font-weight:700;padding:2px 8px;border-radius:8px;background:${isOk?'#f0fdf4':'#fef2f2'};color:${isOk?'#16a34a':'#dc2626'}">${isOk?'Succeed':'Failed'}</span>
            </div>
            <p style="font-size:.73rem;color:#94a3b8;margin:4px 0 0">Total syncs: ${rows.length}</p>
        `);
    });

    // Sync In
    $('#sessionValue').on('click', function () {
        const storeId = $(this).val();
        $('#loadingModal').modal('show');
        $('#loadingModal').one('shown.bs.modal', function () {
            ajaxGetData(`/sync-data/${storeId}`, function (res) {
                $('#loadingModal').modal('hide');
                $('.modal-backdrop').remove(); $('body').removeClass('modal-open');
                Swal.fire(res?.status==200 ? {title:'Sync In Complete',icon:'success',text:'Data synced successfully.'} : {title:'Failed',icon:'error',text:'Sync failed.'});
            }, function () {
                $('#loadingModal').modal('hide');
                $('.modal-backdrop').remove(); $('body').removeClass('modal-open');
                Swal.fire('Error','An error occurred.','error');
            });
        });
    });

    // Sync Out
    $('#syncOutBtn').on('click', function () {
        let result = null;
        const storeId = $('#sessionValue').val();
        $('#loadingModal').one('shown.bs.modal', function () {
            fetch(`/sync/out/data/${storeId}`, { method:'GET', headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content} })
            .then(r=>r.json()).then(d=>{ result={success:d.success,message:d.message}; $('#loadingModal').modal('hide'); })
            .catch(()=>{ result={success:false,message:'Unexpected error.'}; $('#loadingModal').modal('hide'); });
        });
        $('#loadingModal').one('hidden.bs.modal', function () {
            if (!result) return;
            $('.modal-backdrop').remove(); $('body').removeClass('modal-open');
            Swal.fire(result.success ? {title:'Sync Out Complete',icon:'success',text:result.message} : {title:'Failed',icon:'error',text:result.message});
        });
        $('#loadingModal').modal('show');
    });
});
</script>
@endsection
