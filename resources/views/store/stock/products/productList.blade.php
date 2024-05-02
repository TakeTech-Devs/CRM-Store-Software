<!-- resources/views/admin/stocks/index.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Product - List')


<style>
    #message.bg-success{
        display:none;
    }
</style>
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center">

        <h2 class="text-center">Products List</h2>
        <div id="message" class="text-center bg-success text-light p-3"></div>

        <div class="search-product d-flex align-items-center justify-content-between">
            <form class="d-flex align-items-center justify-content-between">
                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search" placeholder="Enter Product Name">
                </div>
                <div class="form-group">
                    <a href="{{ url('admin/product/create') }}" class="btn btn-secondary btn-md">Add Product</a>
                </div>
            </form>
        </div>
    </div>

    <!-- EDIT MODEL  -->
    <div class="modal fade" id="editProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProductModalLabel">Edit Product</h5>
                </div>
                <div class="modal-body">
                    <form id="editProductForm" class="container d-flex flex-wrap">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_product_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="edit_product_name" name="edit_product_name" required>
                            </div>
                        </div>
                        <!-- Brand Section  -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_brand">Brand Name</label>
                                <div class="form-group d-flex align-items-center">
                                    
                                    <select name="edit_brand" id="edit_brand" class="form-control mr-2">
                                        <option value="">Choose Brand Name...</option>
                                    </select>
                                    <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addBrandModal">
                                        <i class="fas fa-plus"></i>
                                    </button> -->
                                </div>
                            </div> 
                        </div>
                        <!-- End of Brand Section  -->
                    
                        <!-- Category Section  -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_category">Select Category</label>
                                <div class="form-group d-flex align-items-center">
                                    <select name="edit_category" id="edit_category" class="form-control mr-2">
                                        <option value="">Choose Category...</option>
                                    </select>
                                    <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                                        <i class="fas fa-plus"></i>
                                    </button> -->
                                </div>
                            </div>
                        </div>
                        <!-- End of Category Section -->
                    
                        <!-- SubCategory Section -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_subcategory">Select Sub Category</label>
                                <div class="form-group d-flex align-items-center">
                                    
                                    <select name="edit_subcategory" id="edit_subcategory" class="form-control mr-2">
                                    </select>
                                    <!-- <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addSubCategoryModal">
                                        <i class="fas fa-plus"></i>
                                    </button> -->
                                </div>
                            </div>
                        </div>
                        <!-- End of Sub Category Section -->

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_hsn" class="form-label">HSN Code</label>
                                <select name="edit_hsn" id="edit_hsn" class="form-control">

                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_gst" class="form-label">GST</label>
                                <input type="text" class="form-control" name="edit_gst" id="edit_gst" readonly>
                            </div>
                        </div>

                        <!-- <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_size" class="form-label">Size - ml/mg</label>
                                <select class="form-control form-select-lg" id="edit_size" name="edit_size" required>
                                    <option value="0">Choose Size...</option>
                                </select>
                            </div>
                        </div> -->

                        <div class="col-md-6 d-none">
                            <div class="d-flex align-items-start justify-content-center mt-4">
                                <label for="edit_status" class="form-label">Status: </label>
                                <label class="mx-5">
                                    <input type="radio" class="form-group mx-1" id="edit_statusActive" name="edit_status" value="1">
                                    Active
                                </label>
                                <label>
                                    <input type="radio" class="form-group mx-1" id="edit_statusInactive"  name="edit_status" value="0">
                                    Deactive
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-12">

                            <button type="button" id="updateProductForm" class="btn btn-info float-right btn-sm">Edit Product</button>
                        </div>                    
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- LIST TABLE -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive border" style="height: 60vh; overflow:auto"  >
                    <table class="table text-dark border table-hover text-center"  id="product-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>GST%</th>
                                <th>HSN</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
            
                        </tbody>
                    </table>
                    <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No Product found</div>
                </div>
            </div>
        </div>
    </div>   
    
    
    <!-- ADD BRAND MODEL  -->
    <div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addBrandModalLabel">Add Brand</h5>
                </div>
                <div class="modal-body">
                    <form id="addBrand" action="{{url('/brands')}}" method="POST" class="container">
                        <div id="message" class="text-center bg-success text-light p-3"></div>
                        <div class="form-group">
                            <label for="newBrand">Brand Name</label>
                            <input type="text" class="form-control" id="brand_name" name="brand_name" required>
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

    <!-- ADD CATEGORY MODEL -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                </div>
                <div class="modal-body">
                    <form id="addCategory" class="container" action="{{url('category')}}" method="POST">
                        <div id="message" class="text-center bg-success text-light p-3"></div>

                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                        <div class="form-group d-flex d-none">
                            <label>Status:&nbsp;&nbsp;&nbsp;</label>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="statusActive" name="status"
                                    value="1" checked>
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            &nbsp;&nbsp;
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="statusInactive" name="status"
                                    value="0">
                                <label class="form-check-label" for="statusInactive">Deactive</label>
                            </div>
                        </div>
                        <div class="save-button d-flex align-items-center justify-content-center">
                            <button type="submit" id="addCategoryFormBtn" class="btn btn-success mx-2">Save</button>
                            <button type="button" id="addBrandFormBtn" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD SUBCATEGORY MODEL  -->
    <div class="modal fade" id="addSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addBrandModalLabel">Add Sub Category</h5>
                </div>
                <div class="modal-body">
                    <form id="addSubCategory" action="{{url('/subcategory')}}" method="POST" class="container">
                        <div id="message" class="text-center bg-success text-light p-3"></div>

                        <div class="form-group">
                            <label for="newCategory">Category Name</label>
                            <select name="newCategory" id="newCategory" class="form-control">
                                <option value="">Select Category</option>
                                
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="newSubCategory">Sub Category Name</label>
                            <input type="text" class="form-control" id="newSubCategory" name="newSubCategory" required>
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
                            <button type="button" id="addBrandFormBtn" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>

<script>
    //EDIT PRODUCT   
    $(document).ready(function() {        

        //GETTING THE CURRENT DATA TO EDIT
        $(document).ready(function() {
            $(document).on('click', '.editProduct', function() {
                let id = $(this).val();
                ajaxGetData(`/api/products/${id}`, (response) => {
                    CategoryList(response?.data?.category_id);
                    BrandList(response?.data?.brand_id);
                    SubcategoryList(response?.data?.sub_category_id);
                    hsn_gstList(response?.data?.hsn_code);
                    $('#edit_product_name').val(response?.data?.product_name);
                    $('#edit_gst').val(response?.data?.gst);
                    $('#edit_hsn').val(response?.data?.hsn_code);
                    $('#edit_size').val(response?.data?.size);
                    $('#updateProductForm').val(response?.data?.id)
                    if (response?.data?.status == 1) {
                        $('#edit_statusActive').prop('checked', true);
                    } else {
                        $('#edit_statusInactive').prop('checked', true);
                    }
                    $('#editProductModal').modal('show');
                })
                
                $('#updateProductForm').on('click', function() {

                    let product_name = $('#edit_product_name').val();
                    let brand_id = $('#edit_brand').val();
                    let category_id = $('#edit_category').val();
                    let sub_category_id = $('#edit_subcategory').val();
                    let gst = $('#edit_gst').val();
                    let hsn_code = $('#edit_hsn').val();
                    let size = $('#edit_size').val();
                    let status = $('input[name="edit_status"]:checked').val();
                    let id = $('#updateProductForm').val();
                    let jsonDataOfProduct = {
                        id:id,
                        product_name: product_name,
                        brand_id: brand_id,
                        category_id: category_id,
                        sub_category_id: sub_category_id,
                        gst: gst,
                        size: size,
                        hsn_code: hsn_code,
                        status: status
                    }
                    ajaxPutData(`/api/products/${id}`, jsonDataOfProduct, (response) => {
                        $('#message').removeClass('bg-danger').addClass('bg-success').html("Product Updated Successfully...").show();                            
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        $('#editProductModal').modal('hide');
                        api_for_product();
                    })
                })
            }); 
        });


        // GETTING CATEGORY LIST 
        function CategoryList(cat_value){
            $.ajax({
                url: '/api/category/',
                type: 'GET',
                success: function(response) {
                    var category = response.data;
                    $('#edit_category').html(" ");
                    $.each(category, function(index, cat) {
                        if(cat.status === 1){
                            if(cat_value == cat.id){
                                $('#edit_category').append('<option value="' + cat.id + '" selected>' + cat.category_name +
                            ' </option>');
                            }else{

                                $('#edit_category').append('<option value="' + cat.id + '">' + cat.category_name +
                                ' </option>');
                            }
                        }
                    });
                }
            });
        }
        
        // FETCHING SUB-CATEGORY 
        function SubcategoryList(subcategoryID) {
            $.ajax({
                url: '/api/subcategory/',
                type: 'GET',
                success: function(response) {
                    var subcategories = response.data;
                    $('#edit_subcategory').empty();
                    $.each(subcategories, function(index, subcategory) {
                        if (subcategory.id == subcategoryID) {
                            $('#edit_subcategory').append('<option value="' + subcategory.id + '" selected>' + subcategory.sub_category_name + '</option>');
                        }else{
                            $('#edit_subcategory').append('<option value="' + subcategory.id + '" >' + subcategory.sub_category_name + '</option>');
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching sub-categories:', error);
                }
            });
        }

        // GETTING THE BRAND NAME FOR EDIT 
        function BrandList(brand_value){
            $.ajax({
                url: '/api/brands/',
                type: 'GET',
                success: function(response) {
                    var brands = response.data;
                    $('#edit_brand').html(" ");
                    $.each(brands, function(index, brand) {
                        if(brand.status === 1){
                            if(brand_value == brand.id){
                                $('#edit_brand').append('<option value="' + brand.id + '" selected>' + brand.brand_name +
                            ' </option>');
                            }else{

                                $('#edit_brand').append('<option value="' + brand.id + '">' + brand.brand_name +
                                ' </option>');
                            }
                        }
                    });
                }
            });
        }

        function hsn_gstList(hsn_gst_value){
            $.ajax({
                url: '/api/tax/',
                type: 'GET',
                success: function(response) {
                    var brands = response.data;
                    $('#edit_hsn').html(" ");
                    $.each(brands, function(index, brand) {
                        if(hsn_gst_value == brand.id){
                            $('#edit_hsn').append('<option value="' + brand.id + '" selected>' + brand.hsn +
                        ' </option>');
                        }else{
                            $('#edit_hsn').append('<option value="' + brand.id + '">' + brand.hsn +
                            ' </option>');
                        }
                    });
                }
            });
            // FETCHING GST BASED ON HSN 
            $('#edit_hsn').change(function() {
                var selectedHsnId = $(this).val();
                $.ajax({
                    url: '/api/tax/' + selectedHsnId,
                    type: 'GET',
                    success: function(response) {
                        if (Array.isArray(response.data) && response.data.length > 0) {
                            var hsnObject = response.data[0];
                            var gst = hsnObject.gst;                    
                            $('#edit_gst').val(gst);
                        } else {
                            console.error("No data Found.");
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr, status, error);
                    }
                });
            });
        }
        

        // DEACTIVATE FUNCATIONALITY 
        $(document).on('click', '.disableProduct', function() {
            let id = $(this).val()
            ajaxGetData(`/api/disable/product/${id}`, (response) => {
                $('#message').removeClass('bg-success').addClass('bg-danger').html(response.message).show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                api_for_product();
            })
            
        });
                
        // ACTIVATE FUNCATIONALITY 
        $(document).on('click', '.enableProduct', function() {
            let id = $(this).val()
            ajaxGetData(`/api/enable/product/${id}`, (response) => {
                $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                api_for_product()
            }) 
        });
    
        // FUNCTION FOR CALLING SUBCATEGORY API
        function api_for_product() {
            ajaxGetData('/api/products', (response) => {
                product_list(response);
            });
        }
        function product_list(response) {
            $('#product-table tbody').empty();

            if (response && response.data.length > 0) {
                $.each(response.data, function(index, product) {
                    let formattedStatus = (product.status == 1) ? 'Active' : 'Deactive';
                    let hsnName = ''; 
                    $.ajax({
                        url: '/api/tax/' + product.hsn_code,
                        type: 'GET',
                        success: function(hsnResponse) {
                            if (hsnResponse && Array.isArray(hsnResponse.data) && hsnResponse.data.length > 0) {
                                hsnName = hsnResponse.data[0].hsn;
                            }



                            appendProductRow(product, formattedStatus, hsnName);
                        },
                        error: function(xhr, status, error) {
                            console.error(xhr, status, error);
                            appendProductRow(product, formattedStatus, hsnName); 
                        }
                    });
                });
            }else {
                $('#product-table tbody').append(`
                    <tr>
                        <td class="text-center">No records found</td>
                    </tr>
                `);
            }
        }

        function appendProductRow(product, formattedStatus, hsnName){
            $('#product-table tbody').append(`
                <tr class="product-row" style="cursor:pointer;" data-id="${product?.id}"> 
                    <td scope="row"> ${product?.id} </td>   
                    <td> ${product?.product_name} </td>   
                    <td> ${product?.brand.brand_name} </td>   
                    <td> ${product?.category.category_name} </td>   
                    <td> ${product?.sub_category.sub_category_name} </td>   
                    <td> ${product?.gst} %</td>   
                    <td> ${hsnName} </td>   
                    <td> ${formattedStatus} </td> 
                    <td>
                    <button type="button" class="btn btn-secondary btn-sm editProduct" value="${product?.id}">&#9998;</button> 
                    ${(product.status == 1) ? `<button type="button" class="btn btn-danger btn-sm disableProduct" value="${product?.id}">&#10005;</button>` : `<button type="button" class="btn btn-success btn-sm enableProduct" value="${product?.id}">&#10003;</button>`}
                    </td>   
                </tr>
            `)
        }
        api_for_product();
            
    });         

    // SEARCH FUNCATIONALITY 
    $(document).ready(function(){
        $('#search').on('input', function(){
            var searchText = $(this).val().toLowerCase();
            var found = false;
            
            $('.product-row').each(function(){
                var brandName = $(this).find('td:eq(1)').text().toLowerCase(); 
                if(brandName.includes(searchText)){
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            
            
            if (found) {
                $('#noBrandFoundMessage').hide();
            } else {
                $('#noBrandFoundMessage').show();
            }
        });
    });
</script>
@endsection
