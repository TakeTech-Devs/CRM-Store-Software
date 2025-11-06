@extends('layouts.dashboard')

@section('title', 'Create Customer Billing')

@section('content')
<style>
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
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Create Customer Billing</h2>
        <div class="text-right">
            <a href="{{ url('store/customer/billing') }}" class="btn btn-secondary btn-sm">View Customer Billing
                List</a>
        </div>
    </div>

    <div class="mt-4 position-relative">
        <form id="customerBillingCreate">
            @csrf
            <div class="form-row mb-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_phone">Customer Phone Number</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="customer_phone[]" id="customer_phone"
                                class="form-control">
                                <option value="">Choose Customer Phone Number...</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal"
                                data-target="#addCustomer">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="doctor_name">Doctor Name</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="doctor_name[]" id="doctor_name"
                                class="form-control">
                                <option value="">Choose Doctor Name...</option>
                            </select>
                            <button type="button" class="btn btn-sm btn-primary mx-3" data-toggle="modal"
                                data-target="#addDoctor">
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
                            <input type="text" name="customer_name" id="customer_name" class="form-control" disabled>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="paymentType">Payment Type</label>
                        <div class="form-group d-flex align-items-center">
                            <select data-enable-search="true" name="paymentType[]" id="paymentType"
                                class="form-control">
                                <option value="">Choose Payment Type...</option>
                                <option value="online">Online</option>
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="invoiceNo">Invoice No</label>
                        <div class="form-group d-flex align-items-center">
                            <input type="text" name="invoiceNo" id="invoiceNo" class="form-control"
                                value="{{ uniqid() }}" disabled>
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
                            <th>Remaining Qty</th>
                            <th>Unit Value</th>
                            <th>Qty</th>
                            <th>Discount</th>
                            <th>Total Amount</th>
                            <th>GST Rate (%)</th>
                            <th>GST Amount</th>
                            <th>CGST</th>
                            <th>SGST</th>

                        </tr>
                    </thead>
                    <tbody id="formBody">

                    </tbody>
                </table>
            </div>

            <button type="button" name="add_row" id="add_row"
                class="btn btn-sm btn-secondary mb-3  mt-3 float-right ml-3">
                Add New Row
            </button>

            <div class="form-group text-right mt-3 mx-4 row d-flex justify-content-end">
                <div class="col-md-2">
                    <label for="totalAmount">Total Amount: </label>
                    <span id="totalAmount">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalGST">GST: </label>
                    <span id="totalGST">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalCGST">CGST: </label>
                    <span id="totalCGST">0</span>
                </div>
                <div class="col-md-2">
                    <label for="totalSGST">SGST: </label>
                    <span id="totalSGST">0</span>
                </div>
            </div>

            <div class="form-group text-right">
                <button type="button" name="submitBilling" id="submitBilling" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>

<!-- ADD CUSTOMER PHONE NUMBER  -->
<div class="modal fade" id="addCustomer" tabindex="-1" role="dialog" aria-labelledby="addCustomerLabel"
    aria-hidden="true">
    <div class="modal-dialog container" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                <h5 class="modal-title" id="addCustomerLabel">Add Customer</h5>
            </div>
            <div class="modal-body">
                <form id="addCustomerForm" class="container">
                    <div class="form-group">
                        <label for="name">Customer Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="mail">Customer Mail</label>
                        <input type="email" class="form-control" id="mail" name="mail" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Customer Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group d-none">
                        <label>Status:</label>
                        <div class="form-group d-flex justify-content-start align-items-center">
                            <div class="form-check mx-3">
                                <input type="radio" class="form-check-input" id="statusActive" name="status" value="1"
                                    checked>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="statusInactive" name="status"
                                    value="0">
                                <label class="form-check-label" for="statusInactive">Deactive</label>
                            </div>
                        </div>
                    </div>
                    <div class="save-button d-flex align-items-center justify-content-center">
                        <button type="submit" id="addCustomerFormBtn" class="btn btn-success mx-2">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            aria-label="Close">Cancel</button>
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
                                <input type="radio" class="form-check-input" id="statusActive" name="status" value="1"
                                    checked>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="statusInactive" name="status"
                                    value="0">
                                <label class="form-check-label" for="statusInactive">Deactive</label>
                            </div>
                        </div>
                    </div>
                    <div class="save-button d-flex align-items-center justify-content-center">
                        <button type="submit" id="addDoctorFormBtn" class="btn btn-success mx-2">Save</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"
                            aria-label="Close">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
            count = 1
            addNewRow(count)
            customerData(null)
            doctorData()
            $(document).on('change', '#customer_phone', function () {
                customerData(this.value)
            });

            $(document).on('change', '.product', function () {
                const count = $(this).data('count'); 

                ajaxGetData(`/products?id=${this.value}`, (res) => {

                    if (res?.data && res.data.length > 0) {
                        const productData = res.data[0];
                        categoryData(productData.category_id, count);
                        subCategoryData(productData.sub_category_id, count);
                        document.getElementById(`gstRate${count}`).value = productData.gst;


                        console.log("Product data: ",productData);
                    } else {
                        console.error('Product data not found');
                    }
                });

                ajaxGetData(`/api/purchase_request?id=${this.value}`, (res) => {
                    if (Array.isArray(res.purchase_request) && res.purchase_request.length > 0) {
                        const requestData = res.purchase_request.find(item => item.id);
                        console.log("Request data Find Statement:", requestData.id);
                        console.log("request data: ",requestData);
                        
                        

                        if (requestData) {
                            $(`#qty${count}`).val(requestData.qty);
                            
                            if (typeof priceData === 'function') {
                                priceData(requestData.price_id, count);
                            } else {
                                console.error('priceData function is not defined');
                            }
                            
                            if (typeof packData === 'function') {
                                packData(requestData.pack_id, count);
                            } else {
                                console.error('packData function is not defined');
                            }
                        } else {
                            $(`#qty${count}`).val('');
                            $(`#unit_value${count}`).val('');
                            $(`#pack${count}`).val('');
                        }
                    } else {
                        $(`#qty${count}`).val('');
                        $(`#unit_value${count}`).val('');
                        $(`#pack${count}`).val('');
                    }
                });
            });


            

            $(document).on('click', '#add_row', function () {
                count = count + 1;
                addNewRow(count)
            })

            // CREATING BILL
            $(document).on('click', '#submitBilling', function () {
                const payload = gatherFormData(); 
                let csrfToken = $('meta[name="csrf-token"]').attr('content');

                ajaxPostData('/customer/billing/create', payload, csrfToken, (response) => {
                    const billId = response.bill_id;
                    // Show success message briefly, then automatically show the bill
                    Swal.fire({
                        title: "Success!",
                        icon: "success",
                        text: "Customer Billing Added Successfully.",
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        // Automatically view and print the bill
                        viewAndPrintBill(billId);
                    });
                }, (error) => {
                    Swal.fire({
                        title: "Error!",
                        icon: "error",
                        text: "Failed to create billing. Please try again.",
                    });
                });
            });



            // ADDING CUSTOMER 
            $('#addCustomer').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: '/api/customer', 
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
                        customerData();
                        Swal.fire({
                            title: "Customer !",
                            icon: "success",
                            text: "Customer Added Successfully.",
                        });
                        $('#addCustomer').modal('hide');
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
                    url: 'api/doctor', 
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

        function customerData(id) { 
            if (id) {
                ajaxGetData(`/customers?id=${id}`, (res)=>{
                    $('#customer_name').val(res?.data[0]?.name || '');
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

        function updateProductQuantity(product_id, assigned_qty) {
            ajaxPostData('/api/update_product_qty', { 
                product_id: product_id, 
                assigned_qty: assigned_qty 
            }, $('meta[name="csrf-token"]').attr('content'), (response) => {
                if (response.success) {
                    console.log(`Product ID ${product_id} quantity updated successfully`);
                } else {
                    console.error(`Failed to update quantity for Product ID ${product_id}`);
                }
            });
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
            ajaxGetData(`/api/purchase_request`, (res) => {
                
                for (let index = 0; index < res?.purchase_request?.length; index++) {
                    const element = res?.purchase_request[index];
                    productData_fetch(element?.product_id, element?.pack_id, count);
                }
            });
        }

        function productData_fetch(id, pack_id, count) {
            ajaxGetData(`/pack?id=${pack_id}`, (res) => {
                const pack_name = res?.data[0].pack_name;

                ajaxGetData(`/products?id=${id}`, (res) => {
                    $(`.product[data-count="${count}"]`).append(`<option value="${res?.data[0].id}" > ${res?.data[0].product_name}-${pack_name} </option>`);
                });
            });
        }




        function categoryData(id, count) {
            ajaxGetData(`/category?id=${id}`, (res)=>{
                $(`#category${count}`).val(res?.data[0].category_name)
            })
        }

        function packData(id, count){
            console.log(id)
            ajaxGetData(`/pack?id=${id}`, (res) =>{
                $(`#pack${count}`).val(res?.data[0].pack_name)

            })
        }
        function priceData(id, count){
            ajaxGetData(`/price?id=${id}`, (res) =>{
                $(`#unit_value${count}`).val(res?.data[0].price_name)

            })
        }

        function subCategoryData(id, count) {
            ajaxGetData(`/sub-category?id=${id}`, (res)=>{
                $(`#subCategory${count}`).val(res?.data[0].sub_category_name)
            })
        }

        function addNewRow(id) {
            productData(); 

            const newRow = `
                <tr id="row_${id}">
                    <td class="table-row-id row_id d-none product">${id}</td>
                    <td class="table-row">
                        <select data-enable-search="true" class="form-control product" data-count="${id}" name="productName[]" id="product_name${id}">
                            <option value="">Choose Product</option>
                        </select>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="category[]" id="category${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="subCategory[]" id="subCategory${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="pack[]" id="pack${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="number" class="form-control" name="total_qty[]" id="qty${id}" readonly/>
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="unit_value[]" id="unit_value${id}" readonly />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="assignQty[]" id="assignQty${id}"  />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="number" class="form-control" name="discount[]" id="discount${id}" value="0" />
                        </div>
                    </td>
                    <td class="table-row">
                        <div class="form-group d-flex align-items-center">
                            <input type="text" class="form-control" name="totalAmount[]" id="totalAmount${id}" readonly />
                        </div>
                    </td>
                    <td><input type="number" class="form-control gstRate" name="gstRate[]" id="gstRate${id}" readonly /></td>
                    <td><input type="text" class="form-control gstAmount" name="gstAmount[]" id="gstAmount${id}" readonly /></td>
                    <td><input type="text" class="form-control cgst" name="cgst[]" id="cgst${id}" readonly /></td>
                    <td><input type="text" class="form-control sgst" name="sgst[]" id="sgst${id}" readonly /></td>

                </tr>
            `;
            $('#formBody').append(newRow);
        }

        function updateTotalForRow(row) {
            const qty = parseFloat(row.find('input[name="assignQty[]"]').val()) || 0;
            const unitValue = parseFloat(row.find('input[name="unit_value[]"]').val()) || 0;
            const discount = parseFloat(row.find('input[name="discount[]"]').val()) || 0;
            const gstRate = parseFloat(row.find('input[name="gstRate[]"]').val()) || 0;

            // Calculate base amount (before discount)
            const baseAmount = qty * unitValue;

            // Discounted amount
            const totalAmount = baseAmount - (baseAmount * discount / 100);
            row.find('input[name="totalAmount[]"]').val(totalAmount.toFixed(2));

            // GST Calculation
            const gstAmount = baseAmount * (gstRate / 100);
            const cgst = gstAmount / 2;
            const sgst = gstAmount / 2;

            // Set values in fields
            row.find('input[name="gstAmount[]"]').val(gstAmount.toFixed(2));
            row.find('input[name="cgst[]"]').val(cgst.toFixed(2));
            row.find('input[name="sgst[]"]').val(sgst.toFixed(2));

            console.log('amounts', {
                qty, unitValue, discount, gstRate,
                baseAmount, totalAmount, gstAmount, cgst, sgst
            }); 
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

        function calculateTotalAmount() {
            let totalAmount = 0;
            let totalGST = 0;
            let totalCGST = 0;
            let totalSGST = 0;

            $('#formBody').find('tr').each(function () {
                const qty = parseFloat($(this).find('[name="assignQty[]"]').val()) || 0;
                const unitValue = parseFloat($(this).find('[name="unit_value[]"]').val()) || 0;
                const discount = parseFloat($(this).find('[name="discount[]"]').val()) || 0;
                const gstRate = parseFloat($(this).find('[name="gstRate[]"]').val()) || 0;

                // --- base amount after discount ---
                const discountDecimal = discount / 100;
                const discountAmount = qty * unitValue * discountDecimal;
                const amount = (qty * unitValue) - discountAmount;

                $(this).find('[name="totalAmount[]"]').val(amount.toFixed(2));
                totalAmount += amount;

                // --- GST calculations ---
                const gstAmount = (amount * gstRate) / 100;
                const cgst = gstAmount / 2;
                const sgst = gstAmount / 2;

                $(this).find('[name="gstAmount[]"]').val(gstAmount.toFixed(2));
                $(this).find('[name="cgst[]"]').val(cgst.toFixed(2));
                $(this).find('[name="sgst[]"]').val(sgst.toFixed(2));

                totalGST += gstAmount;
                totalCGST += cgst;
                totalSGST += sgst;
            });

            // --- Update footer totals ---
            $('#totalAmount').text(totalAmount.toFixed(2));
            $('#totalGST').text(totalGST.toFixed(2));
            $('#totalCGST').text(totalCGST.toFixed(2));
            $('#totalSGST').text(totalSGST.toFixed(2));
        }

        $(document).on('input', '[name="qty[]"], [name="unitValue[]"], [name="discount[]"]', function () {
            calculateTotalAmount();
        });




        function gatherFormData() {
            const rows = document.querySelectorAll('#dynamicForm tbody tr');
            const products = [];

            rows.forEach(row => {
                const productId = row.querySelector(`[name="productName[]"]`).value;
                const category = row.querySelector(`[name="category[]"]`).value;
                const subCategory = row.querySelector(`[name="subCategory[]"]`).value;
                const pack = row.querySelector(`[name="pack[]"]`).value;
                const unitValue = row.querySelector(`[name="unit_value[]"]`).value;
                const qty = row.querySelector(`[name="assignQty[]"]`).value;
                const discount = row.querySelector(`[name="discount[]"]`).value;
                const totalAmount = row.querySelector(`[name="totalAmount[]"]`).value;
                const gstRate = row.querySelector(`[name="gstRate[]"]`).value;
                const gstAmount = row.querySelector(`[name="gstAmount[]"]`).value;
                const cgst = row.querySelector(`[name="cgst[]"]`).value;
                const sgst = row.querySelector(`[name="sgst[]"]`).value;

               
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
            let formattedDate = `${year}-${month}-${day}`;
            const payload = {
                billingType: "customer",
                customer_phone: $('#customer_phone').val(),
                doctor_name: $('#doctor_name').val(),
                paymentType: $('#paymentType').val(),
                invoiceNo: $('#invoiceNo').val(),
                customer_name: $('#customer_name').val(),
                total_amt: $('#totalAmount').text(),
                gstAmount: $('#totalGST').text(),
                cgst: $('#totalCGST').text(),
                sgst: $('#totalSGST').text(),
                billing_date:formattedDate,
                billingType:"Customer Billing",
                product_billings: products
            };

            console.log(payload);

            return payload;
        }

        // Function to view and print the bill
        function viewAndPrintBill(billId) {
            // Fetch bill details and show print modal
            $.ajax({
                url: `/api/customer/bill/${billId}`,
                method: 'GET',
                success: function(response) {
                    if (response.status === 200) {
                        populatePrintModal(response.data);
                        $('#printModal').modal('show');
                    } else {
                        Swal.fire({
                            title: "Error!",
                            icon: "error",
                            text: "Failed to fetch bill details.",
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        title: "Error!",
                        icon: "error",
                        text: "Failed to fetch bill details.",
                    });
                }
            });
        }

        // Function to populate print modal with bill data
        function populatePrintModal(data) {
            let bill = data.bill;
            let items = data.items;
            let store = data.store;

            // Update bill information
            $('.invoiceNo').text(bill.invoiceNo);
            $('.billingDate').text(bill.billing_date);
            $('.customerName').text(bill.customer_name);
            $('.drName').text(bill.doctor_name || 'N/A');
            $('.grandTotal').text(bill.total_amt);
            $('.totalGST').text(bill.gst || 0);
            $('.totalCGST').text(bill.cgst || 0);
            $('.totalSGST').text(bill.sgst || 0);

            // Update store information
            if (store) {
                $('.storeAddress').text(store.store_address || 'Not Provided');
                $('.dlNumber').text(store.dl_number || 'Not Provided');
                $('.helplineNumber').text(store.helpline_number || 'Not Provided');
            }

            // Clear and populate items table
            $('#invoice_table tbody').empty();
            items.forEach((item, index) => {
                $('#invoice_table tbody').append(`
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.category || 'N/A'}</td>
                        <td>${item.qty || '0'}</td>
                        <td>${item.pack || 'N/A'}</td>
                        <td>${item.unitValue || '0.00'}/-</td>
                        <td>${item.totalAmount || '0.00'}/-</td>
                    </tr>
                `);
            });
        }

        // Function to reset the form
        function resetForm() {
            // Reset form fields
            $('#customerBillingCreate')[0].reset();
            $('#customer_name').val('');
            $('#invoiceNo').val('{{ uniqid() }}');
            
            // Clear the dynamic table
            $('#formBody').empty();
            count = 0;
            
            // Reset totals
            $('#totalAmount').text('0');
            $('#totalGST').text('0');
            $('#totalCGST').text('0');
            $('#totalSGST').text('0');
        }

        // Add print modal to the create page
        $(document).ready(function() {
            // Add print modal HTML if it doesn't exist
            if ($('#printModal').length === 0) {
                $('body').append(`
                    <!-- PRINT MODAL -->
                    <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-md" role="document">
                            <div class="modal-content" style="border: none;">
                                <div class="modal-body" style="padding: 10px; width:100%;">
                                    <div id="printArea">
                                        <table class="border p-2">
                                            <h1 class="text-center fw-bold" style="border-bottom:3px solid; border-top:3px solid; padding:5px 0px !important;">RIGHT AID</h1>
                                            <div class="col-md-12">
                                                <div class="details d-flex align-items-start justify-content-between" style="margin-bottom: 15px !important; margin-top: 15px !important;">
                                                    <div class="left">
                                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            Invoice No: <span class="invoiceNo" style="font-size: 14px !important; font-weight:400;"></span>
                                                        </p>
                                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">Date:
                                                            <span class="billingDate" style="font-size: 14px !important; font-weight:400;"></span>
                                                        </p>
                                                        <p style="width: 150%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            GSTIN: <span class="gstin" style="font-size: 14px !important; font-weight:400;">GST123456</span>
                                                        </p>
                                                        <p style="width: 200%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            Customer: <span class="customerName" style="font-size: 14px !important; font-weight:400;"></span>
                                                        </p>
                                                        <p style="width: 200%; font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            Dr Name: <span class="drName" style="font-size: 14px !important; font-weight:400;"></span>
                                                        </p>
                                                    </div>
                                                    <div class="right">
                                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">DL. No. : 
                                                            <span class="dlNumber" style="font-size: 14px !important; font-weight:400;">HL-1046-S</span>
                                                        </p>
                                                        <p style="font-size: 14px !important; font-weight:700; line-height: 5px;">
                                                            Helpline : <span class="helplineNumber" style="font-size: 14px !important; font-weight:400;">8100968101</span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="product-details text-center m-0 col-md-12">
                                                <table class="w-100" id="invoice_table">
                                                    <thead style="border-top:3px solid; text-align: center; border-bottom:3px solid; padding-top: 10px !important;">
                                                        <tr>
                                                            <th>SNo.</th>
                                                            <th>Desc</th>
                                                            <th>Qty</th>
                                                            <th>Pack</th>
                                                            <th>MRP</th>
                                                            <th>Amount</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody style="border-bottom:3px solid; text-align: center; padding: 15px 0px !important;">
                                                    </tbody>
                                                </table>
                                                <div class="col-md-12 my-3" style="text-align:right !important">
                                                    <div><strong>Grand Total:</strong> <span class="grandTotal">0</span>/-</div>
                                                    <div><strong>GST:</strong> <span class="totalGST">0</span>/-</div>
                                                    <div><strong>CGST:</strong> <span class="totalCGST">0</span>/-</div>
                                                    <div><strong>SGST:</strong> <span class="totalSGST">0</span>/-</div>
                                                </div>
                                                <div class="address text-center" style="font-size: 12px !important; margin-top:15px">
                                                    <span>Address : <Span class="storeAddress"></Span></span><br>
                                                    <span>Reg Address : 211. Rain Ram Monan Rov Road Shop No :10, Block-1 Ground Floor, "Merlin Grove Behala Kolkata-700008</span>
                                                </div>
                                                <div class="note text-center" style="font-size: 12px !important;">
                                                    <p>Medicine once sold would not be returned or exchanged</p>
                                                    <span>******** Thank You ********</span>
                                                </div>
                                            </div>
                                        </table>
                                    </div>
                                    <div style="text-align:center; width: 100%; margin-bottom: 25px !important;">
                                        <button class="btn btn-sm shadow btn-primary" id="printButton">Print</button>
                                        <button class="btn btn-sm shadow btn-success" id="createNewBill" style="margin-left: 10px;">Create New Bill</button>
                                        <button class="btn btn-sm shadow btn-secondary" id="backToList" style="margin-left: 10px;">Back to List</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `);

                // Add print functionality
                $(document).on('click', '#printButton', function() {
                    printModalContent();
                });

                // Add create new bill functionality
                $(document).on('click', '#createNewBill', function() {
                   window.location.href = "/store/customer/create/billing";
                });

                // Add back to list functionality
                $(document).on('click', '#backToList', function() {
                    window.location.href = "/store/customer/billing";
                });
            }
        });

        // Print function
        function printModalContent() {
            var printContent = document.getElementById("printArea").innerHTML;
            var originalContent = document.body.innerHTML;

            // Remove all buttons from print content
            printContent = printContent.replace(/<button[^>]*>.*?<\/button>/g, '');

            var modalBackdrop = document.getElementsByClassName("modal-backdrop")[0];
            if (modalBackdrop) {
                modalBackdrop.remove();
            }

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            $('#printModal').modal('show');
        }

</script>
@endsection