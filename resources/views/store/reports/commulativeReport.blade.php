@extends('layouts.dashboard')

@section('title', 'Cumulative Report')

@section('content')
<style>
    @media print {
        body * {
            border: none !important;
            box-shadow: none !important;
        }
        .table tbody+tbody {
            border-top: none !important;
        }
        #printButton {
            display: none;
        }
        @page {
            size: A4 landscape;
        }
    }
</style>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 col-12">
        <div class="col-6">
            <h2 class="text-dark">Cumulative Report</h2>
        </div>
        <div class="col-6">
            <div style="text-align:right; margin-bottom: 25px !important;">
                <button class="btn btn-sm shadow btn-primary" id="printButton">Print Report</button>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 mt-4" id="reportSection">
                <div class="table-responsive">
                    <div id="printArea">
                        <!-- Summary Table -->
                        <table class="table text-dark border table-hover text-center">
                            <thead class="bg-light">
                                <tr><th colspan="2">Summary</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Total Sales Amount</td>
                                    <td id="totalSalesAmount"></td>
                                </tr>
                                <tr>
                                    <td>Cash Payments</td>
                                    <td id="cashPayments"></td>
                                </tr>
                                <tr>
                                    <td>Card Payments</td>
                                    <td id="cardPayments"></td>
                                </tr>
                                <tr>
                                    <td>Online Payments</td>
                                    <td id="onlinePayments"></td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Cash Sources Table -->
                        <!-- <table class="table text-dark border table-hover text-center">
                            <thead class="bg-light">
                                <tr><th colspan="2">Cash Sources</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>God</td>
                                    <td id="cashSourceGod"></td>
                                </tr>
                                <tr>
                                    <td>Doctor</td>
                                    <td id="cashSourceDoctor"></td>
                                </tr>
                                <tr>
                                    <td>Staff</td>
                                    <td id="cashSourceStaff"></td>
                                </tr>
                                <tr>
                                    <td>Diary</td>
                                    <td id="cashSourceDiary"></td>
                                </tr>
                            </tbody>
                        </table> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Fetch and display cumulative report on page load
        $.ajax({
            url: "{{ url('https://rightaid.taketechdevs.com/api/commulative-report') }}",
            method: 'GET',
            success: function(response) {
                if (response.status === 'success') {
                    let data = response.data;

                    // Populate summary data
                    $('#totalSalesAmount').text((data.total_sales_amount || 0).toFixed(2));
                    $('#cashPayments').text((data.cash_payments || 0).toFixed(2)); // Ensure this includes offline payments
                    $('#cardPayments').text((data.card_payments || 0).toFixed(2));
                    $('#onlinePayments').text((data.online_payments || 0).toFixed(2));
                } else {
                    alert('No data found for the cumulative report.');
                }
            },
            error: function() {
                alert('An error occurred while fetching the cumulative report.');
            }
        });
    });

    function printModalContent() {
        // Generate print content
        var printContent = `
            <div style="text-align: center; margin-bottom: 20px;">
                <h2>Cumulative Sales Report</h2>
            </div>
            ${document.getElementById("printArea").innerHTML}
        `;

        // Save the original content
        var originalContent = document.body.innerHTML;

        // Replace content and print
        document.body.innerHTML = printContent;
        window.print();

        // Restore original content
        document.body.innerHTML = originalContent;
    }

    // Add print button event listener
    document.getElementById("printButton").addEventListener("click", function() {
        printModalContent();
    });
</script>

@endsection
