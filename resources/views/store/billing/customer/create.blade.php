@extends('layouts.dashboard')

@section('title', 'Create Customer Billing')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-dark">Create Customer Billing</h2>

            <div class="text-right">
                <a href="{{ url('store/customer/billing') }}" class="btn btn-secondary btn-sm">View Customer Billing List</a>
            </div>
        </div>
    
        <div class="mt-4 position-relative">
            <form action="{{url('')}}" method="POST" id="customerBillingCreate">
                @csrf

                <div class="form-row mb-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_phone">Customer Phone Number</label>
                            <div class="form-group d-flex align-items-center">
                                
                                <select data-enable-search="true"name="customer_phone[]" id="customer_phone" class="form-control ">
                                    <option value="">Choose Customer Phone Number...</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal" data-target="#addBrandModal">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-6"> 
                        <div class="form-group">
                            <label for="doctor_name">Doctor Name</label>
                            <div class="form-group d-flex align-items-center">
                                
                                <select data-enable-search="true"name="doctor_name[]" id="doctor_name" class="form-control ">
                                    <option value="">Choose Doctor Name...</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal" data-target="#addBrandModal">
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
                                
                                <select data-enable-search="true"name="customer_name[]" id="customer_name" class="form-control " disabled>
                                    <option value="">Choose Customer Name...</option>
                                </select>
                               
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="paymentType">Payment Type</label>
                            <div class="form-group d-flex align-items-center">
                                
                                <select data-enable-search="true"name="paymentType[]" id="paymentType" class="form-control ">
                                    <option value="">Choose Payment Type...</option>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
                                </select>
                               
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="paymentType">Invoice Number</label>
                            <div class="form-group d-flex align-items-center">
                                <input type="text" name="invoiceNo" id="invoiceNo" class="form-control">
                            </div>
                        </div> 
                    </div>
                </div>
                <div class="table-responsive overflow-visible">
                    <table class="table border text-center text-dark" id="dynamicForm">
                        <thead>
                            <tr>
                                <th class="d-none">#</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Sub-Category</th>
                                <th>Pack</th>
                                <th>Qty</th>
                                <th>MRP</th>
                                <th>Unit Value</th>
                                <th>Discount</th>
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
                    <button type="button" class="btn btn-sm btn-secondary text-light mb-2" id="add_row">Add New Row</button>
                </div>

            
                <div class="d-flex align-items-center justify-content-end my-3">
                    <button type="button" class="btn btn-primary btn-sm mx-2" id="submitBilling">Save</button>
                    <button type="button" class="btn btn-success btn-sm mx-2" id="submitBillingPrint">Save and Print</a>
                    <button type="button" class="btn btn-sm btn-danger" id="cancel">Cancel</button>
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

        $(document).ready(function () {
            count = 0
            customerData(null)
            doctorData()
            $(document).on('change', '#customer_phone', function () {
                customerData(this.value)
            });

            $(document).on('change', '.product', function () {
                ajaxGetData(`/products?id=${this.value}`, (res)=>{
                    console.log(res, "res");
                    categoryData(res?.data[0].category_id, count)
                    subCategoryData(res?.data[0].sub_category_id, count)
                    packData(null, count)
                    priceData(null, count)
                })
            })

            $(document).on('click', '#add_row', function () {
                count = count + 1;
                addNewRow(count)
            })

            $(document).on('click', '#submitBilling', function () {
                let payload = {
                    customer_phone:$('#customer_phone').val(),
                    doctor_name:$('#doctor_name').val(),
                    paymentType:$('#paymentType').val(),
                    invoiceNo:$('#invoiceNo').val(),
                }

                const rows = document.querySelectorAll('#formBody tbody tr');
                console.log(rows);

                console.log(payload);
            })
           
        });

        function customerData(id) { 
            if (id) {
                ajaxGetData(`/customers?id=${id}`, (res)=>{
                    $('#customer_name').html("")
                    for (let index = 0; index < res?.data?.length; index++) {
                        const element = res?.data[index];
                        $('#customer_name').append('<option selected value="' + element.id + '">' + element.name + '</option>');
                    }
                })
            }else{
                ajaxGetData('/customers', (res)=>{
                    for (let index = 0; index < res?.data?.length; index++) {
                        const element = res?.data[index];
                        $('#customer_phone').append('<option value="' + element.id + '">' + element.phone + '</option>');
                    }
                })
            }
          
        }
        
        function doctorData() { 
            ajaxGetData('/doctors', (res)=>{
                for (let index = 0; index < res?.data?.length; index++) {
                    const element = res?.data[index];
                    $('#doctor_name').append('<option value="' + element.id + '">' + element.name + '</option>');
                }
            })
        }

        function productData() { 
            ajaxGetData(`/products`, (res) =>{
                for (let index = 0; index < res?.data?.length; index++) {
                    const element = res?.data[index];
                    $('.product').append('<option value="' + element.id + '">' + element.product_name + '</option>');
                }
            })
        }

        function categoryData(id, count) {
            ajaxGetData(`/category?id=${id}`, (res)=>{
                $(`#category${count}`).val(res?.data[0].category_name)
            })
        }

        function subCategoryData(id, count) {
            ajaxGetData(`/sub-category?id=${id}`, (res)=>{
                $(`#subCategory${count}`).val(res?.data[0].sub_category_name)
            })
        }

        function packData(id, count) {
            ajaxGetData(`/pack?id=${id}`, (res) =>{
                console.log(res);
                for (let index = 0; index < res?.data?.length; index++) {
                    const element = res?.data[index];
                    $(`#pack${count}`).append('<option value="' + element.id + '">' + element.pack_name + '</option>');
                }
            })
            // ajaxGetData(`/pack?id=${id}`, (res)=>{
            //     $(`#pack${count}`).val(res?.data[0].name)
            // })
        }
        function priceData(id, count) {
            ajaxGetData(`/price?id=${id}`, (res) =>{
                console.log(res);
                for (let index = 0; index < res?.data?.length; index++) {
                    const element = res?.data[index];
                    $(`#mrp${count}`).append('<option value="' + element.id + '">' + element?.price_name + '</option>');
                }
            })
        }

        function addNewRow(id) {
            productData()
            document.addEventListener('DOMContentLoaded', function() {
                updateTotalAmount();
            });
            
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td id="row" class="row_id d-none" ></td>
                <td>
                    <select data-enable-search="true" class="form-control product" name="productName[]" id="product_name${id}">
                        <option value="">Choose Product</option>
                    </select>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control " name="category" id="category${id}" disabled />
                    </div>        
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control " name="subCategory" id="subCategory${id}" disabled />
                    </div>        
                </td>
                <td>
                    <select data-enable-search="true" class="form-control" name="pack[]" id="pack${id}">
                        <option value="">Choose Pack</option>
                    </select>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control " name="qty" id="qty${id}" />
                    </div>
                </td>
                <td>
                    <select data-enable-search="true" class="form-control" name="mrp[]" id="mrp${id}">
                        <option value="">Choose MRP</option>
                    </select>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="number" class="form-control " name="unit_value[]" id="unit_value${id}" />
                    </div>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control" name="discount[]" id="discount${id}" />
                    </div>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control totalAmount" value=0 name="totalAmount[]" id="totalAmount${id}" readonly />
                    </div>
                </td>
                <td>
                    <span class="delete-icon btn btn-danger text-white p-2 px-1" onclick="deleteRow(this)">
                        <i class="fas fa-trash"></i>
                    </span>
                </td>
            `;

            document.getElementById("formBody").appendChild(newRow);
            $(document).on('keyup', `#qty${count}`, function () {
                $(`#totalAmount${count}`).val(Number($(`#qty${count}`).val()) * Number($(`#mrp${count} option:selected` ).text()) - $(`#discount${count}`).val())
                console.log($(`#mrp${count} option:selected`).text());
            })
            $(document).on('change', `#mrp${count}`, function () {
                $(`#totalAmount${count} option:selected`).val(Number($(`#qty${count}`).val()) * Number($(`#mrp${count} option:selected`).text()) - $(`#discount${count}`).val())
                console.log($(this).val());
            })
            $(document).on('change', `#discount${count}`, function () {
                $(`#totalAmount${count} option:selected`).val(Number($(`#qty${count}`).val()) * Number($(`#mrp${count} option:selected`).text()) - $(`#discount${count}`).val())
                console.log($(this).val());
            })
            // updateTotalAmount();
            // clearSelectOptions();

        }

        function deleteRow(element) {
            const row = element.closest("tr");
            row.remove();
            updateTotalAmount();
        }
    </script>
@endsection