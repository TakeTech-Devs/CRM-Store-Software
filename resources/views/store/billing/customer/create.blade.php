@extends('layouts.dashboard')

@section('title', 'Create Customer Billing')

@section('content')
<style>
    .inhouse-row .avail-label { color: #16a34a; font-weight: 600; }
    .billing-header-section {
        background: #fff8f5;
        border: 1px solid #f0d5c8;
        border-radius: 10px;
        padding: 1.2rem 1.4rem 0.4rem;
        margin-bottom: 1.2rem;
    }
    #dynamicForm { display: flex; flex-direction: column; gap: 6px; }
    .product-tbody { display: flex; align-items: flex-end; gap: 8px; padding: 8px 10px; border-radius: 6px; border: 1px solid #e5e5e5; }
    .product-tbody:nth-child(odd)  { background-color: #ffffff; }
    .product-tbody:nth-child(even) { background-color: #fdf4f0; }
    .product-field { display: flex; flex-direction: column; min-width: 0; }
    .product-field small { white-space: nowrap; }
    .pf-brand    { flex: 0.8; }
    .pf-product  { flex: 1.2; }
    .pf-pack     { flex: 0.8; }
    .pf-price    { flex: 0.7; }
    .pf-unit     { flex: 0.4; }
    .pf-qty      { flex: 0.5; }
    .pf-discount { flex: 0.5; }
    .pf-total    { flex: 0.6; }
    @media print {
        body * {
            border: none !important;
            box-shadow: none !important;
        }

        .table tbody+tbody {
            border-top: none !important;
        }

        #printButton {
            display: none;
        }

        @page {
            size: A4 landscape;
        }
    }
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Create Customer Billing</h2>
        <div class="text-right">
            <a href="{{ url('store/customer/billing') }}" class="btn btn-secondary btn-sm">View Customer Billing
                List</a>
        </div>
    </div>

    <div class="mt-4 position-relative">
        <form id="customerBillingCreate">
            @csrf
            <div class="billing-header-section">
            <div class="form-row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_phone">Customer Phone Number</label>
                        <div class="form-group d-flex align-items-center">
                            <select name="customer_phone" id="customer_phone" class="form-control select2-manual" style="width:100%">
                                <option value="">Enter or choose customer phone number...</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal"
                                data-target="#addCustomer">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="doctor_name">Doctor Name</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="doctor_name[]" id="doctor_name"
                                class="form-control">
                                <option value="">Choose Doctor Name...</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal"
                                data-target="#addDoctor">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="customer_name">Customer Name</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" name="customer_name" id="customer_name" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="paymentType">Payment Type</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="paymentType[]" id="paymentType"
                                class="form-control">
                                <option value="">Choose Payment Type...</option>
                                <option value="online">Online</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoiceNo">Invoice No</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" name="invoiceNo" id="invoiceNo" class="form-control"
                                value="Auto-generated on submit" disabled>
                        </div>
                    </div>
                </div>
            </div>
            </div>{{-- end billing-header-section --}}

            <div id="dynamicForm"></div>

            <button type="button" name="add_row" id="add_row"
                class="btn btn-sm btn-secondary mb-3  mt-3 float-right ml-3">
                Add New Row
            </button>

            <div class="form-group text-right mt-3 mx-4 row d-flex justify-content-end">
                <div class="col-md-2">
                    <label for="totalAmount">Total Amount: </label>
                    <span id="totalAmount">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalGST">GST: </label>
                    <span id="totalGST">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalCGST">CGST: </label>
                    <span id="totalCGST">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalSGST">SGST: </label>
                    <span id="totalSGST">0</span>
                </div>
            </div>

            <div class="form-group text-right">
                <button type="button" name="submitBilling" id="submitBilling" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- ADD CUSTOMER PHONE NUMBER  -->
<div class="modal fade" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="addCustomerLabel"
    aria-hidden="true">
    <div class="modal-dialog container" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                <h5 class="modal-title" id="addCustomerLabel">Add Customer</h5>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm" class="container">
                    <div class="form-group">
                        <label for="name">Customer Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="mail">Customer Mail</label>
                        <input type="email" class="form-control" id="mail" name="mail" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Customer Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group d-none">
                        <label>Status:</label>
                        <div class="form-group d-flex justify-content-start align-items-center">
                            <div class="form-check mx-3">
                                <input type="radio" class="form-check-input" id="statusActive" name="status" value="1"
                                    checked>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="statusInactive" name="status"
                                    value="0">
                                <label class="form-check-label" for="statusInactive">Deactive</label>
                            </div>
                        </div>
                    </div>
                    <div class="save-button d-flex align-items-center justify-content-center">
                        <button type="submit" id="addCustomerFormBtn" class="btn btn-success mx-2">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ADD DOCTOR MODAL -->
<div class="modal fade" id="addDoctor" tabindex="-1" role="dialog" aria-labelledby="addDoctorLabel" aria-hidden="true">
    <div class="modal-dialog container" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                <h5 class="modal-title" id="addDoctorLabel">Add Doctor</h5>
            </div>
            <div class="modal-body">
                <form id="addDoctorForm" class="container">
                    <div class="form-group">
                        <label for="name">Doctor Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="mail">Doctor Mail</label>
                        <input type="email" class="form-control" id="mail" name="mail" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Doctor Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="degree">Doctor Degree</label>
                        <input type="text" class="form-control" id="degree" name="degree" required>
                    </div>
                    <div class="form-group d-none">
                        <label>Status:</label>
                        <div class="form-group d-flex justify-content-start align-items-center">
                            <div class="form-check mx-3">
                                <input type="radio" class="form-check-input" id="statusActive" name="status" value="1"
                                    checked>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="statusInactive" name="status"
                                    value="0">
                                <label class="form-check-label" for="statusInactive">Deactive</label>
                            </div>
                        </div>
                    </div>
                    <div class="save-button d-flex align-items-center justify-content-center">
                        <button type="submit" id="addDoctorFormBtn" class="btn btn-success mx-2">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
            count = 1
            addNewRow(count)

            // Initialize select2 once — options are filled by customerData() separately
            $('#customer_phone').select2({
                placeholder: 'Enter or choose customer phone number...',
                allowClear: true,
                width: '100%',
                tags: true,
                createTag: function (params) {
                    const term = $.trim(params.term);
                    if (!term) return null;
                    return { id: term, text: term, newTag: true };
                },
            });
            $('#customer_phone').on('change', function () {
                const phone = $(this).val();
                if (!phone) { $('#customer_name').val(''); return; }
                const opt = $(this).find('option:selected');
                const isNew = opt.data('select2-tag') === true;
                if (isNew) {
                    $('#customer_name').val('');
                    $('#addCustomer').modal('show');
                    $('#phone').val(phone);
                    $('#name').focus();
                } else {
                    const parts = opt.text().split(' — ');
                    $('#customer_name').val(parts.length > 1 ? parts.slice(1).join(' — ') : '');
                }
            });

            customerData()
            doctorData()





            

            $(document).on('click', '#add_row', function () {
                count = count + 1;
                addNewRow(count)
            })

            // CREATING BILL
            $(document).on('click', '#submitBilling', function () {
                // Validation check
                let hasError = false;
                $('#dynamicForm .product-tbody').each(function () {
                    const assignQtyInput = $(this).find('[name="assignQty[]"]');
                    const discountInput = $(this).find('[name="discount[]"]');
                    
                    const isInhouse = $(this).hasClass('inhouse-row');
                    const totalAvailVal = $(this).find('[name="total_qty[]"]').val();
                    const inputQty = parseFloat(assignQtyInput.val()) || 0;
                    const discountVal = parseFloat(discountInput.val()) || 0;

                    // Skip stock check for inhouse products
                    if (!isInhouse && totalAvailVal !== "" && totalAvailVal !== undefined && totalAvailVal !== null) {
                        const totalAvail = parseFloat(totalAvailVal) || 0;
                        if (inputQty > totalAvail) {
                            assignQtyInput.css('border-color', 'red');
                            hasError = true;
                        }
                    }

                    if (discountVal > 100) {
                        discountInput.css('border-color', 'red');
                        hasError = true;
                    }
                });

                if (hasError) {
                    Swal.fire({
                        title: "Validation Error",
                        icon: "error",
                        text: "Please fix the highlighted errors before submitting.",
                    });
                    return;
                }

                const payload = gatherFormData(); 
                let csrfToken = $('meta[name="csrf-token"]').attr('content');

                ajaxPostData('/customer/billing/create', payload, csrfToken, (response) => {
                    const billId = response.bill_id;
                    // Show success message briefly, then automatically show the bill
                    Swal.fire({
                        title: "Success!",
                        icon: "success",
                        text: "Customer Billing Added Successfully.",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Automatically view and print the bill
                        viewAndPrintBill(billId);
                    });
                }, (error) => {
                    Swal.fire({
                        title: "Error!",
                        icon: "error",
                        text: "Failed to create billing. Please try again.",
                    });
                });
            });



            // ADDING CUSTOMER 
            $('#addCustomer').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: '/api/customer', 
                    type: 'POST',
                    data: {
                        name: $('#name').val(),
                        mail: $('#mail').val(),
                        phone: $('#phone').val(),
                        status: $('input[name="status"]:checked').val(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    success: function(response) {
                        const newPhone = $('#phone').val();
                        const newName  = $('#name').val();
                        customerData(function () {
                            $('#customer_phone').val(newPhone).trigger('change.select2');
                            $('#customer_name').val(newName);
                        });
                        Swal.fire({
                            title: "Customer !",
                            icon: "success",
                            text: "Customer Added Successfully.",
                        });
                        $('#addCustomer').modal('hide');
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            });

            // ADD DOCTOR
            $('#addDoctorForm').on('submit', function(event) {
                event.preventDefault();
                const $form = $(this);

                $.ajax({
                    url: '/api/doctor',
                    type: 'POST',
                    data: {
                        name: $form.find('[name="name"]').val(),
                        mail: $form.find('[name="mail"]').val(),
                        phone: $form.find('[name="phone"]').val(),
                        degree: $form.find('[name="degree"]').val(),
                        status: $('input[name="status"]:checked').val(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    success: function(response) {
                        doctorData();
                        Swal.fire({
                            title: "Doctor !",
                            icon: "success",
                            text: "Doctor Added Successfully.",
                        });
                        $('#addDoctor').modal('hide');
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            });
        });  

        function customerData(callback) {
            ajaxGetData('/customers', (res) => {
                const customers = res?.data || [];
                $('#customer_phone').find('option:not(:first)').remove();
                customers.forEach(c => {
                    $('#customer_phone').append(`<option value="${c.phone}">${c.phone} — ${c.name}</option>`);
                });
                if (typeof callback === 'function') callback();
            });
        }

        function updateProductQuantity(product_id, assigned_qty) {
            ajaxPostData('/api/update_product_qty', { 
                product_id: product_id, 
                assigned_qty: assigned_qty 
            }, $('meta[name="csrf-token"]').attr('content'), (response) => {
                if (response.success) {
                    console.log(`Product ID ${product_id} quantity updated successfully`);
                } else {
                    console.error(`Failed to update quantity for Product ID ${product_id}`);
                }
            });
        }

        
        function doctorData() { 
            ajaxGetData('/doctors', (res)=>{
                for (let index = 0; index < res?.data?.length; index++) {
                    const element = res?.data[index];
                    $('#doctor_name').append('<option value="' + element.id + '">' + element.name + '</option>');
                }
            })
        }

        let billingProductOptions = [];

        function loadBillingProductOptions(callback) {
            if (billingProductOptions.length > 0) {
                callback(billingProductOptions);
                return;
            }
            ajaxGetData('/billing/product-options', (res) => {
                billingProductOptions = res?.data || [];
                callback(billingProductOptions);
            });
        }

        // Brand options cache
        let billingBrands = [];
        function loadBrands(callback) {
            if (billingBrands.length > 0) { callback(billingBrands); return; }
            ajaxGetData('/billing/brands', (res) => {
                billingBrands = res?.data || [];
                callback(billingBrands);
            });
        }

        function resetCascadeFrom(id, field) {
            const fields = ['product', 'pack', 'price'];
            fields.slice(fields.indexOf(field)).forEach(f => {
                const sel = $(`#${f}_select${id}`);
                if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
                sel.empty().append(`<option value="">Choose ${f.charAt(0).toUpperCase()+f.slice(1)}</option>`).prop('disabled', true);
                sel.off('select2:select select2:clear');
            });
            $(`#unit_value${id}`).val('');
            $(`#totalAmount${id}`).val('');
            $(`#avail_qty_text_${id}`).text('-');
            $(`#product_name${id}, #inhouse_id${id}, #pack${id}, #pr_ids${id}, #qty${id}, #gstRate${id}`).val('');
            $(`#assignQty${id}`).val('').css('border-color', '');
            $(`#row_block_${id}`).removeClass('inhouse-row');
        }

        function populateBrandSelect(id) {
            loadBrands((brands) => {
                const sel = $(`#brand_select${id}`);
                sel.empty().append('<option value="">Choose Brand</option>');
                brands.forEach(b => sel.append(`<option value="${b.id}">${b.name}</option>`));
                if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
                sel.select2({ width: '100%', placeholder: 'Choose Brand' });

                sel.on('select2:select select2:clear', function () {
                    const brandId = $(this).val();
                    resetCascadeFrom(id, 'product');
                    if (!brandId) return;
                    loadProductsForBrand(id, brandId);
                });
            });
        }

        function loadProductsForBrand(id, brandId) {
            loadBillingProductOptions((options) => {
                const sel = $(`#product_select${id}`);
                sel.empty().append('<option value="">Choose Product</option>');
                const seen = new Set();
                if (brandId === 'inhouse') {
                    options.filter(o => o.type === 'inhouse').forEach(o => {
                        if (!seen.has(o.inhouse_product_id)) {
                            seen.add(o.inhouse_product_id);
                            sel.append(`<option value="${o.inhouse_product_id}" data-type="inhouse">${o.product_name}</option>`);
                        }
                    });
                } else {
                    options.filter(o => o.type === 'regular' && String(o.brand_id) === String(brandId)).forEach(o => {
                        if (!seen.has(o.product_id)) {
                            seen.add(o.product_id);
                            sel.append(`<option value="${o.product_id}" data-type="regular">${o.product_name}</option>`);
                        }
                    });
                }
                sel.prop('disabled', false);
                if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
                sel.select2({ width: '100%', placeholder: 'Choose Product' });
                sel.on('select2:select select2:clear', function () {
                    const productVal = $(this).val();
                    const type = $(this).find('option:selected').data('type');
                    resetCascadeFrom(id, 'pack');
                    if (!productVal) return;
                    loadPacksForProduct(id, productVal, type, brandId);
                });
            });
        }

        function loadPacksForProduct(id, productVal, type, brandId) {
            loadBillingProductOptions((options) => {
                const sel = $(`#pack_select${id}`);
                sel.empty().append('<option value="">Choose Pack</option>');
                let rows = type === 'inhouse'
                    ? options.filter(o => o.type === 'inhouse' && String(o.inhouse_product_id) === String(productVal))
                    : options.filter(o => o.type === 'regular' && String(o.product_id) === String(productVal) && String(o.brand_id) === String(brandId));
                const seen = new Set();
                rows.forEach(o => {
                    if (!seen.has(o.pack_id)) {
                        seen.add(o.pack_id);
                        sel.append(`<option value="${o.pack_id}">${o.pack_name}</option>`);
                    }
                });
                sel.prop('disabled', false);
                if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
                sel.select2({ width: '100%', placeholder: 'Choose Pack' });
                sel.on('select2:select select2:clear', function () {
                    const packId = $(this).val();
                    resetCascadeFrom(id, 'price');
                    if (!packId) return;
                    loadPricesForPack(id, productVal, packId, type, brandId);
                });
            });
        }

        function loadPricesForPack(id, productVal, packId, type, brandId) {
            loadBillingProductOptions((options) => {
                const sel = $(`#price_select${id}`);
                sel.empty().append('<option value="">Choose Price</option>');
                let rows = type === 'inhouse'
                    ? options.filter(o => o.type === 'inhouse' && String(o.inhouse_product_id) === String(productVal) && String(o.pack_id) === String(packId))
                    : options.filter(o => o.type === 'regular' && String(o.product_id) === String(productVal) && String(o.pack_id) === String(packId) && String(o.brand_id) === String(brandId));
                if (type === 'inhouse') {
                    rows.forEach(o => {
                        sel.append(`<option value="inhouse_${o.inhouse_product_id}"
                            data-type="inhouse"
                            data-price="${o.price_name}"
                            data-gst="0"
                            data-pack-name="${o.pack_name}"
                            data-inhouse-id="${o.inhouse_product_id}"
                            data-category="${o.category_name}"
                            data-sub-category="${o.sub_category_name}"
                        >&#8377;${o.price_name}</option>`);
                    });
                } else {
                    // Group batches by price_name — sum qty, collect all IDs
                    const groups = {};
                    rows.forEach(o => {
                        const key = String(o.price_name);
                        if (!groups[key]) {
                            groups[key] = { price_name: o.price_name, gst: o.gst || 0, pack_name: o.pack_name, product_id: o.product_id, total_qty: 0, pr_ids: [], category_name: o.category_name, sub_category_name: o.sub_category_name };
                        }
                        groups[key].total_qty += (parseFloat(o.avail_qty) || 0);
                        groups[key].pr_ids.push(o.purchase_request_id);
                    });
                    Object.values(groups).forEach(g => {
                        sel.append(`<option value="${g.pr_ids[0]}"
                            data-type="regular"
                            data-price="${g.price_name}"
                            data-gst="${g.gst}"
                            data-qty="${g.total_qty}"
                            data-pack-name="${g.pack_name}"
                            data-product-id="${g.product_id}"
                            data-pr-ids='${JSON.stringify(g.pr_ids)}'
                            data-category="${g.category_name}"
                            data-sub-category="${g.sub_category_name}"
                        >&#8377;${g.price_name}</option>`);
                    });
                }
                sel.prop('disabled', false);
                if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
                sel.select2({ width: '100%', placeholder: 'Choose Price' });
                sel.on('select2:select select2:clear', function () {
                    const opt = $(this).find('option:selected');
                    const val = $(this).val();
                    if (!val) {
                        $(`#unit_value${id}, #totalAmount${id}`).val('');
                        $(`#avail_qty_text_${id}`).text('-');
                        $(`#product_name${id}, #inhouse_id${id}, #pack${id}, #pr_ids${id}, #qty${id}, #gstRate${id}, #category${id}, #subCategory${id}`).val('');
                        $(`#assignQty${id}`).val('').css('border-color', '');
                        $(`#row_block_${id}`).removeClass('inhouse-row');
                        return;
                    }
                    const isInhouse = opt.data('type') === 'inhouse';
                    $(`#pack${id}`).val(opt.data('pack-name'));
                    $(`#unit_value${id}`).val(opt.data('price'));
                    $(`#gstRate${id}`).val(opt.data('gst') || 0);
                    $(`#assignQty${id}`).css('border-color', '');
                    if (isInhouse) {
                        $(`#product_name${id}`).val('');
                        $(`#pr_ids${id}`).val('');
                        $(`#inhouse_id${id}`).val(opt.data('inhouse-id'));
                        $(`#qty${id}`).val('');
                        $(`#category${id}`).val(opt.data('category') || '');
                        $(`#subCategory${id}`).val(opt.data('sub-category') || '');
                        $(`#avail_qty_text_${id}`).text('Inhouse');
                        $(`#row_block_${id}`).addClass('inhouse-row');
                    } else {
                        $(`#product_name${id}`).val(opt.data('product-id'));
                        $(`#pr_ids${id}`).val(JSON.stringify(opt.data('pr-ids') || []));
                        $(`#inhouse_id${id}`).val('');
                        $(`#qty${id}`).val(opt.data('qty'));
                        $(`#category${id}`).val(opt.data('category') || '');
                        $(`#subCategory${id}`).val(opt.data('sub-category') || '');
                        $(`#avail_qty_text_${id}`).text('A: ' + opt.data('qty'));
                        $(`#row_block_${id}`).removeClass('inhouse-row');
                    }
                    updateTotalForRow($(`#row_block_${id}`));
                });
            });
        }












        function addNewRow(id) {
            const newTbody = `
                <div class="product-tbody new-row" id="row_block_${id}">
                    <input type="hidden" class="table-row-id row_id" value="${id}">
                    <input type="hidden" name="productName[]" id="product_name${id}" />
                    <input type="hidden" name="inhouse_product_id[]" id="inhouse_id${id}" />
                    <input type="hidden" name="pack[]" id="pack${id}" />
                    <input type="hidden" name="purchase_request_ids[]" id="pr_ids${id}" />
                    <input type="hidden" name="category[]" id="category${id}" />
                    <input type="hidden" name="subCategory[]" id="subCategory${id}" />
                    <input type="hidden" name="total_qty[]" id="qty${id}" />
                    <input type="hidden" class="gstRate" name="gstRate[]" id="gstRate${id}" />
                    <input type="hidden" class="gstAmount" name="gstAmount[]" id="gstAmount${id}" />
                    <input type="hidden" class="cgst" name="cgst[]" id="cgst${id}" />
                    <input type="hidden" class="sgst" name="sgst[]" id="sgst${id}" />

                    <div class="product-field pf-brand">
                        <small class="text-muted font-weight-bold">Brand</small>
                        <select class="form-control brand-select mt-1" data-count="${id}" id="brand_select${id}">
                            <option value="">Choose Brand</option>
                        </select>
                    </div>
                    <div class="product-field pf-product">
                        <small class="text-muted font-weight-bold">Product</small>
                        <select class="form-control mt-1" data-count="${id}" id="product_select${id}" disabled>
                            <option value="">Select brand first</option>
                        </select>
                    </div>
                    <div class="product-field pf-pack">
                        <small class="text-muted font-weight-bold">Pack</small>
                        <select class="form-control mt-1" data-count="${id}" id="pack_select${id}" disabled>
                            <option value="">Choose Pack</option>
                        </select>
                    </div>
                    <div class="product-field pf-price">
                        <small class="text-muted font-weight-bold">Price</small>
                        <select class="form-control mt-1" data-count="${id}" id="price_select${id}" disabled>
                            <option value="">Choose Price</option>
                        </select>
                    </div>
                    <div class="product-field pf-unit">
                        <small class="text-muted font-weight-bold">Unit Value</small>
                        <input type="text" class="form-control mt-1" name="unit_value[]" id="unit_value${id}" readonly />
                    </div>
                    <div class="product-field pf-qty">
                        <small class="text-muted font-weight-bold avail-label">Qty (<span id="avail_qty_text_${id}" class="text-info">-</span>)</small>
                        <input type="text" class="form-control mt-1" name="assignQty[]" id="assignQty${id}" placeholder="Qty" />
                    </div>
                    <div class="product-field pf-discount">
                        <small class="text-muted font-weight-bold">Discount (%)</small>
                        <input type="number" class="form-control mt-1" name="discount[]" id="discount${id}" value="0" />
                    </div>
                    <div class="product-field pf-total">
                        <small class="text-muted font-weight-bold">Total Amount</small>
                        <input type="text" class="form-control mt-1" name="totalAmount[]" id="totalAmount${id}" readonly />
                    </div>
                </div>
            `;
            $('#dynamicForm').append(newTbody);
            populateBrandSelect(id);
        }


        function updateTotalForRow(row) {
            const qty = parseFloat(row.find('input[name="assignQty[]"]').val()) || 0;
            const unitValue = parseFloat(row.find('input[name="unit_value[]"]').val()) || 0;
            const discount = parseFloat(row.find('input[name="discount[]"]').val()) || 0;
            const gstRate = parseFloat(row.find('input[name="gstRate[]"]').val()) || 0;

            // Calculate base amount (before discount)
            const baseAmount = qty * unitValue;

            // Discounted amount
            const totalAmount = baseAmount - (baseAmount * discount / 100);
            row.find('input[name="totalAmount[]"]').val(totalAmount.toFixed(2));

            // GST Calculation
            const gstAmount = baseAmount * (gstRate / 100);
            const cgst = gstAmount / 2;
            const sgst = gstAmount / 2;

            // Set values in fields
            row.find('input[name="gstAmount[]"]').val(gstAmount.toFixed(2));
            row.find('input[name="cgst[]"]').val(cgst.toFixed(2));
            row.find('input[name="sgst[]"]').val(sgst.toFixed(2));

            console.log('amounts', {
                qty, unitValue, discount, gstRate,
                baseAmount, totalAmount, gstAmount, cgst, sgst
            }); 
        }



        function updateOverallTotal() {
            let overallTotal = 0;
            $('input[name="totalAmount[]"]').each(function() {
                overallTotal += parseFloat($(this).val()) || 0;
            });
            $('#totalAmount').text(overallTotal.toFixed(2));
        }

        $(document).ready(function() {
             calculateTotalAmount();
        });

        function calculateTotalAmount() {
            let totalAmount = 0;
            let totalGST = 0;
            let totalCGST = 0;
            let totalSGST = 0;

            $('#dynamicForm .product-tbody').each(function () {
                const qty = parseFloat($(this).find('[name="assignQty[]"]').val()) || 0;
                const unitValue = parseFloat($(this).find('[name="unit_value[]"]').val()) || 0;
                const discount = parseFloat($(this).find('[name="discount[]"]').val()) || 0;
                const gstRate = parseFloat($(this).find('[name="gstRate[]"]').val()) || 0;

                // --- base amount after discount ---
                const discountDecimal = discount / 100;
                const discountAmount = qty * unitValue * discountDecimal;
                const amount = (qty * unitValue) - discountAmount;

                $(this).find('[name="totalAmount[]"]').val(amount.toFixed(2));
                totalAmount += amount;

                // --- GST calculations ---
                const gstAmount = (amount * gstRate) / 100;
                const cgst = gstAmount / 2;
                const sgst = gstAmount / 2;

                $(this).find('[name="gstAmount[]"]').val(gstAmount.toFixed(2));
                $(this).find('[name="cgst[]"]').val(cgst.toFixed(2));
                $(this).find('[name="sgst[]"]').val(sgst.toFixed(2));

                totalGST += gstAmount;
                totalCGST += cgst;
                totalSGST += sgst;
            });

            // --- Update footer totals ---
            $('#totalAmount').text(totalAmount.toFixed(2));
            $('#totalGST').text(totalGST.toFixed(2));
            $('#totalCGST').text(totalCGST.toFixed(2));
            $('#totalSGST').text(totalSGST.toFixed(2));
        }

        $(document).on('input keyup', '[name="assignQty[]"]', function () {
            const row = $(this).closest('.product-tbody');
            const count = row.find('.row_id').val();
            const totalAvailVal = $(`#qty${count}`).val();
            const isInhouse = row.hasClass('inhouse-row');
            if (isInhouse) { $(this).css('border-color', ''); return; }
            if (totalAvailVal === "" || totalAvailVal === undefined || totalAvailVal === null) {
                $(`#avail_qty_text_${count}`).text('-');
                $(this).css('border-color', '');
                return;
            }
            const totalAvail = parseFloat(totalAvailVal) || 0;
            const inputQty = parseFloat($(this).val()) || 0;
            const currentAvail = totalAvail - inputQty;
            
            $(`#avail_qty_text_${count}`).text('A: ' + currentAvail);
            
            if (inputQty > totalAvail) {
                $(this).css('border-color', 'red');
            } else {
                $(this).css('border-color', '');
            }
        });

        $(document).on('input keyup', '[name="discount[]"]', function () {
            const discountVal = parseFloat($(this).val()) || 0;
            if (discountVal > 100) {
                $(this).css('border-color', 'red');
            } else {
                $(this).css('border-color', '');
            }
        });

        $(document).on('input keyup', '[name="assignQty[]"], [name="unit_value[]"], [name="discount[]"]', function () {
            calculateTotalAmount();
        });

        $(document).on('keyup', '.new-row [name="assignQty[]"]', function () {
            $(this).closest('.product-tbody').removeClass('new-row');
            count++;
            addNewRow(count);
        });




        function gatherFormData() {
            const rows = document.querySelectorAll('#dynamicForm .product-tbody');
            const products = [];

            rows.forEach(row => {
                const productId       = row.querySelector(`[name="productName[]"]`).value;
                const inhouseId       = row.querySelector(`[name="inhouse_product_id[]"]`).value;
                const isInhouse       = row.classList.contains('inhouse-row');

                // Include row only if it has a product (regular) or an inhouse product
                if (!productId && !inhouseId) return;

                const pr_ids_raw  = row.querySelector(`[name="purchase_request_ids[]"]`).value;
                const purchase_request_ids = (!isInhouse && pr_ids_raw) ? JSON.parse(pr_ids_raw) : [];
                const category    = row.querySelector(`[name="category[]"]`).value;
                const subCategory = row.querySelector(`[name="subCategory[]"]`).value;
                const pack        = row.querySelector(`[name="pack[]"]`).value;
                const unitValue   = row.querySelector(`[name="unit_value[]"]`).value;
                const qty         = row.querySelector(`[name="assignQty[]"]`).value;
                const discount    = row.querySelector(`[name="discount[]"]`).value;
                const totalAmount = row.querySelector(`[name="totalAmount[]"]`).value;
                const gstRate     = row.querySelector(`[name="gstRate[]"]`).value;
                const gstAmount   = row.querySelector(`[name="gstAmount[]"]`).value;

                products.push({
                    productId: isInhouse ? null : productId,
                    inhouse_product_id: isInhouse ? inhouseId : null,
                    purchase_request_ids: isInhouse ? [] : purchase_request_ids,
                    is_inhouse: isInhouse,
                    category,
                    subCategory,
                    pack,
                    qty,
                    unitValue,
                    discount,
                    totalAmount,
                    gstRate,
                    gstAmount
                });
            });
            let today = new Date();

            let day = String(today.getDate()).padStart(2, '0');
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let year = today.getFullYear();
            let formattedDate = `${year}-${month}-${day}`;
            const payload = {
                billingType: "customer",
                customer_phone: $('#customer_phone').val(),
                doctor_name: $('#doctor_name').val(),
                paymentType: $('#paymentType').val(),
                customer_name: $('#customer_name').val(),
                total_amt: $('#totalAmount').text(),
                gstAmount: $('#totalGST').text(),
                cgst: $('#totalCGST').text(),
                sgst: $('#totalSGST').text(),
                billing_date:formattedDate,
                billingType:"Customer Billing",
                product_billings: products
            };

            console.log(payload);

            return payload;
        }

        // Function to view and print the bill
        function viewAndPrintBill(billId) {
            // Fetch bill details and show print modal
            $.ajax({
                url: `/api/customer/bill/${billId}`,
                method: 'GET',
                success: function(response) {
                    if (response.status === 200) {
                        populatePrintModal(response.data);
                        $('#printModal').modal('show');
                    } else {
                        Swal.fire({
                            title: "Error!",
                            icon: "error",
                            text: "Failed to fetch bill details.",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: "Error!",
                        icon: "error",
                        text: "Failed to fetch bill details.",
                    });
                }
            });
        }

        // Function to populate print modal with bill data
        function populatePrintModal(data) {
            let bill = data.bill;
            let items = data.items;
            let store = data.store;

            // Update bill information
            $('.invoiceNo').text(bill.invoiceNo);
            $('.billingDate').text(bill.billing_date);
            $('.customerName').text(bill.customer_name);
            $('.drName').text(bill.doctor_name || 'N/A');
            $('.grandTotal').text(bill.total_amt);
            $('.totalGST').text(bill.gst || 0);
            $('.totalCGST').text(bill.cgst || 0);
            $('.totalSGST').text(bill.sgst || 0);
            $('.taxableValue').text((bill.total_amt - bill.gst).toFixed(2)); // Add taxable value

            // Update store information
            if (store) {
                $('.storeAddress').text(store.store_address || 'Not Provided');
                $('.dlNumber').text(store.dl_number || 'Not Provided');
                $('.helplineNumber').text(store.helpline_number || 'Not Provided');
            }

            // Clear and populate items table
            $('#invoice_table tbody').empty();
            items.forEach((item, index) => {
                $('#invoice_table tbody').append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.product_name || 'N/A'}</td>
                        <td>${item.qty || '0'}</td>
                        <td>${item.pack || 'N/A'}</td>
                        <td>${item.unitValue || '0.00'}/-</td>
                        <td>${item.gstRate || '0'}%</td>
                        <td>${item.gstAmount || '0.00'}/-</td>
                        <td>${item.totalAmount || '0.00'}/-</td>
                    </tr>
                `);
            });
        }

        // Function to reset the form
        function resetForm() {
            // Reset form fields
            $('#customerBillingCreate')[0].reset();
            $('#customer_name').val('');
            $('#invoiceNo').val('{{ uniqid() }}');
            
            // Clear the dynamic table
            $('#formBody').empty();
            count = 0;
            
            // Reset totals
            $('#totalAmount').text('0');
            $('#totalGST').text('0');
            $('#totalCGST').text('0');
            $('#totalSGST').text('0');
        }

        // Add print modal to the create page
        $(document).ready(function() {
            // Add print modal HTML if it doesn't exist
            if ($('#printModal').length === 0) {
                $('body').append(`
                    <!-- PRINT MODAL -->
                    <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content" style="border: none;">
                                <div class="modal-body" style="padding: 10px; width:100%;">
                                    <div id="printArea">
                                        <table class="border p-2">
                                            <h1 class="text-center fw-bold" style="border-bottom:3px solid; border-top:3px solid; padding:5px 0px !important;">RIGHT AID</h1>
                                            <div class="col-md-12">
                                                <div class="details d-flex align-items-start justify-content-between" style="margin-bottom: 15px !important; margin-top: 15px !important;">
                                                    <div class="left">
                                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            Invoice No: <span class="invoiceNo" style="font-size: 14px !important; font-weight:400;"></span>
                                                        </p>
                                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">Date:
                                                            <span class="billingDate" style="font-size: 14px !important; font-weight:400;"></span>
                                                        </p>
                                                        <p style="width: 150%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            GSTIN: <span class="gstin" style="font-size: 14px !important; font-weight:400;">GST123456</span>
                                                        </p>
                                                        <p style="width: 200%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            Customer: <span class="customerName" style="font-size: 14px !important; font-weight:400;"></span>
                                                        </p>
                                                        <p style="width: 200%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            Dr Name: <span class="drName" style="font-size: 14px !important; font-weight:400;"></span>
                                                        </p>
                                                    </div>
                                                    <div class="right">
                                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">DL. No. : 
                                                            <span class="dlNumber" style="font-size: 14px !important; font-weight:400;">HL-1046-S</span>
                                                        </p>
                                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            Helpline : <span class="helplineNumber" style="font-size: 14px !important; font-weight:400;">8100968101</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="product-details text-center m-0 col-md-12">
                                                <table class="w-100" id="invoice_table">
                                                    <thead style="border-top:3px solid; text-align: center; border-bottom:3px solid; padding-top: 10px !important;">
                                                        <tr>
                                                            <th>SNo.</th>
                                                            <th>Medicine</th>
                                                            <th>Qty</th>
                                                            <th>Pack</th>
                                                            <th>MRP</th>
                                                            <th>GST Rate</th>
                                                            <th>GST Amount</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="border-bottom:3px solid; text-align: center; padding: 15px 0px !important;">
                                                    </tbody>
                                                </table>
                                                <div class="col-md-12 my-3" style="text-align:right !important">
                                                    <table class="w-100 table table-bordered mt-3">
                                                        <thead>
                                                            <tr style="text-align: right;">
                                                                <th>Taxable Value</th>
                                                                <th>CGST</th>
                                                                <th>SGST</th>
                                                                <th>Total Tax Amount</th>
                                                                <th>Grand Total</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody style="text-align: right;">
                                                            <tr>
                                                                <td><span class="taxableValue">0.00</span>/-</td>
                                                                <td><span class="totalCGST">0.00</span>/-</td>
                                                                <td><span class="totalSGST">0.00</span>/-</td>
                                                                <td><span class="totalGST">0.00</span>/-</td>
                                                                <td><span class="grandTotal">0.00</span>/-</td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="address text-center" style="font-size: 12px !important; margin-top:15px">
                                                    <span>Address : <Span class="storeAddress"></Span></span><br>
                                                    <span>Reg Address : 211. Rain Ram Monan Rov Road Shop No :10, Block-1 Ground Floor, "Merlin Grove Behala Kolkata-700008</span>
                                                </div>
                                                <div class="note text-center" style="font-size: 12px !important;">
                                                    <p>Medicine once sold would not be returned or exchanged</p>
                                                    <span>******** Thank You ********</span>
                                                </div>
                                            </div>
                                        </table>
                                    </div>
                                    <div style="text-align:center; width: 100%; margin-bottom: 25px !important;">
                                        <button class="btn btn-sm shadow btn-primary" id="printButton">Print</button>
                                        <button class="btn btn-sm shadow btn-info" id="printButtonTVS">Print TVS RP 45</button>
                                        <button class="btn btn-sm shadow btn-success" id="createNewBill" style="margin-left: 10px;">Create New Bill</button>
                                        <button class="btn btn-sm shadow btn-secondary" id="backToList" style="margin-left: 10px;">Back to List</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                // Add print functionality
                $(document).on('click', '#printButton', function() {
                    printModalContent();
                });

                // Add create new bill functionality
                $(document).on('click', '#createNewBill', function() {
                   window.location.href = "/store/customer/create/billing";
                });

                // Add back to list functionality
                $(document).on('click', '#backToList', function() {
                    window.location.href = "/store/customer/billing";
                });

                // Clicking outside or closing modal refreshes the page
                $('#printModal').on('hidden.bs.modal', function () {
                    window.location.reload();
                });
            }
        });

        // Print function
        function printModalContent() {
            var printContent = document.getElementById("printArea").innerHTML;
            var printWindow = window.open('', '', 'height=800,width=600');
            printWindow.document.write('<html><head><title>Print</title>');
            printWindow.document.write(`
                <style>
                    body { font-family: Arial, sans-serif; font-size: 12px; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { padding: 4px 6px; text-align: left; }
                    .text-center { text-align: center; }
                    .fw-bold { font-weight: bold; }
                    #printButton, #printButtonTVS, #createNewBill, #backToList { display: none; }
                </style>
            `);
            printWindow.document.write('</head><body>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        function printModalContentTVS() {
            var printContent = document.getElementById("printArea").innerHTML;
            var originalContent = document.body.innerHTML;

            var printWindow = window.open('', '', 'height=600,width=400');
            printWindow.document.write('<html><head><title>Print</title>');
            printWindow.document.write(`
                <style>
                    body {
                        font-family: 'Courier New', Courier, monospace;
                        font-size: 8px;
                        width: 4in;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th, td {
                        padding: 2px;
                        text-align: left;
                    }
                    .text-center {
                        text-align: center;
                    }
                    .fw-bold {
                        font-weight: bold;
                    }
                    #printButton, #printButtonTVS, #createNewBill, #backToList {
                        display: none;
                    }
                </style>
            `);
            printWindow.document.write('</head><body>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

        $(document).on('click', '#printButtonTVS', function() {
            printModalContentTVS();
        });

</script>
@endsection