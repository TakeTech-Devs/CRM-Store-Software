@extends('layouts.dashboard')

@section('title', 'Expiry Report')

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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="col-6">
                <h2 class="text-dark">Expiry Report</h2>
            </div>
            <div class="col-6">
                <div style="text-align:right; margin-bottom: 25px !important;">
                    <button class="btn btn-sm shadow btn-primary" id="printButton">Print Report</button>
                </div> 
            </div>
        </div>

        <!-- BRAND LIST TABLE  -->
        <div class="container-fluid">
            <div id="loader" class="text-center mt-3"></div>
            <div class="row">
                <div class="col">
                    <div class="table-responsive" style="height: 60vh; overflow:auto">
                        <table class="table text-dark border table-hover text-center" id="brands-table">
                            <thead class="sticky-top bg-light">
                                <tr>
                                    <th>Product Name</th>
                                    <th>Product Qty</th>
                                    <th>Expiring In</th>
                                </tr>
                            </thead>
                            <tbody id="exp_list">
                                <tr>
                                    <td colspan="3" class="text-center">No Data Available</td>
                                </tr>
                            </tbody>
                        </table>
                        <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No brands found</div>
                    </div>

                    <!-- Pagination Section -->
                    <div class="container mt-3">
                        <div class="row justify-content-end">
                            <div class="col-auto">
                                <nav aria-label="...">
                                    <ul class="pagination pagination-sm" id="pagination-links">
                                        <!-- Pagination links will be added here dynamically -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <!-- END BRAND LIST TABLE -->
    </div>

    <script>
        $(document).ready(function() {
            loadPage(1); // Load the first page initially
        });

        let allData = [];

        function loadPage(page) {
            $('#loader').show();
            $.ajax({
            url: '/api/expiry-report',
            method: 'GET',
            success: function(res) {
                $('#loader').hide();
                $('#exp_list').empty();
                $('#pagination-links').empty();

                // Save all data to the allData array
                allData = res.data;

                // Get records for the current page
                let recordsPerPage = 15;
                let startIndex = (page - 1) * recordsPerPage;
                let endIndex = startIndex + recordsPerPage;
                let currentPageData = allData.slice(startIndex, endIndex);

                if (currentPageData.length === 0) {
                $('#exp_list').append(
                    `<tr><td colspan="3" class="text-center">No Data Available</td></tr>`
                );
                } else {
                currentPageData.forEach((element) => {
                    let currentDate = new Date();
                    let expiryDate = new Date(element?.exp_date);
                    let timeDifference = expiryDate.getTime() - currentDate.getTime();
                    let daysUntilExpiry = Math.ceil(timeDifference / (1000 * 3600 * 24));
                    let monthsUntilExpiry = expiryDate.getMonth() - currentDate.getMonth() + (12 * (expiryDate.getFullYear() - currentDate.getFullYear()));

                    if (monthsUntilExpiry < 0 || (monthsUntilExpiry === 0 && daysUntilExpiry <= 0)) {
                    $('#exp_list').append(
                        `<tr class="text-danger">
                        <td>${element?.product_name}</td>
                        <td>${element?.qty}</td>
                        <td>Expired</td>
                        </tr>`
                    );
                    } else if (monthsUntilExpiry === 0 && daysUntilExpiry > 0) {
                    $('#exp_list').append(
                        `<tr class="text-danger">
                        <td>${element?.product_name}</td>
                        <td>${element?.qty}</td>
                        <td>${daysUntilExpiry} Days</td>
                        </tr>`
                    );
                    } else if (monthsUntilExpiry <= 3) {
                    $('#exp_list').append(
                        `<tr class="text-danger">
                        <td>${element?.product_name}</td>
                        <td>${element?.qty}</td>
                        <td>${monthsUntilExpiry} Months</td>
                        </tr>`
                    );
                    } else if (monthsUntilExpiry <= 6) {
                    $('#exp_list').append(
                        `<tr class="text-warning">
                        <td>${element?.product_name}</td>
                        <td>${element?.qty}</td>
                        <td>${monthsUntilExpiry} Months</td>
                        </tr>`
                    );
                    } else {
                    $('#exp_list').append(
                        `<tr class="text-success">
                        <td>${element?.product_name}</td>
                        <td>${element?.qty}</td>
                        <td>${monthsUntilExpiry} Months</td>
                        </tr>`
                    );
                    }
                });
                }

                // Generate pagination links
                let totalPages = Math.ceil(allData.length / recordsPerPage);
                for (let i = 1; i <= totalPages; i++) {
                $('#pagination-links').append(
                    `<li class="page-item${i === page ? ' active' : ''}"><a class="page-link" href="#" onclick="loadPage(${i})">${i}</a></li>`
                );
                }
            }
            })
        }

        document.getElementById("printButton").addEventListener("click", function() {
            printExpiryReport();
        });

        function printExpiryReport() {
            var printContent = '<table class="table text-dark border table-hover text-center"><thead><tr><th>Product Name</th><th>Product Qty</th><th>Expiring In</th></tr></thead><tbody>';

            // Loop through all the data and build the print content
            allData.forEach((element) => {
                let currentDate = new Date();
                let expiryDate = new Date(element?.exp_date);
                let timeDifference = expiryDate.getTime() - currentDate.getTime();
                let daysUntilExpiry = Math.ceil(timeDifference / (1000 * 3600 * 24));
                let monthsUntilExpiry = expiryDate.getMonth() - currentDate.getMonth() + (12 * (expiryDate
                    .getFullYear() - currentDate.getFullYear()));

                if (monthsUntilExpiry < 0 || (monthsUntilExpiry === 0 && daysUntilExpiry <= 0)) {
                    printContent += `<tr class="text-danger"><td>${element?.product_name}</td><td>${element?.qty}</td><td>Expired</td></tr>`;
                } else if (monthsUntilExpiry === 0 && daysUntilExpiry > 0) {
                    printContent += `<tr class="text-danger"><td>${element?.product_name}</td><td>${element?.qty}</td><td>${daysUntilExpiry} Days</td></tr>`;
                } else if (monthsUntilExpiry <= 3) {
                    printContent += `<tr class="text-danger"><td>${element?.product_name}</td><td>${element?.qty}</td><td>${monthsUntilExpiry} Months</td></tr>`;
                } else if (monthsUntilExpiry <= 6) {
                    printContent += `<tr class="text-warning"><td>${element?.product_name}</td><td>${element?.qty}</td><td>${monthsUntilExpiry} Months</td></tr>`;
                } else {
                    printContent += `<tr class="text-success"><td>${element?.product_name}</td><td>${element?.qty}</td><td>${monthsUntilExpiry} Months</td></tr>`;
                }
            });

            printContent += '</tbody></table>';

            var printWindow = window.open('', '', 'height=800, width=1200');
            printWindow.document.write('<html><head><title>Expiry Report</title>');
            printWindow.document.write('<style>body {font-family: Arial, sans-serif;} table {width: 100%; border-collapse: collapse;} th, td {padding: 10px; border: 1px solid #ddd;} th {background-color: #f2f2f2;} </style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>Expiry Report</h2>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

    </script>
@endsection
