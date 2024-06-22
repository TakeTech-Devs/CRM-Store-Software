@extends('layouts.dashboard')

@section('title', 'Backup')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Backup List</h1>
        <div class="search-add">
            <form class="d-flex align-items-center justify-content-between">
                <!-- <div class="form-group d-flex align-items-center justify-content-center mx-3">
                    <label for="search">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="search">
                </div> -->
                <div class="form-group mt-3">
                    
                    <button type="button" class="btn btn-md btn-secondary" id="gen_back_up">
                        Generate Backup
                    </button>
                </div>
            </form>
        </div>
    </div>



    <!-- BACKUP LIST TABLE  -->
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="table-responsive" style="height: 60vh; overflow:auto"  >
                    <table class="table text-dark border table-hover text-center"  id="backup-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>#</th>
                                <th>Backup Name</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="backupId">
                          
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>  
    <!-- END BACKUP LIST TABLE -->

</div>    

<script>
    $(document).ready(function () {
        list()
       

        $(document).on('click', '#download', function() {
            const fileUrl = $(this).data('file-url');
            console.log(fileUrl);
            
            const link = document.createElement('a');
            link.href = fileUrl;
            link.target = '_blank';
            link.download = '';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        });

        $(document).on('click', '#del', function(){
            console.log()
            ajaxGetData(`/api/backup/${$(this).val()}`, (response)=>{
                alert("deleted");
                list()
            })
        })

        $(document).on('click', '#gen_back_up', function(){
            console.log()
            ajaxGetData(`/api/store/backup`, (response)=>{
                list()
            })
        })
        

    });

    function list(){
        ajaxGetData(`/api/backups`, (response)=>{
            $('#backupId').html('')
            for (let index = 0; index < response?.data.length; index++) {
                const element = response?.data[index];
                $('#backupId').append(`
                      <tr>
                                <td>${index+1}</td>
                                <td>${element?.file_name}</td>
                                <td>${element?.date}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" id="download" data-file-url="${element?.file_path}" >Downlaod</button>
                                    <button class="btn btn-sm btn-warning" id="del" value = ${element?.id}>Delete</button>
                                </td>
                            </tr>
                `)
            }
        })
    }
</script>

    <!-- Include Bootstrap JS and Popper.js -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

@endsection
                    