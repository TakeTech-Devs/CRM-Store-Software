@extends('layouts.dashboard')

@section('title', 'Customer Billing')

@section('content')
<style>
    .pagination {
        margin-top: 10px;
    }

    .table tbody+tbody {
        border-top: none !important;
    }

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

    .loader {
        border: 10px solid #f3f3f3;
        border-top: 10px solid #A54217;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        animation: spin 2s linear infinite;
        margin-top: 10%;
        margin-left: 50%;
        display: none;
        bottom: 25px;
        position: absolute;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between">
        <h2 class="text-dark bold ">Customer Billing Page</h1>
            <div class="text-right">
                <a href="{{ url('store/customer/create/billing') }}" class="btn btn-secondary btn-sm">Create New
                    Billing</a>
            </div>
    </div>
    <div class="form-row d-flex align-items-center justify-content-between">
        <div class="col-md-12 form-group d-flex align-items-start justify-content-between mb-0">
            <div class="form-group d-flex align-items-start justify-content-around">
                <div class="form-group mx-1">
                    <label for="start_date_input">Start Date</label>
                    <input type="date" class="form-control" id="start_date_input" name="start_date_input">
                </div>
                <div class="form-group mx-1">

                    <label for="end_date_input">End Date</label>
                    <input type="date" class="form-control" id="end_date_input" name="end_date_input">
                </div>
                <div class="form-group" style="margin-top: 2rem !important;">
                    <button type="button" class="btn btn-success btn-md mx-1 filterBtn" id="filterBilling">Find</button>
                </div>
            </div>
            <div class="d-flex align-items-start justify-content-around">
                <div class="form-group mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="searchBillingNumber" placeholder="Search Billing No.">
                </div>
            </div>
        </div>
    </div>
    <div class="form-row btn-group d-flex align-items-center justify-content-between" role="group"
        aria-label="Show Entries and Export">
        <div class="d-flex align-items-center justify-content-center">
            <!-- <div class="show-entries">
                    <label for="showEntries" class="d-inline-block">Show: &nbsp;</label>
                    <select data-enable-search="true"class="form-control form-control-md mt-1" style="width: auto;" id="showbillingEntries" onchange="updatePagination()">
                        <option selected >10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                </div> -->
            {{-- <div class="download-buttons" style="margin-left:25px !important;">
                <div class="download-options d-flex align-items-baseline justify-content-between">


                    <p>Export as : </p>&nbsp;&nbsp;&nbsp;
                    <button type="button" class="btn mx-1 btn-md btn-success" id="exportReport">
                        <i class="fas fa-file-excel"></i>
                    </button>
                    <button type="button" class="btn mx-1 btn-md btn-primary">
                        <i class="fas fa-file-word"></i>
                    </button>
                    <button type="button" class="btn mx-1 btn-md btn-danger">
                        <i class="fas fa-file-pdf"></i>
                    </button>
                </div>
            </div> --}}
        </div>
        <div class="grandTotalAmount text-right">
            <strong>Total Amount: 0.00/-</strong>
            <div class="totalAmount">
                <strong>Total Amount: <span id="total">0/- </span></strong>
            </div>
        </div>

        <div class="table-responsive border mt-3">
            <table id="purchase-entry-table" class="table p-2 text-center">
                <thead>
                    <tr>
                        <th class="text-dark">#</th>
                        <th class="text-dark">Customer Bill No</th>
                        <th class="text-dark">Customer Name</th>
                        <th class="text-dark">Bill Date</th>
                        <th class="text-dark">Payment Type</th>
                        <th class="text-dark">Total Amount</th>
                        <th class="text-dark">Actions</th>
                    </tr>
                </thead>

                <tbody id="billing">


                </tbody>
            </table>
            <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No Bill Entry found</div>

        </div>
        <div class="container mt-2 mb-3">
            <div class="row justify-content-between align-items-center">
                <div class="col-auto">
                    <small class="text-muted" id="billPaginationInfo"></small>
                </div>
                <div class="col-auto">
                    <nav aria-label="Bill pagination">
                        <ul class="pagination pagination-sm mb-0"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <!-- PRINT MODEL  -->
    <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-md" role="document">
            <div class="modal-content" style="border: none;">
                <div class="modal-body" style="padding: 10px; width:100%;">
                    <div id="printArea">
                        <table class="border p-2">
                            <h1 class="text-center fw-bold"
                                style="border-bottom:3px solid; border-top:3px solid; padding:5px 0px !important;">RIGHT
                                AID</h1>
                            <div class="col-md-12">
                                <div class="details d-flex align-items-start justify-content-between"
                                    style="margin-bottom: 15px !important; margin-top: 15px !important;">
                                    <div class="left">
                                        <p style="font-size: 16px !important; font-weight:800; line-height: 5px;">
                                            Invoice No: <span class="invoiceNo"
                                                style="font-size: 16px !important; font-weight:600;"></span> </p>
                                        <p style="font-size: 16px !important; font-weight:800; line-height: 5px;">Date:
                                            <span class="billingDate"
                                                style="font-size: 16px !important; font-weight:600;"></span>
                                        </p>
                                        <p
                                            style="width: 150%; font-size: 16px !important; font-weight:800; line-height: 5px;">
                                            GSTIN: <span class="gstin"
                                                style="font-size: 16px !important; font-weight:600;">GST123456</span>
                                        </p>
                                        <p
                                            style="width: 200%; font-size: 16px !important; font-weight:800; line-height: 5px;">
                                            Customer: <span class="customerName"
                                                style="font-size: 16px !important; font-weight:600;"></span></p>
                                        <p
                                            style="width: 200%; font-size: 16px !important; font-weight:800; line-height: 5px;">
                                            Dr Name: <span class="drName"
                                                style="font-size: 16px !important; font-weight:600;"></span></p>
                                    </div>
                                    <div class="right">
                                        <p style="font-size: 16px !important; font-weight:800; line-height: 5px;">DL.
                                            No. : <span class="dlNumber"
                                                style="font-size: 16px !important; font-weight:600;">HL-1046-S</span>
                                        </p>
                                        <p style="font-size: 16px !important; font-weight:800; line-height: 5px;">
                                            Helpline : <span class="helplineNumber"
                                                style="font-size: 16px !important; font-weight:600;">8100968101</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="product-details text-center m-0 col-md-12">

                                <table class="w-100" id="invoice_table">
                                    <thead
                                        style="border-top:3px solid; text-align: center; border-bottom:3px solid; padding-top: 10px !important; font-size: 16px; font-weight: 800;">
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

                                    <tbody
                                        style="border-bottom:3px solid; text-align: center; padding: 15px 0px !important; font-size: 16px; font-weight: 600;">

                                    </tbody>
                                </table>
                                <div class="col-md-12 my-3" style="text-align:right !important">
                                    <table class="w-100 table table-bordered mt-3" style="font-size: 16px; font-weight: 600;">
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
                                    <p class="creditBreakdown" style="display:none; font-size:14px; font-weight:700;">
                                        Credit Applied: <span class="creditApplied"></span>/- &nbsp;|&nbsp; Amount Paid: <span class="amountPaid"></span>/-
                                    </p>
                                </div>

                                <div class="address text-center" style="font-size: 14px !important; font-weight: 600; margin-top:15px; color: #000000 !important;">
                                    <span>Address : <Span class="storeAddress"></Span></span><br>
                                    <span>Reg Address : 211. Rain Ram Monan Rov Road Shop No :10, Block-1 Ground Floor,
                                        "Merlin Grove Behala
                                        Kolkata-700008</span>
                                </div>
                                <div class="note text-center" style="font-size: 14px !important; font-weight: 600;">
                                    <p>Medicine once sold would not be returned or exchanged</p>
                                    <span>******** Thank You ********</span>
                                </div>
                            </div>

                        </table>
                    </div>
                    <div style="text-align:center; width: 100%; margin-bottom: 25px !important;">
                        <button class="btn btn-sm shadow btn-primary" id="printButton">Print</button>
                        <button class="btn btn-sm shadow btn-info" id="printButtonTVS">Print TVS RP 45</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT BILL MODAL (same-day, decrease-only) -->
    <div class="modal fade" id="editBillModal" tabindex="-1" role="dialog" aria-labelledby="editBillModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBillModalLabel">Edit Bill (Same Day Only)</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Quantity can only be reduced or removed. Inhouse products cannot be edited.</p>
                    <div class="table-responsive">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Pack</th>
                                    <th>Current Qty</th>
                                    <th>New Qty</th>
                                </tr>
                            </thead>
                            <tbody id="editBillItemsBody"></tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitEditBill">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

</div>


<script>
    // Keep end date always >= start date
    $('#start_date_input').on('change', function () {
        $('#end_date_input').attr('min', $(this).val());
        if ($('#end_date_input').val() && $('#end_date_input').val() < $(this).val()) {
            $('#end_date_input').val($(this).val());
        }
    });

    $(document).on('click', '#filterBilling', function () {
        let startDate = $('#start_date_input').val();
        let endDate = $('#end_date_input').val();

        if (!startDate || !endDate) {
            alert("Please select both start and end dates.");
            return;
        }

        let filterButton = $(this);
        filterButton.prop('disabled', true).text('Filtering...');

        $.ajax({
            type: 'GET',
            url: `/api/customer/billing/list?start_date=${startDate}&end_date=${endDate}`,
            success: function (response) {
                if (response.status === 200) {
                    bill_list(response.data);
                } else {
                    showNoRecords();
                }
            },
            error: function (xhr) {
                if (xhr.status === 404) {
                    showNoRecords();
                } else {
                    alert('Failed to fetch data. Please try again.');
                }
            },
            complete: function () {
                filterButton.prop('disabled', false).text('Find');
            }
        });
    });

    let allBills = [];
    const PAGE_SIZE = 10;
    let currentPage = 1;

    function getTodayStr() {
        const today = new Date();
        const day = String(today.getDate()).padStart(2, '0');
        const month = String(today.getMonth() + 1).padStart(2, '0');
        const year = today.getFullYear();
        return `${year}-${month}-${day}`;
    }

    function showNoRecords() {
        $('#purchase-entry-table tbody').html('<tr><td colspan="7" class="text-center">No records found</td></tr>');
        $('.grandTotalAmount').html('<strong>Total Amount: 0.00/-</strong>');
        $('.pagination').empty();
        $('#billPaginationInfo').text('');
    }

    function renderPage(page) {
        currentPage = page;
        const start = (page - 1) * PAGE_SIZE;
        const pageItems = allBills.slice(start, start + PAGE_SIZE);
        const tbody = $('#purchase-entry-table tbody').empty();

        pageItems.forEach((brand, i) => {
            let totalAmount = parseFloat(brand?.total_amt) || 0;
            const isToday = brand?.billing_date === getTodayStr();
            // MySQL EXISTS(...) comes back as the string "0"/"1" — !! alone would
            // treat "0" as truthy, so compare against the string explicitly.
            const hasReturn = String(brand?.has_return) === '1';
            const canEdit = isToday && !hasReturn;
            tbody.append(`
                <tr class="bill-row" style="cursor:pointer;" data-id="${brand?.id}">
                    <td>${start + i + 1}</td>
                    <td>${brand?.invoiceNo}</td>
                    <td>${brand?.customer_name}</td>
                    <td>${brand?.billing_date}</td>
                    <td>${brand?.paymentType}</td>
                    <td>${totalAmount.toFixed(2)}</td>
                    <td>
                        <button class="bg-info px-2 py-1 viewBill text-white" data-toggle="modal" data-target="#printModal" data-store-id="${brand.id}">View</button>
                        ${canEdit ? `<button class="bg-warning px-2 py-1 editBill text-white" data-bill-id="${brand.id}">Edit</button>` : ''}
                    </td>
                </tr>
            `);
        });

        renderPagination();
        const showing = Math.min(start + PAGE_SIZE, allBills.length);
        $('#billPaginationInfo').text(`Showing ${start + 1} to ${showing} of ${allBills.length} records`);
    }

    function renderPagination() {
        const totalPages = Math.ceil(allBills.length / PAGE_SIZE);
        const $ul = $('.pagination').empty();
        if (totalPages <= 1) return;

        $ul.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a></li>`);

        for (let p = 1; p <= totalPages; p++) {
            $ul.append(`<li class="page-item ${p === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${p}">${p}</a></li>`);
        }

        $ul.append(`<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a></li>`);
    }

    $(document).on('click', '.pagination .page-link', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page >= 1 && page <= Math.ceil(allBills.length / PAGE_SIZE)) {
            renderPage(page);
        }
    });

    function bill_list(response) {
        if (Array.isArray(response) && response.length > 0) {
            allBills = response.slice().reverse();
            let grandTotal = allBills.reduce((s, b) => s + (parseFloat(b.total_amt) || 0), 0);
            $('.grandTotalAmount').html(`<strong>Total Amount: ${grandTotal.toFixed(2)}/-</strong>`);
            renderPage(1);
        } else {
            allBills = [];
            showNoRecords();
        }
    }



    $(document).ready(function() {
        api_for_bill();

        // SEARCH FUNCTIONALITY
        $('#searchBillingNumber').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            var found = false;
            $('.bill-row').each(function() {
                var invoiceNo = $(this).find('td:eq(1)').text().toLowerCase();
                if (invoiceNo.includes(searchText)) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            $('#noBrandFoundMessage').toggle(!found);
        });

        let productList = {};
        let doctorList = {};

        function fetchProducts() {
            return $.ajax({
                url: '/products', // Adjust the endpoint as necessary
                method: 'GET'
            }).done(function(response) {
                if (response.status === 200) {
                    productList = response.data.reduce((acc, product) => {
                        acc[product.id] = product.product_name;
                        return acc;
                    }, {});
                } else {
                    console.error('Failed to fetch products:', response);
                }
            }).fail(function(xhr, status, error) {
                console.error('Error fetching products:', error);
            });
        }

        function fetchDoctors() {
            return $.ajax({
                url: '/doctors', 
                method: 'GET'
            }).done(function(response) {
                if (response.status === 200) {
                    doctorList = response.data.reduce((acc, doctor) => {
                        acc[doctor.id] = doctor.name;
                        return acc;
                    }, {});
                } else {
                    console.error('Failed to fetch doctors:', response);
                }
            }).fail(function(xhr, status, error) {
                console.error('Error fetching doctors:', error);
            });
        }

        function fetchStoreInfo() {
            return $.ajax({
                url: '/api/store/info',
                method: 'GET'
            }).done(function(response) {
                if (response.status === 200) {
                    // Update store information in the print modal
                    $('.dlNumber').text(response.data.dl_number);
                    $('.helplineNumber').text(response.data.helpline_number);
                    $('.storeAddress').text(response.data.store_address);
                } else {
                    console.error('Failed to fetch store info:', response);
                }
            }).fail(function(xhr, status, error) {
                console.error('Error fetching store info:', error);
            });
        }

        $(document).ready(function() {
            $.when(fetchProducts(), fetchDoctors(), fetchStoreInfo()).done(function() {
                console.log('Data loaded successfully');
            });
        });


        $(document).on('click', '.viewBill', function() {
        let billId = $(this).data('store-id');

        $.ajax({
            url: `/api/customer/bill/${billId}`,
            method: 'GET',
            success: function(response) {
                if (response.status === 200) {
                    console.log(response.data);
                    
                    let data = response.data;
                    let bill = data.bill;
                    let items = data.items;
                    let store = data.store;

                    $('.invoiceNo').text(bill.invoiceNo);
                    $('.billingDate').text(bill.billing_date);
                    $('.customerName').text(bill.customer_name);
                    $('.drName').text(data.doctor_name || 'N/A');
                    $('.grandTotal').text(parseFloat(bill.total_amt).toFixed(2));
                    $('.taxableValue').text((parseFloat(bill.total_amt) - parseFloat(bill.gst || 0)).toFixed(2));
                    $('.totalGST').text(parseFloat(bill.gst || 0).toFixed(2));
                    $('.totalCGST').text(parseFloat(bill.cgst || 0).toFixed(2));
                    $('.totalSGST').text(parseFloat(bill.sgst || 0).toFixed(2));
                    if (bill.credit_applied_amt) {
                        const paid = (parseFloat(bill.total_amt) - parseFloat(bill.credit_applied_amt)).toFixed(2);
                        $('.creditApplied').text(parseFloat(bill.credit_applied_amt).toFixed(2));
                        $('.amountPaid').text(paid);
                        $('.creditBreakdown').show();
                    } else {
                        $('.creditBreakdown').hide();
                    }
                    $('.storeAddress').text(store?.store_address || 'Not Provided');
                    $('.dlNumber').text(store?.dl_number || 'Not Provided');
                    $('.helplineNumber').text(store?.helpline_number || 'Not Provided');

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

                    $('#printModal').modal('show');
                } else {
                    console.error('Failed to fetch bill details:', response);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching bill details:', error);
            }
        });
    });

    // EDIT BILL (same-day, decrease-only)
    let editingBillId = null;

    $(document).on('click', '.editBill', function () {
        if ($(this).is(':disabled')) return;
        editingBillId = $(this).data('bill-id');

        $.ajax({
            url: `/api/customer/bill/${editingBillId}`,
            method: 'GET',
            success: function (response) {
                if (response.status !== 200) return;
                const items = response.data.items || [];
                const tbody = $('#editBillItemsBody').empty();
                items.forEach(item => {
                    const isInhouse = !!item.inhouse_product_id;
                    tbody.append(`
                        <tr data-item-id="${item.id}">
                            <td>${item.product_name || 'N/A'}${isInhouse ? ' <span class="badge badge-secondary">Inhouse</span>' : ''}</td>
                            <td>${item.pack}</td>
                            <td>${item.qty}</td>
                            <td>
                                ${isInhouse
                                    ? `<input type="text" class="form-control" value="${item.qty}" readonly disabled />`
                                    : `<input type="number" class="form-control edit-qty-input" min="0" max="${item.qty}" value="${item.qty}" data-original-qty="${item.qty}" />`
                                }
                            </td>
                        </tr>
                    `);
                });
                $('#editBillModal').modal('show');
            },
            error: function () {
                Swal.fire({ title: 'Error', icon: 'error', text: 'Failed to load bill details.' });
            }
        });
    });

    $(document).on('input', '.edit-qty-input', function () {
        const max = parseFloat($(this).data('original-qty')) || 0;
        const val = parseFloat($(this).val());
        $(this).css('border-color', (isNaN(val) || val < 0 || val > max) ? 'red' : '');
    });

    $(document).on('click', '#submitEditBill', function () {
        let hasError = false;
        const items = [];

        $('#editBillItemsBody tr').each(function () {
            const itemId = $(this).data('item-id');
            const $input = $(this).find('.edit-qty-input');
            if (!$input.length) return; // inhouse row — not editable, skip

            const originalQty = parseFloat($input.data('original-qty'));
            const newQty = parseFloat($input.val());

            if (isNaN(newQty) || newQty < 0 || newQty > originalQty) {
                hasError = true;
                return;
            }
            if (newQty !== originalQty) {
                items.push({ item_id: itemId, new_qty: newQty });
            }
        });

        if (hasError) {
            Swal.fire({ title: 'Validation Error', icon: 'error', text: 'Quantity can only be decreased, not increased.' });
            return;
        }
        if (!items.length) {
            Swal.fire({ title: 'No Changes', icon: 'info', text: 'Change a quantity before saving.' });
            return;
        }

        const csrfToken = $('meta[name="csrf-token"]').attr('content');
        ajaxPostData(`/customer/billing/${editingBillId}/same-day-edit`, { items: items }, csrfToken, () => {
            Swal.fire({ title: 'Updated!', icon: 'success', text: 'Bill updated successfully.', timer: 1500, showConfirmButton: false }).then(() => {
                $('#editBillModal').modal('hide');
                api_for_bill();
            });
        }, (error) => {
            const msg = error?.responseJSON?.message || 'Failed to update bill.';
            Swal.fire({ title: 'Error', icon: 'error', text: msg });
        });
    });

    function api_for_bill() {
        $('#loader').show();
        ajaxGetData(`/api/customer/billing/list`, (response) => {
            console.log(response);
            
            $('#loader').hide();
            bill_list(response.data);
        });
    }


    // DISPLAY DATE FILTER 
    function displayFilteredData(data) {
        let tableBody = $('#purchase-entry-table tbody');
        tableBody.empty();
        if (data.length > 0) {
            data.forEach((customerBill, index) => {
                tableBody.append(`
                <tr>
                <td scope="row">${index + 1}</td>   
                    <td>${customerBill.invoiceNo}</td>   
                    <td>${customerBill.customer_name}</td>
                    <td>${customerBill.billing_date}</td>
                    <td>${customerBill.paymentType}</td>
                    <td>${customerBill.total_amt}</td>
                    <td>
                        <button class="bg-info px-2 py-1 viewBill" data-toggle="modal" data-target="#printModal" data-store-id="${customerBill.id}">&#x1F441;</button>
                        <i class="fa fa-download bg-warning text-light px-2 py-2"></i>
                    </td>
                </tr>
                `);
            });
        } else {
            tableBody.append('<tr><td colspan="6">No records found</td></tr>');
        }
    }

    function populateModal(bill) {
        console.log(bill);
        $('#printModal .modal-body').html(`
            <p><strong>Invoice No:</strong> ${bill.invoiceNo}</p>
            <p><strong>Customer Name:</strong> ${bill.customer_name}</p>
            <p><strong>Billing Date:</strong> ${bill.billing_date}</p>
            <p><strong>Payment Type:</strong> ${bill.paymentType}</p>
            <p><strong>Total Amount:</strong> ${bill.total_amt}</p>
        `);
    }

    function printModalContent() {
        var printContent = document.getElementById("printArea").innerHTML;
        var originalContent = document.body.innerHTML;

        printContent = printContent.replace('<button class="btn btn-sm shadow btn-primary" id="printButton">Print</button>', '');

        var modalBackdrop = document.getElementsByClassName("modal-backdrop")[0];
        if (modalBackdrop) {
            modalBackdrop.remove();
        }

        document.body.innerHTML = printContent;

        window.print();

        document.body.innerHTML = originalContent;

        // Remove stale backdrop that was captured in originalContent
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');

        $('#printModal').modal('show');
    }

    document.getElementById("printButton").addEventListener("click", function() {
        printModalContent();
    });


});
</script>



<script>
    document.getElementById("printButtonTVS").addEventListener("click", function() {
        printModalContentTVS();
    });

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
                #printButton, #printButtonTVS {
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
</script>
@endsection