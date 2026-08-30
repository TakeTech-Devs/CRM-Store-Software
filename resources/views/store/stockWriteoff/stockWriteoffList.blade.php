@extends('layouts.dashboard')

@section('title', 'Stock Write-off')

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
    .badge-defective { background:#dc3545; color:#fff; }
    .badge-broken    { background:#fd7e14; color:#fff; }
    .badge-expired   { background:#6c757d; color:#fff; }
    .badge-other     { background:#adb5bd; color:#000; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Stock Write-off</h2>
        <a href="{{ url('store/create/stockWriteoff') }}" class="btn btn-primary btn-sm">+ Create Write-off</a>
    </div>

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
                    <th>Write-off No.</th>
                    <th>Date</th>
                    <th>Items</th>
                    <th>Total Qty</th>
                    <th>Notes</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="writeoffBody">
                <tr><td colspan="7" class="text-muted">Loading…</td></tr>
            </tbody>
        </table>
    </div>
</div>

{{-- Detail Modal --}}
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Write-off Detail – <span id="modal_writeoff_no"></span></h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Date:</strong> <span id="modal_date"></span></div>
                    <div class="col-md-6"><strong>Notes:</strong> <span id="modal_notes"></span></div>
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
                                <th>Reason</th>
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
    function reasonBadge(reason) {
        const labels = { defective: 'Defective', broken: 'Broken', expired: 'Expired', other: 'Other' };
        return `<span class="badge badge-${reason}">${labels[reason] || reason}</span>`;
    }

    function loadWriteoffs(startDate, endDate) {
        $('#loader').show();
        $('#writeoffBody').html('<tr><td colspan="7" class="text-muted">Loading…</td></tr>');

        $.ajax({
            url: '/api/stock-writeoff/list',
            type: 'GET',
            data: { start_date: startDate || '', end_date: endDate || '' },
            success: function (res) {
                $('#loader').hide();
                if (res.status !== 200 || !res.data.length) {
                    $('#writeoffBody').html('<tr><td colspan="7" class="text-muted">No write-offs found.</td></tr>');
                    return;
                }

                let html = '';
                res.data.forEach(function (w, idx) {
                    html += `
                        <tr>
                            <td>${idx + 1}</td>
                            <td><strong>${w.writeoff_no}</strong></td>
                            <td>${w.writeoff_date}</td>
                            <td>${w.item_count}</td>
                            <td>${w.total_qty}</td>
                            <td>${w.notes || '–'}</td>
                            <td>
                                <button class="btn btn-sm btn-info view-detail" data-id="${w.id}">View</button>
                            </td>
                        </tr>`;
                });
                $('#writeoffBody').html(html);
            },
            error: function () {
                $('#loader').hide();
                $('#writeoffBody').html('<tr><td colspan="7" class="text-danger">Failed to load write-offs.</td></tr>');
            }
        });
    }

    $('#filterBtn').on('click', function () {
        const s = $('#start_date').val();
        const e = $('#end_date').val();
        if (s && e && e < s) {
            Swal.fire('Validation', 'End date must be after start date.', 'warning');
            return;
        }
        loadWriteoffs(s, e);
    });

    $('#clearBtn').on('click', function () {
        $('#start_date').val('');
        $('#end_date').val('');
        loadWriteoffs('', '');
    });

    // View detail
    $(document).on('click', '.view-detail', function () {
        const id = $(this).data('id');
        $.get(`/api/stock-writeoff/${id}`, function (res) {
            if (res.status !== 200) { Swal.fire('Error', 'Could not load detail.', 'error'); return; }
            const d = res.data;
            $('#modal_writeoff_no').text(d.writeoff.writeoff_no);
            $('#modal_date').text(d.writeoff.writeoff_date);
            $('#modal_notes').text(d.writeoff.notes || '–');

            let itemsHtml = '';
            d.items.forEach(function (item, i) {
                itemsHtml += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${item.product_name}</td>
                        <td>${item.pack_name}</td>
                        <td>${item.unit_value}</td>
                        <td>${item.qty}</td>
                        <td>${reasonBadge(item.reason)}</td>
                    </tr>`;
            });
            $('#modal_items').html(itemsHtml);
            $('#detailModal').modal('show');
        }).fail(function () {
            Swal.fire('Error', 'Could not load detail.', 'error');
        });
    });

    // Initial load
    loadWriteoffs('', '');
});
</script>
@endsection
