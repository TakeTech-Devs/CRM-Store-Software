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
            #printButton{
                display: none;
            }
            @page{
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
        #printButton{
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="text-dark bold ">Customer Billing Page</h1>
            <div class="text-right">
                <a href="{{ url('store/customer/create/billing') }}" class="btn btn-secondary btn-sm">Create New Billing</a>
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
                    <div class="form-group">
                        <button type="button" class="btn btn-success btn-md mx-1 storeFilterBtn">Find</button>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-around">
                    <form class="d-flex align-items-center justify-content-between">
                        <div class="form-group d-flex align-items-center justify-content-center mx-3">
                            <label for="search"class="mt-2">Search: </label> &nbsp;&nbsp;
                            <input type="text" class="form-control" id="search" placeholder="Search Bill No.">
                        </div>  
                    </form>   
                </div>
            </div>
        </div>
        <div class="form-row btn-group d-flex align-items-center justify-content-between" role="group" aria-label="Show Entries and Export">  
            <div class="d-flex align-items-center justify-content-center">
                <div class="show-entries form-group d-flex align-items-baseline justify-content-between">
                    <label for="showEntries" class="d-inline-block">Show Entries: &nbsp;</label>
                    <select data-enable-search="true"class="form-control form-control-md mt-1" style="width: auto;" id="showEntries" onchange="updatePagination()">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                </div>
                <div class="download-buttons" style="margin-left:25px !important;">
                    <div class="download-options d-flex align-items-baseline justify-content-between">
                        
                        
                        <p>Export as : </p>&nbsp;&nbsp;&nbsp;
                        <button type="button" class="btn mx-1 btn-md btn-success" id="exportReport" >
                            <i class="fas fa-file-excel"></i>
                        </button>
                        <button type="button" class="btn mx-1 btn-md btn-primary">
                            <i class="fas fa-file-word"></i>
                        </button>
                        <button type="button" class="btn mx-1 btn-md btn-danger">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                </div>
            </div> 
            <div class="grandTotalAmount text-right mt-3">
                <strong>Total Amount: 0.00/-</strong>
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
                <tbody>
                   
                    
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
    <div class="modal fade" id="viewBillModal" tabindex="-1" role="dialog" aria-labelledby="viewBillModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="border: none;">
                <div class="modal-body" style="padding: 25px; width:100%;">
                    <div style="padding: 1rem; display: flex; align-items:center; justify-content:space-between; margin-bottom:0px;">
                        <h2 style="color: black; display:inline-block;">Invoice - <span id="printPurchaseBillNo"></span></h2>

                        <div style="float:right; text-align:right;">
                            <img class="logo" src="{{asset('./assets/img/logo.png')}}" alt="">
                        </div>
                    </div>

                    <div style="margin-top: 10px; position:relative; color:dark;">
                        <table style="width: 35%; border-collapse: collapse;">
                            <tr>
                                <th style="padding: 0 0.5rem;">SKU Date - </th>
                                <td id="printSkuDate" style="padding: 0 0.5rem;"></td>
                            </tr>
                            <tr>
                                <th style="padding: 0 0.5rem;">SKU ID - </th>
                                <td id="printSkuId" style="padding: 0 0.5rem;"></td>
                            </tr>
                            <tr>
                                <th style="padding: 0 0.5rem;">Supplier Name - </th>
                                <td id="printSupplierName" style="padding: 0 0.5rem;"></td>
                            </tr>
                        </table>
                        <table style="width: 100%; border-collapse: none; border:none; margin-top: 15px; text-align:center;">

                            <tr class="border:none">
                                <th style="padding: 0 0.5rem;">Brand Name</th>
                                <th style="padding: 0 0.5rem;">Category</th>
                                <th style="padding: 0 0.5rem;">Sub Category</th>
                                <th style="padding: 0 0.5rem;">Product</th>
                                <th style="padding: 0 0.5rem;">Pack</th>
                                <th style="padding: 0 0.5rem;">MRP</th>
                                <th style="padding: 0 0.5rem;">Quantity</th>
                                <th style="padding: 0 0.5rem;">Expiry Date</th>
                                <th style="padding: 0 0.5rem;">Total Amount</th>

                            </tr>
                            
                            <tbody id="purchaseEntryPrintTable" class="border:none;">
                                
                                <tr class="border-collapse:none; border:none;">
                                    <div class="loader"></div>
                                    <td id="brandName" style="padding: 0 0.5rem;"></td>
                                    <td id="categoryName" style="padding: 0 0.5rem;"></td>
                                    <td id="subCategoryName" style="padding: 0 0.5rem;"></td>
                                    <td id="productName" style="padding: 0 0.5rem;"></td>
                                    <td id="packSize" style="padding: 0 0.5rem;"></td>
                                    <td id="printMRP" style="padding: 0 0.5rem;"></td>
                                    <td id="printQuantity" style="padding: 0 0.5rem;"></td>
                                    <td id="exDate" style="padding: 0 0.5rem;"></td>
                                    <td id="printTotal" style="padding: 0 0.5rem;"></td>
                                </tr>
                            </tbody>

                        </table>
                        <div class="col-md-12 mb-5">

                            <span class="grandTotal" style="color:black; font-size:18px; float:right; margin-top: 15px; font-weight: 600;"></span>
                        </div>
                        <div style="text-align:center; margin-top: 50px; width: 100%;">
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
                    url: '/api/customer/bill/filter/',
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
            
        });


        function api_for_bill() {
            $('#loader').show();
            ajaxGetData(`/api/customer/billing/list`, (response) => {
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
                            <td> ${brand?.customer_name} </td>
                            <td> ${brand?.billing_date} </td>
                            <td> ${brand?.paymentType} </td>
                            <td> ${totalAmount.toFixed(2)} </td>
                            <td>
                                <button class="bg-info px-2 py-1 viewBill" data-toggle="modal" data-target="#viewBillModal" data-store-id=${brand.id}>&#x1F441;</button>
                                
                                <i class="fa fa-download bg-warning text-light px-2 py-2">

                            </td>
                        </tr>
                    `);
                });
                $('.grandTotalAmount').html(`<strong>Total Amount: ${grandTotal.toFixed(2)}/-</strong>`);
            } else {
                $('##purchase-entry-table tbody').append(`
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
                        <td>${customerBill.customer_name}</td>
                        <td>${customerBill.billing_date}</td>
                        <td>${customerBill.paymentType}</td>
                        <td>${customerBill.total_amt}</td>
                        <td>
                            <button class="bg-info px-2 py-1 viewBill" data-toggle="modal" data-target="#viewBillModal" data-store-id="${customerBill.id}">&#x1F441;</button>
                                
                            <i class="fa fa-download bg-warning text-light px-2 py-2">
                            
                        </td>
                    </tr>
                    `);
                });
            } else {
                tableBody.append('<tr><td colspan="6">No records found</td></tr>');
            }
        }
    </script>

    


@endsection
                    