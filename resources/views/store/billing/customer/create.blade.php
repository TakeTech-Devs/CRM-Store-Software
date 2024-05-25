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
                            <label for="supplier_id">Doctor Name</label>
                            <div class="form-group d-flex align-items-center">
                                
                                <select data-enable-search="true"name="supplier_id[]" id="supplier_id" class="form-control ">
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
                                
                                <select data-enable-search="true"name="customer_name[]" id="customer_name" class="form-control ">
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
                    <button type="button" class="btn btn-sm btn-secondary text-light mb-2" onclick="addNewRow()">Add New Row</button>
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
        function addNewRow() {
            document.addEventListener('DOMContentLoaded', function() {
                updateTotalAmount();
            });
            
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td id="row" class="row_id d-none" ></td>
                <td>
                    <select data-enable-search="true" class="form-control" name="productName[]" id="product_name">
                    
                    </select>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control " name="category" id="category" />
                    </div>        
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control " name="subCategory" id="subCategory" />
                    </div>        
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control " name="pack" id="pack" />
                    </div>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control " name="pack" id="pack" />
                    </div>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control mrp" name="mrp" id="mrp" />
                    </div>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="number" class="form-control quantity" name="quantity[]" id="quantity" />
                    </div>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control" name="expiryDate[]" id="expiryDate" />
                    </div>
                </td>
                <td>
                    <div class="form-group d-flex align-items-center">
                        <input type="text" class="form-control totalAmount" value=0 name="totalAmount[]" id="totalAmount" readonly />
                    </div>
                </td>
                <td>
                    <span class="delete-icon btn btn-danger text-white p-2 px-1" onclick="deleteRow(this)">
                        <i class="fas fa-trash"></i>
                    </span>
                </td>
            `;

            document.getElementById("formBody").appendChild(newRow);
            updateTotalAmount();
            clearSelectOptions();

        }

        function deleteRow(element) {
            const row = element.closest("tr");
            row.remove();
            updateTotalAmount();
        }
    </script>
@endsection