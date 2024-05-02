@extends('layouts.dashboard')
@section('title', 'Stores - List')

<style>
    #message.bg-success{
        display:none;
    }
</style>

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Stores List</h1>
        <div id="message" class="text-center bg-success text-light p-3"></div>
        <div class="search-add">
            <form class="d-flex align-items-center justify-content-between">
                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search" placeholder="Search Store Name">
                </div>
                <div class="form-group">
                    
                    <button type="button" class="btn btn-md btn-secondary" data-toggle="modal" data-target="#addStoreModal">
                        Add Store
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ADD STORE MODEL  -->
    <div class="modal fade" id="addStoreModal" tabindex="-1" role="dialog" aria-labelledby="addStoreModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addStoreModalLabel">Add Store</h5>
                </div>
                <div class="modal-body">
                    <form id="addStore" action="{{url('/stores')}}" method="POST" class="container d-flex align-items-center justify-content-between flex-wrap">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Store Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="store_mail">Store Email</label>
                                <input type="email" class="form-control" id="store_mail" name="store_mail" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="store_start_date">Store Start Year</label>
                                <input type="date" class="form-control" id="store_start_date" name="store_start_date" required>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <div class="form-group">
                                <label for="store_address">Store Address</label>
                                <textarea type="text" class="form-control" id="store_address" name="store_address" required></textarea>
                            </div>
                        </div>
                        <div class="col-md-6 d-none">
                            <div class="form-group">
                                <label>Status:</label>
                                <div class="form-group d-flex justify-content-start align-items-center">
                                    
                                    <div class="form-check mx-3">
                                        <input type="radio" class="form-check-input" id="statusActive" name="store_status" value="1" checked>
                                        <label class="form-check-label" for="statusActive">Active</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" id="statusInactive" name="store_status" value="0">
                                        <label class="form-check-label" for="statusInactive">Deactive</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-4 float-right">
                                <input type="button" class="form-control bg-secondary text-light btn-sm" id="store_credentials"value="Generate Credentials">
                            </div>
                        </div>
                        <div class="col-md-12 d-flex align-items-center justify-content-between text-center">
                            <div class="col-md-6">

                                <div class="form-group">
                                    <label for="store_id">Store ID</label>
                                    <input type="text" class="form-control" id="store_id" name="store_meta_id" required>
                                </div>
                            </div>
                            <div class="col-md-6">

                                <div class="form-group">        
                                    <label for="store_passkey">Store Passkey</label>
                                    <input type="text" class="form-control" id="store_passkey" name="store_pass_key" required readonly>
                                </div>
                            </div>
                        </div>                        
                        <div class="save-button d-flex align-items-center justify-content-center col-md-12 mt-3">
                            <button type="submit" id="addStoreBtn" class="btn btn-success mx-2 px-4">Add Store</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- END STORE MODEL  -->

    <!-- EDIT STORE MODEL -->
    <div class="modal fade" id="editStoreModal" tabindex="-1" role="dialog" aria-labelledby="editStoreModalLabel"
        aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStoreModalLabel">Edit Store</h5>
                </div>
                <div class="modal-body">
                    <form id="addStoreForm" class="container d-flex align-items-center justify-content-between flex-wrap">
                    <div class="col-md-6">

                        <div class="form-group">
                            <label for="newStore">Store Name</label>
                            <input type="text" class="form-control" id="edit_store_name" name="edit_store_name" required>
                        </div>
                    </div>    
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="newStoreAddress">Store Address</label>
                                <input type="text" class="form-control" id="edit_store_address" name="edit_store_address" required>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="newStartYear">Store Start Year</label>
                                <input type="date" class="form-control" id="edit_store_start_date" name="edit_store_start_date" required>
                            </div>
                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="newEmail">Store Email</label>
                                <input type="email" class="form-control" id="edit_store_mail" name="edit_store_mail" required>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="newStoreId">Store ID</label>
                                <input type="text" class="form-control" id="edit_store_meta_id" name="edit_store_meta_id" required>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <div class="form-group">
                                <label for="newPassKey">Store Passkey</label>
                                <input type="text" class="form-control" id="edit_store_pass_key" name="edit_store_pass_key" required>
                            </div>
                        </div>
                        <div class="col-md-6 d-none">

                            <div class="form-group">
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
                        </div>
                        <div class="col-md-12">

                            <button type="button" id="updateStoreFormBtn" class="btn btn-primary float-right flex-end">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT STORE MODEL -->

    <!-- VIEW DETAILS MODEL  -->
    <div class="modal fade" id="viewStoreModal" tabindex="-1" role="dialog" aria-labelledby="viewStoreModalLabel"
        aria-hidden="true">
        <div class="modal-dialog  modal-lg" role="document">
            <div class="modal-content ">
                <div class="modal-header">
                    <h3 class="modal-title" id="viewStoreModalLabel">View Store Details</h5>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table text-dark "  id="view-store-table">
                            <tr>
                                <th>Store Name</th>                                    
                                <th>-</th> 
                                <td></td>
                            </tr>
                            <tr>
                                <th>Store Email</th>
                                <th>-</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Store Address</th>
                                <th>-</th>
                                <td></td>
                            </tr>
                            
                            <tr>
                                <th>Store Start Year</th>
                                <th>-</th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>Store ID</th>
                                <th>-</th>
                                <td></td>
                            </tr>
                            <tr>                                
                                <th>Store Passkey</th>
                                <th>-</th>
                                <td></td>
                            </tr>
                            <tr>                                
                                <th>Status</th>
                                <th>-</th>
                                <td></td>
                            </tr>
                        </table>
                        <div class="col-md-12 ">
                            <button type="button" class="btn btn-secondary float-right" data-dismiss="modal" aria-label="Close">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END VIEW DETAILS MODEL  -->

    <!-- STORE LIST TABLE  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive" style="height: 60vh; overflow:auto"  >
                    <table class="table text-dark border table-hover text-center"  id="store-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Store Name</th>
                                <th scope="col">Store ID</th>
                                <th scope="col">Store Email</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No Store found</div>
                </div>
            </div>
        </div>
    </div>  
    <!-- END STORE LIST TABLE -->

</div>
<script>
    // ADDING STORE 
    $(document).ready(function(){
        $('#addStore').on('submit', function(event){
            event.preventDefault();

            $.ajax({
                url: '/api/stores',
                data: jQuery('#addStore').serialize(), 
                method: 'POST',

                success:function(result){
                    jQuery('#addStore')[0].reset();
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

    // EDIT STORE 
    $(document).ready(function() {
        api_for_store()
        $('#addStoreBtn').on('click', function() {
            $('#addStoreModal').modal('show');
        });
        $(document).on('click', '.editStore', function() {
            let id = $(this).data('store-id');

            ajaxGetData(`/api/stores/${id}`, (response) => {
                $('#edit_store_name').val(response?.data?.name);
                $('#edit_store_address').val(response?.data?.store_address);
                $('#edit_store_mail').val(response?.data?.store_mail);
                $('#edit_store_start_date').val(response?.data?.store_start_date);
                $('#edit_store_meta_id').val(response?.data?.store_meta_id);
                $('#edit_store_pass_key').val(response?.data?.store_pass_key);
                $('#updateStoreFormBtn').val(response?.data?.id)
                if (response?.data?.store_status == 1) {
                    $('#edit_statusActive').prop('checked', true);
                } else {
                    $('#edit_statusInactive').prop('checked', true);
                }
                $('#editStoreModal').modal('show');
            })
        });

        // DEACTIVATE STORE 
        $(document).on('click', '.disableStore', function() {
            let id = $(this).val()
            ajaxGetData(`/api/disable/stores/${id}`, (response) => {
                $('#message').removeClass('bg-success').addClass('bg-danger').html(response.message).show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                api_for_store()
            })
           
        });

        // ACIVATE STORE 
        $(document).on('click', '.enableStore', function() {
            let id = $(this).val()
            ajaxGetData(`/api/enable/stores/${id}`, (response) => {
                $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                api_for_store()
            })
           
        });

        $('#addStoreFormBtn').on('click', function() {
            let name = $('#newStore').val();
            let store_address = $('#newStoreAddress').val();
            let store_start_date = $('#newStartYear').val();
            let store_mail = $('#newEmail').val();
            let store_meta_id = $('#newStoreId').val();
            let store_pass_key = $('#newPassKey').val();
            let store_status = $('input[name="status"]:checked').val();

            let jsonDataOfStore = {
                name: name,
                store_address: store_address,
                store_start_date: store_start_date,
                store_mail: store_mail,
                store_meta_id: store_meta_id,
                store_pass_key: store_pass_key,
                store_status: store_status
            }
            ajaxPostData('/api/stores', jsonDataOfStore, (response) => {
                $('#addStoreModal').modal('hide');
                api_for_store()
            })
        })

        $('#updateStoreFormBtn').on('click', function() {
            let name = $('#edit_store_name').val();
            let store_address = $('#edit_store_address').val();
            let store_start_date = $('#edit_store_start_date').val();
            let store_mail = $('#edit_store_mail').val();
            let store_meta_id = $('#edit_store_meta_id').val();
            let store_pass_key =$('#edit_store_pass_key').val();
            let store_status = $('input[name="edit_status"]:checked').val();
            let id = $('#updateStoreFormBtn').val();
            let jsonDataOfStore = {
                name: name,
                store_address: store_address,
                store_start_date: store_start_date,
                store_mail: store_mail,
                store_meta_id: store_meta_id,
                store_pass_key: store_pass_key,
                store_status: store_status
            }

            ajaxPutData(`/api/stores/${id}`, jsonDataOfStore, (response) => {
                $('#editStoreModal').modal('hide');
                $('.modal-backdrop.show').css('opacity', '0');

                $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);     
                api_for_store()
            })
            
        })
    });

    // GETTING DATA FROM API 
    function api_for_store() {
        ajaxGetData('/api/stores', (response) => {
            store_list(response)
        })
    }

    // LISTING THE STORES 
    function store_list(response){
        
        $('#store-table tbody').empty();
        if (response && response.data.length > 0) {
            response.data.reverse();
            $.each(response.data, function(index, store) {
                let formattedStatus = (store.store_status == 1) ? 'Active' : 'Deactive';
                $('#store-table tbody').append(`
                    <tr class="store-row" style="cursor:pointer;" data-id="${store.id}"> 
                        <td scope="row">${index+1}</td>   
                        <td >${store.name}</td>   
                        <td >${store.store_meta_id}</td> 
                        <td >${store.store_mail}</td>
                        <td >${formattedStatus}</td>
                        <td >
                            <button type="button" class="btn btn-secondary btn-sm editStore" data-toggle="modal" data-target="#editStoreModal" data-store-id="${store.id}">&#9998;</button>
                            <button type="button" class="btn btn-info btn-sm viewStore" data-toggle="modal" data-target="#viewStoreModal" data-store-id="${store.id}">&#x1F441;</button>

                            ${(store.store_status == 1) ? `<button type="button" class="btn btn-danger btn-sm disableStore" value="${store.id}">&#10005;</button>` : `<button type="button" class="btn btn-success btn-sm enableStore" value="${store.id}">&#10003;</button>`}
                        </td>   
                    </tr>
                `);
            });
        } else {
            $('#store-table tbody').append(`
                <tr>
                    <td class="text-center" colspan="9">No records found</td>
                </tr>
            `);
        }
    }


    // VIEW STORE 
    $(document).on('click', '.viewStore', function() {
        let id = $(this).data('store-id');

        ajaxGetData(`/api/stores/${id}`, (response) => {
            $('#view-store-table tr:nth-child(1) td:nth-child(3)').text(response.data.name);
            $('#view-store-table tr:nth-child(2) td:nth-child(3)').text(response.data.store_mail); 
            $('#view-store-table tr:nth-child(3) td:nth-child(3)').text(response.data.store_address); 
            $('#view-store-table tr:nth-child(4) td:nth-child(3)').text(response.data.store_start_date); 
            $('#view-store-table tr:nth-child(5) td:nth-child(3)').text(response.data.store_meta_id);
            $('#view-store-table tr:nth-child(6) td:nth-child(3)').text(response.data.store_pass_key); 
            $('#view-store-table tr:nth-child(7) td:nth-child(3)').text(response.data.store_status == 1 ? 'Active' : 'Deactive');
        })
    });    
    // SEARCH FUNCATIONALITY 
    $(document).ready(function(){
        $('#search').on('input', function(){
            var searchText = $(this).val().toLowerCase();
            var found = false;
            
            $('.store-row').each(function(){
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

    // GENERATE RANDOM PASSKEY
    function generatePasskey(length) {
        let chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        let passkey = '';
        for (let i = 0; i < length; i++) {
            passkey += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return passkey;
    }

    $(document).ready(function() {
        $('#store_credentials').click(function() {
            let passkey = generatePasskey(8); 
            $('#store_passkey').val(passkey);
        });
    });

    


</script>

@endsection
