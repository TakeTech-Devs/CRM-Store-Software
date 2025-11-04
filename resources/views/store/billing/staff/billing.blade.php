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
        <h2 class="text-dark bold ">Staff Billing Page</h1>
            <div class="text-right">
                <a href="{{ url('store/staff/create/billing') }}" class="btn btn-secondary btn-sm">Create New
                    Billing</a>
            </div>
    </div>
    <div class="form-row d-flex align-items-center justify-content-between my-3">
        <div class="col-md-12 form-group d-flex align-items-end justify-content-between">
            <div class="form-group d-flex align-items-end justify-content-around">
                <div class="form-group mx-1">
                    <label for="start_date_input">Start Date</label>
                    <input type="date" class="form-control" id="start_date_input" name="start_date_input">
                </div>
                <div class="form-group mx-1">

                    <label for="end_date_input">End Date</label>
                    <input type="date" class="form-control" id="end_date_input" name="end_date_input">
                </div>
                <!-- <div class="form-group"> -->
                <!-- <button type="button" class="btn btn-success btn-md mx-1 storeFilterBtn">Find</button> -->
                <div class="form-group" style="margin-top: 1.85rem !important;">
                    <button type="button" class="btn btn-success btn-md mx-1 filterBtn" id="filterBilling">Find</button>
                </div>
            </div>
            <div class="d-flex align-items-center justify-content-around">
                <form class="d-flex align-items-center justify-content-between">
                    <div class="form-group d-flex align-items-center justify-content-center mx-3">
                        <label for="search" class="mt-2">Search: </label> &nbsp;&nbsp;
                        <!-- <input type="text" class="form-control" id="search" placeholder="Search Bill No."> -->
                        <input type="text" class="form-control" id="searchBillingNumber"
                            placeholder="Search Billing No.">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="form-row btn-group d-flex align-items-center justify-content-between" role="group"
        aria-label="Show Entries and Export">
        <div class="d-flex align-items-center justify-content-center">
            <div class="show-entries form-group d-flex align-items-baseline justify-content-between">
                <label for="showEntries" class="d-inline-block">Show Entries: &nbsp;</label>
                <select data-enable-search="true" class="form-control form-control-md mt-1" style="width: auto;"
                    id="showbillingEntries" onchange="updatePagination()">
                    <option selected>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
            </div>
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
        <div class="grandTotalAmount text-right mt-3">
            <strong>Total Amount: 0.00/-</strong>
            <div class="totalAmount">
                <strong>Total Amount: <span id="total">0/- </span></strong>
            </div>
        </div>

        <div class="table-responsive border mt-3 mb-5">
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
        <div class="container mt-3">
            <div class="row justify-content-end">
                <div class="col-auto">
                    <nav aria-label="...">
                        <ul class="pagination pagination-sm">
                        </ul>
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
                        <table class="border p-2 my-2">
                            <h1 class="text-center fw-bold"
                                style="border-bottom:3px solid; border-top:3px solid; padding:5px 0px !important;">RIGHT
                                AID</h1>
                            <div class="col-md-12">
                                <div class="details d-flex align-items-start justify-content-between"
                                    style="margin-bottom: 15px !important; margin-top: 15px !important;">
                                    <div class="left">
                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">
                                            Invoice No: <span class="invoiceNo"
                                                style="font-size: 14px !important; font-weight:400;"></span> </p>
                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">Date:
                                            <span class="billingDate"
                                                style="font-size: 14px !important; font-weight:400;"></span>
                                        </p>
                                        <p
                                            style="width: 150%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                            GSTIN: <span class="gstin"
                                                style="font-size: 14px !important; font-weight:400;">GST123456</span>
                                        </p>
                                        <p
                                            style="width: 200%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                            Staff: <span class="customerName"
                                                style="font-size: 14px !important; font-weight:400;"></span></p>
                                        <p
                                            style="width: 200%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                            Dr Name: <span class="drName"
                                                style="font-size: 14px !important; font-weight:400;"></span></p>
                                    </div>
                                    <div class="right">
                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">DL.
                                            No. : <span class="dlNumber"
                                                style="font-size: 14px !important; font-weight:400;">HL-1046-S</span>
                                        </p>
                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">
                                            Helpline : <span class="helplineNumber"
                                                style="font-size: 14px !important; font-weight:400;">8100968101</span>
                                        </p>
                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">
                                            Address : <span class="storeAddress"
                                                style="font-size: 12px !important; font-weight:400;">Loading...</span>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="product-details text-center m-0 col-md-12">

                                <table class="w-100" id="invoice_table">
                                    <thead
                                        style="border-top:3px solid; text-align: center; border-bottom:3px solid; padding-top: 10px !important;">
                                        <tr>
                                            <th>SNo.</th>
                                            <th>Desc</th>
                                            <th>Qty</th>
                                            <th>Pack</th>
                                            <th>MRP</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>

                                    <tbody
                                        style="border-bottom:3px solid; text-align: center; padding: 15px 0px !important;">

                                    </tbody>
                                </table>
                                <div class="col-md-12 my-3" style="text-align:right !important">
                                    <th>Grand Total: <span class="grandTotal"></span>/-</th>
                                </div>

                                <div class="address text-center" style="font-size: 12px !important; margin-top:15px">
                                    <span>Address : 15/1A Mohini Monen Road, Bhawanipur (Near Jadubabu bazar) Kolkata
                                        700020</span><br>
                                    <span>Reg Address : 211. Rain Ram Monan Rov Road Shop No :10, Block-1 Ground Floor,
                                        "Merlin Grove Behala
                                        Kolkata-700008</span>
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
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>



<script>
    $(document).ready(function() {
        api_for_bill();

        // SEARCH FUNCATIONALITY 
        $('#search').on('input', function() {
            var searchText = $(this).val().toLowerCase();
            var found = false;
            $('.bill-row').each(function() {
                var brandName = $(this).find('td:eq(1)').text().toLowerCase();
                if (brandName.includes(searchText)) {
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            if (found) {
                $('#noBrandFoundMessage').hide();
            } else {
                $('#noBrandFoundMessage').show();
            }
        });

        // DATE FILTER 
        $(document).on('click', '.storeFilterBtn', function() {
            let startDate = $('#start_date_input').val();
            let endDate = $('#end_date_input').val();
            $.ajax({
                url: '/api/staff/bill/filter/',
                method: 'GET',
                data: {
                    start_date_input: startDate,
                    end_date_input: endDate
                },
                success: function(response) {
                    if (response.status === 200) {
                        displayFilteredData(response.data);
                    } else {
                        console.error('Failed to fetch data:', response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        });

        let productList = {};
        let doctorList = {};

        // Fetch products list
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
            url: `/api/staff/bill/${billId}`,
            method: 'GET',
            success: function(response) {
                if (response.status === 200) {
                    let data = response.data;
                    let bill = data.bill;
                    let items = data.items;

                    var grandTotalElement = document.querySelector('.grandTotal');
                    var invoiceElement = document.querySelector('.invoiceNo');
                    var billingDateElement = document.querySelector('.billingDate');
                    var customernameElement = document.querySelector('.customerName');
                    var drNameElement = document.querySelector('.drName');

                    if (grandTotalElement) {
                        grandTotalElement.textContent = bill.total_amt;
                    }
                    if (invoiceElement) {
                        invoiceElement.textContent = bill.invoiceNo;
                    }
                    if (billingDateElement) {
                        billingDateElement.textContent = bill.billing_date;
                    }
                    if (customernameElement) {
                        customernameElement.textContent = bill.staff_name;
                    }
                    if (drNameElement) {
                        drNameElement.textContent = doctorList[bill.doctor_name] || 'N/A';
                    }

                    $('#invoice_table tbody').empty();
                    items.forEach((item, index) => {
                        $('#invoice_table tbody').append(`
                            <tr>
                                <td>${index + 1}</td>
                                <td>${productList[item.productId] || 'N/A'}</td>
                                <td>${item.qty || '0'}</td>
                                <td>${item.pack || 'N/A'}</td>
                                <td>${item.unitValue || '0.00'}/-</td>
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

        function api_for_bill() {
            $('#loader').show();
            ajaxGetData(`/api/staff/billing/list`, (response) => {
                $('#loader').hide();
                bill_list(response.data);
            });
        }

        function bill_list(response) {
            $('#purchase-entry-table tbody').empty();
            let grandTotal = 0;
            if (Array.isArray(response) > 0) {
                response.reverse();
                $.each(response, function(index, brand) {
                    let totalAmount = parseFloat(brand?.total_amt) || 0;
                    grandTotal += totalAmount;
                    let formattedStatus = (brand.status == 1) ? 'Active' : 'Deactive';
                    $('#purchase-entry-table tbody').append(`
                        <tr class="bill-row" style="cursor:pointer;" data-id="${brand?.id}">
                            <td scope="row"> ${index+1} </td>
                            <td> ${brand?.invoiceNo} </td>
                            <td> ${brand?.staff_name} </td>
                            <td> ${brand?.billing_date} </td>
                            <td> ${brand?.paymentType} </td>
                            <td> ${totalAmount.toFixed(2)} </td>
                            <td>
                                <button class="bg-info px-2 py-1 viewBill text-white" data-toggle="modal" data-target="#printModal" data-store-id="${brand.id}">View</button>
                            </td>
                        </tr>
                    `);
                });
                $('.grandTotalAmount').html(`<strong>Total Amount: ${grandTotal.toFixed(2)}/-</strong>`);
            } else {
                $('#purchase-entry-table tbody').append(`
                    <tr>
                        <td class="text-center" colspan="4">No Brand Found</td>
                    </tr>
                `);
                $('.grandTotalAmount').html(`<strong>Total Amount: 0.00/-</strong>`);
            }
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
                        <td>${customerBill.staff_name}</td>
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
                <p><strong>Staff Name:</strong> ${bill.staff_name}</p>
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

            $('#printModal').modal('show');
        }

        document.getElementById("printButton").addEventListener("click", function() {
            printModalContent();
        });


    });


        
</script>
@endsection