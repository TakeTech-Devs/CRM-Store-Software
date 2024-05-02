<!-- resources/views/admin/stocks/index.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Customers - List')

<style>
    #message.bg-success{
        display:none;
    }
</style>

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Customers List</h1>
        <div id="message" class="text-center bg-success text-light p-3"></div>
        <div class="search-add">
            <form class="d-flex align-items-center justify-content-between">
                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search" placeholder="Enter Customer Name">
                </div>
                <div class="form-group">
                    
                    <button type="button" class="btn btn-md btn-secondary" data-toggle="modal" data-target="#addCustomerModal">
                        Add Customer
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ADD CUSTOMER MODEL  -->
    <div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addCustomerModalLabel">Add Customer</h5>
                </div>
                <div class="modal-body">
                    <form id="addCustomer" action="{{url('/customers')}}" method="POST" class="container">
                        <div id="message" class="text-center bg-success text-light p-3"></div>
                        <div class="form-group">
                            <label for="">Customer Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="">Customer Mail</label>
                            <input type="email" class="form-control" id="mail" name="mail" required>
                        </div>
                        <div class="form-group">
                            <label for="">Customer Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
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
                            <button type="submit" id="addCustomerFormBtn" class="btn btn-success mx-2">Save</button>
                            <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- END CUSTOMER MODEL  -->

    <!-- EDIT CUSTOMER MODEL -->
    <div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCustomerModalLabel">Edit Customer</h5>
                </div>
                <div class="modal-body">
                    <form id="addCustomerForm">
                        <div id="message" class="text-center bg-success text-light p-3"></div>
                        <div class="form-group">
                            <label for="newCustomer">Customer Name</label>
                            <input type="text" class="form-control" id="edit_customer_name" name="edit_customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="">Customer Mail</label>
                            <input type="email" class="form-control" id="edit_customer_mail" name="edit_customer_mail" required>
                        </div>
                        <div class="form-group">
                            <label for="">Customer Phone Number</label>
                            <input type="tel" class="form-control" id="edit_customer_phone" name="edit_customer_phone" required>
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
                        <div class="save-button d-flex align-items-center justify-content-center">
                            <button type="submit" id="updateCustomerForm" class="btn btn-success btn-sm mx-2">Save Changes</button>
                            <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT CUSTOMER   MODEL -->



    <!-- CUSTOMER    LIST TABLE  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive" style="height: 60vh; overflow:auto"  >
                    <table class="table text-dark border table-hover text-center"  id="customer-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <div id="noCustomerFoundMessage" class="text-center mt-3" style="display: none;">No Customer found</div>

                </div>
            </div>
        </div>
    </div>  
    <!-- END CUSTOMER LIST TABLE -->

</div>

<script>
    // ADDING Customer FUNCTIONALITY
    $(document).ready(function(){
        $('#addCustomer').on('submit', function(event){
            event.preventDefault();

            $.ajax({
                url: '/api/customer',
                data: jQuery('#addCustomer').serialize(), 
                method: 'POST',

                success:function(result){
                    jQuery('#addCustomer')[0].reset();
                    alert("Customer Added Successfully");
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

    // EDIT CUSTOMER FUNCTIONALITY
    $(document).on('click', '.editCustomer', function() {
        let id = $(this).val();
        ajaxGetData(`/api/customer/${id}`, (response) => {
            $('#edit_customer_name').val(response?.data?.name);
            $('#edit_customer_mail').val(response?.data?.mail);
            $('#edit_customer_phone').val(response?.data?.phone);
            $('#updateCustomerForm').val(response?.data?.id)
            if (response?.data?.status == 1) {
                $('#edit_statusActive').prop('checked', true);
            } else {
                $('#edit_statusInactive').prop('checked', true);
            }
            $('#editCustomerModal').modal('show');
        })
        
        $('#updateCustomerForm').on('click', function(event) {
            event.preventDefault(); 
            let name = $('#edit_customer_name').val();
            let mail = $('#edit_customer_mail').val();
            let phone = $('#edit_customer_phone').val();
            let id = $('#updateCustomerForm').val();
            let status = $('input[name="edit_status"]:checked').val();
            let jsonDataOfCustomer = {
                id:id,
                name: name,
                mail: mail,
                phone:phone,
                status: status
            }
            ajaxPutData(`/api/customer/${id}`, jsonDataOfCustomer, (response) => {
                $('#message').removeClass('bg-danger').addClass('bg-success').html("Customer Updated Successfully...").show();                            
                setTimeout(function() {
                    $('#message').hide();
                }, 3000);
                $('#editCustomerModal').modal('hide');
                api_for_customer();
            })
        })
    });        

    // FETCHING DOCTOR
    // FUNCTION FOR CALLING API FOR DOCTOR
    function api_for_customer() {
        fetch(`/api/customer`)
            .then(response => response.json())
            .then(data => customer_list(data))
            .catch(error => console.error('Error fetching data:', error));
    }

    // FUNCTION FOR APPENDING LIST
    function customer_list(response) {
        $('#customer-table tbody').empty();
        if (response && response.data.length > 0) {
            response.data.reverse();
            $.each(response.data, function(index, customer) {
                let formattedStatus = (customer.status == 1) ? 'Active' : 'Deactive';
                $('#customer-table tbody').append(`
                    <tr class="customer-row" style="cursor:pointer;" data-id="${customer?.id}"> 
                        <td scope="row"> ${index+1} </td>   
                        <td> ${customer?.name} </td>   
                        <td> ${customer?.phone} </td>   
                        <td> ${customer?.mail} </td>   
                        <td> ${formattedStatus} </td> 
                        <td>
                            <button type="button" class="btn btn-secondary btn-sm editCustomer" value="${customer?.id}">&#9998;</button> 
                            ${(customer.status == 1) ? `<button type="button" class="btn btn-danger btn-sm disableCustomer" value="${customer?.id}">&#10005;</button>` : `<button type="button" class="btn btn-success btn-sm enableCustomer" value="${customer?.id}">&#10003;</button>`}
                        </td>   
                    </tr>
                `)
            });
        } 
        else {
            $('#customerdoctor-table tbody').append(`
                <tr>
                    <td class="text-center">${response.message}</td>
                </tr>
            `);
        }
    }

    api_for_customer();

    // DEACIVATE FUNCTIONALITY 
    $(document).on('click', '.disableCustomer', function() {
        let id = $(this).val()
        ajaxGetData(`/api/disable/customer/${id}`, (response) => {
            $('#message').removeClass('bg-success').addClass('bg-danger').html(response.message).show();                            
            setTimeout(function() {
                $('#message').hide();
            }, 3000);
            api_for_customer()
        })
       
    });
    
    
    // ACTIVATE FUNCTIONALITY 
    $(document).on('click', '.enableCustomer', function() {
        let id = $(this).val()
        ajaxGetData(`/api/enable/customer/${id}`, (response) => {
            $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();                            
            setTimeout(function() {
                $('#message').hide();
            }, 3000);
            api_for_customer()
        })
       
    });


    // SEARCH FUNCATIONALITY 
    $(document).ready(function(){
        $('#search').on('input', function(){
            var searchText = $(this).val().toLowerCase();
            var found = false;
            
            $('.customer-row').each(function(){
                var customer = $(this).find('td:eq(1)').text().toLowerCase(); 
                if(customer.includes(searchText)){
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            
            
            if (found) {
                $('#noCustomerFoundMessage').hide();
            } else {
                $('#noCustomerFoundMessage').show();
            }
        });
    });
</script>
@endsection
