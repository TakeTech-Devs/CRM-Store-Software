@extends('layouts.dashboard')

@section('title', 'Create Staff Billing')

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
    .inhouse-row .avail-label { color: #16a34a; font-weight: 600; }
    #dynamicForm { display: flex; flex-direction: column; gap: 6px; }
    .product-tbody { display: flex; align-items: flex-end; gap: 8px; padding: 8px 10px; border-radius: 6px; border: 1px solid #e5e5e5; background-color: #ffffff; }
    .product-tbody:nth-child(even) { background-color: #fdf4f0; border-color: #a8a8a8; }
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
    .billing-header-section {
        background: #f5e0d0;
        border: 1px solid #e0b89e;
        border-radius: 10px;
        padding: 1.2rem 1.4rem 0.4rem;
        margin-bottom: 1.2rem;
    }
    .billing-header-section label { font-weight: 700; }
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
            <div class="billing-header-section">
            <div class="form-row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="staff_phone">Staff Phone Number</label>
                        <div class="form-group d-flex align-items-center">
                            <select name="staff_phone" id="staff_phone" class="form-control select2-manual" style="width:100%">
                                <option value="">Enter or choose staff phone number...</option>
                            </select>
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
            </div>{{-- end billing-header-section --}}

            <div id="dynamicForm"></div>

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
                                        <th>Brand</th>
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

        $('#staff_phone').select2({
            placeholder: 'Enter or choose staff phone number...',
            allowClear: true,
            width: '100%',
            tags: true,
            createTag: function (params) {
                const term = $.trim(params.term);
                if (!term) return null;
                return { id: term, text: term, newTag: true };
            },
        });
        $('#staff_phone').on('change', function () {
            const phone = $(this).val();
            if (!phone) { $('#staff_name').val(''); return; }
            const opt = $(this).find('option:selected');
            const isNew = opt.data('select2-tag') === true;
            if (isNew) {
                $('#staff_name').val('');
                $('#addStaff').modal('show');
                $('#staff_modal_phone').val(phone);
            } else {
                const parts = opt.text().split(' — ');
                $('#staff_name').val(parts.length > 1 ? parts.slice(1).join(' — ') : '');
                openSelect2Safe('#doctor_name');
            }
        });

        $('#paymentType').select2({ width: '100%', placeholder: 'Choose Payment Type...' });

        $(document).on('select2:select', '#doctor_name', function () {
            openSelect2Safe('#paymentType');
        });
        $(document).on('select2:select', '#paymentType', function () {
            const firstRowId = $('#dynamicForm .product-tbody').first().find('.row_id').val();
            if (firstRowId) openSelect2Safe(`#brand_select${firstRowId}`);
        });

        staffData(function () {
            // Land the cursor in the phone search box only once options are loaded
            openSelect2Safe('#staff_phone');
        });
        doctorData();


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
        let addStaffJustSaved = false;
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
                    addStaffJustSaved = true;
                    const newPhone = $('#staff_modal_phone').val();
                    const newName  = $('#staff_modal_name').val();
                    staffData(function () {
                        $('#staff_phone').val(newPhone).trigger('change.select2');
                        $('#staff_name').val(newName);
                    });
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
        let addDoctorJustSaved = false;
        $('#addDoctorForm').on('submit', function (event) {
            event.preventDefault();

            $.ajax({
                url: '/api/doctor',
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
                    addDoctorJustSaved = true;
                    doctorData();
                    Swal.fire({ title: "Doctor!", icon: "success", text: "Doctor Added Successfully." });
                    $('#addDoctor').modal('hide');
                    openSelect2Safe('#paymentType');
                },
                error: function (xhr) {
                    alert('An error occurred: ' + xhr.responseText);
                }
            });
        });

        // Any Select2 dropdown left open behind a modal doesn't close itself —
        // close them all before any modal is shown, regardless of trigger path.
        $(document).on('show.bs.modal', '.modal', function () {
            closeAllSelect2();
        });

        // If a modal is dismissed WITHOUT saving (Cancel/X/Escape/backdrop),
        // reopen the field that triggered it so the user can pick up where
        // they left off. On a successful save the chain already continues
        // to the next field, so skip reopening in that case.
        $('#addStaff').on('hidden.bs.modal', function () {
            if (!addStaffJustSaved) openSelect2Safe('#staff_phone');
            addStaffJustSaved = false;
        });
        $('#addDoctor').on('hidden.bs.modal', function () {
            if (!addDoctorJustSaved) openSelect2Safe('#doctor_name');
            addDoctorJustSaved = false;
        });

        // Focus the first field the instant a modal opens, regardless of how it was opened
        $('#addStaff').on('shown.bs.modal', function () {
            document.getElementById('staff_modal_stf_id')?.focus();
        });
        $('#addDoctor').on('shown.bs.modal', function () {
            document.getElementById('doctor_modal_name')?.focus();
        });

        // Keyboard shortcuts
        $(document).on('keydown', function (e) {
            // Ctrl+Enter — submit the bill (skip while any modal is open)
            if (e.ctrlKey && e.key === 'Enter') {
                if ($('.modal.show').length) return;
                e.preventDefault();
                $('#submitBilling').trigger('click');
                return;
            }

            // Ctrl+Backspace — delete the row the cursor is currently in
            if (e.ctrlKey && e.key === 'Backspace') {
                const $row = $(document.activeElement).closest('.product-tbody');
                if ($row.length) {
                    e.preventDefault();
                    deleteRow($row);
                }
                return;
            }

            // Ctrl+Up / Ctrl+Down — jump to the same field in the previous/next row
            if (e.ctrlKey && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
                const info = getFocusedFieldInfo();
                if (!info) return;
                e.preventDefault();
                const rowIds = $('#dynamicForm .product-tbody').map(function () {
                    return $(this).find('.row_id').val();
                }).get();
                const currentIndex = rowIds.indexOf(info.rowId);
                if (currentIndex === -1) return;
                const targetIndex = e.key === 'ArrowDown' ? currentIndex + 1 : currentIndex - 1;
                if (targetIndex < 0 || targetIndex >= rowIds.length) return;
                focusSlotInRow(info.slot, rowIds[targetIndex]);
                return;
            }

            // Ctrl+S — open Add Staff
            if (e.ctrlKey && !e.shiftKey && (e.key === 's' || e.key === 'S')) {
                if ($('.modal.show').length) return;
                e.preventDefault();
                $('#addStaff').modal('show');
                return;
            }

            // Ctrl+D — open Add Doctor
            if (e.ctrlKey && !e.shiftKey && (e.key === 'd' || e.key === 'D')) {
                if ($('.modal.show').length) return;
                e.preventDefault();
                $('#addDoctor').modal('show');
                return;
            }

            // Print modal shortcuts — only while it's open
            if ($('#printModal').hasClass('show')) {
                if (e.key === 'p' || e.key === 'P') {
                    e.preventDefault();
                    printModalContent();
                    return;
                }
                if (e.key === 'r' || e.key === 'R') {
                    e.preventDefault();
                    printModalContentTVS();
                    return;
                }
            }
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

    // Closes every currently-open Select2 dropdown (optionally skipping one id) —
    // Select2 doesn't always clean up a stale open dropdown on its own when
    // something else (another field, a modal) takes over programmatically.
    function closeAllSelect2(exceptId) {
        $('.select2-hidden-accessible').each(function () {
            const $s = $(this);
            if (this.id !== exceptId && $s.data('select2') && $s.data('select2').isOpen()) {
                $s.select2('close');
            }
        });
    }

    // Opens a Select2 dropdown, but only if it has actually finished initializing —
    // guards against chaining into a field whose options are still loading async.
    // Select2 doesn't reliably move keyboard focus into its own search box when
    // opened programmatically, so force it directly here every time.
    function openSelect2Safe(selector) {
        const $el = $(selector);
        if (!$el.hasClass('select2-hidden-accessible')) return;
        closeAllSelect2($el.attr('id'));
        $el.select2('open');
        setTimeout(function () {
            document.querySelector('.select2-search__field')?.focus({ preventScroll: true });
        }, 0);
    }

    function deleteRow($row) {
        const $rows = $('#dynamicForm .product-tbody');
        const id = $row.find('.row_id').val();
        if ($rows.length <= 1) {
            resetRowCompletely(id, $row);
        } else {
            $row.remove();
            calculateTotalAmount();
        }
    }

    function resetRowCompletely(id, $row) {
        resetCascadeFrom(id, 'product');
        $(`#discount${id}`).val('0');
        const brandSel = $(`#brand_select${id}`);
        if (brandSel.hasClass('select2-hidden-accessible')) brandSel.select2('destroy');
        populateBrandSelect(id);
        $row.removeClass('inhouse-row');
        calculateTotalAmount();
    }

    // Field-slot id patterns — used by Ctrl+Up/Ctrl+Down row navigation
    const ROW_FIELD_PATTERNS = {
        brand: /^brand_select(\d+)$/,
        product: /^product_select(\d+)$/,
        pack: /^pack_select(\d+)$/,
        price: /^price_(?:select|display)(\d+)$/,
        unit: /^unit_value(\d+)$/,
        qty: /^assignQty(\d+)$/,
        discount: /^discount(\d+)$/,
        total: /^totalAmount(\d+)$/,
    };

    // Figures out which row + field "slot" the cursor is currently in, whether
    // it's a plain input or a Select2 field (searching or just tabbed-to).
    function getFocusedFieldInfo() {
        const active = document.activeElement;
        let fieldId = null;

        if (active.classList.contains('select2-search__field')) {
            // Select2 stamps aria-controls="select2-<originalId>-results" on the
            // search box — the most reliable way to trace back to its owner field.
            const controls = active.getAttribute('aria-controls') || '';
            const m = controls.match(/^select2-(.+)-results$/);
            fieldId = m ? m[1] : null;
        } else if ($(active).closest('.select2-container').length) {
            fieldId = $(active).closest('.select2-container').prev('select, input').attr('id');
        } else {
            fieldId = active.id;
        }

        if (!fieldId) return null;

        for (const slot in ROW_FIELD_PATTERNS) {
            const m = fieldId.match(ROW_FIELD_PATTERNS[slot]);
            if (m) return { slot, rowId: m[1] };
        }
        return null;
    }

    function focusSlotInRow(slot, rowId) {
        if (slot === 'price') {
            const $priceSelect = $(`#price_select${rowId}`);
            if ($priceSelect.hasClass('select2-hidden-accessible') && $priceSelect.is(':visible')) {
                openSelect2Safe(`#price_select${rowId}`);
            } else {
                document.getElementById(`price_display${rowId}`)?.focus({ preventScroll: true });
            }
            return;
        }

        const idMap = {
            brand: 'brand_select', product: 'product_select', pack: 'pack_select',
            unit: 'unit_value', qty: 'assignQty', discount: 'discount', total: 'totalAmount',
        };
        const $target = $(`#${idMap[slot]}${rowId}`);
        if (!$target.length) return;

        if ($target.hasClass('select2-hidden-accessible')) {
            openSelect2Safe(`#${idMap[slot]}${rowId}`);
        } else {
            $target[0].focus({ preventScroll: true });
        }
    }

    function staffData(callback) {
        ajaxGetData('/staffs', (res) => {
            const staffs = res?.data || [];
            $('#staff_phone').find('option:not(:first)').remove();
            staffs.forEach(s => {
                $('#staff_phone').append(`<option value="${s.phone}">${s.phone} — ${s.name}</option>`);
            });
            if (typeof callback === 'function') callback();
        });
    }

    function doctorData(callback) {
        ajaxGetData('/doctors', (res) => {
            $('#doctor_name').find('option:not(:first)').remove();
            for (let index = 0; index < res?.data?.length; index++) {
                const element = res?.data[index];
                $('#doctor_name').append('<option value="' + element.id + '">' + element.name + '</option>');
            }
            if ($('#doctor_name').hasClass('select2-hidden-accessible')) {
                $('#doctor_name').select2('destroy');
            }
            $('#doctor_name').select2({ width: '100%', placeholder: 'Choose Doctor Name...' });
            if (typeof callback === 'function') callback();
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
            sel.empty().append(`<option value="">Choose ${f.charAt(0).toUpperCase()+f.slice(1)}</option>`).prop('disabled', true).show();
            sel.off('select2:select select2:clear');
        });
        $(`#price_display${id}`).hide().val('');
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
                    if (!seen.has(o.product_name)) {
                        seen.add(o.product_name);
                        sel.append(`<option value="${o.product_name}" data-type="inhouse">${o.product_name}</option>`);
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
            openSelect2Safe(sel);
        });
    }

    function loadPacksForProduct(id, productVal, type, brandId) {
        loadBillingProductOptions((options) => {
            const sel = $(`#pack_select${id}`);
            sel.empty().append('<option value="">Choose Pack</option>');
            let rows = type === 'inhouse'
                ? options.filter(o => o.type === 'inhouse' && String(o.product_name) === String(productVal))
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
            openSelect2Safe(sel);
        });
    }

    function applyPriceSelection(id, opt) {
        const val = opt.val();
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
        calculateTotalAmount();
        document.getElementById(`assignQty${id}`)?.focus({ preventScroll: true });
    }

    function loadPricesForPack(id, productVal, packId, type, brandId) {
        loadBillingProductOptions((options) => {
            const sel = $(`#price_select${id}`);
            const display = $(`#price_display${id}`);
            sel.empty().append('<option value="">Choose Price</option>');
            let rows = type === 'inhouse'
                ? options.filter(o => o.type === 'inhouse' && String(o.product_name) === String(productVal) && String(o.pack_id) === String(packId))
                : options.filter(o => o.type === 'regular' && String(o.product_id) === String(productVal) && String(o.pack_id) === String(packId) && String(o.brand_id) === String(brandId));
            let priceCount = 0;
            if (type === 'inhouse') {
                priceCount = rows.length;
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
                const groups = {};
                rows.forEach(o => {
                    const key = String(o.price_name);
                    if (!groups[key]) {
                        groups[key] = { price_name: o.price_name, gst: o.gst || 0, pack_name: o.pack_name, product_id: o.product_id, total_qty: 0, pr_ids: [], category_name: o.category_name, sub_category_name: o.sub_category_name };
                    }
                    groups[key].total_qty += (parseFloat(o.avail_qty) || 0);
                    groups[key].pr_ids.push(o.purchase_request_id);
                });
                priceCount = Object.keys(groups).length;
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

            sel.off('select2:select select2:clear');

            if (priceCount === 1) {
                // Only one price available — auto-load it, no dropdown needed
                if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
                const onlyOpt = sel.find('option').not('[value=""]').first();
                sel.val(onlyOpt.val());
                sel.hide();
                display.val('₹' + onlyOpt.data('price')).show();
                applyPriceSelection(id, onlyOpt);
            } else {
                display.hide().val('');
                sel.show().prop('disabled', false);
                if (sel.hasClass('select2-hidden-accessible')) sel.select2('destroy');
                sel.select2({ width: '100%', placeholder: 'Choose Price' });
                sel.on('select2:select select2:clear', function () {
                    applyPriceSelection(id, $(this).find('option:selected'));
                });
                openSelect2Safe(sel);
            }
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
                    <input type="text" class="form-control mt-1" id="price_display${id}" readonly style="display:none;" />
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

    function ensureNextRow($row) {
        let $next = $row.next('.product-tbody');
        if ($next.length === 0) {
            $row.removeClass('new-row');
            count++;
            addNewRow(count);
            $next = $row.next('.product-tbody');
        }
        return $next;
    }

    $(document).on('keyup', '.new-row [name="assignQty[]"]', function (e) {
        if (e.key === 'Enter') return; // handled on keydown below
        ensureNextRow($(this).closest('.product-tbody'));
    });

    // Enter in Qty — jump straight to the next row's Brand field (create the row if needed)
    $(document).on('keydown', '[name="assignQty[]"]', function (e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        const $row = $(this).closest('.product-tbody');
        const $nextRow = ensureNextRow($row);
        const nextId = $nextRow.find('.row_id').val();
        openSelect2Safe(`#brand_select${nextId}`);
    });

    function gatherFormData() {
        const rows = document.querySelectorAll('#dynamicForm .product-tbody');
        const products = [];

        rows.forEach(row => {
            const productId = row.querySelector(`[name="productName[]"]`).value;
            const inhouseId = row.querySelector(`[name="inhouse_product_id[]"]`).value;
            const isInhouse = row.classList.contains('inhouse-row');

            if (!productId && !inhouseId) return;

            const pr_ids_raw = row.querySelector(`[name="purchase_request_ids[]"]`).value;
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
                    <td>${item.brand_name || 'N/A'}</td>
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
