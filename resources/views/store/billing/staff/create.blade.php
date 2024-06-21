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
            <form action="{{url('')}}" method="POST" id="staffBillingCreate">
                @csrf

                <div class="form-row mb-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="staff_phone">Staff Phone Number</label>
                            <div class="form-group d-flex align-items-center">
                                
                                <select data-enable-search="true"name="staff_phone[]" id="staff_phone" class="form-control ">
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
                            <label for="doctor_name">Doctor Name</label>
                            <div class="form-group d-flex align-items-center">
                                
                                <select data-enable-search="true" name="doctor_name[]" id="doctor_name" class="form-control ">
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
                                
                                <select data-enable-search="true" name="staff_name[]" id="staff_name" class="form-control ">
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
                                    <option value="online">Online Mode</option>
                                    <option value="offline">Offline Mode</option>
                                </select>
                               
                            </div>
                        </div> 
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="invoiceNo">Invoice No</label>
                            <div class="form-group d-flex align-items-center">
                                <input type="text" name="invoiceNo" id="invoiceNo" class="form-control" value="{{ uniqid() }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-responsive overflow-visible">
                    <table class="table border text-center text-dark" id="dynamicForm">
                        <thead>
                            <tr>
                                <th class="d-none">#</th>
                                <th>Product</th>
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
                    <button type="button" class="btn btn-sm btn-danger" id="cancel">Cancel</button>
                </div>
            </form>

            
        </div>
    </div>
    
    <!-- ADD STAFF PHONE NUMBER  -->
    <div class="modal fade" id="addStaffNumber" tabindex="-1" role="dialog" aria-labelledby="addStaffNumberLabel" aria-hidden="true">
        <div class="modal-dialog container modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addStaffNumberLabel">Add Staff Number</h5>

                </div>
                <div class="modal-body">
                    <form id="addStaff" class="container d-flex align-items-center justify-content-between flex-wrap">
                        <div class="form-group col-md-6">
                            <label for="newStaff">Staff Name</label>
                            <input type="text" class="form-control" id="staff_name" name="staff_name" placeholder="Enter Staff Name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="newStaff">Staff Email</label>
                            <input type="email" class="form-control" id="staff_mail" name="staff_mail" placeholder="Enter Staff Email" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="newStaff">Staff Phone Number</label>
                            <input type="mobile" class="form-control" id="staff_phone" name="staff_phone" placeholder="Enter Staff Phone Number" required>
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
                        <div class="col-md-12 save-button d-flex align-items-end justify-content-end">
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
        <div class="modal-dialog container modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addDoctorLabel">Add Doctor</h5>

                </div>
                <div class="modal-body">
                    <form id="addDoctor" action="{{url('/brands')}}" method="POST" class="container d-flex align-items-center justify-content-between flex-wrap">
                        <div class="form-group col-md-6">
                            <label for="newDoctor">Doctor Name</label>
                            <input type="text" class="form-control" id="doctor_name" name="doctor_name" placeholder="Enter Doctor Name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="newDoctor">Doctor Email</label>
                            <input type="email" class="form-control" id="doctor_mail" name="doctor_mail" placeholder="Enter Doctor Name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="newDoctor">Doctor Phone Number</label>
                            <input type="mobile" class="form-control" id="doctor_phone" name="doctor_phone" placeholder="Enter Doctor Name" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="newDoctor">Doctor Degree</label>
                            <input type="text" class="form-control" id="doctor_degree" name="doctor_degree" placeholder="Enter Doctor Name" required>
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
                        <div class="col-md-12 save-button d-flex align-items-end justify-content-end">
                            <button type="submit" id="addBrandFormBtn" class="btn btn-success mx-2">Save</button>
                            <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            count = 0
            staffData(null)
            doctorData()
            $(document).on('change', '#staff_phone', function () {
                staffData(this.value)
            });

            

            $(document).on('change', '.product', function () {
                ajaxGetData(`/products?id=${this.value}`, (res)=>{
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
                const payload = gatherFormData();
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                ajaxPostData('/staff/billing/create', payload, csrfToken, (response)=>{
                    // ADD SUCCESS MESSAGE HERE FOR BILLING INSERTION 
                    console.log("Response: ", response);
                })
            })
           
        });

        function staffData(id) { 
            if (id) {
                ajaxGetData(`/staffs?id=${id}`, (res)=>{
                    $('#staff_name').html("")
                    for (let index = 0; index < res?.data?.length; index++) {
                        const element = res?.data[index];
                        $('#staff_name').append('<option selected value="' + element.id + '">' + element.name + '</option>');
                    }
                })
            }else{
                ajaxGetData('/staffs', (res)=>{
                    for (let index = 0; index < res?.data?.length; index++) {
                        const element = res?.data[index];
                        $('#staff_phone').append('<option value="' + element.id + '">' + element.phone + '</option>');
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
                for (let index = 0; index < res?.data?.length; index++) {
                    const element = res?.data[index];
                    console.log("ELEMENT PACK NAME:",element.pack_name);
                    $(`#pack${count}`).append('<option value="' + element.id + '">' + element.pack_name + '</option>');
                }
            })
            // ajaxGetData(`/pack?id=${id}`, (res)=>{
            //     $(`#pack${count}`).val(res?.data[0].name)
            // })
        }
        function priceData(id, count) {
            ajaxGetData(`/price?id=${id}`, (res) =>{
                for (let index = 0; index < res?.data?.length; index++) {
                    const element = res?.data[index];
                    $(`#mrp${count}`).append('<option value="' + element.id + '">' + element?.price_name + '</option>');
                }
            })
        }

        function addNewRow(id) {
            productData();

            const newRow = `
                <tr>
                    <td id="row" class="row_id d-none product">${id}</td>
                    <td>
                        <select data-enable-search="true" class="form-control product" name="productName[]" id="product_name${id}">
                            <option value="">Choose Product</option>
                        </select>
                    </td>
                    <td>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="category" id="category${id}" readonly />
                        </div>        
                    </td>
                    <td>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="subCategory" id="subCategory${id}" readonly />
                        </div>        
                    </td>
                    <td>
                        <select data-enable-search="true" class="form-control" name="pack[]" id="pack${id}">
                            <option value="">Choose Pack</option>
                        </select>
                    </td>
                    <td>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="qty" id="qty${id}" />
                        </div>
                    </td>
                    <td>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="mrp[]" id="mrp${id}" />
                        </div>
                    </td>
                    <td>
                        <div class="form-group d-flex align-items-center">
                            <input type="number" class="form-control" name="unit_value[]" id="unit_value${id}" />
                        </div>
                    </td>
                    <td>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="discount[]" id="discount${id}" />
                        </div>
                    </td>
                    <td>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control totalAmount" value="0" name="totalAmount[]" id="totalAmount${id}" readonly />
                        </div>
                    </td>
                    <td>
                        <span class="delete-icon btn btn-danger text-white p-2 px-1" onclick="deleteRow(this)">
                            <i class="fas fa-trash"></i>
                        </span>
                    </td>
                </tr>
            `;

            document.getElementById("formBody").insertAdjacentHTML('beforeend', newRow);

            $(document).on('keyup', `#qty${id}`, function () {
                updateRowTotal(id);
            });
            $(document).on('change', `#mrp${id}`, function () {
                updateRowTotal(id);
            });
            $(document).on('change', `#discount${id}`, function () {
                updateRowTotal(id);
            });

            function updateRowTotal(id) {
                const qty = Number($(`#qty${id}`).val());
                const mrp = Number($(`#mrp${id} option:selected`).text());
                const discount = Number($(`#discount${id}`).val());
                $(`#totalAmount${id}`).val((qty * mrp) - discount);
                updateTotalAmount();
            }
        }

        function updateTotalAmount() {
            let total = 0;
            $('.totalAmount').each(function() {
                total += parseFloat($(this).val()) || 0;
            });
            $('#totalAmount').text(total);
        }


        function deleteRow(element) {
            const row = element.closest("tr");
            row.remove();
            updateTotalAmount();
        }
        function gatherFormData() {
            const rows = document.querySelectorAll('#dynamicForm tbody tr');
            const products = [];

            rows.forEach(row => {
                const productId = row.querySelector(`[name="productName[]"]`).value;
                const category = row.querySelector(`[name="category"]`).value;
                const subCategory = row.querySelector(`[name="subCategory"]`).value;
                const pack = row.querySelector(`[name="pack[]"]`).value;
                const qty = row.querySelector(`[name="qty"]`).value;
                const mrp = row.querySelector(`[name="mrp[]"]`).value;
                const unitValue = row.querySelector(`[name="unit_value[]"]`).value;
                const discount = row.querySelector(`[name="discount[]"]`).value;
                const totalAmount = row.querySelector(`[name="totalAmount[]"]`).value;

                products.push({
                    productId,
                    category,
                    subCategory,
                    pack,
                    qty,
                    mrp,
                    unitValue,
                    discount,
                    totalAmount
                });
            });
            let today = new Date();

            let day = String(today.getDate()).padStart(2, '0');
            let month = String(today.getMonth() + 1).padStart(2, '0');
            let year = today.getFullYear();

            let formattedDate = `${day}/${month}/${year}`;



            const payload = {
                staff_phone: $('#staff_phone').val(),
                doctor_name: $('#doctor_name').val(),
                paymentType: $('#paymentType').val(),
                invoiceNo: $('#invoiceNo').val(),
                staff_name: $('#staff_name option:selected').text(),
                total_amt: $('#totalAmount').text(),
                billing_date:formattedDate,
                product_billings: products
            };

            return payload;
        }
        // function addNewRow() {
        //     document.addEventListener('DOMContentLoaded', function() {
        //         updateTotalAmount();
        //     });
            
        //     const newRow = document.createElement("tr");
        //     newRow.innerHTML = `
        //         <td id="row" class="row_id d-none" ></td>
        //         <td>
        //             <select data-enable-search="true" class="form-control" name="productName[]" id="product_name">
                    
        //             </select>
        //         </td>
        //         <td>
        //             <div class="form-group d-flex align-items-center">
        //                 <input type="text" class="form-control " name="category" id="category" />
        //             </div>        
        //         </td>
        //         <td>
        //             <div class="form-group d-flex align-items-center">
        //                 <input type="text" class="form-control " name="subCategory" id="subCategory" />
        //             </div>        
        //         </td>
        //         <td>
        //             <div class="form-group d-flex align-items-center">
        //                 <input type="text" class="form-control " name="pack" id="pack" />
        //             </div>
        //         </td>
        //         <td>
        //             <div class="form-group d-flex align-items-center">
        //                 <input type="text" class="form-control " name="pack" id="pack" />
        //             </div>
        //         </td>
                
        //         <td>
        //             <div class="form-group d-flex align-items-center">
        //                 <input type="number" class="form-control quantity" name="quantity[]" id="quantity" />
        //             </div>
        //         </td>
        //         <td>
        //             <div class="form-group d-flex align-items-center">
        //                 <input type="text" class="form-control" name="expiryDate[]" id="expiryDate" />
        //             </div>
        //         </td>
        //         <td>
        //             <div class="form-group d-flex align-items-center">
        //                 <input type="text" class="form-control totalAmount" value=0 name="totalAmount[]" id="totalAmount" readonly />
        //             </div>
        //         </td>
        //         <td>
        //             <span class="delete-icon btn btn-danger text-white p-2 px-1" onclick="deleteRow(this)">
        //                 <i class="fas fa-trash"></i>
        //             </span>
        //         </td>
        //     `;

        //     document.getElementById("formBody").appendChild(newRow);
        //     updateTotalAmount();
        //     clearSelectOptions();

        // }

        // function deleteRow(element) {
        //     const row = element.closest("tr");
        //     row.remove();
        //     updateTotalAmount();
        // }
    </script>
@endsection