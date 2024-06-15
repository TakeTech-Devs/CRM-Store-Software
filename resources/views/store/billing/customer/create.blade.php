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
            <form action="{{ url('') }}" method="POST" id="customerBillingCreate">
                @csrf
                <div class="form-row mb-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="customer_phone">Customer Phone Number</label>
                            <div class="form-group d-flex align-items-center">
                                <select data-enable-search="true" name="customer_phone[]" id="customer_phone" class="form-control">
                                    <option value="">Choose Customer Phone Number...</option>
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
                            <label for="customer_name">Customer Name</label>
                            <div class="form-group d-flex align-items-center">
                                <select data-enable-search="true" name="customer_name[]" id="customer_name" class="form-control" disabled>
                                    <option value="">Choose Customer Name...</option>
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
                                <!-- <th>MRP</th> -->
                                <th>Unit Value</th>
                                <th>Discount</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="formBody">
                            {{-- <tr>
                                <td id="row" class="row_id d-none">1</td>
                                <td>
                                    <select data-enable-search="true" class="form-control product" name="productName[]" id="product_name1">
                                        <option value="">Choose Product</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="form-group d-flex align-items-center">
                                        <input type="text" class="form-control" name="category" id="category1" disabled />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group d-flex align-items-center">
                                        <input type="text" class="form-control" name="subCategory" id="subCategory1" disabled />
                                    </div>
                                </td>
                                <td>
                                    <select data-enable-search="true" class="form-control" name="pack[]" id="pack1">
                                        <option value="">Choose Pack</option>
                                    </select>
                                </td>
                                <td>
                                    <div class="form-group d-flex align-items-center">
                                        <input type="text" class="form-control" name="qty" id="qty1" />
                                    </div>
                                </td>
                                <!-- <td>
                                    <select data-enable-search="true" class="form-control" name="mrp[]" id="mrp1">
                                        <option value="">Choose MRP</option>
                                    </select>
                                </td> -->
                                <td>
                                    <div class="form-group d-flex align-items-center">
                                        <input type="number" class="form-control" name="unit_value[]" id="unit_value1" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group d-flex align-items-center">
                                        <input type="text" class="form-control" name="discount[]" id="discount1" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group d-flex align-items-center">
                                        <input type="text" class="form-control totalAmount" value="0" name="totalAmount[]" id="totalAmount1" readonly />
                                    </div>
                                </td>
                                <td>
                                    <span class="delete-icon btn btn-danger text-white p-2 px-1" onclick="deleteRow(this)">
                                        <i class="fas fa-trash"></i>
                                    </span>
                                </td>
                            </tr> --}}
                        </tbody>
                    </table>
                </div>

                <button type="button" name="add_row" id="add_row" class="btn btn-sm btn-info mb-3">
                    Add More
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
    
    <!-- ADD STAFF PHONE NUMBER  -->
    <div class="modal fade" id="addStaffNumber" tabindex="-1" role="dialog" aria-labelledby="addStaffNumberLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addStaffNumberLabel">Add Customer Number</h5>

                </div>
                <div class="modal-body">
                    <form id="addStaffNumber" class="container">
                        <div class="form-group">
                            <label for="customerPhone">Customer Phone Number</label>
                            <input type="text" class="form-control" id="customerPhone" name="customerPhone" placeholder="Enter Customer Phone Number" required>
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
                const payload = gatherFormData();
                console.log(typeof  (payload));
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                ajaxPostData('/customer/billing/create', payload, csrfToken, (response)=>{

                    console.log(res);
                })
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
                            <input type="text" class="form-control" name="category" id="category${id}" disabled />
                        </div>        
                    </td>
                    <td>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="subCategory" id="subCategory${id}" disabled />
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

            // Add event listeners to the newly added row
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
            // Get today's date
            let today = new Date();

            // Get the day, month, and year
            let day = String(today.getDate()).padStart(2, '0');
            let month = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
            let year = today.getFullYear();

            // Combine them to form the desired format
            let formattedDate = `${day}/${month}/${year}`;

            // console.log(formattedDate);


            const payload = {
                customer_phone: $('#customer_phone').val(),
                doctor_name: $('#doctor_name').val(),
                paymentType: $('#paymentType').val(),
                invoiceNo: $('#invoiceNo').val(),
                customer_name: $('#customer_name option:selected').text(),
                total_amt: $('#totalAmount').text(),
                biilling_date:formattedDate,
                product_billings: products
            };

            return payload;
        }

    </script>
@endsection