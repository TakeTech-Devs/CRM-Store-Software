@extends('layouts.dashboard')

@section('title', 'Stock Transfer')

@section('content')
<style>
    .loader {
        border: 10px solid #f3f3f3;
        border-top: 10px solid #A54217;
        border-radius: 50%;
        width: 50px; height: 50px;
        animation: spin 1.5s linear infinite;
        margin: 60px auto; display: none;
    }
    @keyframes spin { 0%{ transform:rotate(0deg);} 100%{ transform:rotate(360deg);} }
    .badge-pending  { background:#ffc107; color:#000; }
    .badge-received { background:#28a745; color:#fff; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Stock Transfer</h2>
        <a href="{{ url('store/create/stockTransfer') }}" class="btn btn-primary btn-sm">+ Create Transfer</a>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs mb-3" id="transferTabs">
        <li class="nav-item">
            <a class="nav-link active" href="#" data-type="sent">Sent</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#" data-type="received">Received</a>
        </li>
    </ul>

    {{-- Filters --}}
    <div class="form-row align-items-end mb-3">
        <div class="col-auto">
            <label>Start Date</label>
            <input type="date" class="form-control" id="start_date">
        </div>
        <div class="col-auto">
            <label>End Date</label>
            <input type="date" class="form-control" id="end_date">
        </div>
        <div class="col-auto" style="margin-top:1.85rem">
            <button class="btn btn-success btn-md" id="filterBtn">Find</button>
            <button class="btn btn-secondary btn-md ml-1" id="clearBtn">Clear</button>
        </div>
    </div>

    <div class="loader" id="loader"></div>

    <div class="table-responsive border mt-2 mb-5">
        <table class="table table-bordered text-center mb-0">
            <thead class="thead-light">
                <tr>
                    <th>#</th>
                    <th>Transfer No.</th>
                    <th>From Store</th>
                    <th>To Store</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="transferBody">
                <tr><td colspan="8" class="text-muted">Loading…</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transfer Detail – <span id="modal_transfer_no"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4"><strong>From:</strong> <span id="modal_from"></span></div>
                    <div class="col-md-4"><strong>To:</strong> <span id="modal_to"></span></div>
                    <div class="col-md-4"><strong>Date:</strong> <span id="modal_date"></span></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-4"><strong>Status:</strong> <span id="modal_status"></span></div>
                    <div class="col-md-4"><strong>Received At:</strong> <span id="modal_received_at"></span></div>
                    <div class="col-md-4"><strong>Notes:</strong> <span id="modal_notes"></span></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th>Product</th>
                                <th>Pack</th>
                                <th>Price / Unit</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody id="modal_items"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let currentType = 'sent';

    function loadTransfers(type, startDate, endDate) {
        $('#loader').show();
        $('#transferBody').html('<tr><td colspan="8" class="text-muted">Loading…</td></tr>');

        $.ajax({
            url: '/api/stock-transfer/list',
            type: 'GET',
            data: { type: type, start_date: startDate || '', end_date: endDate || '' },
            success: function (res) {
                $('#loader').hide();
                if (res.status !== 200 || !res.data.length) {
                    $('#transferBody').html('<tr><td colspan="8" class="text-muted">No transfers found.</td></tr>');
                    return;
                }

                let html = '';
                res.data.forEach(function (t, idx) {
                    const statusBadge = t.status === 'received'
                        ? `<span class="badge badge-received">Received</span>`
                        : `<span class="badge badge-pending">Pending</span>`;

                    html += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td><strong>${t.transfer_no}</strong></td>
                            <td>${t.from_store_name}</td>
                            <td>${t.to_store_name}</td>
                            <td>${t.transfer_date}</td>
                            <td>${t.item_count}</td>
                            <td>${statusBadge}</td>
                            <td>
                                <button class="btn btn-sm btn-info view-detail" data-id="${t.id}">View</button>
                            </td>
                        </tr>`;
                });
                $('#transferBody').html(html);
            },
            error: function () {
                $('#loader').hide();
                $('#transferBody').html('<tr><td colspan="8" class="text-danger">Failed to load transfers.</td></tr>');
            }
        });
    }

    // Tab switching
    $('#transferTabs a').on('click', function (e) {
        e.preventDefault();
        $('#transferTabs a').removeClass('active');
        $(this).addClass('active');
        currentType = $(this).data('type');
        loadTransfers(currentType, $('#start_date').val(), $('#end_date').val());
    });

    $('#filterBtn').on('click', function () {
        const s = $('#start_date').val();
        const e = $('#end_date').val();
        if (s && e && e < s) {
            Swal.fire('Validation', 'End date must be after start date.', 'warning');
            return;
        }
        loadTransfers(currentType, s, e);
    });

    $('#clearBtn').on('click', function () {
        $('#start_date').val('');
        $('#end_date').val('');
        loadTransfers(currentType, '', '');
    });

    // View detail
    $(document).on('click', '.view-detail', function () {
        const id = $(this).data('id');
        $.get(`/api/stock-transfer/${id}`, function (res) {
            if (res.status !== 200) { Swal.fire('Error', 'Could not load detail.', 'error'); return; }
            const d = res.data;
            $('#modal_transfer_no').text(d.transfer.transfer_no);
            $('#modal_from').text(d.from_store_name);
            $('#modal_to').text(d.to_store_name);
            $('#modal_date').text(d.transfer.transfer_date);
            const statusBadge = d.transfer.status === 'received'
                ? `<span class="badge badge-received">Received</span>`
                : `<span class="badge badge-pending">Pending</span>`;
            $('#modal_status').html(statusBadge);
            $('#modal_received_at').text(d.transfer.received_at ?? '–');
            $('#modal_notes').text(d.transfer.notes || '–');

            let itemsHtml = '';
            d.items.forEach(function (item, i) {
                itemsHtml += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.product_name}</td>
                        <td>${item.pack_name}</td>
                        <td>${item.unit_value}</td>
                        <td>${item.qty}</td>
                    </tr>`;
            });
            $('#modal_items').html(itemsHtml);
            $('#detailModal').modal('show');
        }).fail(function () {
            Swal.fire('Error', 'Could not load detail.', 'error');
        });
    });

    // Initial load
    loadTransfers(currentType, '', '');
});
</script>
@endsection
