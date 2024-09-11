@extends('layouts.dashboard')

@section('title', 'Store Stock Transfer')

@section('content')
    <style>
        /* Styling as needed */
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
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="text-dark bold">Store Stock Transfer</h2>
            <div class="text-right">
                <a href="{{ url('store/create/stockTransfer') }}" class="btn btn-secondary btn-sm">Create Stock Transfer</a>
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
                    <div class="form-group" style="margin-top: 1.85rem !important;">
                        <button type="button" class="btn btn-success btn-md mx-1" id="filterBilling">Find</button>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-around">
                    <form class="d-flex align-items-center justify-content-between">
                        <div class="form-group d-flex align-items-center justify-content-center mx-3">
                            <label for="searchBillingNumber" class="mt-2">Search: </label> &nbsp;&nbsp;
                            <input type="text" class="form-control" id="searchBillingNumber" placeholder="Search Billing No.">
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="form-row btn-group d-flex align-items-center justify-content-between" role="group" aria-label="Show Entries and Export">  
            <div class="d-flex align-items-center justify-content-center">
                <div class="show-entries form-group d-flex align-items-baseline justify-content-between">
                    <label for="showbillingEntries" class="d-inline-block">Show Entries: &nbsp;</label>
                    <select data-enable-search="true" class="form-control form-control-md mt-1" style="width: auto;" id="showbillingEntries">
                        <option selected>10</option>
                        <option>25</option>
                        <option>50</option>
                        <option>100</option>
                    </select>
                </div>
            </div> 
            <div class="grandTotalAmount text-right mt-3">
                <strong>Total Amount: <span id="total">0.00/-</span></strong>
            </div>
        </div>
        
        <div class="table-responsive border mt-3 mb-5">
            <table id="purchase-entry-table" class="table p-2 text-center">
                <thead>
                    <tr>
                        <th style="padding: 0 0.5rem;">Sno.</th>
                        <th style="padding: 0 0.5rem;">Stock From</th>
                        <th style="padding: 0 0.5rem;">Stock To</th>
                        <th style="padding: 0 0.5rem;">Product</th>
                        <th style="padding: 0 0.5rem;">Quantity</th>
                        <th style="padding: 0 0.5rem;">Unit Value</th>
                        <th style="padding: 0 0.5rem;">Total Amount</th>
                    </tr>
                </thead>
                <tbody id="billing"></tbody>
            </table>   
            <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No Bill Entry found</div>
        </div>

        <div class="container mt-3">
            <div class="row justify-content-end">
                <div class="col-auto">
                    <nav aria-label="Page navigation">
                        <ul class="pagination pagination-sm"></ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    
    
    <div class="loader" id="loader"></div>


    <script>
        $(document).ready(function () {
    let products = {};
    let stores = {};

    function fetchProducts() {
        return $.ajax({
            url: 'http://localhost:8000/api/products',
            type: 'GET',
            success: function (response) {
                products = response.data.reduce((acc, item) => {
                    acc[item.id] = item.product_name;
                    return acc;
                }, {});
            }
        });
    }

    function fetchStores() {
        return $.ajax({
            url: 'http://localhost:8000/api/stores',
            type: 'GET',
            success: function (response) {
                stores = response.data.reduce((acc, item) => {
                    acc[item.id] = item.name;
                    return acc;
                }, {});
            }
        });
    }

    function fetchStockTransferData(page = 1, filters = {}) {
        $('#loader').show();
        $.when(fetchProducts(), fetchStores()).done(function () {
            $.ajax({
                url: 'http://localhost:8000/api/transfer/store/list',
                type: 'GET',
                data: { page: page, ...filters },
                success: function (response) {
                    $('#loader').hide();
                    
                    $('#billing').empty();
                    
                    if (response.data && response.data.length > 0) {
                        let tableContent = '';
                        let totalAmount = 0;

                        response.data.forEach((item, index) => {
                            // Calculate total price for the row
                            const rowTotal = item.unit_value * item.qty;
                            totalAmount += rowTotal;

                            tableContent += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${stores[item.stock_from] || item.stock_from}</td>
                                    <td>${stores[item.stock_to] || item.stock_to}</td>
                                    <td>${products[item.product] || item.product}</td>
                                    <td>${item.qty}</td>
                                    <td>${item.unit_value}</td>
                                    <td>${rowTotal.toFixed(2)}</td>
                                </tr>
                            `;
                        });

                        $('#billing').html(tableContent);
                        $('#total').text(totalAmount.toFixed(2) + '/-');
                    } else {
                        $('#billing').html('<tr><td colspan="7">No data found</td></tr>');
                        $('#total').text('0.00/-');
                    }
                },
                error: function() {
                    $('#loader').hide();
                    $('#billing').html('<tr><td colspan="7">Error loading data</td></tr>');
                    $('#total').text('0.00/-');
                }
            });
        });
    }

    fetchStockTransferData();

    $('#filterBilling').on('click', function () {
        const startDate = $('#start_date_input').val();
        const endDate = $('#end_date_input').val();
        fetchStockTransferData(1, { start_date: startDate, end_date: endDate });
    });

    $('#searchBillingNumber').on('keyup', function () {
        const searchQuery = $(this).val();
        fetchStockTransferData(1, { search: searchQuery });
    });

    $('#showbillingEntries').on('change', function () {
        fetchStockTransferData(1, { per_page: $(this).val() });
    });

    $(document).on('click', '.pagination a', function (e) {
        e.preventDefault();
        const page = $(this).attr('href').split('page=')[1];
        fetchStockTransferData(page);
    });

    $('#printButton').on('click', function () {
        window.print();
    });
});


    </script>
@endsection
