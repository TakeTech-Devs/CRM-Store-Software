@extends('layouts.dashboard')
@section('title', 'Sub-Categories - List')

<style>
    #message.bg-success{
        display:none;
    }
</style>
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Sub Categories List</h1>
        <div id="message" class="text-center bg-success text-light p-3"></div>

        <div class="search-add">
            <form class="d-flex align-items-center justify-content-between">
                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search" placeholder="Search Subcategory Name">
                </div>
                <div class="form-group">
                    
                    <button type="button" class="btn btn-md btn-secondary" data-toggle="modal" data-target="#addBrandModal">
                        Add Sub Category
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ADD SUBCATEGORY MODEL  -->
    <div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addBrandModalLabel">Add Sub Category</h5>
                </div>
                <div class="modal-body">
                    <form id="addSubCategory" action="{{url('/subcategory')}}" method="POST" class="container">
                        <div class="form-group">
                            <label for="category">Category Name</label>
                            <select name="category" id="category" class="form-control">
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
                            <button type="button" id="addBrandFormBtn" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- EDIT SUBCATEGORY MODEL  -->
    <div class="modal fade" id="editBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBrandModalLabel">Edit Sub-Category</h5>
                </div>
                <div class="modal-body">
                    <form id="editSubCategory" class="container">
                        <div class="form-group">
                            <label for="editCategory">Category Name</label>
                            <select name="editCategory" id="editCategory" class="form-control">
                                <option value="">Select Category</option>
                                
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editSubCategoryForm">Sub Category Name</label>
                            <input type="text" class="form-control" id="editSubCategoryForm" name="editSubCategoryForm" value="" required>
                        </div>

                        <div class="form-group d-none">
                            <label>Status:</label>
                            <div class="form-group d-flex justify-content-start align-items-center">
                                <div class="form-check mx-3">
                                    <input type="radio" class="form-check-input" id="edit_statusActive" name="edit_status" value="1" >
                                    <label class="form-check-label" for="edit_statusActive">Active</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="edit_statusInactive" name="edit_status" value="0">
                                    <label class="form-check-label" for="edit_statusInactive">Deactive</label>
                                </div>
                            </div>
                        </div>

                        <div class="save-button d-flex align-items-center justify-content-center">
                            <button type="button" id="updateSubCategoryFormBtn" class="btn btn-success mx-2">Save</button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    </div>

    


    <!-- DISPLAY TABLE  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive border" style="height: 60vh; overflow:auto"  >
                    <table class="table text-dark border table-hover text-center"  id="subcategory-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Sub-Category Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No SubCategory found</div>
                </div>
            </div>
        </div>
    </div> 

</div>

<script>
    $(document).ready(function() {
        // GETTING THE CATEGORY NAME 
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

        // GETTING THE CATEGORY NAME FOR EDIT 
        function CategoryList(cat_value){
            $.ajax({
                url: '/api/category/',
                type: 'GET',
                success: function(response) {
                    var category = response.data;
                    $('#editCategory').html(" ");
                    $.each(category, function(index, cat) {
                        if(cat.status === 1){
                            if(cat_value == cat.id){
                                $('#editCategory').append('<option value="' + cat.id + '" selected>' + cat.category_name +
                            ' </option>');
                            }else{

                                $('#editCategory').append('<option value="' + cat.id + '">' + cat.category_name +
                                ' </option>');
                            }
                        }
                    });
                }
            });
        }
        
        //HANDLING EDIT SUB CATEGORY MODAL
        $(document).ready(function() {
            $(document).on('click', '.editSubcategory', function() {
                let id = $(this).val()
                ajaxGetData(`/api/subcategory/${id}`, (response) => {
                    CategoryList(response?.stock?.category_id);
                    $('#editSubCategoryForm').val(response?.stock?.sub_category_name);
                    $('#updateSubCategoryFormBtn').val(response?.stock?.id)
                    if (response?.stock?.status == 1) {
                        $('#edit_statusActive').prop('checked', true);
                    } else {
                        $('#edit_statusInactive').prop('checked', true);
                    }
                    $('#editBrandModal').modal('show');
                })
                
                $('#updateSubCategoryFormBtn').on('click', function() {
                    let category_id = $('#editCategory').val();
                    let sub_category_name = $('#editSubCategoryForm').val();
                    let status = $('input[name="edit_status"]:checked').val();
                    let id = $('#updateSubCategoryFormBtn').val();
                    let jsonDataOfBrand = {
                        category_id: category_id,
                        sub_category_name: sub_category_name,
                        status: status
                    }
                    ajaxPutData(`/api/subcategory/${id}`, jsonDataOfBrand, (response) => {
                        $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();

                        $('#editBrandModal').modal('hide');
                        api_for_subcategory();
                    })
                })
            }); 
        });

        // DEACTIVATE FUNCATIONALITY 
        $(document).on('click', '.disableSubcategory', function() {
            let id = $(this).val()
            ajaxGetData(`/api/disable/subcategory/${id}`, (response) => {
                $('#message').removeClass('bg-success').addClass('bg-danger').html(response.message).show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                api_for_subcategory();
            })
            
        });
                
        // ACTIVATE FUNCATIONALITY 
        $(document).on('click', '.enableSubcategory', function() {
            let id = $(this).val()
            ajaxGetData(`/api/enable/subcategory/${id}`, (response) => {
                $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                api_for_subcategory()
            }) 
        });
    
        // FUNCTION FOR CALLING SUBCATEGORY API
        function api_for_subcategory() {
            ajaxGetData('/api/subcategory', (response) => {
                subcategory_list(response);
            });
        }
        function subcategory_list(response) {
            $('#subcategory-table tbody').empty();

            if (response && response.data.length > 0) {
                response.data.reverse();
                $.each(response.data, function(index, subcategory) {
                    let formattedStatus = (subcategory.status == 1) ? 'Active' : 'Deactive';
                    $('#subcategory-table tbody').append(`
                        <tr class="subcategory-row" style="cursor:pointer;" data-id="${subcategory?.id}"> 
                            <td scope="row"> ${index+1} </td>   
                            <td> ${subcategory?.category.category_name} </td>   
                            <td> ${subcategory?.sub_category_name} </td>   
                            <td> ${formattedStatus} </td> 
                            <td>
                                <button type="button" class="btn btn-secondary btn-sm editSubcategory" value="${subcategory?.id}">&#9998;</button> 
                                ${(subcategory.status == 1) ? `<button type="button" class="btn btn-danger btn-sm disableSubcategory" value="${subcategory?.id}">&#10005;</button>` : `<button type="button" class="btn btn-success btn-sm enableSubcategory" value="${subcategory?.id}">&#10003;</button>`}
                            </td>   
                        </tr>
                    `)
                });  
            }else {
                $('#subcategory-table tbody').append(`
                    <tr>
                        <td class="text-center">No records found</td>
                    </tr>
                `);
            }
        }
        api_for_subcategory();
    }); 


    
    // ADDING THE SUB CATEGORY 
    $('#addSubCategory').submit(function(event) {
        event.preventDefault();
        let categoryId = $('#category').val();
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
                jQuery('#addSubCategory')[0].reset();
                alert(response.message);
                location.reload();                
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert("Failed to add sub-category.");
            }
        });
    });

     // SEARCH FUNCATIONALITY 
     $(document).ready(function(){
        $('#search').on('input', function(){
            var searchText = $(this).val().toLowerCase();
            var found = false;
            
            $('.subcategory-row').each(function(){
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



