@extends('layouts.dashboard')

@section('title', 'Sync History')

@section('content')
<style>
    .badge-succeed  { background: #28a745; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; }
    .badge-failed   { background: #dc3545; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; }
    .badge-in       { background: #007bff; color: #fff; padding: 4px 8px; border-radius: 10px; font-size: 0.75rem; }
    .badge-out      { background: #fd7e14; color: #fff; padding: 4px 8px; border-radius: 10px; font-size: 0.75rem; }
    #syncData tr:hover { background: #f8f9fa; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark mb-0">Store Sync History</h2>
        <div>
            <button type="button" class="btn btn-primary mr-2" id="sessionValue" value="{{ Session::get('storeId') }}">
                <i class="fa fa-download mr-1"></i> Sync In
            </button>
            <button type="button" class="btn btn-warning" id="syncOutBtn">
                <i class="fa fa-upload mr-1"></i> Sync Out
            </button>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="form-row align-items-end">
                <div class="col-md-3">
                    <label class="mb-1">Start Date</label>
                    <input type="date" class="form-control" id="startDate">
                </div>
                <div class="col-md-3">
                    <label class="mb-1">End Date</label>
                    <input type="date" class="form-control" id="endDate">
                </div>
                <div class="col-auto">
                    <button class="btn btn-success" id="findBtn">Find</button>
                    <button class="btn btn-secondary ml-2" id="clearBtn">Clear</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row mb-4" id="summaryCards" style="display:none!important">
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1">Total Syncs</h6>
                    <h4 class="mb-0 font-weight-bold" id="totalSyncs">0</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1">Successful</h6>
                    <h4 class="mb-0 font-weight-bold text-success" id="successSyncs">0</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1">Sync In</h6>
                    <h4 class="mb-0 font-weight-bold text-primary" id="syncInCount">0</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center shadow-sm">
                <div class="card-body py-3">
                    <h6 class="text-muted mb-1">Sync Out</h6>
                    <h4 class="mb-0 font-weight-bold text-warning" id="syncOutCount">0</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f4f6f9;">
                        <tr>
                            <th class="pl-3" style="width:60px">#</th>
                            <th>Sync Date</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="syncData">
                        <tr><td colspan="4" class="text-center text-muted py-4">Loading…</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Loading Modal --}}
<div class="modal fade" id="loadingModal" tabindex="-1" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center p-4">
            <div class="spinner-border text-primary mb-3 mx-auto" role="status"></div>
            <p class="mb-0 font-weight-bold">Syncing Data…</p>
            <small class="text-muted">Please wait, do not close the page.</small>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    getSyncHist();

    $('#sessionValue').on('click', function () {
        sync($(this).val());
    });

    $('#syncOutBtn').on('click', syncOut);

    $('#findBtn').on('click', function () {
        const s = $('#startDate').val();
        const e = $('#endDate').val();
        if (s && e && e < s) { Swal.fire('Validation', 'End date must be after start date.', 'warning'); return; }
        getSyncHist(s, e);
    });

    $('#clearBtn').on('click', function () {
        $('#startDate').val('');
        $('#endDate').val('');
        getSyncHist();
    });
});

function getSyncHist(startDate = '', endDate = '') {
    const url = `/api/get/sync/history?start_date=${startDate}&end_date=${endDate}`;
    ajaxGetData(url, function (response) {
        const rows = response?.data ?? [];
        $('#syncData').empty();

        if (!rows.length) {
            $('#syncData').html('<tr><td colspan="4" class="text-center text-muted py-4">No sync records found.</td></tr>');
            $('#summaryCards').hide();
            return;
        }

        let successCount = 0, inCount = 0, outCount = 0;

        rows.forEach(function (item, idx) {
            const isSuccess = (item.sync_status || '').toLowerCase() === 'succeed';
            const statusBadge = isSuccess
                ? `<span class="badge-succeed">Succeed</span>`
                : `<span class="badge-failed">${item.sync_status}</span>`;

            // Guess type from sync_date or a type field if it exists
            const syncType = item.sync_type || '';
            let typeBadge = '';
            if (syncType === 'in' || syncType === 'Sync In') {
                typeBadge = `<span class="badge-in">Sync In</span>`;
                inCount++;
            } else if (syncType === 'out' || syncType === 'Sync Out') {
                typeBadge = `<span class="badge-out">Sync Out</span>`;
                outCount++;
            } else {
                typeBadge = `<span class="text-muted">–</span>`;
            }

            if (isSuccess) successCount++;

            // Format date nicely
            const rawDate = item.sync_date ?? '';
            let displayDate = rawDate;
            try {
                const d = new Date(rawDate);
                if (!isNaN(d)) displayDate = d.toLocaleDateString('en-IN', { day:'2-digit', month:'short', year:'numeric' });
            } catch(e) {}

            $('#syncData').append(`
                <tr>
                    <td class="pl-3 text-muted">${idx + 1}</td>
                    <td><strong>${displayDate}</strong></td>
                    <td>${typeBadge}</td>
                    <td>${statusBadge}</td>
                </tr>
            `);
        });

        // Update summary cards
        $('#totalSyncs').text(rows.length);
        $('#successSyncs').text(successCount);
        $('#syncInCount').text(inCount);
        $('#syncOutCount').text(outCount);
        if (inCount > 0 || outCount > 0) $('#summaryCards').show();
    }, function () {
        $('#syncData').html('<tr><td colspan="4" class="text-center text-danger py-4">Failed to load sync history.</td></tr>');
    });
}

function sync(store_id) {
    $('#loadingModal').modal('show');
    $('#loadingModal').one('shown.bs.modal', function () {
        ajaxGetData(`/sync-data/${store_id}`, function (response) {
            $('#loadingModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            if (response?.status == 200) {
                Swal.fire('Sync In Complete', 'Data synced successfully.', 'success');
                getSyncHist();
            } else {
                Swal.fire('Sync Failed', 'Could not sync data.', 'error');
            }
        }, function () {
            $('#loadingModal').modal('hide');
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            Swal.fire('Error', 'An error occurred while syncing.', 'error');
        });
    });
}

function syncOut() {
    let syncResult = null;
    const storeId = $('#sessionValue').val();

    $('#loadingModal').one('shown.bs.modal', function () {
        fetch(`/sync/out/data/${storeId}`, {
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
        })
        .then(r => r.json())
        .then(data => {
            syncResult = { success: data.success, message: data.message };
            $('#loadingModal').modal('hide');
        })
        .catch(err => {
            syncResult = { success: false, message: 'An unexpected error occurred.' };
            $('#loadingModal').modal('hide');
        });
    });

    $('#loadingModal').one('hidden.bs.modal', function () {
        if (!syncResult) return;
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
        if (syncResult.success) {
            Swal.fire('Sync Out Complete', syncResult.message, 'success');
            getSyncHist();
        } else {
            Swal.fire('Sync Out Failed', syncResult.message, 'error');
        }
    });

    $('#loadingModal').modal('show');
}
</script>
@endsection
