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

            <!-- Search / filter / rows -->
            <div class="row mb-3">
                <div class="col-md-5 mb-2">
                    <input type="text" id="reportSearch" class="form-control" placeholder="Search product name...">
                </div>
                <div class="col-md-4 mb-2">
                    <select id="statusFilter" class="form-control select2-manual">
                        <option value="all">All medicines</option>
                        <option value="expired">Expired</option>
                        <option value="soon">Going to expire soon (within 3 months)</option>
                    </select>
                </div>
                <div class="col-md-3 mb-2">
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
        <!-- END BRAND LIST TABLE -->
    </div>

    <script src="{{ asset('assets/js/report-table.js') }}"></script>
    <script>
        // Works out how long a batch has left, and which colour / filter group it belongs to.
        function expiryInfo(element) {
            let currentDate = new Date();
            let expiryDate = new Date(element?.exp_date);
            let timeDifference = expiryDate.getTime() - currentDate.getTime();
            let daysUntilExpiry = Math.ceil(timeDifference / (1000 * 3600 * 24));
            let monthsUntilExpiry = expiryDate.getMonth() - currentDate.getMonth() +
                (12 * (expiryDate.getFullYear() - currentDate.getFullYear()));

            if (monthsUntilExpiry < 0 || (monthsUntilExpiry === 0 && daysUntilExpiry <= 0)) {
                return { cls: 'text-danger', label: 'Expired', status: 'expired' };
            } else if (monthsUntilExpiry === 0) {
                return { cls: 'text-danger', label: daysUntilExpiry + ' Days', status: 'soon' };
            } else if (monthsUntilExpiry <= 3) {
                return { cls: 'text-danger', label: monthsUntilExpiry + ' Months', status: 'soon' };
            } else if (monthsUntilExpiry <= 6) {
                return { cls: 'text-warning', label: monthsUntilExpiry + ' Months', status: 'later' };
            }
            return { cls: 'text-success', label: monthsUntilExpiry + ' Months', status: 'ok' };
        }

        const esc = ReportTable.escapeHtml;

        const table = ReportTable({
            tbody: '#exp_list',
            pager: '#pagination-links',
            info: '#tableInfo',
            search: '#reportSearch',
            pageSize: '#pageSizeSelect',
            filter: '#statusFilter',
            colspan: 3,
            searchText: (el) => el?.product_name || '',
            filterFn: (el, f) => f === 'all' || expiryInfo(el).status === f,
            rowHtml: (el) => {
                const info = expiryInfo(el);
                return `<tr class="${info.cls}">
                    <td>${esc(el?.product_name)}</td>
                    <td>${esc(el?.qty)}</td>
                    <td>${info.label}</td>
                </tr>`;
            }
        });

        $(document).ready(function() {
            $('#loader').show();
            $.ajax({
                url: '/api/expiry-report',
                method: 'GET',
                success: function(res) {
                    $('#loader').hide();
                    // Soonest expiry first, so the batches that need action are on page 1.
                    const rows = (res.data || []).slice().sort((a, b) => new Date(a.exp_date) - new Date(b.exp_date));
                    table.setData(rows);
                },
                error: function() {
                    $('#loader').hide();
                    $('#exp_list').html('<tr><td colspan="3" class="text-center text-danger">Could not load the report.</td></tr>');
                }
            });
        });

        document.getElementById("printButton").addEventListener("click", function() {
            printExpiryReport();
        });

        // Prints whatever the search / filter currently shows (all pages, not just the visible one).
        function printExpiryReport() {
            var printContent = '<table class="table text-dark border table-hover text-center"><thead><tr><th>Product Name</th><th>Product Qty</th><th>Expiring In</th></tr></thead><tbody>';

            table.getFiltered().forEach((element) => {
                const info = expiryInfo(element);
                printContent += `<tr class="${info.cls}"><td>${esc(element?.product_name)}</td><td>${esc(element?.qty)}</td><td>${info.label}</td></tr>`;
            });

            printContent += '</tbody></table>';

            var printWindow = window.open('', '', 'height=800, width=1200');
            printWindow.document.write('<html><head><title>Expiry Report</title>');
            printWindow.document.write('<style>body {font-family: Arial, sans-serif;} table {width: 100%; border-collapse: collapse;} th, td {padding: 10px; border: 1px solid #ddd;} th {background-color: #f2f2f2;} .text-danger {color: #dc3545;} .text-warning {color: #ffc107;} .text-success {color: #28a745;} </style>');
            printWindow.document.write('</head><body>');
            printWindow.document.write('<h2>Expiry Report</h2>');
            printWindow.document.write(printContent);
            printWindow.document.write('</body></html>');
            printWindow.document.close();
            printWindow.print();
        }

    </script>
@endsection
