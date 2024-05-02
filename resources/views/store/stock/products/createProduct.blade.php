<!-- resources/views/admin/stocks/create.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Add Product')
<style>
    #message.bg-success{
        display:none;
    }
</style>


@section('content')
<div class="container-fluid mb-5">
    <div class="heading d-flex align-items-center justify-content-between">
        <h2 class="text-dark">Add Product</h2>
        <div id="message" class="text-center bg-success text-light p-3"></div>
        <a href="{{url('admin/product')}}" class="btn btn-secondary btn-sm">View Products List</a>
    </div>
    
    <form id="createStockForm" action="{{ url('products') }}" method="POST" class="container d-flex flex-wrap my-4">
        @csrf
        
        <div class="col-md-6">
            <div class="form-group">
                <label for="product_name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="product_name" name="product_name" required>
            </div>
        </div>
        <!-- Brand Section  -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="brand">Brand Name</label>
                <div class="form-group d-flex align-items-center">
                    
                    <select name="brand" id="brand" class="form-control mr-2">
                        <option value="">Choose Brand...</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addBrandModal">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div> 
        </div>
        <!-- End of Brand Section  -->
    
        <!-- Category Section  -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="category">Select Category</label>
                <div class="form-group d-flex align-items-center">
                    <select name="category" id="category" class="form-control mr-2">
                        <option value="">Choose Category...</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addCategoryModal">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- End of Category Section -->
    
        <!-- SubCategory Section -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="subcategory">Select Sub Category</label>
                <div class="form-group d-flex align-items-center">
                    
                    <select name="subcategory" id="subcategory" class="form-control mr-2">
                    </select>
                    <button type="button" class="btn btn-sm btn-primary" id="subCategoryBtn" data-toggle="modal" data-target="#addSubCategoryModal">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- End of Sub Category Section -->

        <!-- HSN  -->
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label for="hsn" class="form-label">HSN Code</label>
                <div class="form-group d-flex align-items-center">
                    
                    <select name="hsn" id="hsn" class="form-control mr-2">
                        <option value="">Choose HSN...</option>
                    </select>
                    <button type="button" class="btn btn-sm btn-primary" id="hsnBtn" data-toggle="modal" data-target="#addHSNModal">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- GST  -->
        <div class="col-md-6">
            <div class="mb-3">
                <label for="gst" class="form-label">GST</label>
                <input type="text" name="gst" id="gst" placeholder="GST%" class="form-control" readonly>
            </div>
        </div>

        <!-- SIZE -->
        <!-- <div class="col-md-6">
            <div class="mb-3">
                <label for="stock_type" class="form-label">Size - ml/mg</label>
                <select class="form-control form-select-lg" id="size" name="size" required>
                    <option value="">Choose Size...</option>
                </select>
            </div>
        </div> -->

        <!-- STATUS -->
        <div class="col-md-6 mt-5 d-none">
            <div class="form-check d-flex align-items-start justify-content-start">
                <label for="status" class="form-label">Status: </label>
                <label class="mx-5">
                    <input type="radio" class="form-input-check mx-1" id="statusActive" name="status" value="1" checked>
                    Active
                </label>
                <label>
                    <input type="radio" class="form-input-check mx-1" id="statusInactive"  name="status" value="0">
                    Deactive
                </label>
            </div>
        </div>

        <div class="col-md-12">

            <button type="button" id="submitStockForm" class="btn btn-info float-right btn-sm">Save Product</button>
        </div>
    </form>

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
                        <div class="form-group">
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
                        <div class="form-group d-flex">
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
    <div class="modal fade " id="addSubCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addSubCategoryModalLabel"
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
                        <div class="form-group">
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

    <!-- ADD HSN MODEL  -->
    <div class="modal fade " id="addHSNModal" tabindex="-1" role="dialog" aria-labelledby="addHSNModalLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addBrandModalLabel">Add HSN</h5>
                </div>
                <div class="modal-body">
                    <form id="addHSN" action="{{url('/hsn')}}" method="POST" class="container">
                        <div id="message" class="text-center bg-success text-light p-3"></div>

                        <div class="form-group">
                            <label for="newCategory">HSN Code</label>
                            <input type="text" class="form-control" name="hsn" id="hsn">
                        </div>
                        <div class="form-group">
                            <label for="gst">GST %</label>
                            <input type="number" class="form-control" id="gst" name="gst" required>
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
                            <button type="submit" id="addBrandFormBtn" class="btn btn-success mx-2">Save HSN</button>
                            <button type="button" id="addBrandFormBtn" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>



</div>

<script>
    // BRANDS SECTION 
    $(document).ready(function() {
        
        // FETCHING BRANDS
        $(document).ready(function(){

            $.ajax({
                url: '/api/brands/',
                type: 'GET',
                success: function(response) {
                    var brands = response.data;
                    
                    $.each(brands, function(index, brand) {
                        if(brand.status === 1){
                            $('#brand').append('<option value="' + brand.id + '">' + brand.brand_name + '</option>');
                        }
                        
                    });
                }
            });
        });

        // EVENT HANDLING FOR ADDING BRANDS
        $('#addBrandBtn').on('click', function() {
            $('#addBrandModal').modal('show');
        });
        
        // ADDING BRAND 
        $(document).ready(function(){
            $('#addBrand').on('submit', function(event){
                event.preventDefault();

                $.ajax({
                    url: '/api/brands',
                    data: jQuery('#addBrand').serialize(), 
                    method: 'POST',

                    success:function(result){
                        $('#brand').append('<option value="' + result.data.id + '">' +
                            result.data.brand_name + '</option>');
                        $('#message').removeClass('bg-danger').addClass('bg-success').html(result.message).show();
                        jQuery('#addBrand')[0].reset();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        
                        $('#addBrandModal').hide();
                        $('.modal-backdrop').remove();
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).join('<br>') : xhr.responseJSON.message;
                        $('#message').removeClass('bg-success').addClass('bg-danger').html(errorMessage).show();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);

                    }
                })
            });
        });
    });
    // END OF BRAND SECTION

    // CATEGORY SECTION
    $(document).ready(function() {
        
        // FETCHING CATEGORY 
        $.ajax({
            url: '/api/category/',
            type: 'GET',
            success: function(response) {
                var category = response.data;

                $.each(category, function(index, cat) {
                    if(cat.status === 1){
                    $('#category').append('<option value="' + cat.id + '">' + cat.category_name +
                        ' </option>');
                    }
                });
            }
        });

        // EVENT HANDLING FOR ADDING CATEGORY 
        $('#addCategoryBtn').on('click', function() {
            $('#addCategoryModal').modal('show');
        });

        //ADDING CATEGORY
        $(document).ready(function(){
            $('#addCategory').on('submit', function(event){
                event.preventDefault();

                $.ajax({
                    url: '/api/category',
                    data: jQuery('#addCategory').serialize(), 
                    method: 'POST',

                    success:function(result){
                        $('#category').append('<option value="' + result.data.id + '">' +
                            result.data.category_name + '</option>');
                        $('#message').removeClass('bg-danger').addClass('bg-success').html(result.message).show();
                        jQuery('#addCategory')[0].reset();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);
                        $('#addCategoryModal').hide();
                        $('.modal-backdrop').remove();
                    },
                    error: function(xhr, status, error) {
                        var errorMessage = xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).join('<br>') : xhr.responseJSON.message;
                        $('#message').removeClass('bg-success').addClass('bg-danger').html(errorMessage).show();
                        setTimeout(function() {
                            $('#message').hide();
                        }, 3000);

                    }
                })
            });
        }); 
    });
    // END OF CATEGORY SECTION

    // SUB-CATEGORY SECTION
    $(document).ready(function() {
        
        // FETCHING SUB-CATEGORY
        $(document).ready(function(){
            $('#category').on('change', function() {
                $('#subcategory').empty();
                var SelectedCategoryId = $('#category').val();
                $.ajax({
                    url: '/api/subcategory/',
                    type: 'GET',
                    success: function(response) {
                        var subcategory = response.data;
                        $.each(subcategory, function(index, cat) {
                            if (cat.category_id === parseInt(SelectedCategoryId)) {
                                $('#subcategory').append('<option value="' + cat.id + '">' + cat.sub_category_name + ' </option>');
                            }
                        });
                    }
                });
            });
        });


        // EVENT HANDLING FOR SUB-CATEGORY MODEL 
        $('#addSubCategoryBtn').on('click', function() {
            $('#addSubCategoryModal').modal('show');
        });

        //FETCHING CATEGORY FOR NEW SUB CATEGORY
        $(document).ready(function(){
            function FetchCategory(){

                $.ajax({
                    url: '/api/category/',
                    type: 'GET',
                    success: function(response) {
                        var category = response.data;
                        
                        $('#newCategory').empty();
                        $.each(category, function(index, cat) {
                            if(cat.status === 1){
                                $('#newCategory').append('<option value="' + cat.id + '">' + cat.category_name +
                                ' </option>');
                            }
                        });
                    }
                });
            }

            $('#subCategoryBtn').on('click', function (event) {
                FetchCategory();
            });
        });

        // ADDING NEW SUB_CATEGORY 
        $('#addSubCategory').submit(function(event) {
            event.preventDefault();

            let categoryId = $('#newCategory').val();
            let subCategoryName = $('#newSubCategory').val();
            let status = $('input[name="status"]:checked').val();

            $.ajax({
                url: "/api/subcategory",
                method: "POST",
                data: {
                    category_id: categoryId,
                    sub_category_name: subCategoryName,
                    status: status 
                },
                dataType: "json",
                success: function(response) {
                    
                    $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();
                    jQuery('#addSubCategory')[0].reset();
                    setTimeout(function() {
                        $('#message').hide();
                    }, 3000);
                    
                    $('#addSubCategoryModal').hide();
                    $('.modal-backdrop').remove();
                    var subcategory = response.data;
                    $('#category').on('change', function() {
                        
                        $('#subcategory').empty();
                        var SelectedCategoryId = $('#category').val();
                        $.each(subcategory, function(index, cat) {
                            if (cat.category_id === parseInt(SelectedCategoryId)){
                                $('#subcategory').append('<option value="' + cat.id + '">' + cat.sub_category_name + ' </option>');
                            }
                        });
                    });
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert("Failed to add sub-category.");
                }
            });
        });



    });
    // END OF SUB-CATEGORY SECTION

    // ADDING HSN & GST 
    $(document).ready(function(){
        $('#addHSN').on('submit', function(event){
            event.preventDefault();
            
            $.ajax({
                url: '/api/tax',
                data: jQuery('#addHSN').serialize(), 
                method: 'POST',
                
                success:function(result){
                    $('#hsn').append('<option value="' + result.data.id + '" selected>' +
                        result.data.hsn + '</option>');
                    $('#gst').append('<option value="' + result.data.id + '" selected>' +
                        result.data.gst + '</option>');
                    $('#message').removeClass('bg-danger').addClass('bg-success').html(result.message).show();
                    jQuery('#addHSN')[0].reset();
                    setTimeout(function() {
                        $('#message').hide();
                    }, 3000);
                    
                    $('#addHSNModal').hide();
                    $('.modal-backdrop').remove();

                },
                error: function(xhr, status, error) {
                    var errorMessage = xhr.responseJSON.errors ? Object.values(xhr.responseJSON.errors).join('<br>') : xhr.responseJSON.message;
                    $('#message').removeClass('bg-success').addClass('bg-danger').html(errorMessage).show();
                    setTimeout(function() {
                        $('#message').hide();
                    }, 3000);
                    
                }
            })
        });
    });

    // FETCHING HSN & GST 
    $(document).ready(function(){
        // FETCHING HSN
        $.ajax({
            url: '/api/tax/',
            type: 'GET',
            success: function(response) {
                var hsn_code = response.data;            
                $.each(hsn_code, function(index, hsn) {
                    $('#hsn').append('<option value="' + hsn.id + '">' + hsn.hsn + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr, status, error);
            }
        });

        // FETCHING GST BASED ON HSN 
        $('#hsn').change(function() {
            var selectedHsnId = $(this).val();
            $.ajax({
                url: '/api/tax/' + selectedHsnId,
                type: 'GET',
                success: function(response) {
                    if (Array.isArray(response.data) && response.data.length > 0) {
                        var hsnObject = response.data[0];
                        var gst = hsnObject.gst;                    
                        $('#gst').val(gst);
                    } else {
                        console.error("No data Found.");
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr, status, error);
                }
            });
        });
    });

    // PRODUCT ADDING
    $(document).ready(function() {
        $('#submitStockForm').click(function() {
            let status = $('input[name="status"]:checked').val();
            let product_name= $('#product_name').val();
            let brand= $('#brand').val();
            let category =  $('#category').val();
            let subcategory =  $('#subcategory').val();
            let gst = $('#gst').val();
            let hsn = $('#hsn').val();
            $.ajax({
                url: '/api/products/',
                method: 'POST',
                data: {
                    product_name: product_name,
                    brand_id: brand,
                    category_id: category,
                    sub_category_id: subcategory,
                    hsn_code: hsn,
                    gst: gst,
                    status: status
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                dataType: 'json',
                success: function(response) {
                    alert("Product Added Successfully");
                    window.location.href = '{{ url('admin/product') }}';
                },
                error: function(error) {
                    console.error('Error:', error); 
                }
            });
        });
    });
    // END OF PRODUCT ADDING 


    // FETCHING PACK 
    // $(document).ready(function(){

    //     $.ajax({
    //         url: '/api/pack/',
    //         type: 'GET',
    //         success: function(response) {
    //             var packs = response.data;
                
    //             $.each(packs, function(index, pack) {
    //                 if(pack.status === 1){
    //                     $('#size').append('<option value="' + pack.id + '">' + pack.pack_name + '</option>');
    //                 }
                    
    //             });
    //         }
    //     });
    // });
</script>
@endsection
