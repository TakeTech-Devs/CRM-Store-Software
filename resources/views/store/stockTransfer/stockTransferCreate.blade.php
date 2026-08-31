@extends('layouts.dashboard')

@section('title', 'Create Stock Transfer')

@section('content')
<style>
    /* Visible keyboard focus ring — the sb-admin-2 theme suppresses this by
       default (outline:0), which makes keyboard-only navigation unusable.
       Scoped to this page only; the sidebar keeps its default (no ring) look. */
    a:focus,
    button:focus,
    .btn:focus,
    input:focus,
    select:focus,
    textarea:focus,
    .select2-selection:focus,
    .select2-container--default .select2-selection--single:focus,
    .select2-search__field:focus {
        outline: 2px solid #0d6efd !important;
        outline-offset: 2px !important;
    }
    .sidebar a:focus,
    .sidebar button:focus,
    #sidebarToggle:focus {
        outline: none !important;
    }
</style>
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
                    <th style="min-width:150px">Brand</th>
                    <th style="min-width:180px">Product</th>
                    <th style="min-width:120px">Pack</th>
                    <th style="min-width:150px">Price</th>
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
    let brandsList = [];
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

    // Brands with current in-stock, non-expired products — Inhouse excluded,
    // since inhouse products aren't stock-tracked per store and can't be transferred.
    function loadBrands(callback) {
        if (brandsList.length > 0) { callback(brandsList); return; }
        $.get('/billing/brands', function (res) {
            brandsList = (res.data || []).filter(b => b.id !== 'inhouse');
            callback(brandsList);
        });
    }

    function loadProductOptions(callback) {
        if (productOptions.length > 0) { callback(productOptions); return; }
        $.get('/api/billing/product-options', function (res) {
            productOptions = (res.data || []).filter(o => o.type === 'regular');
            callback(productOptions);
        });
    }

    // Opens a Select2 dropdown, but only if it has actually finished initializing.
    // Select2 doesn't reliably move keyboard focus into its own search box when
    // opened programmatically, so force it directly here every time. It also
    // doesn't always close a previously-open dropdown when a different one is
    // opened programmatically, so do that explicitly first.
    function openSelect2Safe(selector) {
        const $el = $(selector);
        if (!$el.hasClass('select2-hidden-accessible')) return;
        closeAllSelect2($el.attr('id'));
        $el.select2('open');
        setTimeout(function () {
            document.querySelector('.select2-search__field')?.focus({ preventScroll: true });
        }, 0);
    }

    function closeAllSelect2(exceptId) {
        $('.select2-hidden-accessible').each(function () {
            const $s = $(this);
            if (this.id !== exceptId && $s.data('select2') && $s.data('select2').isOpen()) {
                $s.select2('close');
            }
        });
    }

    // Select2 sometimes opens a long results list scrolled to a stale position
    $(document).on('select2:open', function () {
        setTimeout(function () {
            const el = document.querySelector('.select2-results__options');
            if (el) el.scrollTop = 0;
        }, 0);
    });

    function resetRowFrom(id, field) {
        const fields = ['product', 'pack', 'price'];
        fields.slice(fields.indexOf(field)).forEach(f => {
            const sel = $(`#${f}_select_${id}`);
            if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
            sel.empty().append(`<option value="">-- Select ${f.charAt(0).toUpperCase() + f.slice(1)} --</option>`).prop('disabled', true);
            sel.off('select2:select select2:clear');
        });
        $(`#avail_qty_${id}`).text('–');
        $(`#unit_value_${id}`).text('–');
        $(`#transfer_qty_${id}`).val('').removeAttr('max');
    }

    function populateBrandSelect(id) {
        loadBrands(function (brands) {
            const sel = $(`#brand_select_${id}`);
            sel.empty().append('<option value="">-- Select Brand --</option>');
            brands.forEach(b => sel.append(`<option value="${b.id}">${b.name}</option>`));
            if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
            sel.select2({ width: '100%', placeholder: '-- Select Brand --' });
            sel.off('select2:select select2:clear').on('select2:select select2:clear', function () {
                const brandId = $(this).val();
                resetRowFrom(id, 'product');
                if (!brandId) return;
                loadProductsForBrand(id, brandId);
            });
        });
    }

    function loadProductsForBrand(id, brandId) {
        loadProductOptions(function (options) {
            const sel = $(`#product_select_${id}`);
            sel.empty().append('<option value="">-- Select Product --</option>');
            const seen = new Set();
            options.filter(o => String(o.brand_id) === String(brandId)).forEach(o => {
                if (!seen.has(o.product_id)) {
                    seen.add(o.product_id);
                    sel.append(`<option value="${o.product_id}">${o.product_name}</option>`);
                }
            });
            sel.prop('disabled', false);
            if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
            sel.select2({ width: '100%', placeholder: '-- Select Product --' });
            sel.off('select2:select select2:clear').on('select2:select select2:clear', function () {
                const productId = $(this).val();
                resetRowFrom(id, 'pack');
                if (!productId) return;
                loadPacksForProduct(id, productId, brandId);
            });
            openSelect2Safe(`#product_select_${id}`);
        });
    }

    function loadPacksForProduct(id, productId, brandId) {
        loadProductOptions(function (options) {
            const sel = $(`#pack_select_${id}`);
            sel.empty().append('<option value="">-- Select Pack --</option>');
            const seen = new Set();
            options.filter(o => String(o.product_id) === String(productId) && String(o.brand_id) === String(brandId)).forEach(o => {
                if (!seen.has(o.pack_id)) {
                    seen.add(o.pack_id);
                    sel.append(`<option value="${o.pack_id}">${o.pack_name}</option>`);
                }
            });
            sel.prop('disabled', false);
            if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
            sel.select2({ width: '100%', placeholder: '-- Select Pack --' });
            sel.off('select2:select select2:clear').on('select2:select select2:clear', function () {
                const packId = $(this).val();
                resetRowFrom(id, 'price');
                if (!packId) return;
                loadPricesForPack(id, productId, packId, brandId);
            });
            openSelect2Safe(`#pack_select_${id}`);
        });
    }

    function loadPricesForPack(id, productId, packId, brandId) {
        loadProductOptions(function (options) {
            const sel = $(`#price_select_${id}`);
            sel.empty().append('<option value="">-- Select Price --</option>');
            // Batches (purchase_request rows) sharing the same price are merged into
            // one option — staff have no batch/expiry number to tell them apart, so
            // showing "Qty: 3" once matches the Stock Report instead of confusing
            // duplicate-looking "Qty: 2" / "Qty: 1" entries. The backend splits the
            // entered qty across the underlying batches itself (oldest-expiry-first)
            // when the transfer is submitted.
            const rows = options.filter(o =>
                String(o.product_id) === String(productId) &&
                String(o.pack_id) === String(packId) &&
                String(o.brand_id) === String(brandId)
            );
            const merged = new Map();
            rows.forEach(o => {
                if (!merged.has(o.price_id)) merged.set(o.price_id, { ...o, avail_qty: 0 });
                merged.get(o.price_id).avail_qty += parseFloat(o.avail_qty) || 0;
            });
            merged.forEach(o => {
                const label = `₹${o.price_name} (Qty: ${o.avail_qty})`;
                sel.append(`<option value="${o.price_id}"
                    data-product-id="${o.product_id}"
                    data-product-name="${o.product_name}"
                    data-pack-id="${o.pack_id}"
                    data-pack-name="${o.pack_name}"
                    data-price-id="${o.price_id}"
                    data-price-name="${o.price_name}"
                    data-brand-id="${o.brand_id ?? ''}"
                    data-avail-qty="${o.avail_qty}"
                >${label}</option>`);
            });
            sel.prop('disabled', false);
            if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
            sel.select2({ width: '100%', placeholder: '-- Select Price --' });
            sel.off('select2:select select2:clear').on('select2:select select2:clear', function () {
                applyPriceSelection(id, $(this).find('option:selected'));
            });
            openSelect2Safe(`#price_select_${id}`);
        });
    }

    function applyPriceSelection(id, opt) {
        const val = opt.val();
        if (!val) {
            $(`#avail_qty_${id}`).text('–');
            $(`#unit_value_${id}`).text('–');
            $(`#transfer_qty_${id}`).val('').removeAttr('max');
            return;
        }
        const avail = opt.data('avail-qty');
        $(`#avail_qty_${id}`).text(avail);
        $(`#unit_value_${id}`).text(opt.data('price-name'));
        $(`#transfer_qty_${id}`).attr('max', avail);
        document.getElementById(`transfer_qty_${id}`)?.focus({ preventScroll: true });
    }

    // Load product options first, THEN add the first row
    loadProductOptions(function () {
        addRow();
    });

    function addRow() {
        rowCount++;
        const id = rowCount;
        const row = `
            <tr id="row_${id}">
                <td>
                    <select class="form-control" id="brand_select_${id}" data-row="${id}">
                        <option value="">-- Select Brand --</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" id="product_select_${id}" data-row="${id}" disabled>
                        <option value="">Select brand first</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" id="pack_select_${id}" data-row="${id}" disabled>
                        <option value="">-- Select Pack --</option>
                    </select>
                </td>
                <td>
                    <select class="form-control" id="price_select_${id}" data-row="${id}" disabled>
                        <option value="">-- Select Price --</option>
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
        populateBrandSelect(id);
    }

    $('#addRow').on('click', addRow);

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
            const sel   = $(`#price_select_${rowId}`);
            const opt   = sel.find('option:selected');
            const qty   = parseFloat($(`#transfer_qty_${rowId}`).val()) || 0;

            if (!sel.val()) { valid = false; Swal.fire('Validation', 'Please select a product for every row.', 'warning'); return false; }
            if (qty <= 0)   { valid = false; Swal.fire('Validation', 'Transfer qty must be > 0.', 'warning'); return false; }
            const avail = parseFloat(opt.data('avail-qty')) || 0;
            if (qty > avail){ valid = false; Swal.fire('Validation', `Transfer qty exceeds available stock for ${opt.data('product-name')}.`, 'warning'); return false; }

            items.push({
                product_id:   opt.data('product-id'),
                product_name: opt.data('product-name'),
                pack_id:      opt.data('pack-id'),
                pack_name:    opt.data('pack-name'),
                price_id:     opt.data('price-id'),
                brand_id:     opt.data('brand-id') || null,
                unit_value:   opt.data('price-name'),
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
