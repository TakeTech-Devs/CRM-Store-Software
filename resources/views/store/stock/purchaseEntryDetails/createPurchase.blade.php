<!-- resources/views/admin/purchase/create.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Create Purchase Entry')
<style>
    #message.bg-success{
        display:none;
    }
</style>

@section('content')
<style>
    .delete-icon {
        cursor: pointer;
        color: red;
    }

    .table {
        width: 200%;
    }

    .total-amount {
        font-weight: bold;
    }

    .bottom-right-buttons {
        position: absolute;
        bottom: 0;
        right: 0;
        margin-bottom: 20px;
    }
</style>
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Create Purchase</h2>
        <div id="message" class="text-center bg-success text-light p-3"></div>

        <div class="text-right">
            <a href="{{ url('admin/purchase-stock') }}" class="btn btn-secondary btn-sm">View Purchase Entries</a>
        </div>
    </div>
    
    <div class="mt-4 position-relative">
        <form action="{{url('/purchase/create')}}" method="POST" id="purchaseStockCreate">
            @csrf

            <!-- SKU Date and SKU ID -->
            <div class="form-row mb-2">
                <div class="form-group col-md-6">
                    <label for="skuDate">SKU Date</label>
                    <input type="date" class="form-control" id="sku_date" name="sku_date[]" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="sku_id">SKU ID</label>
                    <input type="text" class="form-control" id="sku_id" name="sku_id[]" >
                </div>
            </div>
            
            <div class="form-row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="supplier_id">Supplier Name</label>
                        <div class="form-group d-flex align-items-center">
                            
                            <select name="supplier_id[]" id="supplier_id" class="form-control mr-2">
                                <option value="">Choose Supplier Name...</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addBrandModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div> 
                </div>
                
                <div class="form-group col-md-6">
                    <label for="purchase_bill_number">Purchase Bill No</label>
                    <input type="text" class="form-control" id="purchase_bill_number" name="purchase_bill_number[]" >
                </div>
            </div>
            <div class="table-responsive overflow-visible">
                <table class="table border text-center text-dark" id="dynamicForm">
                    <thead>
                        <tr>
                            <th class="d-none">ID</th>
                            <th>Brand Name</th>
                            <th>Category</th>
                            <th>Sub-Category</th>
                            <th>Product Name</th>
                            <th>Pack</th>
                            <th>MRP</th>
                            <th>Qty</th>
                            <th>Expiry Date</th>
                            <th>Total Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="formBody">
                            
                    </tbody>
                </table>
            </div>

            <div class="new-row d-flex align-items-center justify-content-end mt-3">
                <p class="total-amount mx-3">Total Amount: <span id="totalAmount">0</span></p>
                <button type="button" class="btn btn-sm btn-secondary text-light mb-2" onclick="addNewRow()">Add New Row</button>
            </div>

        
            <div class="d-flex align-items-center justify-content-end my-3">
                <button type="button" class="btn btn-primary btn-sm mx-2" id="submitPurchaseEntry">Save</button>
                <button type="button" class="btn btn-success btn-sm mx-2" id="submitPurchaseEntry&Assign">Save and Assign</a>
                <button type="button" class="btn btn-sm btn-danger">Cancel</button>
            </div>
        </form>

        <!-- SUPPLIER MODEL  -->
        <div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel"
            aria-hidden="true">
            <div class="modal-dialog container" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                        <h5 class="modal-title" id="addBrandModalLabel">Add Supplier</h5>
                    </div>
                    <div class="modal-body">
                        <form id="addBrand" action="{{url('/supplier')}}" method="POST" class="container">
                            <div id="message" class="text-center bg-success text-light p-3"></div>
                            <div class="form-group">
                                <label for="newBrand">Supplier Name</label>
                                <input type="text" class="form-control" id="supplier_name" name="supplier_name" required>
                            </div>
                            <div class="form-group">
                                <label>Status:</label>
                                <div class="form-group d-flex justify-content-start align-items-center">

                                    <div class="form-check mx-3">
                                        <input type="radio" class="form-check-input" id="statusActive" name="status" value="1">
                                        <label class="form-check-label" for="statusActive">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="statusInactive" name="status" value="0">
                                        <label class="form-check-label" for="statusInactive">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <div class="save-button d-flex align-items-center justify-content-center">
                                <button type="submit" id="addBrandFormBtn" class="btn btn-success mx-2">Save</button>
                                <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- PACK MODEL -->
        <div class="modal fade" id="addPackModal" tabindex="-1" role="dialog" aria-labelledby="addPackModalLabel"
            aria-hidden="true">
            <div class="modal-dialog container" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                        <h5 class="modal-title" id="addPackModalLabel">Add Pack</h5>
                    </div>
                    <div class="modal-body">
                        <form id="addPack" action="{{url('/pack')}}" method="POST" class="container">
                            <div id="message" class="text-center bg-success text-light p-3"></div>
                            <div class="form-group">
                                <label for="newBrand">Pack Name</label>
                                <input type="text" class="form-control" id="pack_name" name="pack_name" required>
                            </div>
                            <div class="form-group d-none">
                                <label>Status:</label>
                                <div class="form-group d-flex justify-content-start align-items-center">

                                    <div class="form-check mx-3">
                                        <input type="radio" class="form-check-input" id="statusActive" name="status" value="1" checked>
                                        <label class="form-check-label" for="statusActive">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="statusInactive" name="status" value="0">
                                        <label class="form-check-label" for="statusInactive">Deactive</label>
                                    </div>
                                </div>
                            </div>
                            <div class="save-button d-flex align-items-center justify-content-center">
                                <button type="submit" id="addBrandFormBtn" class="btn btn-success mx-2">Save</button>
                                <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- MRP MODEL  -->
        <div class="modal fade" id="addMRPModal" tabindex="-1" role="dialog" aria-labelledby="addMRPModalLabel"
            aria-hidden="true">
            <div class="modal-dialog container" role="document">
                <div class="modal-content">
                    <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                        <h5 class="modal-title" id="addMRPModalLabel">Add MRP</h5>
                    </div>
                    <div class="modal-body">
                        <form id="addMRP" action="{{url('/prices')}}" method="POST" class="container">
                            <div class="form-group">
                                <label for="price_name">MRP</label>
                                <input type="number" class="form-control" id="price_name" name="price_name" required>
                            </div>
                            <div class="save-button d-flex align-items-center justify-content-center">
                                <button type="submit" id="addMRPButton" class="btn btn-success mx-2">Save</button>
                                <button type="button" id="addBrandFormBtn" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                            </div>
                            
                        </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
        var rowID = 0;
        $(document).ready(function(){
            ajaxGetData('/api/next/sku', (res)=>{
                $('#sku_id').val(res?.data);
            })


            getSupplier();

            // $('#expiryDate').datepicker({
            //     dateFormat: 'yy-mm-dd',
            //     changeMonth: true,
            //     changeYear: true,
            //     minDate: '+3M', 
            // });

            $(document).on('click', '#submitPurchaseEntry', function(){
                let sku_date = $('#sku_date').val();
                let sku_id = $('#sku_id').val();
                let supplier_id = $('#supplier_id').val();
                let purchase_bill = $('#purchase_bill_number').val();
                let totalAmount = $('#totalAmount').text();
                let ProductEntryRequest = [];
                $('#formBody tr').each(function() {
                    let rowID = $(this).find('td#row').text();
                    let brand_id = $(this).find(`#brand${rowID}`).val();
                    let category_id = $(this).find(`#category${rowID}`).val();
                    let sub_category_id = $(this).find(`#sub_category${rowID}`).val();
                    let product_id = $(this).find(`#product_name${rowID}`).val();
                    let pack_id = $(this).find(`#pack${rowID}`).val();
                    let price_id = $(this).find(`#mrp${rowID} option:selected`).attr('id');
                    let qty = $(this).find(`#quantity${rowID}`).val();
                    let exp_date = $(this).find(`#expiryDate${rowID}`).val();

                    
                    ProductEntryRequest.push({
                        brand_id: brand_id,
                        category_id: category_id,
                        sub_category_id: sub_category_id,
                        product_id: product_id,
                        pack_id: pack_id,
                        price_id: price_id,
                        qty: qty,
                        exp_date: exp_date
                        // exp_date: $('#expiryDate').val()
                    });
                });


                let jsonObj = {
                    sku_date:sku_date,
                    sku_id:sku_id,
                    purchase_bill_number: purchase_bill,
                    supplier_id:supplier_id,
                    stock_entry: ProductEntryRequest,
                    total: totalAmount,
                };

                ajaxPostData(`/api/purchase/stock`, jsonObj, (response)=>{
                    alert("Purchase Entry Created Successfully...");
                    window.location.href = "/admin/purchase-stock";
                })

            })
        });

       
        function addNewRow() {
            rowID = rowID + 1;
            document.addEventListener('DOMContentLoaded', function() {
                updateTotalAmount();
            });
            getBrands();
            getCategories();
            getPacks();
            getPrices();
            calculateTotalAmount();
            
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td id="row" class="row_id d-none" >${rowID}</td>
                <td>
                    <select class="form-control" name="brand[]" id="brand${rowID}">
                    
                    </select>
                </td>
                <td>
                    <select class="form-control" name="category[]" id="category${rowID}" data-row-id="${rowID}">
                    
                    </select>
                </td>
                <td>
                    <select class="form-control" name="subCategory[]" id="sub_category${rowID}" data-row-id="${rowID}">
                    
                    </select>
                </td>
                <td>
                    <select class="form-control" name="productName[]" id="product_name${rowID}">
                    
                    </select>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <select class="form-control mr-2" name="pack[]" id="pack${rowID}">
                        
                        </select>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addPackModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <select class="form-control mrp mr-2" name="mrp[]" id="mrp${rowID}">
                        
                        </select>
                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addMRPModal">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="quantity[]" id="quantity${rowID}" />
                </td>
                <td>
                    <input type="date" class="form-control" name="expiryDate[]" id="expiryDate${rowID}" />
                </td>
                <td>
                    <input type="text" class="form-control totalAmount" value=0 name="totalAmount[]" id="totalAmount${rowID}" readonly />
                </td>
                <td>
                    <span class="delete-icon btn btn-danger text-white" onclick="deleteRow(this)">
                        <i class="fas fa-trash"></i>
                    </span>
                </td>
            `;

            document.getElementById("formBody").appendChild(newRow);
            updateTotalAmount();
            clearSelectOptions();

            $(document).on('change', `#category${rowID}`, function(){
                
                let curr_row=$(this).data('row-id');
                let categoryId = $(this).val();
                getSubCategories(categoryId,curr_row);
            })
            $(document).on('change', `#sub_category${rowID}`, function () { 
                let curr_row=$(this).data('row-id');
                let sub_category_id = $(this).val();
                let category_id = $(`#category${rowID}`).val();
                let brand_id = $(`#brand${rowID}`).val();
                getProducts(brand_id, category_id, sub_category_id,curr_row)

            })
            $(document).on('input',`#quantity${rowID}`,calculateTotalAmount);
            $(document).on('change',`#mrp${rowID}`,calculateTotalAmount);
        }

        function deleteRow(element) {
            const row = element.closest("tr");
            row.remove();
            updateTotalAmount();
        }

        function clearSelectOptions() {
            $(`#brand${rowID}`).empty();
            $(`#category${rowID}`).empty();
            $(`#sub_category${rowID}`).empty();
            $(`#product_name${rowID}`).empty();
            $(`#pack${rowID}`).empty();
            $(`#mrp${rowID}`).empty();
        }


        function calculateTotalAmount() {
            
            var mrpPriceName=$(`#mrp${rowID}`).val();
            var mrp = parseFloat(mrpPriceName); 
            var quantity = parseFloat($(`#quantity${rowID}`).val()); 
            if (!isNaN(mrp) && !isNaN(quantity)) {
                var totalAmount = mrp * quantity; 
                $(`#totalAmount${rowID}`).val(totalAmount.toFixed(2)); 
            } else {
                $(`#totalAmount${rowID}`).val('0');
            }
            updateTotalAmount();
        }

        function updateTotalAmount() {
            let totalAmount = 0;
            const totalAmountElements = document.getElementsByName("totalAmount[]");

            totalAmountElements.forEach((element) => {
                totalAmount += parseFloat(element.value) || 0;
            });

            document.getElementById("totalAmount").innerText = totalAmount.toFixed(2);
        }
       
        function getStores() {
            ajaxGetData('/api/stores', (response) => {
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    $('#store').append(`<option value=${element.id}>${element.name}</option>`)
                }
            })
        }

        function generateRandomPattern(storeName) {
            var prefix = storeName.toUpperCase().split(' ').map(word => word[0]).join('') + "-";
            var numbers = Math.floor(Math.random() * 10000);
            var suffix = "";
            for (var i = 0; i < 3; i++) {
                suffix += String.fromCharCode(97 + Math.floor(Math.random() * 26));
            }
            return prefix + numbers.toString().padStart(4, '0') + "-" + suffix;
        }

        function getBrands(){
            ajaxGetData('/api/brands', (response)=>{
                $(`#brand${rowID}`).append(`<option value="">Choose</option>`);
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];

                    $(`#brand${rowID}`).append(`<option value=${element.id}>${element.brand_name}</option>`)
                }
            })
        }

        function getCategories(){
            ajaxGetData('/api/category', (response)=>{
                $(`#category${rowID}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data?.length; index++) {
                    const element = response?.data[index];
                    $(`#category${rowID}`).append(`<option value=${element.id}>${element.category_name}</option>`)
                }
            })
        }

        function getSubCategories(categoryId,curr_row) { 
            $(`#sub_category${curr_row}`).html(" ");
            ajaxGetData(`/api/get/subcategory/${categoryId}`, (response)=>{
                $(`#sub_category${curr_row}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    $(`#sub_category${curr_row}`).append(`<option value=${element.id}>${element.sub_category_name}</option>`)
                }
            }, (response)=>{
                $(`#sub_category${curr_row}`).empty();
                $(`#sub_category${curr_row}`).append(`<option value="" selected>No sub-category found</option>`)
            })
        }

        function getProducts(brand_id, category_id, sub_category_id,curr_row){
            ajaxGetData(`/api/products?brand_id=${brand_id}&category_id=${category_id}&sub_category_id=${sub_category_id}`, (response)=>{
                $(`#product_name${curr_row}`).empty();
                $(`#product_name${curr_row}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    $(`#product_name${curr_row}`).append(`<option value=${element.id}>${element.product_name}</option>`);
                }
            },(response)=>{
                $(`#product_name${curr_row}`).empty();
                $(`#product_name${curr_row}`).append(`<option value="" selected>No products found</option>`);
            })
        }

        // FETCHING PACKS 
        function getPacks() { 
            ajaxGetData(`/api/pack`, (response)=>{
                $(`#pack${rowID}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    $(`#pack${rowID}`).append(`<option value=${element.id}>${element.pack_name}</option>`)
                }
            })
        }
        // ADDING PACKS
        $(document).ready(function(){
            $('#addPack').on('submit', function(event){
                event.preventDefault();
                
                $.ajax({
                    url: '/api/pack',
                    data: jQuery('#addPack').serialize(), 
                    method: 'POST',
                    
                    success:function(result){

                        $('#pack'+ rowID).append('<option value="' + result.data.id + '" selected>' +
                            result.data.pack_name + '</option>');
                        $('#message').removeClass('bg-danger').addClass('bg-success').html(result.message).show();
                        jQuery('#addPack')[0].reset();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        
                        $('#addPackModal').hide();
                        $('.modal-backdrop').remove();

                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).join('<br>') : xhr.responseJSON.message;
                        $('#message').removeClass('bg-success').addClass('bg-danger').html(errorMessage).show();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        
                    }
                })
            });
        });


        // FETCHING PRICES 
        function getPrices(){
            ajaxGetData(`/api/prices`, (response)=>{
                $(`#mrp${rowID}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    $(`#mrp${rowID}`).append(`<option id=${element.id} value=${element.price_name}>${element.price_name}</option>`)
                }
            })
        }
        // ADDING PRICES
        $(document).ready(function(){
            $('#addMRP').on('submit', function(event){
                event.preventDefault();
                
                $.ajax({
                    url: '/api/prices',
                    data: jQuery('#addMRP').serialize(), 
                    method: 'POST',
                    
                    success:function(result){
                        // $('#price_id').html('<option value="">Choose MRP</option>')
                        $('#mrp' + rowID).append('<option id="' + result.data.id + '" value="' + result.data.price_name + '" selected>' +
                            result.data.price_name + '</option>');
                        $('#message').removeClass('bg-danger').addClass('bg-success').html(result.message).show();
                        jQuery('#addMRP')[0].reset();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        
                        $('#addMRPModal').hide();
                        $('.modal-backdrop').remove();

                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).join('<br>') : xhr.responseJSON.message;
                        $('#message').removeClass('bg-success').addClass('bg-danger').html(errorMessage).show();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        
                    }
                })
            });
        });


        //FETCHING SUPPLIER
        function getSupplier(){
            $.ajax({
                url: '/api/supplier/',
                type: 'GET',
                success: function(response) {
                    var supplier = response.data;
                    $.each(supplier, function(index, supp) {
                        if(supp.status === 1){
                            $('#supplier_id').append('<option value="' + supp.id + '">' + supp.supplier_name +
                            ' </option>');
                        }
                    });
                }
            });
        }
        // ADDING SUPPLIER 
        $(document).ready(function(){
            $('#addBrand').on('submit', function(event){
                event.preventDefault();
                
                $.ajax({
                    url: '/api/supplier',
                    data: jQuery('#addBrand').serialize(), 
                    method: 'POST',
                    
                    success:function(result){

                        $('#supplier_id').append('<option value="' + result.data.id + '">' +
                            result.data.supplier_name + '</option>');
                        $('#message').removeClass('bg-danger').addClass('bg-success').html(result.message).show();
                        jQuery('#addBrand')[0].reset();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        
                        $('#addBrandModal').hide();
                        $('.modal-backdrop').remove();

                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).join('<br>') : xhr.responseJSON.message;
                        $('#message').removeClass('bg-success').addClass('bg-danger').html(errorMessage).show();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        
                    }
                })
            });
        });

    </script>
@endsection
