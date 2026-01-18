@extends('layouts.dashboard')

@section('title', 'Edit Customer Billing')

@section('content')
<style>
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
    <h2 class="text-dark">Edit Customer Billing</h2>
        <div class="text-right">
            <a href="{{ url('store/customer/billing') }}" class="btn btn-secondary btn-sm">View Customer Billing
                List</a>
        </div>
    </div>

    <div class="mt-4 position-relative">
        <form id="customerBillingEdit">
            @csrf
            <div class="form-row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_phone">Customer Phone Number</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" list="customer_phones" name="customer_phone" id="customer_phone" class="form-control" placeholder="Enter or choose customer phone number...">
                            <datalist id="customer_phones">
                            </datalist>
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
                                value="{{ uniqid() }}" disabled>
                        </div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="dynamicForm">
                    <thead>
                        <tr class="table">
                            <th>Product</th>
                            <th>Category</th>
                            <th>Sub Category</th>
                            <th>Pack</th>
                            <th style="min-width: 70px; width: 70px;">Pack Size</th>

                            <th style="min-width: 80px; width: 80px;">Remaining Qty</th>
                            <th>Unit Value</th>
                            <th>Qty</th>
                            <th>Discount</th>
                            <th>Total Amount</th>
                            <th>GST Rate (%)</th>
                            <th>GST Amount</th>
                            <th>CGST</th>
                            <th>SGST</th>

                        </tr>
                    </thead>
                    <tbody id="formBody">

                    </tbody>
                </table>
            </div>

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
            customerData(null)
            doctorData()
            $(document).on('blur', '#customer_phone', function () {
                customerData(this.value);
            });

            $(document).on('change', '.product', function () {
                console.log('Product changed');
                const count = $(this).data('count');
                const productId = this.value;

                if (productId) {
                    // Fetch product details
                    ajaxGetData(`/products?id=${productId}`, (res) => {
                        if (res?.data && res.data.length > 0) {
                            const productData = res.data[0];
                            categoryData(productData.category_id, count);
                            subCategoryData(productData.sub_category_id, count);
                            document.getElementById(`gstRate${count}`).value = productData.gst;
                        } else {
                            console.error('Product data not found');
                        }
                    });

                    // Fetch available pack sizes
                    packData_fetch(productId, count);
                } else {
                    // Clear fields if no product is selected
                    $(`#category${count}`).val('');
                    $(`#subCategory${count}`).val('');
                    $(`#gstRate${count}`).val('');
                    $(`#pack_selector${count}`).empty().append('<option value="">Choose Pack</option>');
                    $(`#qty${count}`).val('');
                    $(`#unit_value${count}`).val('');
                    $(`#pack${count}`).val('');
                }
            });

            $(document).on('change', '.pack-selector', function () {
                const count = $(this).data('count');
                const productId = $(`#product_name${count}`).val();
                const packId = this.value;

                if (packId) {
                    ajaxGetData(`/api/purchase_request?product_id=${productId}&pack_id=${packId}`, (res) => {
                        if (Array.isArray(res.purchase_request) && res.purchase_request.length > 0) {
                            const requestData = res.purchase_request[0];
                            if (requestData) {
                                $(`#qty${count}`).val(requestData.qty);
                                priceData(requestData.price_id, count);
                                // Also update the readonly pack size field
                                $(`#pack${count}`).val($(`#pack_selector${count} option:selected`).text());
                            } else {
                                $(`#qty${count}`).val('');
                                $(`#unit_value${count}`).val('');
                                $(`#pack${count}`).val('');
                            }
                        } else {
                            $(`#qty${count}`).val('');
                            $(`#unit_value${count}`).val('');
                            $(`#pack${count}`).val('');
                        }
                    });
                } else {
                    $(`#qty${count}`).val('');
                    $(`#unit_value${count}`).val('');
                    $(`#pack${count}`).val('');
                }
            });




            

            $(document).on('click', '#add_row', function () {
                count = count + 1;
                addNewRow(count)
            })

            // EDIT / UPDATE BILL
            const billId = "{{ $billId ?? '' }}";

            // If billId present, fetch and prefill the form
            if (billId) {
                fetchBillDetails(billId);
            }

            $(document).on('click', '#submitBilling', function () {
                const payload = gatherFormData();
                let csrfToken = $('meta[name="csrf-token"]').attr('content');

                if (billId) {
                    // Update existing bill
                    $.ajax({
                        url: `/customer/billing/update/${billId}`,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        contentType: 'application/json',
                        data: JSON.stringify(payload),
                        success: function(response) {
                            if (response.status === 200) {
                                Swal.fire({
                                    title: "Success!",
                                    icon: "success",
                                    text: "Customer Billing Updated Successfully.",
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => {
                                    viewAndPrintBill(billId);
                                });
                            } else {
                                Swal.fire({
                                    title: "Error!",
                                    icon: "error",
                                    text: "Failed to update billing.",
                                });
                            }
                        },
                        error: function(xhr) {
                            let msg = 'Failed to update billing. Please try again.';
                            try {
                                const json = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
                                if (json.message) msg = json.message;
                                else if (json.error) msg = json.error;
                                else if (xhr.responseText) msg = xhr.responseText;
                            } catch (e) {
                                // ignore parse errors
                            }
                            console.error('Update billing error:', xhr);
                            Swal.fire({
                                title: "Error!",
                                icon: "error",
                                text: msg,
                            });
                        }
                    });
                } else {
                    // Fallback to create if billId not present
                    ajaxPostData('/customer/billing/create', payload, csrfToken, (response) => {
                        const newBillId = response.bill_id;
                        Swal.fire({
                            title: "Success!",
                            icon: "success",
                            text: "Customer Billing Added Successfully.",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            viewAndPrintBill(newBillId);
                        });
                    }, (error) => {
                        Swal.fire({
                            title: "Error!",
                            icon: "error",
                            text: "Failed to create billing. Please try again.",
                        });
                    });
                }
            });

            // Fetch bill and prefill form for editing
            function fetchBillDetails(billId) {
                if (!billId) return;
                $.ajax({
                    url: `/api/customer/bill/${billId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                                    const bill = response.data.bill;
                                    const items = response.data.items || [];

                                    $('#customer_phone').val(bill.customer_phone);
                                    $('#customer_name').val(bill.customer_name);
                                    $('#invoiceNo').val(bill.invoiceNo);

                                    // Ensure doctors are loaded first then set selected
                                    doctorData(bill.doctor_name);

                                    // Set payment type (if exists)
                                                if (bill.paymentType) {
                                                    const paymentSel = $('#paymentType');
                                                    const wanted = String(bill.paymentType || '').trim().toLowerCase();
                                                    let matched = false;

                                                    // First pass: match normalized option values or exact text
                                                    paymentSel.find('option').each(function() {
                                                        const opt = $(this);
                                                        const optVal = String(opt.val() || '').trim().toLowerCase();
                                                        const optText = String(opt.text() || '').trim().toLowerCase();
                                                        if (optVal && (optVal === wanted)) {
                                                            paymentSel.val(opt.val());
                                                            matched = true;
                                                            return false; // break
                                                        }
                                                        if (optText === wanted) {
                                                            paymentSel.val(opt.val());
                                                            matched = true;
                                                            return false;
                                                        }
                                                    });

                                                    // Second pass: fuzzy contains match (helps when values/text include extra words)
                                                    if (!matched) {
                                                        paymentSel.find('option').each(function() {
                                                            const opt = $(this);
                                                            const optText = String(opt.text() || '').trim().toLowerCase();
                                                            if (optText.includes(wanted) || wanted.includes(optText)) {
                                                                paymentSel.val(opt.val());
                                                                matched = true;
                                                                return false;
                                                            }
                                                        });
                                                    }

                                                    // Trigger change in case any plugins/watchers need it
                                                    paymentSel.trigger('change');
                                                }

                                    // Clear existing rows and populate
                                    $('#formBody').empty();
                                    count = 0;

                                    // Fill each item in sequence to ensure product/pack selectors are populated before selection
                                    const fillSequence = (index) => {
                                        if (index >= items.length) {
                                            calculateTotalAmount();
                                            return;
                                        }
                                        const item = items[index];
                                        count++;
                                        addNewRow(count);
                                        // fill row with proper sequencing
                                        fillRowWithData(count, item, () => fillSequence(index + 1));
                                    };

                                    fillSequence(0);
                        } else {
                            Swal.fire({title: 'Error!', icon: 'error', text: 'Failed to load bill details.'});
                        }
                    },
                    error: function(xhr) {
                        console.error(xhr.responseText);
                        Swal.fire({title: 'Error!', icon: 'error', text: 'Failed to fetch bill data.'});
                    }
                });
            }

            function fillRowWithData(count, item, done = null) {
                // first populate product selector, then select product and fetch packs
                productData(count, item.productId, function() {
                    // After products loaded, set category/subcategory and values
                    $(`#product_name${count}`).val(item.productId);
                    $(`#category${count}`).val(item.category);
                    $(`#subCategory${count}`).val(item.subCategory);

                    // Fetch packs for this product and select matching pack (by id or name)
                    packData_fetch(item.productId, count, item.pack, function() {
                        // After pack selector populated and selected, try to get selected pack id
                        const selectedPackId = $(`#pack_selector${count}`).val();
                        const selectedPackText = $(`#pack_selector${count} option:selected`).text();
                        if (selectedPackText && selectedPackText !== 'Choose Pack') {
                            $(`#pack${count}`).val(selectedPackText);
                        } else {
                            $(`#pack${count}`).val(item.pack);
                        }

                        // Fetch remaining qty and price for this product-pack combination
                        if (selectedPackId) {
                            ajaxGetData(`/api/purchase_request?product_id=${item.productId}&pack_id=${selectedPackId}`, (res) => {
                                if (Array.isArray(res.purchase_request) && res.purchase_request.length > 0) {
                                    const requestData = res.purchase_request[0];
                                    if (requestData) {
                                        $(`#qty${count}`).val(requestData.qty);
                                        priceData(requestData.price_id, count);
                                    } else {
                                        $(`#qty${count}`).val('');
                                    }
                                } else {
                                    // no purchase_request result, try to set unit and qty from item
                                    $(`#qty${count}`).val(item.remaining_qty ?? '');
                                    $(`#unit_value${count}`).val(item.unitValue ?? '');
                                }

                                // fill remaining fields
                                $(`#assignQty${count}`).val(item.qty);
                                $(`#unit_value${count}`).val(item.unitValue);
                                $(`#discount${count}`).val(item.discount ?? 0);
                                $(`#gstRate${count}`).val(item.gstRate ?? 0);
                                $(`#gstAmount${count}`).val(item.gstAmount ?? 0);
                                $(`#totalAmount${count}`).val(item.totalAmount ?? 0);
                                $(`#cgst${count}`).val(((item.gstAmount ?? 0) / 2).toFixed(2));
                                $(`#sgst${count}`).val(((item.gstAmount ?? 0) / 2).toFixed(2));

                                if (typeof done === 'function') done();
                            });
                        } else {
                            // no pack id, try match by pack text via purchase_request without pack_id
                            ajaxGetData(`/api/purchase_request?product_id=${item.productId}`, (res) => {
                                if (Array.isArray(res.purchase_request) && res.purchase_request.length > 0) {
                                    const requestData = res.purchase_request.find(r => r.pack_name === item.pack) || res.purchase_request[0];
                                    if (requestData) {
                                        $(`#qty${count}`).val(requestData.qty);
                                        priceData(requestData.price_id, count);
                                    }
                                }

                                $(`#assignQty${count}`).val(item.qty);
                                $(`#unit_value${count}`).val(item.unitValue);
                                $(`#discount${count}`).val(item.discount ?? 0);
                                $(`#gstRate${count}`).val(item.gstRate ?? 0);
                                $(`#gstAmount${count}`).val(item.gstAmount ?? 0);
                                $(`#totalAmount${count}`).val(item.totalAmount ?? 0);
                                $(`#cgst${count}`).val(((item.gstAmount ?? 0) / 2).toFixed(2));
                                $(`#sgst${count}`).val(((item.gstAmount ?? 0) / 2).toFixed(2));

                                if (typeof done === 'function') done();
                            });
                        }
                    });
                });
            }



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
                        customerData();
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

                $.ajax({
                    url: 'api/doctor', 
                    type: 'POST',
                    data: {
                        name: $('#name').val(),
                        mail: $('#mail').val(),
                        phone: $('#phone').val(),
                        degree: $('#degree').val(),
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

        function customerData(phone) {
            if (phone) {
                ajaxGetData(`/customers?phone=${phone}`, (res)=>{
                    if (res?.data && res.data.length > 0) {
                        $('#customer_name').val(res?.data[0]?.name || '');
                    } else {
                        // Phone number doesn't exist, open modal
                        $('#addCustomer').modal('show');
                        $('#addCustomer #phone').val(phone);
                        $('#addCustomer #name').focus();
                    }
                });
            } else {
                ajaxGetData('/customers', (res)=>{
                    const datalist = $('#customer_phones');
                    datalist.empty();
                    res?.data?.forEach(element => {
                        datalist.append(`<option value="${element.phone}">`);
                    });
                });
            }
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

        
        function doctorData(selected = null, cb = null) { 
            ajaxGetData('/doctors', (res)=>{
                $('#doctor_name').empty().append('<option value="">Choose Doctor Name...</option>');
                for (let index = 0; index < res?.data?.length; index++) {
                    const element = res?.data[index];
                    $('#doctor_name').append('<option value="' + element.id + '">' + element.name + '</option>');
                }
                if (selected) {
                    // try match by value first, then by option text (case-insensitive)
                    const sel = $('#doctor_name').find(`option[value="${selected}"]`);
                    if (sel.length) {
                        $('#doctor_name').val(selected);
                    } else {
                        // match by text
                        $('#doctor_name option').each(function() {
                            if ($(this).text().toLowerCase() === String(selected).toLowerCase()) {
                                $(this).prop('selected', true);
                            }
                        });
                    }
                }
                if (typeof cb === 'function') cb();
            })
        }

        function productData(count, selected = null, cb = null) {
            ajaxGetData(`/products`, (res) => {
                const productSelector = $(`.product[data-count="${count}"]`);
                productSelector.empty().append('<option value="">Choose Product</option>');
                res?.data?.forEach(element => {
                    productSelector.append(`<option value="${element.id}">${element.product_name}</option>`);
                });
                if (selected) {
                    productSelector.val(selected);
                }
                if (typeof cb === 'function') cb();
            });
        }

        function packData_fetch(productId, count, selected = null, cb = null) {
            console.log('Fetching packs for product:', productId);
            ajaxGetData(`/api/packs/${productId}`, (res) => {
                console.log('Response from /api/packs:', res);
                const packSelector = $(`#pack_selector${count}`);
                packSelector.empty().append('<option value="">Choose Pack</option>');
                res?.data?.forEach(element => {
                    packSelector.append(`<option value="${element.id}">${element.pack_name}</option>`);
                });
                if (selected) {
                    // try to select by id first, otherwise by matching text
                    if (packSelector.find(`option[value="${selected}"]`).length) {
                        packSelector.val(selected);
                    } else {
                        // match by display text
                        packSelector.find('option').each(function() {
                            if ($(this).text() === selected) {
                                $(this).prop('selected', true);
                            }
                        });
                    }
                }
                if (typeof cb === 'function') cb();
            });
        }










        function categoryData(id, count) {
            ajaxGetData(`/category?id=${id}`, (res)=>{
                $(`#category${count}`).val(res?.data[0].category_name)
            })
        }

        function packData(id, count){
            console.log(id)
            ajaxGetData(`/pack?id=${id}`, (res) =>{
                $(`#pack${count}`).val(res?.data[0].pack_name)

            })
        }
        function priceData(id, count){
            ajaxGetData(`/price?id=${id}`, (res) =>{
                $(`#unit_value${count}`).val(res?.data[0].price_name)

            })
        }

        function subCategoryData(id, count) {
            ajaxGetData(`/sub-category?id=${id}`, (res)=>{
                $(`#subCategory${count}`).val(res?.data[0].sub_category_name)
            })
        }

        function addNewRow(id) {
            const newRow = `
                <tr id="row_${id}" class="new-row">
                    <td class="table-row-id row_id d-none product">${id}</td>
                    <td class="table-row">
                        <select data-enable-search="true" class="form-control product" data-count="${id}" name="productName[]" id="product_name${id}">
                            <option value="">Choose Product</option>
                        </select>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="category[]" id="category${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="subCategory[]" id="subCategory${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <select data-enable-search="true" class="form-control pack-selector" data-count="${id}" name="pack_selector[]" id="pack_selector${id}">
                            <option value="">Choose Pack</option>
                        </select>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="pack[]" id="pack${id}" readonly />
                        </div>
                    </td>

                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="number" class="form-control" name="total_qty[]" id="qty${id}" readonly/>
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="unit_value[]" id="unit_value${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="assignQty[]" id="assignQty${id}"  />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="number" class="form-control" name="discount[]" id="discount${id}" value="0" />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="totalAmount[]" id="totalAmount${id}" readonly />
                        </div>
                    </td>
                    <td><input type="number" class="form-control gstRate" name="gstRate[]" id="gstRate${id}" readonly /></td>
                    <td><input type="text" class="form-control gstAmount" name="gstAmount[]" id="gstAmount${id}" readonly /></td>
                    <td><input type="text" class="form-control cgst" name="cgst[]" id="cgst${id}" readonly /></td>
                    <td><input type="text" class="form-control sgst" name="sgst[]" id="sgst${id}" readonly /></td>

                </tr>
            `;
            $('#formBody').append(newRow);
            productData(id);
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
            $('tr').each(function() {
                updateTotalForRow($(this));
            });
            updateOverallTotal();
        });

        function calculateTotalAmount() {
            let totalAmount = 0;
            let totalGST = 0;
            let totalCGST = 0;
            let totalSGST = 0;

            $('#formBody').find('tr').each(function () {
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

        $(document).on('input keyup', '[name="assignQty[]"], [name="unit_value[]"], [name="discount[]"]', function () {
            calculateTotalAmount();
        });

        $(document).on('keyup', '.new-row [name="assignQty[]"]', function () {
            $(this).closest('tr').removeClass('new-row');
            count++;
            addNewRow(count);
        });




        function gatherFormData() {
            const rows = document.querySelectorAll('#dynamicForm tbody tr');
            const products = [];

            rows.forEach(row => {
                const productId = row.querySelector(`[name="productName[]"]`).value;

                if (productId) {
                    const category = row.querySelector(`[name="category[]"]`).value;
                    const subCategory = row.querySelector(`[name="subCategory[]"]`).value;
                    const pack = row.querySelector(`[name="pack[]"]`).value;
                    const unitValue = row.querySelector(`[name="unit_value[]"]`).value;
                    const qty = row.querySelector(`[name="assignQty[]"]`).value;
                    const discount = row.querySelector(`[name="discount[]"]`).value;
                    const totalAmount = row.querySelector(`[name="totalAmount[]"]`).value;
                    const gstRate = row.querySelector(`[name="gstRate[]"]`).value;
                    const gstAmount = row.querySelector(`[name="gstAmount[]"]`).value;

                    products.push({
                        productId,
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
                }
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
                invoiceNo: $('#invoiceNo').val(),
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
            }
        });

        // Print function
        function printModalContent() {
            var printContent = document.getElementById("printArea").innerHTML;
            var originalContent = document.body.innerHTML;

            // Remove all buttons from print content
            printContent = printContent.replace(/<button[^>]*>.*?<\/button>/g, '');

            var modalBackdrop = document.getElementsByClassName("modal-backdrop")[0];
            if (modalBackdrop) {
                modalBackdrop.remove();
            }

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            $('#printModal').modal('show');
        }

</script>
@endsection