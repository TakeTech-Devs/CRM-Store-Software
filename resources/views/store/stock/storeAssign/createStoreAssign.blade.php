<!-- resources/views/admin/purchase/create.blade.php -->
@extends('layouts.dashboard')
@section('title', 'New Store Assign')

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
            <h3 class="text-dark">New Store Assign</h3>
            <a href="{{ url('admin/store/assign') }}" class="btn btn-secondary btn-sm">View Store Assign List</a>
        </div>

        <div class="mt-2">
            <div class="form-group d-flex align-items-center">
                <div class="purchase-bill">
                    <label for="supplier" class="text-secondary fs-6">Purchase Bill No</label>
                    <select name="purchase_bill" id="purchase_bill" class="form-control mr-2">
                        <option value="">Select Purchase Bill...</option>
                    </select>
                </div>
                <div class="store mx-3">
                    <label for="store" class="text-secondary fs-6">Select Store</label>
                    <select name="store" id="store" class="form-control mr-2">
                        <option value="">Select Assigned Store...</option>
                    </select>
                </div>
            </div>

            <div class="store-details my-2 mb-3">
                <table>
                    <tr>
                        <th>Store Name: &nbsp;</th>
                        <td id="store_name"></td>
                    </tr>
                    <tr>
                        <th>Store ID: &nbsp;</th>
                        <td id="store_id"></td>
                    </tr>
                    <tr>
                        <th>Assign Bill No: &nbsp;</th>
                        <td id="assign_bill_no"></td>
                    </tr>
                </table>
            </div>

            <div class="table-responsive border">
                <table class="table " id="dynamicForm">
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
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="formBody">
                        
                    </tbody>
                </table>
            </div>
            <div class="new-row d-flex align-items-center justify-content-end mt-3">
                <p class="total-amount mx-3">Total Amount: <span id="totalAmount">0</span></p>
                <button type="button" class="btn btn-secondary text-light mb-2" id="addNewRow">Add New
                    Row</button>
            </div>

            <div class="d-flex align-items-center justify-content-end my-3">
                <button type="button" class="btn btn-info mx-1" id="assign">Assign</button>
                <button type="button" class="btn btn-danger mx-1">Cancel</button>
            </div>
        </div>
    </div>

    <script>
        var rowID = 1;

        $(document).ready(function() {
            getPurchaseBillNo();
            getStores();
            getBrands();
            getCategories();
            getPacks();
            getPrices();

            $(document).on('change', `#store`, function() {
                let id = $(this).val();
                if (id) {
                    ajaxGetData(`/api/stores/${id}`, (response) => {
                        let random_gen = generateRandomPattern(response?.data?.name);
                        $(document).find('#store_name').text(response?.data?.name);
                        $(document).find('#store_id').text(response?.data?.store_meta_id);
                        $(document).find('#assign_bill_no').text(random_gen);
                    })
                } else {
                    $(document).find('#store_name').text("");
                    $(document).find('#store_id').text("");
                    $(document).find('#assign_bill_no').text("");
                }
            })

            $(document).on('change', `#category${rowID}`, function() {
                let categoryId = $(this).val();
                getSubCategories(categoryId, null, rowID);
            })

            $(document).on('change', `#sub_category${rowID}`, function() {
                let sub_category_id = $(this).val();
                let category_id = $(`#category${rowID}`).val();
                let brand_id = $(`#brand${rowID}`).val();
                getProducts(brand_id, category_id, sub_category_id)

            })
            $(document).on('change', `#purchase_bill`, function() {
                let purchase_stock_id = $(this).val();
                $('tbody').html("");
                ajaxGetData(`/api/purchase/stock/entries/${purchase_stock_id}`, (res) => {
                    for (let index = 0; index < res?.data?.length; index++) {
                        const element = res?.data[index];
                        appendRowHtml(element)

                    }
                })
            })

            $(document).on('click', '#assign', function() {
                let purchase_bill = $('#purchase_bill').val();
                let store_id = $('#store').val();
                let assign_bill_no = $('#assign_bill_no').text();
                let totalAmount = $('#totalAmount').text();
                let purchaseRequest = [];
                $('#formBody tr').each(function() {
                    let rowID = $(this).find('td#row').text();
                    console.log(rowID);
                    let brand_id = $(this).find(`#brand${rowID}`).val();
                    let product_id = $(this).find(`#product_name${rowID}`).val();
                    let pack_id = $(this).find(`#pack${rowID}`).val();
                    let price_id = $(this).find(`#mrp${rowID}`).val();
                    let qty = $(this).find(`#quantity${rowID}`).val();
                    let exp_date = $(this).find(`#expiryDate${rowID}`).val();

                    // Push the extracted values into the purchaseRequest array
                    purchaseRequest.push({
                        brand_id: brand_id,
                        product_id: product_id,
                        pack_id: pack_id,
                        price_id: price_id,
                        qty: qty,
                        exp_date: exp_date
                    });
                });


                let jsonObj = {
                    purchase_stock_id: purchase_bill,
                    store_id: store_id,
                    assign_bill_number: assign_bill_no,
                    total: totalAmount,
                    purchase_request: purchaseRequest
                };

                ajaxPostData(`/api/store/assign`, jsonObj, (response) => {
                    console.log(response);
                    window.location.href = "/admin/dashboard";
                })

            })

            $(document).on('click', '#addNewRow', function(){
                rowID = rowID + 1;
                addNewRow(rowID);

            })

            $(document).on('keyup', '.quantity', function () {
                let quantity = $(this).val();
                let id = $(this).attr('id');
                let rowID = id.replace('quantity', '');
                let price = $(`#mrp${rowID}`).children("option:selected").text();
                $(`#totalAmount${rowID}`).val(quantity * price);
                updateTotalAmount()
            })
        });


        function addNewRow() {
            document.addEventListener('DOMContentLoaded', function() {
                updateTotalAmount();
            });

            getBrands(null, rowID);
            getCategories(null, rowID);
            getPacks(null, rowID);
            getPrices(null, rowID);

            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td id="row">${rowID}</td>
                <td>
                    <select class="form-control" name="brand[]" id="brand${rowID}">
                    
                    </select>
                </td>
                <td>
                    <select class="form-control" name="category[]" id="category${rowID}">
                    
                    </select>
                </td>
                <td>
                    <select class="form-control" name="subCategory[]" id="sub_category${rowID}">
                    
                    </select>
                </td>
                <td>
                    <select class="form-control" name="productName[]" id="product_name${rowID}">
                    
                    </select>
                </td>
                <td>
                    <select class="form-control" name="pack[]" id="pack${rowID}">
                    
                    </select>
                </td>
                <td>
                    <select class="form-control" name="mrp[]" id="mrp${rowID}">
                    
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="quantity[]" id="quantity${rowID}" />
                </td>
                <td>
                    <input type="date" class="form-control" name="expiryDate[]" id="expiryDate${rowID}" />
                </td>
                <td>
                    <input type="text" class="form-control" value=0 name="totalAmount[]" id="totalAmount${rowID}" readonly />
                </td>
                <td>
                    <span class="delete-icon btn btn-danger text-white text-center " onclick="deleteRow(this)">
                        <i class="fas fa-trash"></i>
                    </span>
                </td>
            `;

            document.getElementById("formBody").appendChild(newRow);
            
            
            $(document).on('change', `#category${rowID}`, function() {
                let categoryId = $(this).val();
                getSubCategories(categoryId, null, rowID);
            })
            $(document).on('change', `#sub_category${rowID}`, function() {
                let sub_category_id = $(this).val();
                let category_id = $(`#category${rowID}`).val();
                let brand_id = $(`#brand${rowID}`).val();
                getProducts(brand_id, category_id, sub_category_id, null, rowID)
                
            })
            
            updateTotalAmount();
            clearSelectOptions();

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

        function updateTotalAmount() {
            let totalAmount = 0;
            const totalAmountElements = document.getElementsByName("totalAmount[]");

            totalAmountElements.forEach((element) => {
                totalAmount += parseFloat(element.value) || 0;
            });

            document.getElementById("totalAmount").innerText = totalAmount.toFixed(2);
        }

        function getPurchaseBillNo() {
            ajaxGetData('/api/purchase/stock', (response) => {
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    $('#purchase_bill').append(
                        `<option value=${element.id}>${element.purchase_bill_number}</option>`)
                }

            })
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

        function getBrands(brand_id, rowID) {
            ajaxGetData('/api/brands', (response) => {
                $(`#brand${rowID}`).append(`<option value="">Choose</option>`);
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    if (brand_id == element?.id) {
                        $(`#brand${rowID}`).append(
                            `<option value=${element.id} selected>${element.brand_name}</option>`);
                    } else {
                        $(`#brand${rowID}`).append(`<option value=${element.id}>${element.brand_name}</option>`);
                    }

                }
            })
        }

        function getCategories(category_id, rowID) {
            ajaxGetData('/api/category', (response) => {
                $(`#category${rowID}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data?.length; index++) {
                    const element = response?.data[index];
                    if (element?.id == category_id) {
                        $(`#category${rowID}`).append(
                            `<option value=${element.id} selected>${element.category_name}</option>`)
                    } else {
                        $(`#category${rowID}`).append(
                            `<option value=${element.id}>${element.category_name}</option>`)
                    }
                }
            })
        }

        function getSubCategories(categoryId, subCategoryId, rowID) {
            ajaxGetData(`/api/get/subcategory/${categoryId}`, (response) => {
                $(`#sub_category${rowID}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    if (subCategoryId == element?.id) {
                        $(`#sub_category${rowID}`).append(
                            `<option value=${element.id} selected>${element.sub_category_name}</option>`)
                    } else {
                        $(`#sub_category${rowID}`).append(
                            `<option value=${element.id}>${element.sub_category_name}</option>`)
                    }
                }
            })
        }

        function getProducts(brand_id, category_id, sub_category_id, product_id, rowID) {
            ajaxGetData(`/api/products?brand_id=${brand_id}&category_id=${category_id}&sub_category_id=${sub_category_id}`,
                (response) => {
                    $(`#product_name${rowID}`).append(`<option value="">Choose</option>`)
                    for (let index = 0; index < response?.data.length; index++) {
                        const element = response?.data[index];
                        if (product_id == element?.id) {
                            $(`#product_name${rowID}`).append(
                                `<option value=${element.id} selected>${element.product_name}</option>`);
                        } else {
                            $(`#product_name${rowID}`).append(
                                `<option value=${element.id}>${element.product_name}</option>`);
                        }
                    }
                })
        }

        function getPacks(pack_id, rowID) {
            ajaxGetData(`/api/pack`, (response) => {
                $(`#pack${rowID}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    if (element?.id == pack_id) {
                        $(`#pack${rowID}`).append(
                            `<option value=${element.id} selected >${element.pack_name}</option>`)
                    } else {
                        $(`#pack${rowID}`).append(`<option value=${element.id}>${element.pack_name}</option>`)
                    }
                }
            })
        }

        function getPrices(price_id, rowID) {
            ajaxGetData(`/api/prices`, (response) => {
                $(`#mrp${rowID}`).append(`<option value="">Choose</option>`)
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    if (element?.id == price_id) {
                        $(`#mrp${rowID}`).append(
                            `<option value=${element.id} selected>${element.price_name}</option>`)
                    } else {
                        $(`#mrp${rowID}`).append(`<option value=${element.id}>${element.price_name}</option>`)
                    }
                }
            })
        }

        function appendRowHtml(data) {
            // $('tbody').html("");
            document.addEventListener('DOMContentLoaded', function() {
                updateTotalAmount();
            });

            getBrands(data?.brand_id, rowID);
            getCategories(data?.category_id, rowID);
            getSubCategories(data?.category_id, data?.sub_category_id, rowID);
            getProducts(data?.brand_id, data?.category_id, data?.sub_category_id, data?.product_id, rowID);
            getPacks(data?.pack_id, rowID);
            getPrices(data?.price_id, rowID);

            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td id="row">${rowID}</td>
                <td>
                    <select class="form-control" name="brand[]" id="brand${rowID}"> </select>
                </td>
                <td>
                    <select class="form-control" name="category[]" id="category${rowID}"></select>
                </td>
                <td>
                    <select class="form-control" name="subCategory[]" id="sub_category${rowID}"></select>
                </td>
                <td>
                    <select class="form-control" name="productName[]" id="product_name${rowID}"></select>
                </td>
                <td>
                    <select class="form-control" name="pack[]" id="pack${rowID}"></select>
                </td>
                <td>
                    <select class="form-control" name="mrp[]" id="mrp${rowID}"></select>
                </td>
                <td>
                    <input type="number" class="form-control quantity" name="quantity[]" id="quantity${rowID}"/>
                </td>
                <td>
                    <input type="date" class="form-control" name="expiryDate[]" id="expiryDate${rowID}" value =${data?.exp_date}/>
                </td>
                <td>
                    <input type="text" class="form-control" value=0 name="totalAmount[]" id="totalAmount${rowID}" readonly />
                </td>
                <td>
                    <span class="delete-icon btn btn-danger text-white p-2 px-3 " onclick="deleteRow(this)"><i class="fas fa-trash"></i></span>
                </td>
            `;
            rowID = rowID + 1;

            document.getElementById("formBody").appendChild(newRow);
            updateTotalAmount();
            clearSelectOptions();
        }
    </script>
@endsection
