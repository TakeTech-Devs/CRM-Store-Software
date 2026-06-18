@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
<style>
    /* ── Stat Cards ── */
    .stat-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,.07);
        transition: transform .15s, box-shadow .15s;
        overflow: hidden;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.12); }
    .stat-card .card-body { padding: 1.25rem 1.4rem; }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.35rem; flex-shrink: 0;
    }
    .stat-label  { font-size: .72rem; font-weight: 700; letter-spacing: .06em; text-transform: uppercase; color: #6c757d; margin-bottom: 4px; }
    .stat-value  { font-size: 1.55rem; font-weight: 700; color: #1a2035; line-height: 1.1; }
    .stat-sub    { font-size: .75rem; color: #6c757d; margin-top: 4px; }
    .stat-sub .up   { color: #28a745; }
    .stat-sub .down { color: #dc3545; }

    /* icon bg colours */
    .ic-blue   { background: #e8f0fe; color: #4361ee; }
    .ic-green  { background: #e6f9f0; color: #1cc88a; }
    .ic-purple { background: #f0eaff; color: #6f42c1; }
    .ic-orange { background: #fff3e0; color: #fd7e14; }
    .ic-red    { background: #fde8ea; color: #e74a3b; }
    .ic-teal   { background: #e0f5f5; color: #20c9a6; }

    /* ── Section headings ── */
    .section-title { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #6c757d; margin-bottom: .75rem; }

    /* ── Quick-action buttons ── */
    .quick-btn {
        border-radius: 10px; border: 1.5px solid #e2e8f0;
        background: #fff; padding: .7rem 1rem;
        display: flex; align-items: center; gap: .65rem;
        font-size: .85rem; font-weight: 600; color: #1a2035;
        text-decoration: none; transition: background .12s, border-color .12s, transform .12s;
        white-space: nowrap;
    }
    .quick-btn:hover { background: #f0f4ff; border-color: #4361ee; color: #4361ee; transform: translateY(-2px); text-decoration: none; }
    .quick-btn .qicon { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: .9rem; flex-shrink: 0; }

    /* ── Recent bills table ── */
    .recent-card { border-radius: 12px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .recent-card .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; padding: .9rem 1.25rem; border-radius: 12px 12px 0 0; font-weight: 700; font-size: .9rem; }
    .recent-card table thead th { font-size: .72rem; font-weight: 700; text-transform: uppercase; letter-spacing: .05em; color: #6c757d; border: none; padding: .6rem 1rem; background: #f8f9fc; }
    .recent-card table tbody td { padding: .65rem 1rem; vertical-align: middle; font-size: .85rem; border-top: 1px solid #f4f4f4; }
    .bill-badge { font-size: .7rem; padding: 3px 8px; border-radius: 10px; font-weight: 600; }
    .bill-badge.customer { background: #e8f0fe; color: #4361ee; }
    .bill-badge.staff    { background: #e6f9f0; color: #1cc88a; }

    /* ── Alert items ── */
    .alert-item { display: flex; align-items: center; gap: .75rem; padding: .65rem 0; border-bottom: 1px solid #f4f4f4; }
    .alert-item:last-child { border: none; }
    .alert-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

    /* ── Greeting ── */
    .greeting-bar { background: linear-gradient(135deg,#4361ee 0%,#3a0ca3 100%); border-radius: 14px; color: #fff; padding: 1.4rem 1.8rem; margin-bottom: 1.5rem; box-shadow: 0 4px 18px rgba(67,97,238,.3); }
    .greeting-bar h4 { font-weight: 700; margin: 0 0 4px; font-size: 1.15rem; }
    .greeting-bar p  { margin: 0; opacity: .85; font-size: .85rem; }
    .greeting-bar .store-chip { background: rgba(255,255,255,.18); border-radius: 20px; padding: 3px 12px; font-size: .78rem; font-weight: 600; display: inline-block; margin-top: 8px; }
</style>

<div class="container-fluid py-2">

    {{-- Greeting bar --}}
    <div class="greeting-bar d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h4 id="greetingText">Good Morning!</h4>
            <p>Here's what's happening at your store today.</p>
            <span class="store-chip"><i class="fa fa-store mr-1"></i> Store Panel &nbsp;|&nbsp; <span id="todayDate"></span></span>
        </div>
        <div class="text-right mt-2 mt-md-0">
            <a href="{{ url('store/sync/history') }}" class="btn btn-light btn-sm font-weight-bold">
                <i class="fa fa-sync-alt mr-1"></i> Sync History
            </a>
        </div>
    </div>

    {{-- ── KPI Cards Row 1 ── --}}
    <div class="row mb-3">
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3" style="gap:.9rem">
                    <div class="stat-icon ic-blue"><i class="fa fa-rupee-sign"></i></div>
                    <div>
                        <div class="stat-label">Today's Sale</div>
                        <div class="stat-value" id="todaySale"><span class="text-muted" style="font-size:1rem">…</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center" style="gap:.9rem">
                    <div class="stat-icon ic-green"><i class="fa fa-history"></i></div>
                    <div>
                        <div class="stat-label">Yesterday</div>
                        <div class="stat-value" id="yesterdaySale"><span class="text-muted" style="font-size:1rem">…</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center" style="gap:.9rem">
                    <div class="stat-icon ic-purple"><i class="fa fa-calendar-alt"></i></div>
                    <div>
                        <div class="stat-label">Monthly</div>
                        <div class="stat-value" id="monthlyEarnings"><span class="text-muted" style="font-size:1rem">…</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center" style="gap:.9rem">
                    <div class="stat-icon ic-orange"><i class="fa fa-exclamation-triangle"></i></div>
                    <div>
                        <div class="stat-label">Expiring Soon</div>
                        <div class="stat-value" id="expiringSoon"><span class="text-muted" style="font-size:1rem">…</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center" style="gap:.9rem">
                    <div class="stat-icon ic-red"><i class="fa fa-times-circle"></i></div>
                    <div>
                        <div class="stat-label">Expired</div>
                        <div class="stat-value" id="expiredMedicines"><span class="text-muted" style="font-size:1rem">…</span></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 col-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center" style="gap:.9rem">
                    <div class="stat-icon ic-teal"><i class="fa fa-box-open"></i></div>
                    <div>
                        <div class="stat-label">Zero Stock</div>
                        <div class="stat-value" id="zeroStockMedicine"><span class="text-muted" style="font-size:1rem">…</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Quick Actions ── --}}
    <div class="mb-3">
        <div class="section-title">Quick Actions</div>
        <div class="d-flex flex-wrap" style="gap:.65rem">
            <a href="{{ url('store/customer/create/billing') }}" class="quick-btn">
                <div class="qicon ic-blue"><i class="fa fa-file-invoice"></i></div> Customer Billing
            </a>
            <a href="{{ url('store/staff/create/billing') }}" class="quick-btn">
                <div class="qicon ic-green"><i class="fa fa-user-tie"></i></div> Staff Billing
            </a>
            <a href="{{ url('store/create/stockTransfer') }}" class="quick-btn">
                <div class="qicon ic-purple"><i class="fa fa-exchange-alt"></i></div> Stock Transfer
            </a>
            <a href="{{ url('store/stock/report') }}" class="quick-btn">
                <div class="qicon ic-teal"><i class="fa fa-warehouse"></i></div> Stock Report
            </a>
            <a href="{{ url('store/cumulative/report') }}" class="quick-btn">
                <div class="qicon ic-orange"><i class="fa fa-chart-bar"></i></div> Sales Report
            </a>
            <a href="{{ url('store/expiry/report') }}" class="quick-btn">
                <div class="qicon ic-red"><i class="fa fa-pills"></i></div> Expiry Report
            </a>
            <a href="{{ url('store/backup') }}" class="quick-btn">
                <div class="qicon" style="background:#f0eaff;color:#6f42c1"><i class="fa fa-database"></i></div> Backup
            </a>
        </div>
    </div>

    {{-- ── Bottom Section: Recent Bills + Alerts ── --}}
    <div class="row">
        {{-- Recent Bills --}}
        <div class="col-lg-8 mb-3">
            <div class="card recent-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fa fa-receipt mr-2 text-primary"></i>Recent Bills</span>
                    <div>
                        <a href="{{ url('store/customer/billing') }}" class="btn btn-outline-primary btn-sm mr-1">Customer</a>
                        <a href="{{ url('store/staff/billing') }}" class="btn btn-outline-success btn-sm">Staff</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Invoice No.</th>
                                <th>Type</th>
                                <th>Name</th>
                                <th>Amount</th>
                                <th>Payment</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="recentBills">
                            <tr><td colspan="7" class="text-center text-muted py-3">Loading…</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Alerts Panel --}}
        <div class="col-lg-4 mb-3">
            <div class="card recent-card h-100">
                <div class="card-header">
                    <i class="fa fa-bell mr-2 text-warning"></i>Alerts & Notices
                </div>
                <div class="card-body" id="alertsPanel">
                    <div class="text-center text-muted py-3"><i class="fa fa-spinner fa-spin"></i> Loading…</div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
$(document).ready(function () {
    // Greeting & date
    const now = new Date();
    const h = now.getHours();
    const greet = h < 12 ? 'Good Morning' : h < 17 ? 'Good Afternoon' : 'Good Evening';
    $('#greetingText').text(greet + '! 👋');
    $('#todayDate').text(now.toLocaleDateString('en-IN', { weekday:'long', day:'numeric', month:'long', year:'numeric' }));

    const fmt = v => '₹' + Number(v || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

    // Today's Sale
    $.get('/api/today-sale', function (r) {
        $('#todaySale').text(r.status === 'success' ? fmt(r.data) : '–');
    }).fail(() => $('#todaySale').text('–'));

    // Yesterday's Sale
    $.get('/api/yesterday-sale', function (r) {
        $('#yesterdaySale').text(r.status === 'success' ? fmt(r.data) : '–');
    }).fail(() => $('#yesterdaySale').text('–'));

    // Monthly Earnings
    $.get('/api/monthly-earnings', function (r) {
        $('#monthlyEarnings').text(r.status === 'success' ? fmt(r.data) : '–');
    }).fail(() => $('#monthlyEarnings').text('–'));

    // Zero Stock
    $.get('/api/zero-stock-medicine', function (r) {
        $('#zeroStockMedicine').text(r.status === 'success' ? r.data.count : '–');
    }).fail(() => $('#zeroStockMedicine').text('–'));

    // Expiry data → expired + expiring soon (within 30 days)
    $.get('/api/expiry-report', function (r) {
        if (r.status !== 200) { $('#expiredMedicines').text('–'); $('#expiringSoon').text('–'); return; }
        const today = new Date(); today.setHours(0,0,0,0);
        const soon  = new Date(today); soon.setDate(today.getDate() + 30);
        let expired = 0, expiring = 0;
        (r.data || []).forEach(item => {
            const d = new Date(item.exp_date);
            if (d < today) expired++;
            else if (d <= soon) expiring++;
        });
        $('#expiredMedicines').text(expired);
        $('#expiringSoon').text(expiring);

        // Build alerts panel
        let alertsHtml = '';
        if (expired > 0) {
            alertsHtml += `<div class="alert-item">
                <div class="alert-dot" style="background:#dc3545"></div>
                <div><strong>${expired}</strong> medicine batch${expired>1?'es':''} <span class="text-danger font-weight-bold">expired</span>
                <br><small class="text-muted">Remove from stock immediately</small></div>
            </div>`;
        }
        if (expiring > 0) {
            alertsHtml += `<div class="alert-item">
                <div class="alert-dot" style="background:#fd7e14"></div>
                <div><strong>${expiring}</strong> batch${expiring>1?'es':''} expiring <span class="text-warning font-weight-bold">within 30 days</span>
                <br><small class="text-muted">Consider offering discounts</small></div>
            </div>`;
        }

        $.get('/api/zero-stock-medicine', function (zr) {
            const zeroCount = zr.status === 'success' ? zr.data.count : 0;
            if (zeroCount > 0) {
                alertsHtml += `<div class="alert-item">
                    <div class="alert-dot" style="background:#6f42c1"></div>
                    <div><strong>${zeroCount}</strong> product${zeroCount>1?'s':''} with <span class="text-danger font-weight-bold">zero stock</span>
                    <br><small class="text-muted">Request restock from admin</small></div>
                </div>`;
            }
            if (!alertsHtml) {
                alertsHtml = `<div class="text-center py-4">
                    <i class="fa fa-check-circle fa-2x text-success mb-2"></i>
                    <p class="mb-0 text-muted">No alerts right now.<br>Your store looks good!</p>
                </div>`;
            }
            $('#alertsPanel').html(alertsHtml);
        });
    }).fail(() => { $('#expiredMedicines').text('–'); $('#expiringSoon').text('–'); $('#alertsPanel').html('<p class="text-muted text-center py-3">Could not load alerts.</p>'); });

    // Recent Bills — pull from cumulative report (last 10)
    $.get('/api/cumulative-report', function (r) {
        if (r.status !== 200 || !r.data || !r.data.length) {
            $('#recentBills').html('<tr><td colspan="7" class="text-center text-muted py-3">No recent bills.</td></tr>');
            return;
        }
        const recent = r.data.slice(0, 10);
        let html = '';
        recent.forEach(function (b, i) {
            const typeClass = (b.type || '').toLowerCase() === 'customer' ? 'customer' : 'staff';
            const payIcon = b.payment_type === 'cash' ? 'fa-money-bill-wave' : b.payment_type === 'card' ? 'fa-credit-card' : 'fa-mobile-alt';
            const dateStr = b.date ? new Date(b.date).toLocaleDateString('en-IN', {day:'2-digit', month:'short'}) : '–';
            html += `<tr>
                <td class="text-muted">${i+1}</td>
                <td><strong>${b.invoiceNo || '–'}</strong></td>
                <td><span class="bill-badge ${typeClass}">${b.type || '–'}</span></td>
                <td>${b.name || '–'}</td>
                <td class="font-weight-bold">₹${Number(b.sales_amount||0).toLocaleString('en-IN')}</td>
                <td><i class="fa ${payIcon} text-muted mr-1"></i>${b.payment_type || '–'}</td>
                <td class="text-muted">${dateStr}</td>
            </tr>`;
        });
        $('#recentBills').html(html);
    }).fail(() => {
        $('#recentBills').html('<tr><td colspan="7" class="text-center text-muted py-3">Could not load bills.</td></tr>');
    });
});
</script>
@endsection
