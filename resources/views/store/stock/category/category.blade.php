@extends('layouts.dashboard')
@section('title', 'Categories - List')

<style>
    #message.bg-success{
        display:none;
    }
</style>
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Categories List</h1>
        <div id="message" class="text-center bg-success text-light p-3"></div>

        <div class="search-add">
            <form class="d-flex align-items-center justify-content-between">
                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search" placeholder="Search Category Name">
                </div>
                <div class="form-group">
                    
                    <button type="button" class="btn btn-md btn-secondary" data-toggle="modal" data-target="#addCategoryModal">
                        Add Category
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- ADD CATEGORY MODAL  -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1" role="dialog" aria-labelledby="addCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addCategoryModalLabel">Add Category</h5>
                </div>
                <div class="modal-body">
                    <form id="addCategory" class="container" action="{{url('category')}}" method="POST">

                        <div class="form-group">
                            <label for="category_name">Category Name</label>
                            <input type="text" class="form-control" id="category_name" name="category_name" required>
                        </div>
                        <div class="form-group d-none">
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
    <!-- ADD CATEGORY MODAL ENDS HERE -->

    <!-- EDIT CATEGORY MODAL  -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-uppercase text-center" id="editCategoryModalLabel">Edit Category</h5>
                    
                </div>
                <div class="modal-body">
                    <form id="addCategoryForm">
                        <div class="form-group">
                            <label for="newCategory">Category Name</label>
                            <input type="text" class="form-control" id="edit_category_name" name="edit_category_name" required>
                        </div>
                        <div class="form-group d-none">
                            <label>Status:&nbsp;&nbsp;&nbsp;</label>
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="edit_statusActive" name="edit_status" value="1">
                                <label class="form-check-label" for="statusActive">Active</label>
                            </div>
                            &nbsp;&nbsp;
                            <div class="form-check">
                                <input type="radio" class="form-check-input" id="edit_statusInactive" name="edit_status" value="0">
                                <label class="form-check-label" for="statusInactive">Deactive</label>
                            </div>
                        </div>
                        <button type="button" id="updateCategoryFormBtn" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT CATEGORY MODAL  -->

    <!-- CATEGORY LIST TABLE  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive border" style="height: 60vh; overflow:auto"  >
                    <table class="table text-dark border table-hover text-center"  id="category-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Category Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No Category found</div>
                </div>
            </div>
        </div>
    </div>  
    <!-- END CATEGORY LIST TABLE -->

</div>

<script>

    $(document).ready(function() {
        api_for_category()
        $('#addCategoryBtn').on('click', function() {
            $('#addCategoryModal').modal('show');
        });
        $(document).on('click', '.editCategory', function() {
            let id = $(this).val()
            ajaxGetData(`/api/category/${id}`, (response) => {
                $('#edit_category_name').val(response?.stock?.category_name);
                $('#updateCategoryFormBtn').val(response?.stock?.id)
                if (response?.stock?.status == 1) {
                    $('#edit_statusActive').prop('checked', true);
                } else {
                    $('#edit_statusInactive').prop('checked', true);
                }
                $('#editCategoryModal').modal('show');
            })
        });
        $(document).on('click', '.disableCategory', function() {
            let id = $(this).val()
            ajaxGetData(`/api/disable/category/${id}`, (response) => {
                $('#message').removeClass('bg-success').addClass('bg-danger').html(response.message).show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                api_for_category()
            })
        });
        $(document).on('click', '.enableCategory', function() {
            let id = $(this).val()
            ajaxGetData(`/api/enable/category/${id}`, (response) => {
                $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                api_for_category()
            })
        });
        $('#addCategoryFormBtn').on('click', function() {
            let category_name = $('#newCategory').val();
            let status = $('input[name="status"]:checked').val();
            let jsonDataOfBrand = {
                category_name: category_name,
                status: status
            }
            ajaxPostData('/api/category', jsonDataOfBrand, (response) => {
                $('#addCategoryModal').modal('hide');
                api_for_category()
            })
        })
        $('#updateCategoryFormBtn').on('click', function() {
            let category_name = $('#edit_category_name').val();
            let status = $('input[name="edit_status"]:checked').val();
            let id = $('#updateCategoryFormBtn').val();
            let jsonDataOfBrand = {
                category_name: category_name,
                status: status
            }
            ajaxPutData(`/api/category/${id}`, jsonDataOfBrand, (response) => {
                $('#editCategoryModal').modal('hide');
                $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();
                    setTimeout(function() {
                        $('#message').hide();
                    }, 3000);
                api_for_category()
            })
        })
        // PAGINATION 
        $(document).on('click', '.page-item', function() {
               
            if($(this).text().trim() == "Next" && $('.page-item.active .page-link').text() == 1){
                $(this).removeClass('disabled');
            }
            if($(this).text().trim() == "Previous" && $('.page-item.active .page-link').text() == 2){
                $(this).removeClass('disabled');
            }
            if (!$(this).hasClass('disabled')) {
                if ($(this).text().trim() === 'Previous' && !$(this).hasClass('disabled')) {
                    page = Number($('.page-item.active .page-link').text()) - 1;
                } else if ($(this).text().trim() === 'Next' && !$(this).hasClass('disabled')) {
                    page = Number($('.page-item.active .page-link').text()) + 1;
                } else {
                    page = $(this).find('.page-link').text();
                }
                api_for_category(page)
            }
        });
    });


    
    function api_for_category(page) {
        if (page) {
                ajaxGetData(`/api/category?page=${page}`, (response) => {
                category_list(response.data)
                pagination(page, response.data)
            })
        }else{
            ajaxGetData(`/api/category`, (response) => {
                category_list(response.data)
                pagination(page, response.data)
            })
        }
    }
    
    
    //  function for appending list
    function category_list(response) {
    console.log('Full Response:', response);

    $('#category-table tbody').empty();

    if (response.data && response.data.category_name) {
        console.log('Data found in response:');
        console.log(response.data);

        let category = response.data;
        let formattedStatus = (category.status == 1) ? 'Active' : 'Deactive';
        $('#category-table tbody').append(`
            <tr class="category-row" style="cursor:pointer;" data-id="${category.id}">
                <td>1</td>
                <td>${category.category_name}</td>
                <td>${formattedStatus}</td>
                <td>
                    <button type="button" class="btn btn-secondary btn-sm editCategory" value="${category.id}">&#9998;</button>
                    ${(category.status == 1) ? '<button type="button" class="btn btn-danger btn-sm disableCategory" value="${category.id}">&#10005;</button>' : '<button type="button" class="btn btn-success btn-sm enableCategory" value="${category.id}">&#10003;</button>'}
                </td>
            </tr>
        `);
    } else {
        console.log('No data found in response or response structure is incorrect.');
        $('#category-table tbody').append(`
            <tr>
                <td class="text-center" colspan="4">No Category Found</td>
            </tr>
        `);
    }
}




    
    
    // ADD CATEGORY 
    $(document).ready(function(){
        $('#addCategory').on('submit', function(event){
            event.preventDefault();

            $.ajax({
                url: '/api/category',
                data: jQuery('#addCategory').serialize(), 
                method: 'POST',

                success:function(result){
                    jQuery('#addCategory')[0].reset();
                    alert(result.message);
                    location.reload();

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


    // SEARCH FUNCATIONALITY 
    $(document).ready(function(){
        $('#search').on('input', function(){
            var searchText = $(this).val().toLowerCase();
            var found = false;
            
            $('.category-row').each(function(){
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
