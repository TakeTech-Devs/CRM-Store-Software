@extends('layouts.dashboard')

@section('title', 'Software Update')

@section('content')
<style>
    .badge-success  { background: #28a745; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; }
    .badge-failed   { background: #dc3545; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; }
    .badge-pending  { background: #6c757d; color: #fff; padding: 4px 10px; border-radius: 12px; font-size: 0.8rem; }
    #updateHistory tr:hover { background: #f8f9fa; }
    #updateHistory td, #updateHistory th { white-space: nowrap; vertical-align: middle; padding: .55rem 1rem; }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-dark mb-0">Software Update</h2>
        <button type="button" class="btn btn-primary" id="checkUpdateBtn">
            <i class="fa fa-sync mr-1"></i> Check for Update
        </button>
    </div>

    <div class="card mb-4">
        <div class="card-body py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Current Version</h6>
                    <h4 class="mb-0 font-weight-bold" id="currentVersion">–</h4>
                </div>
                <div id="updateAvailableBlock" style="display:none;">
                    <p class="mb-2 text-success font-weight-bold">
                        New version <span id="latestVersion"></span> is available.
                    </p>
                    <button type="button" class="btn btn-success" id="applyUpdateBtn">
                        <i class="fa fa-download mr-1"></i> Apply Update
                    </button>
                </div>
                <div id="upToDateBlock" style="display:none;">
                    <span class="text-muted">You are on the latest version.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- History --}}
    <div class="d-flex justify-content-center">
        <div style="width:700px;">
            <div class="card shadow-sm" style="border-radius:12px;overflow:hidden;">
                <table class="table table-hover mb-0 text-center" id="updateHistory">
                    <thead>
                        <tr style="background:#f4f6f9;">
                            <th>#</th>
                            <th>From</th>
                            <th>To</th>
                            <th>Status</th>
                            <th>Attempts</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody id="updateHistoryBody">
                        <tr><td colspan="6" class="text-muted py-4">Loading…</td></tr>
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
            <p class="mb-0 font-weight-bold" id="loadingModalText">Please wait…</p>
            <small class="text-muted">Do not close the page.</small>
        </div>
    </div>
</div>

<script>
const updateErrors = [];

$(document).ready(function () {
    checkForUpdate(false);
    getUpdateHistory();

    $('#checkUpdateBtn').on('click', function () {
        checkForUpdate(true);
    });

    $('#applyUpdateBtn').on('click', function () {
        applyUpdate();
    });
});

function checkForUpdate(showAlert) {
    ajaxGetData('/api/update/check', function (response) {
        if (response?.status != 200) {
            if (showAlert) Swal.fire('Error', response?.message || 'Could not check for updates.', 'error');
            return;
        }
        const data = response.data;
        $('#currentVersion').text(data.current_version);

        if (data.has_update) {
            $('#latestVersion').text(data.latest_version);
            $('#updateAvailableBlock').show();
            $('#upToDateBlock').hide();
        } else {
            $('#updateAvailableBlock').hide();
            $('#upToDateBlock').show();
            if (showAlert) Swal.fire('Up to Date', 'You are already on the latest version.', 'success');
        }
    }, function () {
        if (showAlert) Swal.fire('Error', 'Could not reach the update server.', 'error');
    });
}

function applyUpdate() {
    $('#loadingModalText').text('Applying update…');
    $('#loadingModal').modal('show');
    $('#loadingModal').one('shown.bs.modal', function () {
        $.ajax({
            url: '/api/update/apply',
            type: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (response) {
                $('#loadingModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                if (response?.status == 200) {
                    Swal.fire('Update Applied', response.message, 'success');
                    checkForUpdate(false);
                    getUpdateHistory();
                } else {
                    Swal.fire('Update Failed', response?.message || 'Could not apply the update.', 'error');
                    getUpdateHistory();
                }
            },
            error: function (xhr) {
                $('#loadingModal').modal('hide');
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                const message = xhr.responseJSON?.message || 'An error occurred while updating.';
                Swal.fire('Update Failed', message, 'error');
                getUpdateHistory();
            }
        });
    });
}

function getUpdateHistory() {
    ajaxGetData('/api/update/history', function (response) {
        const rows = Array.isArray(response?.data) ? response.data : [];
        $('#updateHistoryBody').empty();
        updateErrors.length = 0;

        if (!rows.length) {
            $('#updateHistoryBody').html('<tr><td colspan="6" class="text-center text-muted py-4">No update history yet.</td></tr>');
            return;
        }

        rows.forEach(function (item, idx) {
            const status = (item.status || '').toLowerCase();
            const errIdx = updateErrors.length;
            let statusBadge = `<span class="badge-pending">Pending</span>`;
            if (status === 'success') {
                statusBadge = `<span class="badge-success">Success</span>`;
            } else if (status === 'failed') {
                updateErrors.push(item.error_message || 'Unknown error');
                statusBadge = `<span class="badge-failed" style="cursor:pointer" title="Click to view error" onclick="showUpdateError(${errIdx})">Failed <i class='fa fa-info-circle'></i></span>`;
            }

            const rawDate = item.completed_at || item.started_at || '';
            let displayDate = rawDate;
            try {
                const d = new Date(rawDate);
                if (!isNaN(d)) {
                    const dd  = String(d.getDate()).padStart(2, '0');
                    const mon = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'][d.getMonth()];
                    displayDate = `${dd} ${mon} ${d.getFullYear()}`;
                }
            } catch (e) {}

            $('#updateHistoryBody').append(`
                <tr>
                    <td class="text-muted">${idx + 1}</td>
                    <td>${item.previous_version || '–'}</td>
                    <td class="font-weight-bold">${item.new_version}</td>
                    <td>${statusBadge}</td>
                    <td>${item.attempt_count || 0}</td>
                    <td>${displayDate}</td>
                </tr>
            `);
        });
    }, function () {
        $('#updateHistoryBody').html('<tr><td colspan="6" class="text-center text-danger py-4">Failed to load update history.</td></tr>');
    });
}

function showUpdateError(idx) {
    const msg = updateErrors[idx] || 'Unknown error';
    Swal.fire({
        title: 'Update Error Detail',
        html: `<div style="text-align:left;font-size:.82rem;word-break:break-all;max-height:300px;overflow-y:auto;background:#f8f9fa;padding:10px;border-radius:8px;">${msg}</div>`,
        icon: 'error',
        confirmButtonText: 'Close',
        width: 600,
    });
}
</script>
@endsection
