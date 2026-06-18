@extends('layouts.dashboard')

@section('title', 'Create Stock Transfer')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Create Stock Transfer</h2>
        <a href="{{ url('store/stock/transfer') }}" class="btn btn-secondary btn-sm">View Transfers</a>
    </div>

    <div class="card p-3 mb-3">
        <div class="form-row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Transfer No.</label>
                    <input type="text" class="form-control" value="Auto-generated on submit" readonly>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="to_store_id">Transfer To <span class="text-danger">*</span></label>
                    <select id="to_store_id" class="form-control select2-store">
                        <option value="">-- Select Destination Store --</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="notes">Notes</label>
                    <input type="text" id="notes" class="form-control" placeholder="Optional notes">
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive border mb-2">
        <table class="table table-bordered table-sm text-center mb-0" id="itemsTable">
            <thead class="thead-light">
                <tr>
                    <th style="min-width:280px">Product – Pack – Price</th>
                    <th>Available Qty</th>
                    <th style="min-width:110px">Transfer Qty <span class="text-danger">*</span></th>
                    <th>Unit Value</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="itemsBody"></tbody>
        </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <button type="button" id="addRow" class="btn btn-sm btn-outline-secondary">+ Add Row</button>
    </div>

    <div class="text-right">
        <button type="button" id="submitTransfer" class="btn btn-primary">Submit Transfer</button>
    </div>
</div>

<script>
$(document).ready(function () {
    let productOptions = [];
    let rowCount = 0;

    // Load stores — destroy global select2 init first, repopulate, reinit
    $.get('/api/stores', function (res) {
        if (res.status === 200 && res.data.length) {
            $('#to_store_id').select2('destroy');
            $('#to_store_id').empty().append('<option value="">-- Select Destination Store --</option>');
            $.each(res.data, function (i, s) {
                const addr = s.store_address ? ' – ' + s.store_address : '';
                $('#to_store_id').append(`<option value="${s.id}">${s.name}${addr}</option>`);
            });
            $('#to_store_id').select2({ placeholder: '-- Select Destination Store --', width: '100%' });
        } else {
            console.warn('Stores API response:', res);
        }
    }).fail(function (xhr) {
        console.error('Stores load failed:', xhr.responseText);
    });

    // Load product options first, THEN add the first row
    $.get('/api/billing/product-options', function (res) {
        if (res.status === 200) {
            productOptions = res.data;
        }
        addRow(); // first row added only after options are ready
    }).fail(function () {
        addRow(); // still add a row even if load fails
    });

    function buildProductSelect(rowId, selectedPrId) {
        let opts = `<option value="">-- Select Product –  Pack – Price --</option>`;
        productOptions.forEach(function (p) {
            const label = `${p.product_name} – ${p.pack_name} – ${p.price_name} (Qty: ${p.avail_qty})`;
            const sel   = selectedPrId == p.purchase_request_id ? 'selected' : '';
            opts += `<option value="${p.purchase_request_id}" ${sel}
                        data-product-id="${p.product_id}"
                        data-product-name="${p.product_name}"
                        data-pack-id="${p.pack_id}"
                        data-pack-name="${p.pack_name}"
                        data-price-id="${p.price_id}"
                        data-price-name="${p.price_name}"
                        data-brand-id="${p.brand_id ?? ''}"
                        data-avail-qty="${p.avail_qty}"
                        data-unit-value="${p.unit_value ?? p.price_name}"
                    >${label}</option>`;
        });
        return opts;
    }

    function addRow() {
        rowCount++;
        const id = rowCount;
        const row = `
            <tr id="row_${id}">
                <td>
                    <select class="form-control select2-product product-select" id="product_select_${id}" data-row="${id}">
                        ${buildProductSelect(id, null)}
                    </select>
                </td>
                <td class="align-middle"><span id="avail_qty_${id}">–</span></td>
                <td>
                    <input type="number" class="form-control transfer-qty" id="transfer_qty_${id}" data-row="${id}" min="1" step="1" placeholder="0">
                </td>
                <td class="align-middle"><span id="unit_value_${id}">–</span></td>
                <td class="align-middle">
                    <button type="button" class="btn btn-sm btn-danger remove-row" data-row="${id}"><i class="fa fa-trash"></i></button>
                </td>
            </tr>`;
        $('#itemsBody').append(row);
        $(`#product_select_${id}`).select2({ placeholder: '-- Select Product – Pack – Price --', width: '100%' });
    }

    $('#addRow').on('click', addRow);

    $(document).on('change', '.product-select', function () {
        const rowId = $(this).data('row');
        const opt   = $(this).find('option:selected');
        const avail = opt.data('avail-qty') ?? '–';
        const unit  = opt.data('price-name') ?? opt.data('unit-value') ?? '–';
        $(`#avail_qty_${rowId}`).text(avail !== undefined ? avail : '–');
        $(`#unit_value_${rowId}`).text(unit !== undefined ? unit : '–');
        $(`#transfer_qty_${rowId}`).attr('max', avail);
    });

    $(document).on('click', '.remove-row', function () {
        const rowId = $(this).data('row');
        $(`#row_${rowId}`).remove();
    });

    $('#submitTransfer').on('click', function () {
        const toStoreId = $('#to_store_id').val();
        if (!toStoreId) {
            Swal.fire('Validation', 'Please select a destination store.', 'warning');
            return;
        }

        const items = [];
        let valid = true;

        $('#itemsBody tr').each(function () {
            const rowId = $(this).attr('id').replace('row_', '');
            const sel   = $(`#product_select_${rowId}`);
            const opt   = sel.find('option:selected');
            const qty   = parseFloat($(`#transfer_qty_${rowId}`).val()) || 0;

            if (!sel.val()) { valid = false; Swal.fire('Validation', 'Please select a product for every row.', 'warning'); return false; }
            if (qty <= 0)   { valid = false; Swal.fire('Validation', 'Transfer qty must be > 0.', 'warning'); return false; }
            const avail = parseFloat(opt.data('avail-qty')) || 0;
            if (qty > avail){ valid = false; Swal.fire('Validation', `Transfer qty exceeds available stock for ${opt.data('product-name')}.`, 'warning'); return false; }

            items.push({
                purchase_request_id: sel.val(),
                product_id:   opt.data('product-id'),
                product_name: opt.data('product-name'),
                pack_id:      opt.data('pack-id'),
                pack_name:    opt.data('pack-name'),
                price_id:     opt.data('price-id'),
                brand_id:     opt.data('brand-id') || null,
                unit_value:   opt.data('unit-value') || opt.data('price-name'),
                qty:          qty,
            });
        });

        if (!valid) return;
        if (items.length === 0) { Swal.fire('Validation', 'Add at least one product row.', 'warning'); return; }

        const payload = {
            to_store_id: toStoreId,
            notes:       $('#notes').val(),
            items:       items,
        };

        $.ajax({
            url: '/api/stock-transfer/create',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(payload),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.status === 200) {
                    Swal.fire({
                        title: 'Transfer Created!',
                        icon: 'success',
                        text: `Transfer No: ${res.transfer_no}`,
                    }).then(() => { window.location.href = '/store/stock/transfer'; });
                } else {
                    Swal.fire('Error', res.message || 'Something went wrong.', 'error');
                }
            },
            error: function (xhr) {
                const msg = xhr.responseJSON?.message || 'An error occurred.';
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
@endsection
