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
            <form  id="customerBillingCreate">
                @csrf
                <div class="form-row mb-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="staff_phone">Staff Phone Number</label>
                            <div class="form-group d-flex align-items-center">
                                <select data-enable-search="true" name="staff_phone[]" id="staff_phone" class="form-control">
                                    <option value="">Choose Staff Phone Number...</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal" data-target="#addStaff">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="doctor_name">Doctor Name</label>
                            <div class="form-group d-flex align-items-center">
                                <select data-enable-search="true" name="doctor_name[]" id="doctor_name" class="form-control">
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
                                <select data-enable-search="true" name="staff_name[]" id="staff_name" class="form-control" disabled>
                                    <option value="">Choose Staff Name...</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="paymentType">Payment Type</label>
                            <div class="form-group d-flex align-items-center">
                                <select data-enable-search="true" name="paymentType[]" id="paymentType" class="form-control">
                                    <option value="">Choose Payment Type...</option>
                                    <option value="online">Online</option>
                                    <option value="offline">Offline</option>
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

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dynamicForm">
                        <thead>
                            <tr class="table">
                                <th>Product</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Pack</th>
                                <th>Qty</th>
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

                <button type="button" name="add_row" id="add_row" class="btn btn-sm btn-secondary mb-3  float-right ml-3">
                    Add New Row
                </button>

                <div class="form-group text-right">
                    <label for="totalAmount">Total Amount: </label>
                    <span id="totalAmount">0</span>
                </div>

                <div class="form-group text-right">
                    <button type="button" name="submitBilling" id="submitBilling" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- ADD CUSTOMER PHONE NUMBER  -->
    <div class="modal fade" id="addStaff" tabindex="-1" role="dialog" aria-labelledby="addStaffLabel" aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addStaffLabel">Add Staff</h5>
                </div>
                <div class="modal-body">
                    <form id="addStaffForm" class="container">
                        <div class="form-group">
                            <label for="name">Staff Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="mail">Staff Mail</label>
                            <input type="email" class="form-control" id="mail" name="mail" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Staff Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
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
                            <button type="submit" id="addStaffFormBtn" class="btn btn-success mx-2">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD DOCTOR MODAL -->
    <div class="modal fade" id="addDoctor" tabindex="-1" role="dialog" aria-labelledby="addDoctorLabel" aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addDoctorLabel">Add Doctor</h5>
                </div>
                <div class="modal-body">
                    <form id="addDoctorForm" class="container">
                        <div class="form-group">
                            <label for="name">Doctor Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="mail">Doctor Mail</label>
                            <input type="email" class="form-control" id="mail" name="mail" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Doctor Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>
                        <div class="form-group">
                            <label for="degree">Doctor Degree</label>
                            <input type="text" class="form-control" id="degree" name="degree" required>
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
                            <button type="submit" id="addDoctorFormBtn" class="btn btn-success mx-2">Save</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
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

            // CREATING BILL
            $(document).on('click', '#submitBilling', function () {
                const payload = gatherFormData();
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                ajaxPostData('/staff/billing/create', payload, csrfToken, (response)=>{

                    console.log("Response: ", response);
                    Swal.fire({
                        title: "Staff Billing !",
                        icon: "success",
                        text: "Staff Billing Added Successfully.",
                    }).then((response)=>{
                        window.location.href = "/store/staff/billing";
                    });

                })
            })       

            // ADDING CUSTOMER 
            $('#addStaff').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: '/api/staff', 
                    type: 'POST',
                    data: {
                        name: $('#name').val(),
                        mail: $('#mail').val(),
                        phone: $('#phone').val(),
                        status: $('input[name="status"]:checked').val(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    success: function(response) {
                        staffData();
                        Swal.fire({
                            title: "Staff !",
                            icon: "success",
                            text: "Staff Added Successfully.",
                        });
                        $('#addStaff').modal('hide');
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            });

            // ADD DOCTOR 
            $('#addDoctorForm').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: '/api/doctor', 
                    type: 'POST',
                    data: {
                        name: $('#name').val(),
                        mail: $('#mail').val(),
                        phone: $('#phone').val(),
                        degree: $('#degree').val(),
                        status: $('input[name="status"]:checked').val(),
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') 
                    },
                    success: function(response) {
                        doctorData();
                        Swal.fire({
                            title: "Doctor !",
                            icon: "success",
                            text: "Doctor Added Successfully.",
                        });
                        $('#addDoctor').modal('hide');
                    },
                    error: function(xhr) {
                        alert('An error occurred: ' + xhr.responseText);
                    }
                });
            });
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
            productData();
            const newRow = `
                <tr>
                    <td id="row" class="table-row-id row_id d-none product">${id}</td>
                    <td class="table-row">
                        <select data-enable-search="true" class="form-control product" name="productName[]" id="product_name${id}">
                            <option value="">Choose Product</option>
                        </select>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="category" id="category${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="subCategory" id="subCategory${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <select data-enable-search="true" class="form-control" name="pack[]" id="pack${id}">
                            <option value="">Choose Pack</option>
                        </select>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="qty" id="qty${id}" />
                        </div>
                    </td>
                    
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="number" class="form-control" name="unit_value[]" id="unit_value${id}" />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="discount[]" id="discount${id}" />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control totalAmount" value="0" name="totalAmount[]" id="totalAmount${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <span class="delete-icon btn btn-danger text-white p-2 px-1" onclick="deleteRow(this)">
                            <i class="fas fa-trash"></i>
                        </span>
                    </td>
                </tr>
            `;

            $('table tbody').append(newRow);

            $(`#qty${id}, #unit_value${id}, #discount${id}`).on('input', function() {
                const row = $(this).closest('tr');
                updateTotalForRow(row);
                updateOverallTotal();
            });
        }

        function updateTotalForRow(row) {
            const qty = parseFloat(row.find('input[name="qty"]').val()) || 0;
            const unitValue = parseFloat(row.find('input[name="unit_value[]"]').val()) || 0;
            const discount = parseFloat(row.find('input[name="discount[]"]').val()) || 0;

            const totalAmount = (qty * unitValue) - ((qty * unitValue) * discount / 100);
            row.find('input[name="totalAmount[]"]').val(totalAmount.toFixed(2));
        }

        $(document).on('input', 'input[name="qty"], input[name="unit_value[]"], input[name="discount[]"]', function() {
            const row = $(this).closest('tr');
            updateTotalForRow(row);
            updateOverallTotal();
        });

        function updateOverallTotal() {
            let overallTotal = 0;
            $('input[name="totalAmount[]"]').each(function() {
                overallTotal += parseFloat($(this).val()) || 0;
            });
            $('#totalAmount').text(overallTotal.toFixed(2));
        }

        $(document).ready(function() {
            $('tr').each(function() {
                updateTotalForRow($(this));
            });
            updateOverallTotal();
        });


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
                const unitValue = row.querySelector(`[name="unit_value[]"]`).value;
                const discount = row.querySelector(`[name="discount[]"]`).value;
                const totalAmount = row.querySelector(`[name="totalAmount[]"]`).value;

                products.push({
                    productId,
                    category,
                    subCategory,
                    pack,
                    qty,
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
            console.log(formattedDate);
            const payload = {
                staff_phone: $('#staff_phone').val(),
                doctor_name: $('#doctor_name').val(),
                paymentType: $('#paymentType').val(),
                invoiceNo: $('#invoiceNo').val(),
                staff_name: $('#staff_name option:selected').text(),
                total_amt: $('#totalAmount').text(),
                billing_date:formattedDate,
                billingType:"Staff Billing",
                product_billings: products
            };

            console.log(payload);

            return payload;
        }

    </script>
@endsection