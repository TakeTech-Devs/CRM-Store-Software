@extends('layouts.dashboard')

@section('title', 'Analytics')

@section('content')
<style>
    .stat-card { border-radius: 12px; padding: 20px 24px; color: #fff; display: flex; flex-direction: column; gap: 4px; }
    .stat-card .stat-label { font-size: .78rem; opacity: .85; font-weight: 600; text-transform: uppercase; letter-spacing: .04em; }
    .stat-card .stat-value { font-size: 1.9rem; font-weight: 800; line-height: 1.1; }
    .stat-card .stat-sub { font-size: .75rem; opacity: .75; }
    .analytics-section-title { font-size: .8rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #94a3b8; margin-bottom: 12px; }
    .chart-card { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 6px rgba(0,0,0,.07); }
    .chart-card h6 { font-size: .85rem; font-weight: 700; color: #334155; margin-bottom: 16px; }
    .expiry-badge { font-size: .7rem; padding: 2px 8px; border-radius: 20px; font-weight: 600; }
    .expiry-expired { background: #fee2e2; color: #dc2626; }
    .expiry-soon { background: #fef3c7; color: #d97706; }
    .expiry-ok { background: #dcfce7; color: #16a34a; }
    .bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; }
    .bar-label { font-size: .78rem; color: #475569; width: 130px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .bar-track { flex: 1; background: #f1f5f9; border-radius: 6px; height: 10px; }
    .bar-fill { height: 10px; border-radius: 6px; }
    .bar-val { font-size: .75rem; color: #64748b; width: 40px; text-align: right; }
</style>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="text-dark mb-0">Analytics</h2>
    <button class="btn btn-sm btn-outline-secondary" id="refreshAnalytics"><i class="fa fa-sync-alt mr-1"></i> Refresh</button>
</div>

<!-- Sales Overview Cards -->
<div class="analytics-section-title">Sales Overview</div>
<div class="row mb-4" id="salesOverviewRow">
    <div class="col-md-4 mb-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#A54217,#d4622a);">
            <div class="stat-label">Today's Sales</div>
            <div class="stat-value" id="todaySales">—</div>
            <div class="stat-sub">All bills today</div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#2563eb,#3b82f6);">
            <div class="stat-label">This Week</div>
            <div class="stat-value" id="weekSales">—</div>
            <div class="stat-sub">Monday – today</div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="stat-card" style="background: linear-gradient(135deg,#059669,#10b981);">
            <div class="stat-label">This Month</div>
            <div class="stat-value" id="monthSales">—</div>
            <div class="stat-sub">{{ now()->format('F Y') }}</div>
        </div>
    </div>
</div>

<!-- Monthly Trend + Payment Breakdown -->
<div class="row mb-4">
    <div class="col-md-8 mb-3">
        <div class="chart-card">
            <h6><i class="fa fa-chart-line mr-1 text-primary"></i> Monthly Revenue Trend (Last 6 Months)</h6>
            <canvas id="trendChart" height="90"></canvas>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="chart-card">
            <h6><i class="fa fa-credit-card mr-1 text-warning"></i> Payment Type Breakdown</h6>
            <canvas id="paymentChart" height="160"></canvas>
            <div id="paymentLegend" class="mt-2" style="font-size:.75rem;"></div>
        </div>
    </div>
</div>

<!-- Top Products + Inhouse vs Regular -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="chart-card">
            <h6><i class="fa fa-trophy mr-1 text-warning"></i> Top 5 Products by Quantity Sold</h6>
            <div id="topProductsBars">
                <div class="text-center text-muted py-3" style="font-size:.8rem;"><i class="fa fa-spinner fa-spin"></i> Loading…</div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="chart-card">
            <h6><i class="fa fa-pills mr-1 text-success"></i> Inhouse vs Regular</h6>
            <canvas id="inhouseChart" height="160"></canvas>
            <div id="inhouseLegend" class="mt-2 text-center" style="font-size:.75rem;"></div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="chart-card">
            <h6><i class="fa fa-user-md mr-1 text-info"></i> Top Doctors by Sales</h6>
            <div id="doctorSalesBars">
                <div class="text-center text-muted py-3" style="font-size:.8rem;"><i class="fa fa-spinner fa-spin"></i> Loading…</div>
            </div>
        </div>
    </div>
</div>

<!-- Expiry Track -->
<div class="analytics-section-title">Medicines Expiring Within 90 Days</div>
<div class="chart-card mb-4">
    <div class="table-responsive">
        <table class="table table-sm text-center mb-0" id="expiryTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Pack</th>
                    <th>Qty Left</th>
                    <th>Expiry Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="6" class="text-muted py-3"><i class="fa fa-spinner fa-spin"></i> Loading…</td></tr>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
let trendChartObj = null;
let paymentChartObj = null;
let inhouseChartObj = null;

const PALETTE = ['#A54217','#2563eb','#059669','#d97706','#7c3aed','#db2777'];

function fmt(val) { return '₹' + parseFloat(val || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}); }

function loadAnalytics() {
    $.ajax({
        url: '/api/analytics',
        method: 'GET',
        success: function(r) {
            if (r.status !== 200) return;
            const d = r.data;

            // Sales Overview
            $('#todaySales').text(fmt(d.sales_overview.today));
            $('#weekSales').text(fmt(d.sales_overview.week));
            $('#monthSales').text(fmt(d.sales_overview.month));

            // Monthly Trend Chart
            const months = d.monthly_trend.map(m => m.month);
            const amounts = d.monthly_trend.map(m => m.amount);
            if (trendChartObj) trendChartObj.destroy();
            trendChartObj = new Chart(document.getElementById('trendChart'), {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Revenue (₹)',
                        data: amounts,
                        backgroundColor: 'rgba(165,66,23,.15)',
                        borderColor: '#A54217',
                        borderWidth: 2,
                        borderRadius: 6,
                        type: 'bar',
                    }, {
                        label: 'Trend',
                        data: amounts,
                        borderColor: '#2563eb',
                        borderWidth: 2,
                        pointRadius: 3,
                        type: 'line',
                        fill: false,
                        tension: 0.4,
                    }]
                },
                options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { callback: v => '₹' + v.toLocaleString('en-IN') } } } }
            });

            // Payment Breakdown Donut
            const payLabels = d.payment_breakdown.map(p => p.paymentType || 'Unknown');
            const payAmts = d.payment_breakdown.map(p => parseFloat(p.total) || 0);
            if (paymentChartObj) paymentChartObj.destroy();
            paymentChartObj = new Chart(document.getElementById('paymentChart'), {
                type: 'doughnut',
                data: { labels: payLabels, datasets: [{ data: payAmts, backgroundColor: PALETTE, borderWidth: 2 }] },
                options: { plugins: { legend: { display: false } }, cutout: '65%' }
            });
            $('#paymentLegend').html(payLabels.map((l, i) =>
                `<span class="mr-2"><span style="display:inline-block;width:10px;height:10px;background:${PALETTE[i]};border-radius:50%;"></span> ${l}: ${fmt(payAmts[i])}</span>`
            ).join(''));

            // Top Products Bars
            const maxQty = Math.max(...d.top_products.map(p => p.total_qty), 1);
            if (d.top_products.length === 0) {
                $('#topProductsBars').html('<div class="text-muted text-center py-3" style="font-size:.8rem;">No data</div>');
            } else {
                $('#topProductsBars').html(d.top_products.map((p, i) => `
                    <div class="bar-row">
                        <div class="bar-label" title="${p.product_name}">${p.product_name}</div>
                        <div class="bar-track"><div class="bar-fill" style="width:${(p.total_qty/maxQty*100).toFixed(1)}%;background:${PALETTE[i % PALETTE.length]};"></div></div>
                        <div class="bar-val">${p.total_qty}</div>
                    </div>
                `).join(''));
            }

            // Inhouse vs Regular Donut
            const irData = [d.inhouse_vs_regular.regular, d.inhouse_vs_regular.inhouse];
            if (inhouseChartObj) inhouseChartObj.destroy();
            inhouseChartObj = new Chart(document.getElementById('inhouseChart'), {
                type: 'doughnut',
                data: { labels: ['Regular', 'Inhouse'], datasets: [{ data: irData, backgroundColor: ['#2563eb','#059669'], borderWidth: 2 }] },
                options: { plugins: { legend: { display: false } }, cutout: '60%' }
            });
            $('#inhouseLegend').html(`
                <span style="color:#2563eb; font-weight:600;">&#9632; Regular</span>: ${d.inhouse_vs_regular.regular} units &nbsp;
                <span style="color:#059669; font-weight:600;">&#9632; Inhouse</span>: ${d.inhouse_vs_regular.inhouse} units
            `);

            // Doctor Sales Bars
            if (d.doctor_sales.length === 0) {
                $('#doctorSalesBars').html('<div class="text-muted text-center py-3" style="font-size:.8rem;">No data</div>');
            } else {
                const maxDoc = Math.max(...d.doctor_sales.map(x => parseFloat(x.total_amt)), 1);
                $('#doctorSalesBars').html(d.doctor_sales.map((doc, i) => `
                    <div class="bar-row">
                        <div class="bar-label" title="${doc.doctor_name}">${doc.doctor_name}</div>
                        <div class="bar-track"><div class="bar-fill" style="width:${(parseFloat(doc.total_amt)/maxDoc*100).toFixed(1)}%;background:${PALETTE[i % PALETTE.length]};"></div></div>
                        <div class="bar-val" style="width:60px;">${fmt(doc.total_amt)}</div>
                    </div>
                `).join(''));
            }

            // Expiry Table
            const today = new Date(); today.setHours(0,0,0,0);
            const soon = new Date(); soon.setDate(soon.getDate() + 30);
            if (d.expiry_track.length === 0) {
                $('#expiryTable tbody').html('<tr><td colspan="6" class="text-success py-3">No medicines expiring within 90 days</td></tr>');
            } else {
                $('#expiryTable tbody').html(d.expiry_track.map((item, i) => {
                    const exp = new Date(item.expiry_date);
                    let badge = '';
                    if (exp < today) badge = '<span class="expiry-badge expiry-expired">Expired</span>';
                    else if (exp <= soon) badge = '<span class="expiry-badge expiry-soon">Expiring Soon</span>';
                    else badge = '<span class="expiry-badge expiry-ok">Within 90 days</span>';
                    return `<tr>
                        <td>${i+1}</td>
                        <td>${item.product_name}</td>
                        <td>${item.pack_name}</td>
                        <td>${item.qty}</td>
                        <td>${item.expiry_date}</td>
                        <td>${badge}</td>
                    </tr>`;
                }).join(''));
            }
        },
        error: function() {
            $('#todaySales,#weekSales,#monthSales').text('Error');
        }
    });
}

$(document).ready(function() {
    loadAnalytics();
    $('#refreshAnalytics').on('click', loadAnalytics);
});
</script>
@endsection
