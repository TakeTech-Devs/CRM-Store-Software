<!-- resources/views/admin/stocks/index.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Pack - List')


<style>
    #message.bg-success{
        display:none;
    }
</style>

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Pack List</h1>
        <div id="message" class="text-center bg-success text-light p-3"></div>
        <div class="search-add">
            <form class="d-flex align-items-center justify-content-between">
                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search" placeholder="Search Pack Name">
                </div>
                <div class="form-group">
                    
                    <button type="button" class="btn btn-md btn-secondary" data-toggle="modal" data-target="#addBrandModal">
                        Add Pack
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ADD BRAND MODEL  -->
    <div class="modal fade" id="addBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addBrandModalLabel">Add Pack</h5>
                </div>
                <div class="modal-body">
                    <form id="addBrand" action="{{url('/pack')}}" method="POST" class="container">
                        <div class="form-group">
                            <label for="newBrand">Pack Name</label>
                            <input type="text" class="form-control" id="pack_name" name="pack_name" required>
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
                            <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- END BRAND MODEL  -->

    <!-- EDIT BRAND MODEL -->
    <div class="modal fade" id="editBrandModal" tabindex="-1" role="dialog" aria-labelledby="addBrandModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBrandModalLabel">Edit Pack</h5>
                </div>
                <div class="modal-body">
                    <form id="addBrandForm">
                        <div id="message" class="text-center bg-success text-light p-3"></div>
                        <div class="form-group">
                            <label for="newBrand">Pack Name</label>
                            <input type="text" class="form-control" id="edit_brand_name" name="edit_brand_name" required>
                        </div>
                        <div class="form-group d-none">
                            <label>Status:</label>
                            <div class="form-group d-flex justify-content-start align-items-center">
                                <div class="form-check mx-3">
                                    <input type="radio" class="form-check-input" id="edit_statusActive" name="edit_status" value="1">
                                    <label class="form-check-label" for="statusActive">Active</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" id="edit_statusInactive" name="edit_status" value="0">
                                    <label class="form-check-label" for="statusInactive">Deactive</label>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="updateBrandFormBtn" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT BRAND MODEL -->



    <!-- BRAND LIST TABLE  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive" style="height: 60vh; overflow:auto"  >
                    <table class="table text-dark border table-hover text-center"  id="brands-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Pack Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No Pack found</div>
                </div>
            </div>
        </div>
    </div>  
    <!-- END BRAND LIST TABLE -->

</div>

    <script>
        $(document).ready(function() {
            api_for_pack()
            $('#addBrandBtn').on('click', function() {
                $('#addBrandModal').modal('show');
            });
            $(document).on('click', '.editBrand', function() {
                let id = $(this).val()

                ajaxGetData(`/api/pack/${id}`, (response) => {
                    $('#edit_brand_name').val(response?.stock?.pack_name);
                    $('#updateBrandFormBtn').val(response?.stock?.id)
                    if (response?.stock?.status == 1) {
                        $('#edit_statusActive').prop('checked', true);
                    } else {
                        $('#edit_statusInactive').prop('checked', true);
                    }
                    $('#editBrandModal').modal('show');
                })
            });

            $(document).on('click', '.disableBrand', function() {
                let id = $(this).val()
                ajaxGetData(`/api/disable/pack/${id}`, (response) => {
                    $('#message').removeClass('bg-success').addClass('bg-danger').html(response.message).show();                            
                    setTimeout(function() {
                        $('#message').hide();
                    }, 3000);
                    api_for_pack()
                })
               
            });

            $(document).on('click', '.enableBrand', function() {
                let id = $(this).val()
                ajaxGetData(`/api/enable/pack/${id}`, (response) => {
                    $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();                            
                    setTimeout(function() {
                        $('#message').hide();
                    }, 3000);
                    api_for_pack()
                })
               
            });

            $('#addBrandFormBtn').on('click', function() {
                let pack_name = $('#newBrand').val();
                let status = $('input[name="status"]:checked').val();

                let jsonDataOfBrand = {
                    pack_name: pack_name,
                    status: status
                }
                ajaxPostData('/api/pack', jsonDataOfBrand, (response) => {
                    $('#addBrandModal').modal('hide');
                    api_for_pack()
                })
            })

            $('#updateBrandFormBtn').on('click', function() {
                let pack_name = $('#edit_brand_name').val();
                let status = $('input[name="edit_status"]:checked').val();
                let id = $('#updateBrandFormBtn').val();
                let jsonDataOfBrand = {
                    pack_name: pack_name,
                    status: status
                }

                ajaxPutData(`/api/pack/${id}`, jsonDataOfBrand, (response) => {
                    $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();                            

                    $('#editBrandModal').modal('hide');
                    api_for_pack()
                })
            })
        });

        // function for calling get api for Brand
        function api_for_pack() {
            ajaxGetData('/api/pack', (response) => {
                pack_list(response)
            })
        }

        // Function for appending list
        function pack_list(response) {
            $('#brands-table tbody').empty();

            if (response && response.data.length > 0) {
                response.data.reverse();
                $.each(response.data, function(index, brand) {
                    let formattedStatus = (brand.status == 1) ? 'Active' : 'Deactive';
                    $('#brands-table tbody').append(`
                        <tr class="brand-row" style="cursor:pointer;" data-id="${brand?.id}"> 
                            <td scope="row"> ${index+1} </td>   
                            <td> ${brand?.pack_name} </td>   
                            <td> ${formattedStatus} </td> 
                            <td>
                                <button type="button" class="btn btn-secondary btn-sm editBrand" value="${brand?.id}">&#9998;</button> 
                                ${(brand.status == 1) ? `<button type="button" class="btn btn-danger btn-sm disableBrand" value="${brand?.id}">&#10005</button>` : `<button type="button" class="btn btn-success btn-sm enableBrand" value="${brand?.id}">&#10003</button>`}
                            </td>   
                        </tr>
                    `)
                });
            } else {
                $('#brands-table tbody').append(`
                    <tr>
                        <td class="text-center">No records found</td>
                    </tr>
                `);
            }
        }


        // Adding PACK 
        $(document).ready(function(){
            $('#addBrand').on('submit', function(event){
                event.preventDefault();

                $.ajax({
                    url: '/api/pack',
                    data: jQuery('#addBrand').serialize(), 
                    method: 'POST',

                    success:function(result){
                        jQuery('#addBrand')[0].reset();
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
            
            $('.brand-row').each(function(){
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
