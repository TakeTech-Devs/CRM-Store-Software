@extends('layouts.dashboard')

@section('title', 'Create Staff Billing')

@section('content')
<style>
    .inhouse-row td { background: #f0fdf4 !important; }
    .inhouse-row .avail-label { color: #16a34a !important; font-weight: 600; }
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
        <h2 class="text-dark">Create Staff Billing</h2>
        <div class="text-right">
            <a href="{{ url('store/staff/billing') }}" class="btn btn-secondary btn-sm">View Staff Billing List</a>
        </div>
    </div>

    <div class="mt-4 position-relative">
        <form id="staffBillingCreate">
            @csrf
            <div class="form-row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="staff_phone">Staff Phone Number</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" list="staff_phones" name="staff_phone" id="staff_phone" class="form-control" placeholder="Enter or choose staff phone number...">
                            <datalist id="staff_phones"></datalist>
                            <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal" data-target="#addStaff">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="doctor_name">Doctor Name</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="doctor_name[]" id="doctor_name" class="form-control">
                                <option value="">Choose Doctor Name...</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal" data-target="#addDoctor">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-row mb-2">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="staff_name">Staff Name</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" name="staff_name" id="staff_name" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="paymentType">Payment Type</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="paymentType[]" id="paymentType" class="form-control">
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

            <div class="table-responsive">
                <table class="table table-bordered" id="dynamicForm">
                    <!-- Head removed to save space and use inline labels -->
                </table>
            </div>

            <button type="button" name="add_row" id="add_row"
                class="btn btn-sm btn-secondary mb-3 mt-3 float-right ml-3">
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

<!-- ADD STAFF MODAL -->
<div class="modal fade" id="addStaff" tabindex="-1" role="dialog" aria-labelledby="addStaffLabel" aria-hidden="true">
    <div class="modal-dialog container" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                <h5 class="modal-title" id="addStaffLabel">Add Staff</h5>
            </div>
            <div class="modal-body">
                <form id="addStaffForm" class="container">
                    <div class="form-group">
                        <label for="staff_modal_stf_id">Company Staff ID</label>
                        <input type="text" class="form-control" id="staff_modal_stf_id" name="stf_id" placeholder="e.g. EMP-001" required>
                    </div>
                    <div class="form-group">
                        <label for="staff_modal_name">Staff Name</label>
                        <input type="text" class="form-control" id="staff_modal_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="staff_modal_mail">Staff Mail</label>
                        <input type="email" class="form-control" id="staff_modal_mail" name="mail" required>
                    </div>
                    <div class="form-group">
                        <label for="staff_modal_phone">Staff Phone Number</label>
                        <input type="tel" class="form-control" id="staff_modal_phone" name="phone" required>
                    </div>
                    <div class="form-group d-none">
                        <label>Status:</label>
                        <div class="form-group d-flex justify-content-start align-items-center">
                            <div class="form-check mx-3">
                                <input type="radio" class="form-check-input" id="staffStatusActive" name="status" value="1" checked>
                                <label class="form-check-label" for="staffStatusActive">Active</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="staffStatusInactive" name="status" value="0">
                                <label class="form-check-label" for="staffStatusInactive">Deactive</label>
                            </div>
                        </div>
                    </div>
                    <div class="save-button d-flex align-items-center justify-content-center">
                        <button type="submit" id="addStaffFormBtn" class="btn btn-success mx-2">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
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
                        <label for="doctor_modal_name">Doctor Name</label>
                        <input type="text" class="form-control" id="doctor_modal_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_modal_mail">Doctor Mail</label>
                        <input type="email" class="form-control" id="doctor_modal_mail" name="mail" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_modal_phone">Doctor Phone Number</label>
                        <input type="tel" class="form-control" id="doctor_modal_phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="doctor_modal_degree">Doctor Degree</label>
                        <input type="text" class="form-control" id="doctor_modal_degree" name="degree" required>
                    </div>
                    <div class="form-group d-none">
                        <label>Status:</label>
                        <div class="form-group d-flex justify-content-start align-items-center">
                            <div class="form-check mx-3">
                                <input type="radio" class="form-check-input" id="doctorStatusActive" name="status" value="1" checked>
                                <label class="form-check-label" for="doctorStatusActive">Active</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="doctorStatusInactive" name="status" value="0">
                                <label class="form-check-label" for="doctorStatusInactive">Deactive</label>
                            </div>
                        </div>
                    </div>
                    <div class="save-button d-flex align-items-center justify-content-center">
                        <button type="submit" id="addDoctorFormBtn" class="btn btn-success mx-2">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

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
                                        Staff: <span class="staffName" style="font-size: 14px !important; font-weight:400;"></span>
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
                                <tbody style="border-bottom:3px solid; text-align: center; padding: 15px 0px !important;"></tbody>
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
                                <span>Address : <span class="storeAddress"></span></span><br>
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

<script>
    $(document).ready(function () {
        count = 1;
        addNewRow(count);
        staffData(null);
        doctorData();

        $(document).on('blur', '#staff_phone', function () {
            staffData(this.value);
        });

        $(document).on('change', '.product-pack-price', function () {
            const count = $(this).data('count');
            const sel = $(this).find('option:selected');
            const val = $(this).val();

            if (!val) {
                $(`#product_name${count}`).val('');
                $(`#pr_id${count}`).val('');
                $(`#inhouse_id${count}`).val('');
                $(`#pack${count}`).val('');
                $(`#unit_value${count}`).val('');
                $(`#gstRate${count}`).val('');
                $(`#qty${count}`).val('');
                $(`#category${count}`).val('');
                $(`#subCategory${count}`).val('');
                $(`#avail_qty_text_${count}`).text('Available: -');
                $(`#assignQty${count}`).css('border-color', '');
                $(`#row_block_${count}`).removeClass('inhouse-row');
                return;
            }

            const isInhouse = sel.data('type') === 'inhouse';
            $(`#pack${count}`).val(sel.data('pack-name'));
            $(`#unit_value${count}`).val(sel.data('price'));
            $(`#gstRate${count}`).val(sel.data('gst') || 0);
            $(`#category${count}`).val(sel.data('category'));
            $(`#subCategory${count}`).val(sel.data('sub-category'));
            $(`#assignQty${count}`).css('border-color', '');

            if (isInhouse) {
                $(`#product_name${count}`).val('');
                $(`#pr_id${count}`).val('');
                $(`#inhouse_id${count}`).val(sel.data('inhouse-id'));
                $(`#qty${count}`).val('');
                $(`#avail_qty_text_${count}`).text('Inhouse — no stock limit');
                $(`#row_block_${count}`).addClass('inhouse-row');
            } else {
                $(`#product_name${count}`).val(sel.data('product-id'));
                $(`#pr_id${count}`).val(sel.data('pr-id'));
                $(`#inhouse_id${count}`).val('');
                $(`#qty${count}`).val(sel.data('qty'));
                $(`#avail_qty_text_${count}`).text('Available: ' + sel.data('qty'));
                $(`#row_block_${count}`).removeClass('inhouse-row');
            }
        });

        $(document).on('click', '#add_row', function () {
            count = count + 1;
            addNewRow(count);
        });

        // CREATING BILL
        $(document).on('click', '#submitBilling', function () {
            let hasError = false;
            $('#dynamicForm .product-tbody').each(function () {
                const assignQtyInput = $(this).find('[name="assignQty[]"]');
                const discountInput = $(this).find('[name="discount[]"]');
                const isInhouse = $(this).hasClass('inhouse-row');
                const totalAvailVal = $(this).find('[name="total_qty[]"]').val();
                const inputQty = parseFloat(assignQtyInput.val()) || 0;
                const discountVal = parseFloat(discountInput.val()) || 0;

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

            ajaxPostData('/staff/billing/create', payload, csrfToken, (response) => {
                const billId = response.bill_id;
                Swal.fire({
                    title: "Success!",
                    icon: "success",
                    text: "Staff Billing Added Successfully.",
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
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

        // ADD STAFF
        $('#addStaffForm').on('submit', function (event) {
            event.preventDefault();

            $.ajax({
                url: '/api/staff',
                type: 'POST',
                data: {
                    stf_id: $('#staff_modal_stf_id').val(),
                    name: $('#staff_modal_name').val(),
                    mail: $('#staff_modal_mail').val(),
                    phone: $('#staff_modal_phone').val(),
                    status: $('input[name="status"]:checked').val(),
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    staffData(null);
                    $('#addStaffForm')[0].reset();
                    Swal.fire({ title: "Staff!", icon: "success", text: "Staff Added Successfully." });
                    $('#addStaff').modal('hide');
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.data || 'An error occurred.';
                    Swal.fire({ title: 'Error', icon: 'error', text: msg });
                }
            });
        });

        // ADD DOCTOR
        $('#addDoctorForm').on('submit', function (event) {
            event.preventDefault();

            $.ajax({
                url: 'api/doctor',
                type: 'POST',
                data: {
                    name: $('#doctor_modal_name').val(),
                    mail: $('#doctor_modal_mail').val(),
                    phone: $('#doctor_modal_phone').val(),
                    degree: $('#doctor_modal_degree').val(),
                    status: $('input[name="status"]:checked').val(),
                },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    doctorData();
                    Swal.fire({ title: "Doctor!", icon: "success", text: "Doctor Added Successfully." });
                    $('#addDoctor').modal('hide');
                },
                error: function (xhr) {
                    alert('An error occurred: ' + xhr.responseText);
                }
            });
        });

        // Print buttons
        $(document).on('click', '#printButton', function () {
            printModalContent();
        });

        $(document).on('click', '#printButtonTVS', function () {
            printModalContentTVS();
        });

        $(document).on('click', '#createNewBill', function () {
            window.location.href = "/store/staff/create/billing";
        });

        $(document).on('click', '#backToList', function () {
            window.location.href = "/store/staff/billing";
        });

        calculateTotalAmount();
    });

    function staffData(phone) {
        if (phone) {
            ajaxGetData(`/staffs?phone=${phone}`, (res) => {
                if (res?.data && res.data.length > 0) {
                    $('#staff_name').val(res?.data[0]?.name || '');
                } else {
                    $('#addStaff').modal('show');
                    $('#staff_modal_phone').val(phone);
                    $('#staff_modal_name').focus();
                }
            });
        } else {
            ajaxGetData('/staffs', (res) => {
                const datalist = $('#staff_phones');
                datalist.empty();
                res?.data?.forEach(element => {
                    datalist.append(`<option value="${element.phone}">`);
                });
            });
        }
    }

    function doctorData() {
        ajaxGetData('/doctors', (res) => {
            for (let index = 0; index < res?.data?.length; index++) {
                const element = res?.data[index];
                $('#doctor_name').append('<option value="' + element.id + '">' + element.name + '</option>');
            }
        });
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

    function productData(count) {
        loadBillingProductOptions((options) => {
            const sel = $(`#product_pack_price${count}`);
            sel.empty().append('<option value="">Choose Product</option>');

            const regular = options.filter(o => o.type === 'regular');
            const inhouse = options.filter(o => o.type === 'inhouse');

            if (regular.length) {
                const grp = $('<optgroup label="— Regular Products —">');
                regular.forEach(opt => {
                    grp.append(`<option value="${opt.purchase_request_id}"
                        data-type="regular"
                        data-product-id="${opt.product_id}"
                        data-pack-name="${opt.pack_name}"
                        data-price="${opt.price_name}"
                        data-gst="${opt.gst}"
                        data-qty="${opt.avail_qty}"
                        data-category="${opt.category_name}"
                        data-sub-category="${opt.sub_category_name}"
                        data-pr-id="${opt.purchase_request_id}"
                    >${opt.product_name} - ${opt.pack_name} - ₹${opt.price_name}</option>`);
                });
                sel.append(grp);
            }

            if (inhouse.length) {
                const grp = $('<optgroup label="— Inhouse Products —">');
                inhouse.forEach(opt => {
                    grp.append(`<option value="inhouse_${opt.inhouse_product_id}"
                        data-type="inhouse"
                        data-inhouse-id="${opt.inhouse_product_id}"
                        data-pack-name="${opt.pack_name}"
                        data-price="${opt.price_name}"
                        data-gst="0"
                        data-category="${opt.category_name}"
                        data-sub-category="${opt.sub_category_name}"
                    >[Inhouse] ${opt.product_name} - ${opt.pack_name} - ₹${opt.price_name}</option>`);
                });
                sel.append(grp);
            }

            sel.select2({ width: '100%' });
        });
    }

    function addNewRow(id) {
        const newTbody = `
            <tbody class="product-tbody new-row" id="row_block_${id}">
                <!-- ROW 1 -->
                <tr>
                    <td style="width: 40%" colspan="2">
                        <small class="text-muted font-weight-bold">Product - Pack - Price</small>
                        <input type="hidden" class="table-row-id row_id" value="${id}">
                        <select class="form-control product-pack-price mt-1" data-count="${id}" name="productPackPrice[]" id="product_pack_price${id}">
                            <option value="">Choose Product</option>
                        </select>
                        <!-- Hidden fields populated on selection -->
                        <input type="hidden" name="productName[]" id="product_name${id}" />
                        <input type="hidden" name="inhouse_product_id[]" id="inhouse_id${id}" />
                        <input type="hidden" name="pack[]" id="pack${id}" />
                        <input type="hidden" name="purchase_request_id[]" id="pr_id${id}" />
                        <input type="hidden" name="total_qty[]" id="qty${id}" />
                        <input type="hidden" class="gstRate" name="gstRate[]" id="gstRate${id}" />
                        <input type="hidden" class="gstAmount" name="gstAmount[]" id="gstAmount${id}" />
                        <input type="hidden" class="cgst" name="cgst[]" id="cgst${id}" />
                        <input type="hidden" class="sgst" name="sgst[]" id="sgst${id}" />
                    </td>
                    <td style="width: 30%">
                        <small class="text-muted font-weight-bold">Category</small>
                        <input type="text" class="form-control mt-1" name="category[]" id="category${id}" readonly />
                    </td>
                    <td style="width: 30%">
                        <small class="text-muted font-weight-bold">Sub Category</small>
                        <input type="text" class="form-control mt-1" name="subCategory[]" id="subCategory${id}" readonly />
                    </td>
                </tr>
                <!-- ROW 2 -->
                <tr>
                    <td>
                        <small class="text-muted font-weight-bold">Unit Value</small>
                        <input type="text" class="form-control mt-1" name="unit_value[]" id="unit_value${id}" readonly />
                    </td>
                    <td>
                        <small class="text-muted font-weight-bold avail-label">Qty (<span id="avail_qty_text_${id}" class="text-info">Available: -</span>)</small>
                        <input type="text" class="form-control mt-1" name="assignQty[]" id="assignQty${id}" placeholder="Qty" />
                    </td>
                    <td>
                        <small class="text-muted font-weight-bold">Discount (%)</small>
                        <input type="number" class="form-control mt-1" name="discount[]" id="discount${id}" value="0" />
                    </td>
                    <td>
                        <small class="text-muted font-weight-bold">Total Amount</small>
                        <input type="text" class="form-control mt-1" name="totalAmount[]" id="totalAmount${id}" readonly />
                    </td>
                </tr>
            </tbody>
        `;
        $('#dynamicForm').append(newTbody);
        productData(id);
    }

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

            const discountDecimal = discount / 100;
            const discountAmount = qty * unitValue * discountDecimal;
            const amount = (qty * unitValue) - discountAmount;

            $(this).find('[name="totalAmount[]"]').val(amount.toFixed(2));
            totalAmount += amount;

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

        $('#totalAmount').text(totalAmount.toFixed(2));
        $('#totalGST').text(totalGST.toFixed(2));
        $('#totalCGST').text(totalCGST.toFixed(2));
        $('#totalSGST').text(totalSGST.toFixed(2));
    }

    $(document).on('input keyup', '[name="assignQty[]"]', function () {
        const row = $(this).closest('.product-tbody');
        const count = row.find('.row_id').val();
        const totalAvailVal = $(`#qty${count}`).val();
        if (totalAvailVal === "" || totalAvailVal === undefined || totalAvailVal === null) {
            $(`#avail_qty_text_${count}`).text('Available: -');
            $(this).css('border-color', '');
            return;
        }
        const totalAvail = parseFloat(totalAvailVal) || 0;
        const inputQty = parseFloat($(this).val()) || 0;
        const currentAvail = totalAvail - inputQty;
        $(`#avail_qty_text_${count}`).text('Available: ' + currentAvail);
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
            const productId = row.querySelector(`[name="productName[]"]`).value;
            const inhouseId = row.querySelector(`[name="inhouse_product_id[]"]`).value;
            const isInhouse = row.classList.contains('inhouse-row');

            if (!productId && !inhouseId) return;

            const purchase_request_id = row.querySelector(`[name="purchase_request_id[]"]`).value;
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
                purchase_request_id: isInhouse ? null : purchase_request_id,
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
            billingType: "Staff Billing",
            staff_phone: $('#staff_phone').val(),
            doctor_name: $('#doctor_name').val(),
            paymentType: $('#paymentType').val(),
            staff_name: $('#staff_name').val(),
            total_amt: $('#totalAmount').text(),
            gstAmount: $('#totalGST').text(),
            cgst: $('#totalCGST').text(),
            sgst: $('#totalSGST').text(),
            billing_date: formattedDate,
            product_billings: products
        };

        return payload;
    }

    function viewAndPrintBill(billId) {
        $.ajax({
            url: `/api/staff/bill/${billId}`,
            method: 'GET',
            success: function (response) {
                if (response.status === 200) {
                    populatePrintModal(response.data);
                    $('#printModal').modal('show');
                } else {
                    Swal.fire({ title: "Error!", icon: "error", text: "Failed to fetch bill details." });
                }
            },
            error: function () {
                Swal.fire({ title: "Error!", icon: "error", text: "Failed to fetch bill details." });
            }
        });
    }

    function populatePrintModal(data) {
        let bill = data.bill;
        let items = data.items;
        let store = data.store;

        $('.invoiceNo').text(bill.invoiceNo);
        $('.billingDate').text(bill.billing_date);
        $('.staffName').text(bill.staff_name);
        $('.drName').text(data.doctor_name || 'N/A');
        $('.grandTotal').text(parseFloat(bill.total_amt).toFixed(2));
        $('.taxableValue').text((parseFloat(bill.total_amt) - parseFloat(bill.gst || 0)).toFixed(2));
        $('.totalGST').text(parseFloat(bill.gst || 0).toFixed(2));
        $('.totalCGST').text(parseFloat(bill.cgst || 0).toFixed(2));
        $('.totalSGST').text(parseFloat(bill.sgst || 0).toFixed(2));

        if (store) {
            $('.storeAddress').text(store.store_address || 'Not Provided');
            $('.dlNumber').text(store.dl_number || 'Not Provided');
            $('.helplineNumber').text(store.helpline_number || 'Not Provided');
        }

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

    function printModalContent() {
        var printContent = document.getElementById("printArea").innerHTML;
        printContent = printContent.replace(/<button[^>]*>.*?<\/button>/g, '');
        var modalBackdrop = document.getElementsByClassName("modal-backdrop")[0];
        if (modalBackdrop) modalBackdrop.remove();
        document.body.innerHTML = printContent;
        window.print();
        location.reload();
    }

    function printModalContentTVS() {
        var printContent = document.getElementById("printArea").innerHTML;
        var printWindow = window.open('', '', 'height=600,width=400');
        printWindow.document.write('<html><head><title>Print</title>');
        printWindow.document.write(`
            <style>
                body { font-family: 'Courier New', Courier, monospace; font-size: 8px; width: 4in; }
                table { width: 100%; border-collapse: collapse; }
                th, td { padding: 2px; text-align: left; }
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
</script>
@endsection
