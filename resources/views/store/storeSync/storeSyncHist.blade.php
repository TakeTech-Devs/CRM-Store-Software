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
    <button type="button" class="btn btn-primary" id="sessionValue" value="{{ Session::get('storeId') }}">Sync</button>
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
        <div class="col-md-12">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Sync Date</th>
                        <th>Sync Status</th>
                    </tr>
                </thead>
                <tbody id="syncData">

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
                    <span class="visually-hidden">Loading...</span>
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
            // $('#loadingModal').modal('show');
            sync(store_id);
        });

        $('#findBtn').on('click', function () {
            let startDate = $('#startDate').val();
            let endDate = $('#endDate').val();
            getSyncHist(startDate, endDate);
        });
    });

    function sync(store_id) {
        ajaxGetData(`/sync-data/${store_id}`, (response) => {
            // $('#loadingModal').modal('hide');
            if (response?.status == 200) {
                getSyncHist();
            } else {
                alert('Sync failed!');
            }
        });
    }

    function getSyncHist(startDate = '', endDate = '') {
        // $('#loadingModal').modal('show');
        let url = `/api/get/sync/history?start_date=${startDate}&end_date=${endDate}`;
        
        ajaxGetData(url, (response) => {
            $('#loadingModal').modal('hide');
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
</script>
@endsection
