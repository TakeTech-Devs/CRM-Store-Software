@extends('layouts.dashboard')

@section('title', 'Store Details')

@section('content')
<style>
    .container {
        margin-top: 50px;
    }

    .sync-now {
        float: right;
    }

    .find-btn {
        margin-top: 32px;
    }
</style>

<div class="col text-right">
    <button type="button" class="btn btn-primary" id="sessionValue" value="{{ Session::get('storeId') }}">Sync
        In</button>
    <button type="button" class="btn btn-warning" id="syncOutBtn">Sync Out</button> <!-- New Sync Out button -->
</div>
<div class="container">
    <h1>Store Sync History</h1>
    <div class="row">
        <div class="col-md-3">
            <label for="startDate" class="form-label">Start Date</label>
            <input type="date" class="form-control" id="startDate">
        </div>
        <div class="col-md-3">
            <label for="endDate" class="form-label">End Date</label>
            <input type="date" class="form-control" id="endDate">
        </div>
        <div class="col-md-2 find-btn">
            <button class="btn btn-success w-100" id="findBtn">Find</button>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-12" style="overflow-y: auto; height: 400px;">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sync Date</th>
                        <th>Sync Status</th>
                    </tr>
                </thead>
                <tbody id="syncData">
                    <!-- Sync history data will be populated here -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden"></span>
                </div>
                <p>Syncing Data, please wait...</p>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        getSyncHist();
        
        $(document).on('click', '#sessionValue', function () {
            let store_id = $(this).val();
            
            sync(store_id);
        });
        
        $('#findBtn').on('click', function () {
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();
            getSyncHist(startDate, endDate);
        });
        
        // Sync Out Button
        $('#syncOutBtn').on('click', function () {
            syncOut();
        });
    });
    
    function sync(store_id) {
        // Show loading modal
        $('#loadingModal').modal('show');
        $('#loadingModal').css('display', 'block'); // Ensure modal is displayed

        console.log(store_id);
        
        ajaxGetData(`/sync-data/${store_id}`, (response) => {
            console.log(response);
            
            $('#loadingModal').modal('hide'); // Hide loading modal
            if (response?.status == 200) {
                getSyncHist(); // Refresh sync history
            } else {
                alert('Sync failed!');
            }
        }).fail((error) => {
            console.error('Error during sync:', error);
            $('#loadingModal').modal('hide'); // Hide loading modal
            alert('An error occurred while syncing data.');
        });
    }

    function getSyncHist(startDate = '', endDate = '') {
        let url = `/api/get/sync/history?start_date=${startDate}&end_date=${endDate}`;
        
        // Show loading modal while fetching data
        $('#loadingModal').modal('show');
        
        ajaxGetData(url, (response) => {            
            $('#loadingModal').css('display', 'none'); // Hide loading modal
            $('.modal-backdrop').remove(); // Remove backdrop

            if (response?.status == 404) {
                $('#syncData').html(response?.data);
            } else {
                $('#syncData').html('');
                for (let index = 0; index < response?.data.length; index++) {
                    const element = response?.data[index];
                    $('#syncData').append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td>${element?.sync_date}</td>
                            <td>${element?.sync_status}</td>
                        </tr>
                    `);
                }
            }
        });
    }

    // Sync Out Function
    function syncOut() {
        const loadingModal = $('#loadingModal'); // Show modal
        const storeId = $('#sessionValue').val();
        
        loadingModal.modal('show'); // Show loading modal
        
        // Send request for sync out
        fetch(`/sync/out/data/${storeId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
            .then(response => response.json())
            .then(data => {
                loadingModal.modal('hide'); // Hide loading modal
                if (data.success) {
                    alert(data.message);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                loadingModal.modal('hide'); // Hide loading modal
                console.error('Error:', error);
                alert('An unexpected error occurred.');
            });
    }

</script>
@endsection