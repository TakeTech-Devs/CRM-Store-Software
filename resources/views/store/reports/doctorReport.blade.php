@extends('layouts.dashboard')

@section('title', 'Doctor Wise Report')

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
            <h2 class="text-dark">Doctor Wise Report</h2>
        </div>
        <div class="col-6">
            <div style="text-align:right; margin-bottom: 25px !important;">
                <button class="btn btn-sm shadow btn-primary" id="printButton">Print Report</button>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <form id="reportForm" class="d-flex align-items-center justify-content-start col-md-12">
                <div class="col-md-5">
                    <div class="form-group">
                        <label for="doctor_id">Select Doctor</label>
                        <select name="doctor_id" id="doctor_id" class="form-control">
                            <option value="">Loading doctors...</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <button type="button" id="generateReport" class="btn btn-sm btn-success">Generate Report</button>
                </div>
            </form>

            <div class="col-md-12 mt-4" id="reportSection">
                <div class="table-responsive">
                    <div id="printArea">
                        <table class="table text-dark border table-hover text-center" id="reportTable">
                            <thead class="sticky-top bg-light">
                                <tr>
                                    <th>#</th>
                                    <th>Doctor Name</th>
                                    <th>Billing Date</th>
                                    <th>Invoice No</th>
                                    <th>Product Name</th>
                                    <th>HSN Code</th>
                                    <th>Customer Name</th>
                                    <th>Mobile No</th>
                                    <th>Payment Type</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody id="reportData">
                                <tr>
                                    <td colspan="10" class="text-center">No data available. Please select a doctor and generate the report.</td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div id="paginationInfo"></div>
                            <nav>
                                <ul class="pagination" id="paginationControls"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        let allData = [];
        let currentPage = 1;
        const recordsPerPage = 15;

        $.ajax({
            url: "{{ url('http://127.0.0.1:8000/api/doctor') }}",
            method: 'GET',
            success: function (response) {
                console.log(response);
                
                if (response.status === 'success') {
                    let doctors = response.data;
                    let options = '<option value="">Select Doctor</option>';
                    doctors.forEach(doctor => {
                        options += `<option value="${doctor.id}">${doctor.name}</option>`;
                    });
                    $('#doctor_id').html(options);
                } else {
                    alert('No doctors available.');
                }
            },
            error: function () {
                alert('Failed to load doctors.');
            }
        });

        // Fetch and display report data
        function fetchAndDisplayData() {
            let doctorId = $('#doctor_id').val();
            $.ajax({
                url: "{{ route('doctor.report') }}",
                method: 'GET',
                data: { doctor_name: doctorId },
                success: function (response) {
                    if (response.status === 'success') {
                        allData = response.data; 
                        currentPage = 1; 
                        renderPage();
                    } else {
                        alert('No data found for the selected doctor.');
                    }
                },
                error: function () {
                    alert('An error occurred while fetching the report.');
                }
            });
        }

        // Render current page
        function renderPage() {
            const start = (currentPage - 1) * recordsPerPage;
            const end = start + recordsPerPage;
            const pageData = allData.slice(start, end); // Extract data for the current page
            let html = '';

            pageData.forEach((item, index) => {
                html += `
                    <tr>
                        <td>${start + index + 1}</td>
                        <td>${item.doctor_name}</td>
                        <td>${item.billing_date}</td>
                        <td>${item.invoiceNo}</td>
                        <td>${item.product_name}</td>
                        <td>${item.hsn_code}</td>
                        <td>${item.staff_name ? item.staff_name : item.customer_name}</td>
                        <td>${item.staff_phone ? item.staff_phone : item.customer_phone}</td>
                        <td>${item.paymentType}</td>
                        <td>${item.total_amt}</td>
                    </tr>
                `;
            });

            $('#reportData').html(html);
            updatePaginationControls();
        }

        // Update pagination controls
        function updatePaginationControls() {
            const totalPages = Math.ceil(allData.length / recordsPerPage);
            let paginationControls = '';

            for (let i = 1; i <= totalPages; i++) {
                paginationControls += `
                    <li class="page-item ${currentPage === i ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `;
            }

            $('#paginationControls').html(paginationControls);
            $('#paginationInfo').html(
                `Showing ${Math.min((currentPage - 1) * recordsPerPage + 1, allData.length)} to ${Math.min(currentPage * recordsPerPage, allData.length)} of ${allData.length} records`
            );
        }

        // Handle page navigation
        $(document).on('click', '.page-link', function (e) {
            e.preventDefault();
            const page = parseInt($(this).data('page'));
            if (page && page !== currentPage) {
                currentPage = page;
                renderPage();
            }
        });

        // Fetch report on button click
        $('#generateReport').click(function (e) {
            e.preventDefault();
            fetchAndDisplayData();
        });

        // Print functionality
        function printReport() {
            let doctorName = $('#doctor_id option:selected').text(); // Get selected doctor name
            let doctorId = $('#doctor_id').val(); // Get selected doctor ID

            if (!doctorId) {
                alert("Please select a doctor to print the report.");
                return;
            }

            let printContent = `
                <div style="text-align: center; margin-bottom: 20px;">
                    <h2>Doctor Wise Sales Report</h2>
                    <h4>${doctorName} - ID: ${doctorId}</h4>
                </div>
                <table class="table text-dark border table-hover text-center">
                    <thead class="sticky-top bg-light">
                        <tr>
                            <th>#</th>
                            <th>Doctor Name</th>
                            <th>Billing Date</th>
                            <th>Invoice No</th>
                            <th>Product Name</th>
                            <th>HSN Code</th>
                            <th>Customer Name</th>
                            <th>Mobile No</th>
                            <th>Payment Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            allData.forEach((item, index) => {
                printContent += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>${item.doctor_name}</td>
                        <td>${item.billing_date}</td>
                        <td>${item.invoiceNo}</td>
                        <td>${item.product_name}</td>
                        <td>${item.hsn_code}</td>
                        <td>${item.staff_name ? item.staff_name : item.customer_name}</td>
                        <td>${item.staff_phone ? item.staff_phone : item.customer_phone}</td>
                        <td>${item.paymentType}</td>
                        <td>${item.total_amt}</td>
                    </tr>
                `;
            });

            printContent += `
                    </tbody>
                </table>
            `;

            let originalContent = document.body.innerHTML;

            document.body.innerHTML = printContent;
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }


        $('#printButton').click(function () {
            if (allData.length > 0) {
                printReport();
            } else {
                alert('No data available to print.');
            }
        });
    });
</script>
@endsection

