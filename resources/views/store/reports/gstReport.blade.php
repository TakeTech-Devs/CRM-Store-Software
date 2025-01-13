@extends('layouts.dashboard')

@section('title', 'GST Report')

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
            <h2 class="text-dark">GST Report</h2>
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
                        <table class="table text-dark border table-hover text-center" id="reportTable">
                            <thead class="sticky-top bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>HSN Code</th>
                                    <th>Amount(&#8377;)</th>
                                    <th>GST Rate(%)</th>
                                    <th>CGST(%)</th>
                                    <th>SGST(%)</th>
                                    <th>Total GST(&#8377;)</th>
                                </tr>
                            </thead>
                            <tbody id="reportData"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        function generateReport() {
            $.ajax({
                url: "{{ url('http://localhost:8001/api/gst-report') }}",
                method: 'GET',
                success: function(response) {
                    if (response.status === 'success') {
                        let salesData = response.data;
                        let html = '';

                        salesData.forEach((sale, index) => {
                            let sgst = (sale.total_amount * (sale.gst / 100) / 2).toFixed(2)
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${sale.hsn_code}</td>
                                    <td>${sale.total_amount}</td>
                                    <td>${sale.gst}</td>
                                    <td>${sale.cgst}</td>
                                    <td>${sale.sgst}</td>
                                    <td>${sale.total_gst}</td>
                                </tr>
                            `;
                        });

                        $('#reportData').html(html);
                    } else {
                        alert('No data found for the selected month.');
                    }
                },
                error: function() {
                    alert('An error occurred while generating the report.');
                }
            });
        }

        generateReport();

        document.getElementById("printButton").addEventListener("click", function() {
            printModalContent();
        });
    });

    function printModalContent() {
        var printContent = document.getElementById("printArea").innerHTML;
        var originalContent = document.body.innerHTML;

        var storeHeading = `
            <div style="text-align: center;">
                <h2>GST Report</h2>
            </div>
        `;

        printContent = storeHeading + printContent;

        printContent = printContent.replace('<button class="btn btn-sm shadow btn-primary" id="printButton">Print</button>', '');

        var modalBackdrop = document.getElementsByClassName("modal-backdrop")[0];
        if (modalBackdrop) {
            modalBackdrop.remove();
        }

        document.body.innerHTML = printContent;

        window.print();

        document.body.innerHTML = originalContent;

        $('#printModal').modal('show');
    }
</script>

@endsection
