@extends('layouts.dashboard')

@section('title', 'Stock Report')

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
            <h2 class="text-dark">Stock Report</h2>
        </div>
        <div class="col-6">
            <div style="text-align:right; margin-bottom: 25px !important;">
                <button class="btn btn-sm shadow btn-primary" id="printButton">Print Report</button>
            </div>
        </div>
    </div>

    <!-- Color Coding Instructions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle"></i> Stock Status Color Guide</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-danger mr-2" style="width: 20px; height: 20px;"></span>
                                <strong>Red Rows:</strong>
                            </div>
                            <p class="ml-4 text-muted small">Products that are <strong>expired</strong> or
                                <strong>expiring within 30 days</strong>. These need immediate attention!
                            </p>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-warning mr-2" style="width: 20px; height: 20px;"></span>
                                <strong>Yellow Rows:</strong>
                            </div>
                            <p class="ml-4 text-muted small">Products <strong>expiring within 30 days</strong>. Monitor
                                these closely and prioritize selling.</p>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge badge-success mr-2" style="width: 20px; height: 20px;"></span>
                                <strong>Green Rows:</strong>
                            </div>
                            <p class="ml-4 text-muted small">Products with <strong>good shelf life</strong> (expiring
                                after 30+ days). These are safe to keep in stock.</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> Focus on selling red and yellow
                                items first to reduce waste and improve inventory turnover. Use the print function to
                                create physical reports for staff reference.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- STOCK LIST TABLE  -->
    <div class="container-fluid">
        <div id="loader" class="text-center mt-3"></div>
        <div class="row">
            <div class="col">
                <div class="table-responsive" style="height: 60vh; overflow:auto">
                    <table class="table text-dark border table-hover text-center" id="stock-table">
                        <thead class="sticky-top bg-light">
                            <tr>
                                <th>Product Name</th>
                                <th>Brand</th>
                                <th>Category</th>
                                <th>Sub Category</th>
                                <th>Pack</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Expiry Date</th>
                                <th>HSN Code</th>
                                <th>GST %</th>
                            </tr>
                        </thead>
                        <tbody id="stock_list">
                            <tr>
                                <td colspan="10" class="text-center">No Data Available</td>
                            </tr>
                        </tbody>
                    </table>
                    <div id="noStockFoundMessage" class="text-center mt-3" style="display: none;">No stock found</div>
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
    <!-- END STOCK LIST TABLE -->
</div>

<script>
    $(document).ready(function() {
            loadPage(1); // Load the first page initially
        });

        let allData = [];

        function loadPage(page) {
            $('#loader').show();
            $.ajax({
            url: '/api/stock-report',
            method: 'GET',
            success: function(res) {
                $('#loader').hide();
                $('#stock_list').empty();
                $('#pagination-links').empty();

                // Save all data to the allData array
                allData = res.data;

                // Get records for the current page
                let recordsPerPage = 15;
                let startIndex = (page - 1) * recordsPerPage;
                let endIndex = startIndex + recordsPerPage;
                let currentPageData = allData.slice(startIndex, endIndex);

                if (currentPageData.length === 0) {
                $('#stock_list').append(
                    `<tr><td colspan="10" class="text-center">No Data Available</td></tr>`
                );
                } else {
                currentPageData.forEach((element) => {
                    // Format expiry date
                    let expiryDate = element?.exp_date ? new Date(element.exp_date).toLocaleDateString() : 'N/A';
                    
                    // Check if product is expired or expiring soon
                    let currentDate = new Date();
                    let expDate = new Date(element?.exp_date);
                    let timeDifference = expDate.getTime() - currentDate.getTime();
                    let daysUntilExpiry = Math.ceil(timeDifference / (1000 * 3600 * 24));
                    
                    let rowClass = '';
                    if (daysUntilExpiry < 0) {
                        rowClass = 'text-danger'; // Expired
                    } else if (daysUntilExpiry <= 30) {
                        rowClass = 'text-warning'; // Expiring within 30 days
                    } else {
                        rowClass = 'text-success'; // Good stock
                    }

                    $('#stock_list').append(
                        `<tr class="${rowClass}">
                        <td>${element?.product_name || 'N/A'}</td>
                        <td>${element?.brand_name || 'N/A'}</td>
                        <td>${element?.category_name || 'N/A'}</td>
                        <td>${element?.sub_category_name || 'N/A'}</td>
                        <td>${element?.pack_name || 'N/A'}</td>
                        <td>${element?.price_name || 'N/A'}</td>
                        <td>${element?.qty || '0'}</td>
                        <td>${expiryDate}</td>
                        <td>${element?.hsn_code || 'N/A'}</td>
                        <td>${element?.gst || '0'}%</td>
                        </tr>`
                    );
                });
                }

                // Generate pagination links
                let totalPages = Math.ceil(allData.length / recordsPerPage);
                for (let i = 1; i <= totalPages; i++) {
                $('#pagination-links').append(
                    `<li class="page-item${i === page ? ' active' : ''}"><a class="page-link" href="#" onclick="loadPage(${i})">${i}</a></li>`
                );
                }
            },
            error: function(xhr, status, error) {
                $('#loader').hide();
                $('#stock_list').append(
                    `<tr><td colspan="10" class="text-center text-danger">Error loading data: ${error}</td></tr>`
                );
            }
            })
        }

        document.getElementById("printButton").addEventListener("click", function() {
            printStockReport();
        });

        function printStockReport() {
            var printContent = '<table class="table text-dark border table-hover text-center"><thead><tr><th>Product Name</th><th>Brand</th><th>Category</th><th>Sub Category</th><th>Pack</th><th>Price</th><th>Quantity</th><th>Expiry Date</th><th>HSN Code</th><th>GST %</th></tr></thead><tbody>';

            // Loop through all the data and build the print content
            allData.forEach((element) => {
                let expiryDate = element?.exp_date ? new Date(element.exp_date).toLocaleDateString() : 'N/A';
                
                // Check if product is expired or expiring soon
                let currentDate = new Date();
                let expDate = new Date(element?.exp_date);
                let timeDifference = expDate.getTime() - currentDate.getTime();
                let daysUntilExpiry = Math.ceil(timeDifference / (1000 * 3600 * 24));
                
                let rowClass = '';
                if (daysUntilExpiry < 0) {
                    rowClass = 'text-danger'; // Expired
                } else if (daysUntilExpiry <= 30) {
                    rowClass = 'text-warning'; // Expiring within 30 days
                } else {
                    rowClass = 'text-success'; // Good stock
                }

                printContent += `<tr class="${rowClass}">
                    <td>${element?.product_name || 'N/A'}</td>
                    <td>${element?.brand_name || 'N/A'}</td>
                    <td>${element?.category_name || 'N/A'}</td>
                    <td>${element?.sub_category_name || 'N/A'}</td>
                    <td>${element?.pack_name || 'N/A'}</td>
                    <td>${element?.price_name || 'N/A'}</td>
                    <td>${element?.qty || '0'}</td>
                    <td>${expiryDate}</td>
                    <td>${element?.hsn_code || 'N/A'}</td>
                    <td>${element?.gst || '0'}%</td>
                </tr>`;
            });

            printContent += '</tbody></table>';

            var printWindow = window.open('', '', 'height=800, width=1400');
            printWindow.document.write('<html><head><title>Stock Report</title>');
            printWindow.document.write('<style>body {font-family: Arial, sans-serif;} table {width: 100%; border-collapse: collapse;} th, td {padding: 8px; border: 1px solid #ddd; font-size: 12px;} th {background-color: #f2f2f2;} .text-danger {color: #dc3545;} .text-warning {color: #ffc107;} .text-success {color: #28a745;} </style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>Stock Report</h2>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

</script>
@endsection