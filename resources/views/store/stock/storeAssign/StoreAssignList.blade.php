<!-- resources/views/admin/stocks/index.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Store Assigned List')

@section('content')
    <style>
        .pagination {
            margin-top: 10px;
        }

        .modal-content.store-assigned-details {
            width: 85vw;
            margin-left: -37%;
        }
    </style>

    <div class="container-fluid">
        <div class="d-flex align-items-center justify-content-between">
            <h2 class="text-dark bold ">Store Assign List</h1>
                <div class="text-right">
                    <a href="{{ url('admin/store-assign/create') }}" class="btn btn-secondary btn-sm">New Store Assign</a>
                </div>
        </div>
        <div class="form-row d-flex align-items-center justify-content-between my-3">
            <div class="store-filter form-group col-md-3">
                <label for="store">Store select</label>
                <select name="stores" id="stores" class="form-control">
                </select>
            </div>
            <div class="form-group d-flex align-items-end justify-content-around">
                <div class="form-group mx-1">
                    <label for="startDate">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="startDate">
                </div>
                <div class="form-group mx-1">
                    <label for="endDate">End Date</label>
                    <input type="date" class="form-control" id="endDate" name="endDate">
                </div>
                <div class="form-group">
                    <button class="btn btn-success btn-md mx-1">Find</button>
                </div>
            </div>
        </div>
        <div class="form-row btn-group d-flex align-items-center justify-content-between" role="group"
            aria-label="Show Entries and Export">
            <div class="show-entries form-group d-flex align-items-baseline justify-content-between">
                <label for="showEntries" class="d-inline-block">Show Entries: &nbsp;</label>
                <select class="form-control form-control-sm d-inline-block" style="width: auto;" id="showEntries"
                    onchange="updatePagination()">
                    <option>10</option>
                    <option>25</option>
                    <option>50</option>
                    <option>100</option>
                </select>
            </div>
            <div class="download-buttons">
                <div class="download-options d-flex align-items-baseline justify-content-between">
                    <form class="d-flex align-items-center justify-content-between">
                        <div class="form-group d-flex align-items-center justify-content-center mx-3">
                            <label for="search">Search: </label> &nbsp;&nbsp;
                            <input type="text" class="form-control" id="search" placeholder="Search Assign Bill No.">
                        </div>
                    </form>

                    <p>Export as : &nbsp;</p>
                    <button type="button" class="btn mx-1 btn-md btn-success">
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

        <div class="table-responsive border mt-3 mb-5">
            <table id="store_assign_table" class="table p-2 text-center">
                <thead>
                    <tr>
                        <th class="text-dark">ID</th>
                        <th class="text-dark">Assign Bill No</th>
                        <th class="text-dark">Purchase Bill No</th>
                        <th class="text-dark">Store Name</th>
                        <th class="text-dark">Store ID</th>
                        <th class="text-dark">Total Amount</th>
                        <th class="text-dark">Actions</th>
                    </tr>
                </thead>
                <tbody>


                </tbody>
            </table>
            <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No Assign Bill No. found</div>
        </div>
        
    </div>

    <div class="modal fade bd-example-modal-lg" id="addBrandModal" tabindex="-1" role="dialog"
        aria-labelledby="addBrandModalLabel" aria-hidden="true">
        <div class="modal-dialog container modal-lg" role="document">
            <div class="modal-content store-assigned-details">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addBrandModalLabel">Store Assigned Details</h5>
                </div>
                <div class="modal-body">
                    <form id="addBrandForm" class="container">
                        <div class="store-details my-2 mb-3">
                            <table>
                                <tr>
                                    <th>Store Name: &nbsp;</th>
                                    <td>Store123</td>
                                </tr>
                                <tr>
                                    <th>Store ID: &nbsp;</th>
                                    <td>st123</td>
                                </tr>
                                <tr>
                                    <th>Assign Bill No: &nbsp;</th>
                                    <td>#asgn-1234-xyz</td>
                                </tr>
                                <tr>
                                    <th>Purchase Bill No: &nbsp;</th>
                                    <td>#asgn-1234-xyz</td>
                                </tr>
                            </table>
                        </div>

                        <div class="table-responsive border">
                            <table class="table text-center text-dark " id="dynamicForm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Brand Name</th>
                                        <th>Category</th>
                                        <th>Sub-Category</th>
                                        <th>Product Name</th>
                                        <th>Pack</th>
                                        <th>MRP</th>
                                        <th>Qty</th>
                                        <th>Expiry Date</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="formBody">
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            <select class="form-control" name="brand[]">
                                                <option value="brand1">Brand 1</option>
                                                <option value="brand2">Brand 2</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="category[]">
                                                <option value="category1">Category 1</option>
                                                <option value="category2">Category 2</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="subCategory[]">
                                                <option value="subCategory1">Sub-Category 1</option>
                                                <option value="subCategory2">Sub-Category 2</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="productName[]">
                                                <option value="product1">Product 1</option>
                                                <option value="product2">Product 2</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="pack[]">
                                                <option value="pack1">Pack 1</option>
                                                <option value="pack2">Pack 2</option>
                                            </select>
                                        </td>
                                        <td>
                                            <select class="form-control" name="mrp[]">
                                                <option value="mrp1">MRP 1</option>
                                                <option value="mrp2">MRP 2</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="form-control" name="quantity[]" /></td>
                                        <td><input type="date" class="form-control" name="expiryDate[]" /></td>
                                        <td><input type="text" class="form-control" value=5000 name="totalAmount[]"
                                                readonly /></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="new-row d-flex align-items-center justify-content-end mt-3">
                            <p class="total-amount mx-3">Total Amount: <span id="totalAmount">0</span></p>
                        </div>

                        <div class="save-button d-flex align-items-center justify-content-center">
                            <button type="button" id="addBrandFormBtn" class="btn btn-secondary" data-dismiss="modal"
                                aria-label="Close">Close</button>
                        </div>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    StoresList();
                    // STORE ASSIGN LIST 
                    function api_for_storeAssignList(page) {
                        if (page) {
                            ajaxGetData(`/api/store/assign?page=${page}`, (response) => {
                                store_assign_list(response.data)
                                pagination(page, response.data)
                            })
                        }else{
                            ajaxGetData(`/api/store/assign`, (response) => {
                                store_assign_list(response.data)
                                pagination(page, response.data)
                            })
                        }
                    }


                    function store_assign_list(response) {
                        $('#store_assign_table tbody').empty();
                        if (response && response.data.length > 0) {
                            $.each(response.data, function(index, store) {
                                console.log(store);
                                $('#store_assign_table tbody').append(`
                                        <tr class="store-row" style="cursor:pointer;" data-id="${store.id}"> 
                                        <td scope="row">${index + 1}</td>   
                                        <td>${store.assign_bill_number}</td>   
                                        <td>${store?.purchase_stock?.purchase_bill_number}</td>
                                        <td>${store.store.name}</td>
                                        <td>${store.store_id}</td>
                                        <td>${store.total}</td> 
                                        <td><button class="btn btn-sm btn-info" onclick="print()">Print</button></td>   
                                        </tr>
                                    `);
                            });
                        } else {
                            $('#store_assign_table tbody').append(`
                                    <tr>
                                    <td class="text-center" colspan="9">No records found</td>
                                    </tr>
                            `);
                        }
                    }
                    api_for_storeAssignList()
                });

                function StoresList() {
                    $.ajax({
                        url: '/api/stores/',
                        type: 'GET',
                        success: function(response) {
                            var category = response.data;
                            $('#stores').html(" ");
                            $('#stores').html('<option value="">Select Store</option>')
                            $.each(category, function(index, cat) {
                                $('#stores').append('<option value="' + cat.id + '">' + cat.name +
                                ' </option>');

                            });
                        }
                    });
                }

                // SEARCH FUNCATIONALITY 
                $(document).ready(function() {
                    $('#search').on('input', function() {
                        var searchText = $(this).val().toLowerCase();
                        var found = false;

                        $('.store-row').each(function() {
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
                });


                // PAGINATION 
                $(document).on('click', '.page-item', function() {
                    
                    if($(this).text().trim() == "Next" && $('.page-item.active .page-link').text() == 1){
                        $(this).removeClass('disabled');
                    }
                    if($(this).text().trim() == "Previous" && $('.page-item.active .page-link').text() == 2){
                        $(this).removeClass('disabled');
                    }
                    if (!$(this).hasClass('disabled')) {
                        if ($(this).text().trim() === 'Previous' && !$(this).hasClass('disabled')) {
                            page = Number($('.page-item.active .page-link').text()) - 1;
                        } else if ($(this).text().trim() === 'Next' && !$(this).hasClass('disabled')) {
                            page = Number($('.page-item.active .page-link').text()) + 1;
                        } else {
                            page = $(this).find('.page-link').text();
                        }
                        api_for_storeAssignList(page)
                    }
                });
            </script>
        @endsection
