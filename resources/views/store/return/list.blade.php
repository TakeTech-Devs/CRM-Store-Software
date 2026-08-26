@extends('layouts.dashboard')

@section('title', 'Credit Notes')

@section('content')
<style>
    .pagination { margin-top: 10px; }
    .badge-active { background-color: #16a34a; color: #fff; }
    .badge-redeemed { background-color: #6b7280; color: #fff; }
</style>

<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between">
        <h2 class="text-dark">Credit Notes</h2>
        <div class="text-right">
            <a href="{{ url('store/return/create') }}" class="btn btn-secondary btn-sm">Create New Return</a>
        </div>
    </div>

    <div class="form-row d-flex align-items-center justify-content-between">
        <div class="col-md-12 form-group d-flex align-items-start justify-content-between mb-0">
            <div class="form-group d-flex align-items-start justify-content-around">
                <div class="form-group mx-1">
                    <label for="billing_type_filter">Type</label>
                    <select id="billing_type_filter" class="form-control">
                        <option value="customer">Customer</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
                <div class="form-group mx-1">
                    <label for="status_filter">Status</label>
                    <select id="status_filter" class="form-control">
                        <option value="">All</option>
                        <option value="active">Active</option>
                        <option value="redeemed">Redeemed</option>
                    </select>
                </div>
            </div>
            <div class="d-flex align-items-start justify-content-around">
                <div class="form-group mx-3">
                    <label for="searchCreditNote">Search: </label> &nbsp;&nbsp;
                    <input type="text" class="form-control" id="searchCreditNote" placeholder="Search Credit Note No.">
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive border mt-3">
        <table id="credit-note-table" class="table p-2 text-center">
            <thead>
                <tr>
                    <th class="text-dark">#</th>
                    <th class="text-dark">Credit Note No</th>
                    <th class="text-dark">Name</th>
                    <th class="text-dark">Phone</th>
                    <th class="text-dark">Against Invoice</th>
                    <th class="text-dark">Return Date</th>
                    <th class="text-dark">Amount</th>
                    <th class="text-dark">Status</th>
                    <th class="text-dark">Actions</th>
                </tr>
            </thead>
            <tbody id="creditNoteBody"></tbody>
        </table>
        <div id="noCreditNoteMsg" class="text-center mt-3" style="display: none;">No credit notes found</div>
    </div>

    <div class="container mt-2 mb-3">
        <div class="row justify-content-between align-items-center">
            <div class="col-auto">
                <small class="text-muted" id="cnPaginationInfo"></small>
            </div>
            <div class="col-auto">
                <nav aria-label="Credit note pagination">
                    <ul class="pagination pagination-sm mb-0"></ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- VIEW CREDIT NOTE MODAL -->
<div class="modal fade" id="viewCreditNoteModal" tabindex="-1" role="dialog" aria-labelledby="viewCreditNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewCreditNoteModalLabel">Return Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="form-row mb-3">
                    <div class="col-md-4"><strong>Credit Note No:</strong> <span id="vcnNo"></span></div>
                    <div class="col-md-4"><strong>Status:</strong> <span id="vcnStatus"></span></div>
                    <div class="col-md-4"><strong>Return Date:</strong> <span id="vcnDate"></span></div>
                </div>
                <div class="form-row mb-3">
                    <div class="col-md-4"><strong>Name:</strong> <span id="vcnName"></span></div>
                    <div class="col-md-4"><strong>Phone:</strong> <span id="vcnPhone"></span></div>
                    <div class="col-md-4"><strong>Against Invoice:</strong> <span id="vcnInvoice"></span></div>
                </div>
                <div class="form-row mb-3" id="vcnRedeemedRow" style="display:none;">
                    <div class="col-md-6"><strong>Redeemed On Bill Type:</strong> <span id="vcnRedeemedType"></span></div>
                    <div class="col-md-6"><strong>Redeemed At:</strong> <span id="vcnRedeemedAt"></span></div>
                </div>
                <h6 class="text-dark">Returned Products</h6>
                <div class="table-responsive">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Pack</th>
                                <th>Qty Returned</th>
                                <th>Unit Value</th>
                                <th>Credit Amount</th>
                            </tr>
                        </thead>
                        <tbody id="vcnItemsBody"></tbody>
                    </table>
                </div>
                <div class="text-right"><strong>Total Credit: <span id="vcnTotal"></span></strong></div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    let allCreditNotes = [];
    let searchTerm = '';
    const PAGE_SIZE = 10;
    let currentPage = 1;

    function visibleCreditNotes() {
        if (!searchTerm) return allCreditNotes;
        return allCreditNotes.filter(cn => cn.credit_note_no.toLowerCase().includes(searchTerm));
    }

    function loadCreditNotes() {
        const type = $('#billing_type_filter').val();
        const status = $('#status_filter').val();
        let url = `/return/list?billing_type=${type}`;
        if (status) url += `&status=${status}`;

        ajaxGetData(url, (res) => {
            allCreditNotes = (res?.data || []).slice();
            renderPage(1);
        }, () => {
            allCreditNotes = [];
            renderPage(1);
        });
    }

    function renderPage(page) {
        currentPage = page;
        const items = visibleCreditNotes();
        const start = (page - 1) * PAGE_SIZE;
        const pageItems = items.slice(start, start + PAGE_SIZE);
        const tbody = $('#creditNoteBody').empty();

        if (!items.length) {
            $('#noCreditNoteMsg').show();
            $('#cnPaginationInfo').text('');
            $('.pagination').empty();
            return;
        }
        $('#noCreditNoteMsg').hide();

        pageItems.forEach((cn, i) => {
            const phone = cn.customer_phone || cn.staff_phone;
            const name = cn.customer_name || cn.staff_name;
            const badgeClass = cn.status === 'active' ? 'badge-active' : 'badge-redeemed';
            tbody.append(`
                <tr>
                    <td>${start + i + 1}</td>
                    <td>${cn.credit_note_no}</td>
                    <td>${name || 'N/A'}</td>
                    <td>${phone || 'N/A'}</td>
                    <td>${cn.source_invoice_no || 'N/A'}</td>
                    <td>${cn.return_date}</td>
                    <td>${parseFloat(cn.total_credit_amt).toFixed(2)}</td>
                    <td><span class="badge ${badgeClass}">${cn.status}</span></td>
                    <td>
                        <button class="btn btn-sm btn-info viewCreditNote text-white" data-id="${cn.id}">View</button>
                    </td>
                </tr>
            `);
        });

        renderPagination();
        const showing = Math.min(start + PAGE_SIZE, items.length);
        $('#cnPaginationInfo').text(`Showing ${start + 1} to ${showing} of ${items.length} records`);
    }

    function renderPagination() {
        const totalPages = Math.ceil(visibleCreditNotes().length / PAGE_SIZE);
        const $ul = $('.pagination').empty();
        if (totalPages <= 1) return;

        $ul.append(`<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage - 1}">&laquo;</a></li>`);
        for (let p = 1; p <= totalPages; p++) {
            $ul.append(`<li class="page-item ${p === currentPage ? 'active' : ''}">
                <a class="page-link" href="#" data-page="${p}">${p}</a></li>`);
        }
        $ul.append(`<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="${currentPage + 1}">&raquo;</a></li>`);
    }

    $(document).on('click', '.pagination .page-link', function (e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page >= 1 && page <= Math.ceil(visibleCreditNotes().length / PAGE_SIZE)) {
            renderPage(page);
        }
    });

    $('#billing_type_filter, #status_filter').on('change', loadCreditNotes);

    $('#searchCreditNote').on('input', function () {
        searchTerm = $(this).val().trim().toLowerCase();
        renderPage(1);
    });

    $(document).on('click', '.viewCreditNote', function () {
        const id = $(this).data('id');
        const type = $('#billing_type_filter').val();

        ajaxGetData(`/return/${id}?billing_type=${type}`, (res) => {
            const cn = res?.data?.credit_note;
            const items = res?.data?.items || [];
            if (!cn) return;

            const phone = cn.customer_phone || cn.staff_phone;
            const name = cn.customer_name || cn.staff_name;

            $('#vcnNo').text(cn.credit_note_no);
            $('#vcnStatus').text(cn.status);
            $('#vcnDate').text(cn.return_date);
            $('#vcnName').text(name || 'N/A');
            $('#vcnPhone').text(phone || 'N/A');
            $('#vcnInvoice').text(cn.source_invoice_no || 'N/A');
            $('#vcnTotal').text(parseFloat(cn.total_credit_amt).toFixed(2));

            if (cn.status === 'redeemed') {
                $('#vcnRedeemedType').text(cn.redeemed_bill_type || 'N/A');
                $('#vcnRedeemedAt').text(cn.redeemed_at || 'N/A');
                $('#vcnRedeemedRow').show();
            } else {
                $('#vcnRedeemedRow').hide();
            }

            const tbody = $('#vcnItemsBody').empty();
            items.forEach(item => {
                tbody.append(`
                    <tr>
                        <td>${item.product_name || 'N/A'}</td>
                        <td>${item.pack}</td>
                        <td>${item.qty}</td>
                        <td>${item.unitValue}</td>
                        <td>${parseFloat(item.totalAmount).toFixed(2)}</td>
                    </tr>
                `);
            });

            $('#viewCreditNoteModal').modal('show');
        }, () => {
            Swal.fire({ title: 'Error', icon: 'error', text: 'Failed to load return details.' });
        });
    });

    loadCreditNotes();
});
</script>
@endsection
