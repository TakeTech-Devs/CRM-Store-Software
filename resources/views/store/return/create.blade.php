@extends('layouts.dashboard')

@section('title', 'Create Return')

@section('content')
<style>
    .billing-header-section {
        background: #f5e0d0;
        border: 1px solid #e0b89e;
        border-radius: 10px;
        padding: 1.2rem 1.4rem 0.4rem;
        margin-bottom: 1.2rem;
    }
    .billing-header-section label { font-weight: 700; }
    .bill-card {
        border: 1px solid #e0b89e;
        border-radius: 8px;
        padding: 10px 14px;
        margin-bottom: 8px;
        cursor: pointer;
        background: #fff;
    }
    .bill-card:hover { background: #fdf4f0; }
    .bill-card.selected { border-color: #A54217; background: #fdf4f0; box-shadow: 0 0 0 2px #A5421733; }
    .product-tbody {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 6px;
        border: 1px solid #e5e5e5;
        background-color: #ffffff;
        margin-bottom: 6px;
    }
    .product-tbody:nth-child(even) { background-color: #fdf4f0; border-color: #a8a8a8; }
    .product-field { display: flex; flex-direction: column; min-width: 0; }
    .product-field small { white-space: nowrap; }
    .pf-product  { flex: 1.4; }
    .pf-pack     { flex: 0.8; }
    .pf-unit     { flex: 0.6; }
    .pf-billed   { flex: 0.6; }
    .pf-returnable { flex: 0.6; }
    .pf-return-qty { flex: 0.7; }
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Create Return</h2>
        <div class="text-right">
            <a href="{{ url('store/return/list') }}" class="btn btn-secondary btn-sm">View Credit Notes</a>
        </div>
    </div>

    <div class="billing-header-section">
        <div class="form-row mb-2">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Billing Type</label>
                    <select id="billing_type" class="form-control">
                        <option value="customer">Customer</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
            </div>
            <div class="col-md-5">
                <div class="form-group">
                    <label for="return_phone">Phone Number</label>
                    <select id="return_phone" class="form-control select2-manual" style="width:100%">
                        <option value="">Choose phone number...</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="button" id="findBillsBtn" class="btn btn-primary form-control">Find Recent Bills</button>
                </div>
            </div>
        </div>
    </div>

    <div id="billsWrap" style="display:none;">
        <h5 class="text-dark">Select a bill to return from <small class="text-muted">(last 3 bills, within 1 month)</small></h5>
        <div id="billCards"></div>
        <div id="noBillsMsg" class="text-muted" style="display:none;">No eligible bills found for this phone number in the last month.</div>
    </div>

    <div id="itemsWrap" style="display:none;" class="mt-4">
        <h5 class="text-dark">Items on this bill</h5>
        <div id="dynamicItems"></div>

        <div class="form-group text-right mt-3">
            <label>Estimated Credit: </label>
            <span id="estimatedCredit">0.00</span>
        </div>
        <div class="form-group text-right">
            <button type="button" id="submitReturn" class="btn btn-primary">Create Return &amp; Issue Credit Note</button>
        </div>
    </div>
</div>

<!-- CREDIT NOTE PRINT MODAL -->
<div class="modal fade" id="creditNoteModal" tabindex="-1" role="dialog" aria-labelledby="creditNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content" style="border: none;">
            <div class="modal-body" style="padding: 10px; width:100%;">
                <div id="creditNotePrintArea">
                    <h2 class="text-center fw-bold" style="border-bottom:3px solid; border-top:3px solid; padding:8px 0;">CREDIT NOTE</h2>
                    <p style="font-size:14px;font-weight:700;">Credit Note No: <span id="cnNo" style="font-weight:400;"></span></p>
                    <p style="font-size:14px;font-weight:700;">Date: <span id="cnDate" style="font-weight:400;"></span></p>
                    <p style="font-size:14px;font-weight:700;">Against Invoice: <span id="cnSourceInvoice" style="font-weight:400;"></span></p>
                    <p style="font-size:14px;font-weight:700;">Total Credit Amount: <span id="cnTotal" style="font-weight:400;"></span></p>
                    <p style="font-size:12px;">This credit note can be redeemed in full against a future bill of equal or greater value.</p>
                </div>
                <div style="text-align:center; width: 100%; margin-bottom: 15px;">
                    <button class="btn btn-sm shadow btn-primary" id="printCreditNoteBtn">Print</button>
                    <button class="btn btn-sm shadow btn-success" id="newReturnBtn">New Return</button>
                    <button class="btn btn-sm shadow btn-secondary" id="cnBackToList">Back to List</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    $('#return_phone').select2({ placeholder: 'Choose phone number...', allowClear: true, width: '100%' });

    function loadPhones() {
        const type = $('#billing_type').val();
        const url = type === 'staff' ? '/staffs' : '/customers';
        ajaxGetData(url, (res) => {
            const list = res?.data || [];
            $('#return_phone').empty().append('<option value="">Choose phone number...</option>');
            list.forEach(p => {
                $('#return_phone').append(`<option value="${p.phone}">${p.phone} — ${p.name}</option>`);
            });
            $('#return_phone').trigger('change.select2');
        });
    }

    $('#billing_type').on('change', function () {
        $('#billsWrap, #itemsWrap').hide();
        loadPhones();
    });
    loadPhones();

    let selectedBillId = null;
    let selectedBill = null;
    let eligibleData = [];

    $('#findBillsBtn').on('click', function () {
        const phone = $('#return_phone').val();
        const type = $('#billing_type').val();
        if (!phone) {
            Swal.fire({ title: 'Missing phone', icon: 'warning', text: 'Choose a phone number first.' });
            return;
        }
        $('#itemsWrap').hide();
        ajaxGetData(`/return/eligible-bills?phone=${encodeURIComponent(phone)}&billing_type=${type}`, (res) => {
            eligibleData = res?.data || [];
            renderBillCards();
        }, () => {
            Swal.fire({ title: 'Error', icon: 'error', text: 'Failed to fetch bills.' });
        });
    });

    function renderBillCards() {
        $('#billsWrap').show();
        const wrap = $('#billCards').empty();
        if (!eligibleData.length) {
            $('#noBillsMsg').show();
            return;
        }
        $('#noBillsMsg').hide();
        eligibleData.forEach(entry => {
            const b = entry.bill;
            wrap.append(`
                <div class="bill-card" data-bill-id="${b.id}">
                    <strong>${b.invoiceNo}</strong> &nbsp; ${b.billing_date} &nbsp; Total: ₹${parseFloat(b.total_amt).toFixed(2)}
                </div>
            `);
        });
    }

    $(document).on('click', '.bill-card', function () {
        $('.bill-card').removeClass('selected');
        $(this).addClass('selected');
        selectedBillId = $(this).data('bill-id');
        const entry = eligibleData.find(e => String(e.bill.id) === String(selectedBillId));
        selectedBill = entry.bill;
        renderItems(entry.items);
    });

    function renderItems(items) {
        $('#itemsWrap').show();
        const wrap = $('#dynamicItems').empty();
        items.forEach(item => {
            const isInhouse = !!item.inhouse_product_id;
            wrap.append(`
                <div class="product-tbody" data-item-id="${item.id}" data-original-qty="${item.qty}" data-total-amount="${item.totalAmount}">
                    <div class="product-field pf-product">
                        <small class="text-muted font-weight-bold">Product</small>
                        <input type="text" class="form-control mt-1" value="${item.product_name || 'N/A'}" readonly />
                    </div>
                    <div class="product-field pf-pack">
                        <small class="text-muted font-weight-bold">Pack</small>
                        <input type="text" class="form-control mt-1" value="${item.pack}" readonly />
                    </div>
                    <div class="product-field pf-unit">
                        <small class="text-muted font-weight-bold">Unit Value</small>
                        <input type="text" class="form-control mt-1" value="${item.unitValue}" readonly />
                    </div>
                    <div class="product-field pf-billed">
                        <small class="text-muted font-weight-bold">Billed Qty</small>
                        <input type="text" class="form-control mt-1" value="${item.qty}" readonly />
                    </div>
                    <div class="product-field pf-returnable">
                        <small class="text-muted font-weight-bold">Returnable</small>
                        <input type="text" class="form-control mt-1" value="${isInhouse ? 'N/A' : item.returnable_qty}" readonly />
                    </div>
                    <div class="product-field pf-return-qty">
                        <small class="text-muted font-weight-bold">Return Qty</small>
                        ${isInhouse
                            ? `<input type="text" class="form-control mt-1" value="Not returnable" readonly disabled />`
                            : `<input type="text" class="form-control mt-1 return-qty-input" data-max="${item.returnable_qty}" placeholder="0" />`
                        }
                    </div>
                </div>
            `);
        });
        calculateEstimatedCredit();
    }

    $(document).on('input', '.return-qty-input', function () {
        const max = parseFloat($(this).data('max')) || 0;
        const val = parseFloat($(this).val()) || 0;
        $(this).css('border-color', val > max ? 'red' : '');
        calculateEstimatedCredit();
    });

    function calculateEstimatedCredit() {
        let total = 0;
        $('#dynamicItems .product-tbody').each(function () {
            const returnQty = parseFloat($(this).find('.return-qty-input').val()) || 0;
            const originalQty = parseFloat($(this).data('original-qty')) || 0;
            const originalAmount = parseFloat($(this).data('total-amount')) || 0;
            if (returnQty > 0 && originalQty > 0) {
                total += originalAmount * (returnQty / originalQty);
            }
        });
        $('#estimatedCredit').text(total.toFixed(2));
    }

    $('#submitReturn').on('click', function () {
        const type = $('#billing_type').val();
        const phone = $('#return_phone').val();
        let hasError = false;
        const returnItems = [];

        $('#dynamicItems .product-tbody').each(function () {
            const max = parseFloat($(this).find('.return-qty-input').data('max')) || 0;
            const qty = parseFloat($(this).find('.return-qty-input').val()) || 0;
            if (qty > max) hasError = true;
            if (qty > 0) {
                returnItems.push({ source_item_id: $(this).data('item-id'), qty: qty });
            }
        });

        if (hasError) {
            Swal.fire({ title: 'Validation Error', icon: 'error', text: 'Return quantity exceeds returnable quantity on one or more items.' });
            return;
        }
        if (!returnItems.length) {
            Swal.fire({ title: 'Nothing to return', icon: 'warning', text: 'Enter a return quantity for at least one item.' });
            return;
        }

        const payload = {
            billing_type: type,
            source_bill_id: selectedBillId,
            phone: phone,
            name: selectedBill ? (selectedBill.customer_name || selectedBill.staff_name) : '',
            return_items: returnItems,
        };
        const csrfToken = $('meta[name="csrf-token"]').attr('content');

        ajaxPostData('/return/create', payload, csrfToken, (response) => {
            $('#cnNo').text(response.credit_note_no);
            $('#cnDate').text(new Date().toLocaleDateString());
            $('#cnSourceInvoice').text(selectedBill.invoiceNo);
            $('#cnTotal').text(parseFloat(response.total_credit_amt).toFixed(2));
            $('#creditNoteModal').modal('show');
        }, (error) => {
            const msg = error?.responseJSON?.message || 'Failed to create return.';
            Swal.fire({ title: 'Error', icon: 'error', text: msg });
        });
    });

    $('#printCreditNoteBtn').on('click', function () {
        const printContent = document.getElementById('creditNotePrintArea').innerHTML;
        const printWindow = window.open('', '', 'height=600,width=500');
        printWindow.document.write('<html><head><title>Credit Note</title></head><body>' + printContent + '</body></html>');
        printWindow.document.close();
        printWindow.print();
    });

    $('#newReturnBtn').on('click', function () {
        window.location.reload();
    });
    $('#cnBackToList').on('click', function () {
        window.location.href = '/store/return/list';
    });
});
</script>
@endsection
