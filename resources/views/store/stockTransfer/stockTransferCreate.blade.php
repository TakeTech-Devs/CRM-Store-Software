@extends('layouts.dashboard')

@section('title', 'Create Store Stock Transfer')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2 class="text-dark">Create Store Transfer Billing</h2>
            <div class="text-right">
                <a href="{{ url('store/stock/transfer') }}" class="btn btn-secondary btn-sm">View Transfer List</a>
            </div>
        </div>

        <div class="mt-4 position-relative">
            <form id="customerBillingCreate">
                @csrf
                <div class="form-row mb-2">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="store_id">Store ID</label>
                            <input type="text" name="store_id" id="store_id" class="form-control" value="{{ session('storeId', 'Store not found') }}" readonly>
                        </div>
                    </div>

                    <div class="col-md-4">
                        
                        <div class="form-group">
                            <label for="store">Store To</label>
                            <select data-enable-search="true"name="store_to" id="store_to" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="transfer_id">Transfer ID</label>
                            <input type="text" name="transfer_id" id="transfer_id" class="form-control" value="{{ uniqid() }}" readonly>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dynamicForm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Pack</th>
                                <th>Remaining Qty</th>
                                <th>Unit Value</th>
                                <th>Assign Qty</th>
                                <th>Discount</th>
                                <th>Total Amount</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="formBody">
                        </tbody>
                    </table>
                </div>

                <button type="button" id="add_row" class="btn btn-sm btn-secondary mb-3 float-right ml-3">Add New Row</button>

                <div class="form-group text-right">
                    <label for="totalAmount">Total Amount: </label>
                    <span id="totalAmount">0</span>
                </div>

                <div class="form-group text-right">
                    <button type="button" id="submitBilling" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            StoresList();
            function StoresList() {
                $.ajax({
                    url: 'http://localhost:8000/api/stores',
                    type: 'GET',
                    success: function(response) {
                        var stores = response.data;
                        $('#store_to').html('<option value="">Select Store</option>');
                        $.each(stores, function(index, store) {
                            $('#store_to').append('<option value="' + store.id + '">' + store.name + '</option>');
                        });
                    }
                });
            }

            count = 0;

            $(document).on('change', '.product', function () {
                const count = $(this).data('count'); 
                console.log("This Value", this.value);

                ajaxGetData(`/products?id=${this.value}`, (res) => {

                    if (res?.data && res.data.length > 0) {
                        const productData = res.data[0];
                        categoryData(productData.category_id, count);
                        subCategoryData(productData.sub_category_id, count);
                        // console.log("Product data: ",productData);
                    } else {
                        console.error('Product data not found');
                    }
                });
                // console.log(this.value);
                
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

            $(document).on('click', '#submitBilling', function () {
                const payload = gatherFormData();
                let csrfToken = $('meta[name="csrf-token"]').attr('content');
                
                ajaxPostData('http://localhost:8000/api/store-transfer', payload, csrfToken, (response) => {
                    if (response.status === 200) {
                        Swal.fire({
                            title: "Store Stock Billing!",
                            icon: "success",
                            text: response.data || "Customer Billing Added Successfully.",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "/store/stock/transfer";
                            }
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            icon: "error",
                            text: response || "Something went wrong.", 
                        });
                    }
                }, (error) => {
                    Swal.fire({
                        title: "Error!",
                        icon: "error",
                        text: error.responseJSON || "An error occurred while processing the request.",
                    });
                });
            });
   
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
                    <tr>
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
                                <input type="number" class="form-control" name="qty[]" id="qty${id}" readonly/>
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
                                <input type="number" class="form-control" name="discount[]" id="discount${id}" />
                            </div>
                        </td>
                        <td class="table-row">
                            <div class="form-group d-flex align-items-center">
                                <input type="text" class="form-control" name="totalAmount[]" id="totalAmount${id}" readonly />
                            </div>
                        </td>
                        <td class="table-row">
                            <button type="button" class="btn btn-sm btn-danger remove-row" data-count="${id}"><i class="fa fa-trash"></i></button>
                        </td>
                    </tr>
                `;
                $('#formBody').append(newRow);
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

            function calculateTotalAmount() {
                let totalAmount = 0;
                $('#formBody').find('tr').each(function () {
                    const qty = parseFloat($(this).find('[name="assignQty[]"]').val()) || 0;
                    const unitValue = parseFloat($(this).find('[name="unit_value[]"]').val()) || 0;
                    const discount = parseFloat($(this).find('[name="discount[]"]').val()) || 0;
                    const discountDecimal = discount / 100;
                    const discountAmount = qty * unitValue * discountDecimal;
                    const amount = (qty * unitValue) - discountAmount;

                    $(this).find('[name="totalAmount[]"]').val(amount.toFixed(2));
                    totalAmount += amount;
                });
                $('#totalAmount').text(totalAmount.toFixed(2));
            }

            $(document).on('input', '[name="qty[]"], [name="unitValue[]"], [name="discount[]"]', function () {
                calculateTotalAmount();
            });

            function deleteRow(element) {
                const row = element.closest("tr");
                row.remove();
                calculateTotalAmount(); 
            }
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
                

            }
        });
    </script>
@endsection
