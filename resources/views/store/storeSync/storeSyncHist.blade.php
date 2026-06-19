@extends('layouts.dashboard')

@section('title', 'Sync History')

@section('content')
<style>
    .badge-succeed  { background: #28a745; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; }
    .badge-failed   { background: #dc3545; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; }
    .badge-in       { background: #007bff; color: #fff; padding: 4px 8px; border-radius: 10px; font-size: 0.75rem; }
    .badge-out      { background: #fd7e14; color: #fff; padding: 4px 8px; border-radius: 10px; font-size: 0.75rem; }
    #syncData tr:hover { background: #f8f9fa; }
    #syncData td, #syncData th { white-space: nowrap; vertical-align: middle; padding: .55rem 1rem; }
    .table thead th { white-space: nowrap; }
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
    </div>

    {{-- Table --}}
    <div class="d-flex justify-content-center">
        <div style="width:500px;">
            <div class="card shadow-sm" style="border-radius:12px;overflow:hidden;">
                <table class="table table-hover mb-0 text-center">
                    <thead>
                        <tr style="background:#f4f6f9;">
                            <th style="width:50px;padding:.65rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;font-weight:700;">#</th>
                            <th style="padding:.65rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;font-weight:700;">Sync Date</th>
                            <th style="width:110px;padding:.65rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;font-weight:700;">Type</th>
                            <th style="width:110px;padding:.65rem .75rem;font-size:.78rem;text-transform:uppercase;letter-spacing:.05em;color:#6c757d;font-weight:700;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="syncData">
                        <tr><td colspan="4" class="text-muted py-4">Loading…</td></tr>
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

const syncErrors = [];

function getSyncHist(startDate = '', endDate = '') {
    const url = `/api/get/sync/history?start_date=${startDate}&end_date=${endDate}`;
    ajaxGetData(url, function (response) {
        const rows = response?.data ?? [];
        $('#syncData').empty();
        syncErrors.length = 0; // reset on each load

        if (!rows.length) {
            $('#syncData').html('<tr><td colspan="4" class="text-center text-muted py-4">No sync records found.</td></tr>');
            $('#summaryCards').hide();
            return;
        }

        let successCount = 0;

        rows.forEach(function (item, idx) {
            const isSuccess = (item.sync_status || '').toLowerCase() === 'succeed';
            const errIdx = syncErrors.length;
            if (!isSuccess) syncErrors.push(item.sync_status || 'Unknown error');
            const statusBadge = isSuccess
                ? `<span class="badge-succeed">Succeed</span>`
                : `<span class="badge-failed" style="cursor:pointer" title="Click to view error" onclick="showError(${errIdx})">Failed <i class='fa fa-info-circle'></i></span>`;

            if (isSuccess) successCount++;

            // Format date nicely
            const rawDate = item.sync_date ?? '';
            let displayDate = rawDate;
            try {
                const d = new Date(rawDate);
                if (!isNaN(d)) {
                    const dd  = String(d.getDate()).padStart(2,'0');
                    const mon = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][d.getMonth()];
                    displayDate = `${dd} ${mon} ${d.getFullYear()}`;
                }
            } catch(e) {}

            const syncType = item.sync_type || '–';
            const typeBadge = syncType === 'Sync In'
                ? `<span class="badge-in">Sync In</span>`
                : syncType === 'Sync Out'
                    ? `<span class="badge-out">Sync Out</span>`
                    : `<span class="text-muted">–</span>`;

            $('#syncData').append(`
                <tr>
                    <td class="text-muted" style="padding:.5rem .75rem">${idx + 1}</td>
                    <td style="padding:.5rem .75rem;font-weight:600">${displayDate}</td>
                    <td style="padding:.5rem .75rem">${typeBadge}</td>
                    <td style="padding:.5rem .75rem">${statusBadge}</td>
                </tr>
            `);
        });

        // Update summary cards
        $('#totalSyncs').text(rows.length);
        $('#successSyncs').text(successCount);
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

function showError(idx) {
    const msg = syncErrors[idx] || 'Unknown error';
    Swal.fire({
        title: 'Sync Error Detail',
        html: `<div style="text-align:left;font-size:.82rem;word-break:break-all;max-height:300px;overflow-y:auto;background:#f8f9fa;padding:10px;border-radius:8px;">${msg}</div>`,
        icon: 'error',
        confirmButtonText: 'Close',
        width: 600,
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
