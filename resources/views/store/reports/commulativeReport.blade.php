@extends('layouts.dashboard')

@section('title', 'Cumulative Report')

@section('content')
<style>
    @media print {
        body * { border: none !important; box-shadow: none !important; }
        .table tbody+tbody { border-top: none !important; }
        .no-print { display: none !important; }
        @page { size: A4 landscape; }
    }
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="text-dark">Cumulative Report</h2>
        <div class="no-print">
            <button class="btn btn-sm btn-primary" id="printButton">Print Report</button>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="form-row align-items-end mb-4 no-print">
        <div class="col-md-3 form-group">
            <label for="start_date">Start Date</label>
            <input type="date" class="form-control" id="start_date">
        </div>
        <div class="col-md-3 form-group">
            <label for="end_date">End Date</label>
            <input type="date" class="form-control" id="end_date">
        </div>
        <div class="col-md-2 form-group">
            <button class="btn btn-success btn-md" id="filterReport">Find</button>
            <button class="btn btn-secondary btn-md ml-1" id="clearFilter">Clear</button>
        </div>
    </div>

    <div id="printArea">
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body text-center">
                        <h6 class="card-title">Total Sales</h6>
                        <h4 id="totalSalesAmount">0.00</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h6 class="card-title">Cash Payments</h6>
                        <h4 id="cashPayments">0.00</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h6 class="card-title">Card Payments</h6>
                        <h4 id="cardPayments">0.00</h4>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h6 class="card-title">Online Payments</h6>
                        <h4 id="onlinePayments">0.00</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Invoice No</th>
                        <th>Type</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Payment Type</th>
                        <th>Amount (₹)</th>
                    </tr>
                </thead>
                <tbody id="transactionBody">
                    <tr><td colspan="8" class="text-center text-muted">Loading...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    $('#start_date').on('change', function () {
        $('#end_date').attr('min', $(this).val());
        if ($('#end_date').val() && $('#end_date').val() < $(this).val()) {
            $('#end_date').val($(this).val());
        }
    });

    function fetchReport(startDate, endDate) {
        let url = '/api/cumulative-report';
        if (startDate && endDate) {
            url += `?start_date=${startDate}&end_date=${endDate}`;
        }

        $.ajax({
            url: url,
            method: 'GET',
            success: function (response) {
                if (response.status === 'success') {
                    let data = response.data;

                    $('#totalSalesAmount').text(data.total_sales_amount.toFixed(2));
                    $('#cashPayments').text(data.cash_payments.toFixed(2));
                    $('#cardPayments').text(data.card_payments.toFixed(2));
                    $('#onlinePayments').text(data.online_payments.toFixed(2));

                    $('#transactionBody').empty();
                    if (data.transactions.length === 0) {
                        $('#transactionBody').append('<tr><td colspan="8" class="text-center">No records found</td></tr>');
                        return;
                    }
                    data.transactions.forEach((t, i) => {
                        $('#transactionBody').append(`
                            <tr>
                                <td>${i + 1}</td>
                                <td>${t.date}</td>
                                <td>${t.invoiceNo}</td>
                                <td><span class="badge badge-${t.type === 'Customer' ? 'primary' : 'secondary'}">${t.type}</span></td>
                                <td>${t.name || '-'}</td>
                                <td>${t.phone || '-'}</td>
                                <td>${t.payment_type}</td>
                                <td>${t.sales_amount.toFixed(2)}</td>
                            </tr>
                        `);
                    });
                } else {
                    $('#transactionBody').html('<tr><td colspan="8" class="text-center">No data found.</td></tr>');
                }
            },
            error: function () {
                $('#transactionBody').html('<tr><td colspan="8" class="text-center text-danger">Failed to load report.</td></tr>');
            }
        });
    }

    $(document).ready(function () {
        fetchReport(null, null);

        $('#filterReport').on('click', function () {
            let start = $('#start_date').val();
            let end = $('#end_date').val();
            if (!start || !end) {
                alert('Please select both start and end dates.');
                return;
            }
            fetchReport(start, end);
        });

        $('#clearFilter').on('click', function () {
            $('#start_date').val('');
            $('#end_date').val('');
            fetchReport(null, null);
        });

        $('#printButton').on('click', function () {
            var printContent = `<div style="text-align:center;margin-bottom:20px;"><h2>Cumulative Sales Report</h2></div>${document.getElementById('printArea').innerHTML}`;
            var original = document.body.innerHTML;
            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = original;
            location.reload();
        });
    });
</script>
@endsection
