<!-- resources/views/admin/stocks/index.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Doctors - List')

<style>
    #message.bg-success{
        display:none;
    }
</style>

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Doctors List</h1>
        <div id="message" class="text-center bg-success text-light p-3"></div>
        <div class="search-add">
            <form class="d-flex align-items-center justify-content-between">
                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search" placeholder="Enter Doctor Name">
                </div>
                <div class="form-group">
                    
                    <button type="button" class="btn btn-md btn-secondary" data-toggle="modal" data-target="#addDoctorModal">
                        Add Doctor
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- ADD DOCTOR MODEL  -->
    <div class="modal fade" id="addDoctorModal" tabindex="-1" role="dialog" aria-labelledby="addDoctorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog container" role="document">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center align-items-center text-uppercase">
                    <h5 class="modal-title" id="addDoctorModalLabel">Add Doctor</h5>
                </div>
                <div class="modal-body">
                    <form id="addDoctor" action="{{url('/doctor')}}" method="POST" class="container">
                        <div id="message" class="text-center bg-success text-light p-3"></div>
                        <div class="form-group">
                            <label for="name">Doctor Name</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="mail">Doctor Mail</label>
                            <input type="email" class="form-control" id="mail" name="mail" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Doctor Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" required>
                        </div>

                        <div class="form-group">
                            <label for="degree">Doctor Degree</label>
                            <input type="tel" class="form-control" id="degree" name="degree" required>
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
                            <button type="submit" id="addDoctorFormBtn" class="btn btn-success mx-2">Save</button>
                            <button type="button" id="" class="btn btn-secondary" data-dismiss="modal" aria-label="Close">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- END DOCTOR MODEL  -->

    <!-- EDIT DOCTOR MODEL -->
    <div class="modal fade" id="editDoctorModal" tabindex="-1" role="dialog" aria-labelledby="editDoctorModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editDoctorModalLabel">Edit Doctor</h5>
                </div>
                <div class="modal-body">
                    <form id="addDoctorForm">
                        <div id="message" class="text-center bg-success text-light p-3"></div>
                        <div class="form-group">
                            <label for="newDoctor">Doctor Name</label>
                            <input type="text" class="form-control" id="edit_doctor_name" name="edit_doctor_name" required>
                        </div>
                        <div class="form-group">
                            <label for="">Doctor Mail</label>
                            <input type="email" class="form-control" id="edit_doctor_mail" name="edit_doctor_mail" required>
                        </div>
                        <div class="form-group">
                            <label for="">Doctor Phone Number</label>
                            <input type="tel" class="form-control" id="edit_doctor_phone" name="edit_doctor_phone" required>
                        </div>
                        <div class="form-group">
                            <label for="">Doctor Degree</label>
                            <input type="text" class="form-control" id="edit_doctor_degree" name="edit_doctor_degree" readonly>
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
                            <button type="submit" id="updateDoctorForm" class="btn btn-success btn-sm mx-2">Save Changes</button>
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
                    <table class="table text-dark border table-hover text-center"  id="doctor-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Phone Number</th>
                                <th>Email</th>
                                <th>Degree</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <div id="noDoctorFoundMessage" class="text-center mt-3" style="display: none;">No Doctor found</div>
                </div>
            </div>
        </div>
    </div>  
    <!-- END DOCTOR LIST TABLE -->

</div>

<script>
    // ADDING DOCTOR FUNCTIONALITY
    $(document).ready(function(){
        $('#addDoctor').on('submit', function(event){
            event.preventDefault();

            $.ajax({
                url: '/api/doctor',
                data: jQuery('#addDoctor').serialize(), 
                method: 'POST',

                success:function(result){
                    jQuery('#addDoctor')[0].reset();
                    alert("Doctor Added Successfully");
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

    // EDIT DOCTOR FUNCTIONALITY
    // $(document).ready(function() {
        $(document).on('click', '.editDoctor', function() {
            let id = $(this).val();
            ajaxGetData(`/api/doctor/${id}`, (response) => {
                $('#edit_doctor_name').val(response?.data?.name);
                $('#edit_doctor_mail').val(response?.data?.mail);
                $('#edit_doctor_phone').val(response?.data?.phone);
                $('#edit_doctor_degree').val(response?.data?.degree);
                $('#updateDoctorForm').val(response?.data?.id)
                if (response?.data?.status == 1) {
                    $('#edit_statusActive').prop('checked', true);
                } else {
                    $('#edit_statusInactive').prop('checked', true);
                }
                $('#editDoctorModal').modal('show');
            })
            
            $('#updateDoctorForm').on('click', function(event) {
                event.preventDefault(); 
                let name = $('#edit_doctor_name').val();
                let mail = $('#edit_doctor_mail').val();
                let phone = $('#edit_doctor_phone').val();
                let degree = $('#edit_doctor_degree').val();
                let id = $('#updateDoctorForm').val();
                let status = $('input[name="edit_status"]:checked').val();
                let jsonDataOfDoctor = {
                    id:id,
                    name: name,
                    mail: mail,
                    phone:phone,
                    degree:degree,
                    status: status
                }
                ajaxPutData(`/api/doctor/${id}`, jsonDataOfDoctor, (response) => {
                    $('#message').removeClass('bg-danger').addClass('bg-success').html("Doctor Updated Successfully...").show();                            
                    setTimeout(function() {
                        $('#message').hide();
                    }, 3000);
                    $('#editDoctorModal').modal('hide');
                    api_for_doctor();
                })
            })
        });        
    // }); 

    // FETCHING DOCTOR
    // FUNCTION FOR CALLING API FOR DOCTOR
    function api_for_doctor() {
        fetch(`/api/doctor`)
            .then(response => response.json())
            .then(data => doctor_list(data))
            .catch(error => console.error('Error fetching data:', error));
    }

    // FUNCTION FOR APPENDING LIST
    function doctor_list(response) {
        $('#doctor-table tbody').empty();
        if (response && response.data.length > 0) {
            response.data.reverse();
            $.each(response.data, function(index, doctor) {
                let formattedStatus = (doctor.status == 1) ? 'Active' : 'Deactive';
                $('#doctor-table tbody').append(`
                    <tr class="doctor-row" style="cursor:pointer;" data-id="${doctor?.id}"> 
                        <td scope="row"> ${index+1} </td>   
                        <td> ${doctor?.name} </td>   
                        <td> ${doctor?.phone} </td>   
                        <td> ${doctor?.mail} </td>   
                        <td> ${doctor?.degree} </td>   
                        <td> ${formattedStatus} </td> 
                        <td>
                            <button type="button" class="btn btn-secondary btn-sm editDoctor" value="${doctor?.id}">&#9998;</button> 
                            ${(doctor.status == 1) ? `<button type="button" class="btn btn-danger btn-sm disableDoctor" value="${doctor?.id}">&#10005;</button>` : `<button type="button" class="btn btn-success btn-sm enableDoctor" value="${doctor?.id}">&#10003;</button>`}
                        </td>   
                    </tr>
                `)
            });
        } 
        else {
            $('#doctor-table tbody').append(`
                <tr>
                    <td class="text-center">${response.message}</td>
                </tr>
            `);
        }
    }

    api_for_doctor();

    // DEACIVATE FUNCTIONALITY 
    $(document).on('click', '.disableDoctor', function() {
        let id = $(this).val()
        ajaxGetData(`/api/disable/doctor/${id}`, (response) => {
            $('#message').removeClass('bg-success').addClass('bg-danger').html(response.message).show();                            
            setTimeout(function() {
                $('#message').hide();
            }, 3000);
            api_for_doctor()
        })
       
    });
    
    
    // ACTIVATE FUNCTIONALITY 
    $(document).on('click', '.enableDoctor', function() {
        let id = $(this).val()
        ajaxGetData(`/api/enable/doctor/${id}`, (response) => {
            $('#message').removeClass('bg-danger').addClass('bg-success').html(response.message).show();                            
            setTimeout(function() {
                $('#message').hide();
            }, 3000);
            api_for_doctor()
        })
       
    });


    // SEARCH FUNCATIONALITY 
    $(document).ready(function(){
        $('#search').on('input', function(){
            var searchText = $(this).val().toLowerCase();
            var found = false;
            
            $('.doctor-row').each(function(){
                var brandName = $(this).find('td:eq(1)').text().toLowerCase(); 
                if(brandName.includes(searchText)){
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            
            
            if (found) {
                $('#noDoctorFoundMessage').hide();
            } else {
                $('#noDoctorFoundMessage').show();
            }
        });
    });
</script>


@endsection
