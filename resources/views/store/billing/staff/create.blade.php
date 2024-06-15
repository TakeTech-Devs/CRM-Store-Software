@extends('layouts.dashboard')

@section('title', 'Create Staff Billing')

@section('content')
<div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-dark">Create Staff Billing</h2>

            <div class="text-right">
                <a href="{{ url('store/staff/billing') }}" class="btn btn-secondary btn-sm">View Staff Billing List</a>
            </div>
        </div>
    
        <div class="mt-4 position-relative">
            <form action="{{url('')}}" method="POST" id="customerBillingCreate">
                @csrf

                <div class="form-row mb-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_phone">Staff Phone Number</label>
                            <div class="form-group d-flex align-items-center">
                                
                                <select data-enable-search="true"name="customer_phone[]" id="customer_phone" class="form-control ">
                                    <option value="">Choose Staff Phone Number...</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal" data-target="#addStaffNumber">
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
                                
                                <select data-enable-search="true"name="staff_name[]" id="staff_name" class="form-control ">
                                    <option value="">Choose Staff Name...</option>
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
                                <!-- <th>MRP</th> -->
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
                    <button type="button" class="btn btn-sm btn-danger" id="cancel">Cancel</button>
                </div>
            </form>

            
        </div>
    </div>
    
    <!-- ADD STAFF PHONE NUMBER  -->
    <div class="modal fade" id="addStaffNumber" tabindex="-1" role="dialog" aria-labelledby="addStaffNumberLabel" aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addStaffNumberLabel">Add Staff Number</h5>

                </div>
                <div class="modal-body">
                    <form id="addStaffNumber" class="container">
                        <div class="form-group">
                            <label for="staffPhone">Staff Phone Number</label>
                            <input type="text" class="form-control" id="staffPhone" name="staffPhone" placeholder="Enter Staff Phone Number" required>
                        </div>
                        <div class="form-group d-none">
                            <label>Status:</label>
                            <div class="form-group d-flex justify-content-start align-items-center">

                                <div class="form-check mx-3">
                                    <input type="radio" class="form-check-input" id="statusActive" name="status" value="1"checked>
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
            
    <!-- ADD DOCTOR NUMBER  -->
    <div class="modal fade" id="addDoctor" tabindex="-1" role="dialog" aria-labelledby="addDoctorLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addDoctorLabel">Add Doctor</h5>

                </div>
                <div class="modal-body">
                    <form id="addBrand" action="{{url('/brands')}}" method="POST" class="container">
                        <div class="form-group">
                            <label for="newDoctor">Doctor Name</label>
                            <input type="text" class="form-control" id="doctor_name" name="doctor_name" placeholder="Enter Doctor Name" required>
                        </div>
                        <div class="form-group d-none">
                            <label>Status:</label>
                            <div class="form-group d-flex justify-content-start align-items-center">

                                <div class="form-check mx-3">
                                    <input type="radio" class="form-check-input" id="statusActive" name="status" value="1"checked>
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