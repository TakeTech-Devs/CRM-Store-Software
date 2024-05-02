<!-- resources/views/admin/stocks/index.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Employees - List')

<style>
    #message.bg-success{
        display:none;
    }
</style>

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Employees List</h1>
        <div id="message" class="text-center bg-success text-light p-3"></div>
        <div class="search-add">
            <form class="d-flex align-items-center justify-content-between">
                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search" placeholder="Search Employee Name">
                </div>
                <div class="form-group">
                    
                    <button type="button" class="btn btn-md btn-secondary" data-toggle="modal" data-target="#addEmployeeModal">
                        Add Employee
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ADD EMPLOYEE MODEL  -->
    <div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addEmployeeModalLabel">Add Employee</h5>
                </div>
                <div class="modal-body">
                    <form id="addEmployeeForm" action="{{ url('admin/create/employee') }}" class="container d-flex flex-wrap">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_name" class="form-label">Employee Name</label>
                                <input type="text" class="form-control" id="employee_name" name="employee_name">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_mail" class="form-label">Employee Mail</label>
                                <input type="text" class="form-control" id="employee_mail" name="employee_mail">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group col-md-3 float-right">
                                <input type="button" class="form-control bg-secondary text-light btn-sm" id="employee_credentials" value="Generate Credentials">
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_ID" class="form-label">Employee ID</label>
                                <input type="text" class="form-control" id="employee_ID" name="employee_ID">
                            </div>
                        </div>
                    
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="employee_passkey" class="form-label">Employee Passkey</label>
                                <input type="text" class="form-control" id="employee_passkey" name="employee_passkey" readonly required>
                            </div>
                        </div>

                        

                        <div class="col-md-12 d-none">
                            <div class="d-flex align-items-start justify-content-start">
                                <label for="status" class="form-label">Status: </label>
                                <label class="mx-5">
                                    <input type="radio" class="form-group mx-1" id="statusActive" name="status" value="1"checked>
                                    Active
                                </label>
                                <label>
                                    <input type="radio" class="form-group mx-1" id="statusInactive"  name="status" value="0">
                                    Deactive
                                </label>
                            </div>
                        </div>
                        <div class="col-md-12">  
                            <div class="form-group">
                                <label for="role_assign" class="form-label">Assign Role:</label>
                                <input type="text" class="form-control" id="role_assign" name="role_assign" required>
                            </div>    
                            <!-- <label for="role_assign">Assign Role:</label> -->
                            <!-- <div class="form-group d-flex align-items-center justify-content-between">
                                
                                <select name="role_assign" id="role_assign" class="form-control mr-2">
                                    <option value="">Select Role...</option>
                                </select>
                                <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#addRoleModal"><i class="fas fa-plus"></i></button>
                            </div> -->
                        </div>
                        <div class="col-md-12 d-flex align-items-center justify-content-between flex-wrap mt-3 mb-4">
                            <div class="col-md-6 d-flex align-items-center flex-wrap">
                                <label for="stock_module">Stock Module</label>
                                <div class=" d-flex align-items-center flex-wrap">

                                    <div class="form-check">
                                        <input type="checkbox" name="all" class="form-check-label" id="stock_module_all">
                                        <label for="stock_module_all">All</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="create" class="form-check-label" id="stock_module_create">
                                        <label for="stock_module_create">Create</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="disable" class="form-check-label" id="stock_module_disable">
                                        <label for="stock_module_disable">Disable</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="view" class="form-check-label" id="stock_module_view">
                                        <label for="stock_module_view">View</label>                                
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center flex-wrap">
                                <label for="user_module">User Module</label>
                                <div class=" d-flex align-items-center flex-wrap">

                                    <div class="form-check">
                                        <input type="checkbox" name="all" class="form-check-label" id="user_module_all">
                                        <label for="user_module_all">All</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="create" class="form-check-label" id="user_module_create">
                                        <label for="user_module_create">Create</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="disable" class="form-check-label" id="user_module_disable">
                                        <label for="user_module_disable">Disable</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="view" class="form-check-label" id="user_module_view">
                                        <label for="user_module_view">View</label>                                
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-4">
                                <label for="report_module">Report Module</label>
                                <div class=" d-flex align-items-center flex-wrap">

                                    <div class="form-check">
                                        <input type="checkbox" name="all" class="form-check-label" id="report_module_all">
                                        <label for="report_module_all">All</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="create" class="form-check-label" id="report_module_create">
                                        <label for="report_module_create">Create</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="disable" class="form-check-label" id="report_module_disable">
                                        <label for="report_module_disable">Disable</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="view" class="form-check-label" id="report_module_view">
                                        <label for="report_module_view">View</label>                                
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6 d-flex align-items-center flex-wrap mt-4">
                                <label for="store_module">Store Module</label>
                                <div class=" d-flex align-items-center flex-wrap">

                                    <div class="form-check">
                                        <input type="checkbox" name="all" class="form-check-label" id="store_module_all">
                                        <label for="store_module_all">All</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="create" class="form-check-label" id="store_module_create">
                                        <label for="store_module_create">Create</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="disable" class="form-check-label" id="store_module_disable">
                                        <label for="store_module_disable">Disable</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="checkbox" name="view" class="form-check-label" id="store_module_view">
                                        <label for="store_module_view">View</label>                                
                                    </div>
                                </div>

                            </div>
                        </div>
                        
                        <div class="col-md-12">

                            <button type="button" id="addEmployeeBtn" class="btn btn-info text-center float-right">Add Employee</button>
                        </div>                    
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- END ADD EMPLOYEE MODEL  -->

    <!-- EDIT EMPLOYEE MODEL -->
    <div class="modal fade" id="editEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="editEmployeeModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmployeeModalLabel">Edit Product</h5>
                </div>
                <div class="modal-body">
                    <form id="editEmployeeForm" class="container d-flex flex-wrap">
                                          
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- END EDIT EMPLOYEE MODEL -->

    <!-- ADD ROLES MODEL -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" role="dialog" aria-labelledby="addRoleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog bg-dark text-light" role="document">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">Add Role</h5>
                </div>
                <div class="modal-body">
                    <form id="addRole" action="{{ url('/roles')}}" method="POST" class="container">
                        <div class="form-group">
                            <label for="newRole">Role Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>  
                        <div class="save-button d-flex align-items-center justify-content-center">
                            <button type="submit" id="addRoleForm" class="btn btn-success mx-2">Save</button>
                            <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>          
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--END ADD ROLE MODEL -->






    <!-- EMPLOYEE LIST TABLE  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive" style="height: 60vh; overflow:auto"  >
                    <table class="table text-dark border table-hover text-center"  id="admin-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>ID</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    <div id="noEmployeeFoundMessage" class="text-center mt-3" style="display: none;">No Category found</div>

                </div>
            </div>
        </div>
    </div>  
    <!-- END ADMIN LIST TABLE -->

</div>

<script>

    // ADD ROLES 
    // FETCHING ROLES 
    function getRoles() { 
        ajaxGetData(`/api/roles`, (response)=>{
            $(`#role_assign${rowID}`).append(`<option value="">Choose</option>`)
            for (let index = 0; index < response?.data.length; index++) {
                const element = response?.data[index];
                $(`#role_assign${rowID}`).append(`<option value=${element.id}>${element.pack_name}</option>`)
            }
        })
    }
    // ADDING ROLES
    $(document).ready(function(){
        $('#addRole').on('submit', function(event){
            event.preventDefault();
            
            $.ajax({
                url: '/api/roles',
                data: jQuery('#addRole').serialize(), 
                method: 'POST',
                
                success:function(result){
                    console.log(result)
                    $('#role_assign').append('<option value="' + result.data.id + '" selected>' +
                        result.data.name + '</option>');
                    $('#message').removeClass('bg-danger').addClass('bg-success').html(result.message).show();
                    jQuery('#addRole')[0].reset();
                    setTimeout(function() {
                        $('#message').hide();
                    }, 3000);
                    
                    $('#addRoleModal').hide();
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

     // SEARCH FUNCATIONALITY 
     $(document).ready(function(){
        $('#search').on('input', function(){
            var searchText = $(this).val().toLowerCase();
            var found = false;
            
            $('.employee-row').each(function(){
                var EmployeeName = $(this).find('td:eq(1)').text().toLowerCase(); 
                if(EmployeeName.includes(searchText)){
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            
            
            if (found) {
                $('#noEmployeeFoundMessage').hide();
            } else {
                $('#noEmployeeFoundMessage').show();
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
        $('#employee_credentials').click(function() {
            let passkey = generatePasskey(8); 
            $('#employee_passkey').val(passkey);
        });
    });

</script>

@endsection
