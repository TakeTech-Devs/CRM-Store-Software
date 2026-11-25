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

        <!-- Search / rows -->
        <div class="row mb-3">
            <div class="col-md-8 mb-2">
                <input type="text" id="reportSearch" class="form-control"
                    placeholder="Search product, brand, category, sub category or HSN code...">
            </div>
            <div class="col-md-4 mb-2">
                <select id="pageSizeSelect" class="form-control select2-manual" title="Rows per page">
                    <option value="10">Show 10 rows</option>
                    <option value="25" selected>Show 25 rows</option>
                    <option value="50">Show 50 rows</option>
                    <option value="100">Show 100 rows</option>
                </select>
            </div>
        </div>

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
                </div>

                <!-- Pagination Section -->
                <div class="container mt-3">
                    <div class="row align-items-center">
                        <div class="col text-muted small" id="tableInfo"></div>
                        <div class="col-auto">
                            <nav aria-label="...">
                                <ul class="pagination pagination-sm mb-0" id="pagination-links">
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

<script src="{{ asset('assets/js/report-table.js') }}"></script>
<script>
    const esc = ReportTable.escapeHtml;

    // Row colour: expired = red, expiring within 30 days = yellow, otherwise green.
    function stockRowClass(element) {
        let daysUntilExpiry = Math.ceil((new Date(element?.exp_date).getTime() - Date.now()) / (1000 * 3600 * 24));
        if (daysUntilExpiry < 0) return 'text-danger';
        if (daysUntilExpiry <= 30) return 'text-warning';
        return 'text-success';
    }

    function stockCells(element) {
        let expiryDate = element?.exp_date ? new Date(element.exp_date).toLocaleDateString() : 'N/A';
        return `<td>${esc(element?.product_name || 'N/A')}</td>
            <td>${esc(element?.brand_name || 'N/A')}</td>
            <td>${esc(element?.category_name || 'N/A')}</td>
            <td>${esc(element?.sub_category_name || 'N/A')}</td>
            <td>${esc(element?.pack_name || 'N/A')}</td>
            <td>${esc(element?.price_name || 'N/A')}</td>
            <td>${esc(element?.qty || '0')}</td>
            <td>${expiryDate}</td>
            <td>${esc(element?.hsn_code || 'N/A')}</td>
            <td>${esc(element?.gst || '0')}%</td>`;
    }

    const table = ReportTable({
        tbody: '#stock_list',
        pager: '#pagination-links',
        info: '#tableInfo',
        search: '#reportSearch',
        pageSize: '#pageSizeSelect',
        colspan: 10,
        searchText: (el) => [el?.product_name, el?.brand_name, el?.category_name, el?.sub_category_name, el?.hsn_code].join(' '),
        rowHtml: (el) => `<tr class="${stockRowClass(el)}">${stockCells(el)}</tr>`
    });

    $(document).ready(function() {
        $('#loader').show();
        $.ajax({
            url: '/api/stock-report',
            method: 'GET',
            success: function(res) {
                $('#loader').hide();
                table.setData(res.data || []);
            },
            error: function(xhr, status, error) {
                $('#loader').hide();
                $('#stock_list').html(`<tr><td colspan="10" class="text-center text-danger">Error loading data: ${esc(error)}</td></tr>`);
            }
        });
    });

    document.getElementById("printButton").addEventListener("click", function() {
        printStockReport();
    });

    // Prints whatever the search currently shows (all pages, not just the visible one).
    function printStockReport() {
        var printContent = '<table class="table text-dark border table-hover text-center"><thead><tr><th>Product Name</th><th>Brand</th><th>Category</th><th>Sub Category</th><th>Pack</th><th>Price</th><th>Quantity</th><th>Expiry Date</th><th>HSN Code</th><th>GST %</th></tr></thead><tbody>';

        table.getFiltered().forEach((element) => {
            printContent += `<tr class="${stockRowClass(element)}">${stockCells(element)}</tr>`;
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