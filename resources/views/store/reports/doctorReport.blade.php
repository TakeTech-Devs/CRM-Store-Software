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
            #printButton{
                display: none;
            }
            @page{
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
                        <label for="doctor_id">Select Store</label>
                        <select name="doctor_id" id="doctor_id" class="form-control">
                            <option value="">Loading stores...</option>
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
    $.ajax({
        url: "{{ url('http://localhost:8000/api/doctor') }}", 
        method: 'GET',
        success: function(response) {
            if (response.status === 200) {
                let stores = response.data;
                let storeOptions = '<option value="">Select The Doctor</option>';
                stores.forEach(store => {
                    storeOptions += `<option value="${store.id}">${store.name}</option>`;
                });
                $('#doctor_id').html(storeOptions);
            } else {
                alert('No stores available.');
            }
        },
        error: function() {
            alert('Failed to load stores.');
        }
    });



        $('#generateReport').click(function(e) {
            e.preventDefault();

            let doctorId = $('#doctor_id').val();

            $.ajax({
                url: "{{ route('doctor.report') }}",
                method: 'GET',
                data: { doctor_name: doctorId },
                success: function(response) {
                    if (response.status === 'success') {
                        let salesData = response.data;
                        let html = '';

                        salesData.forEach((sale, index) => {
                            html += `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${sale.doctor_name}</td>
                                    <td>${sale.billing_date}</td>
                                    <td>${sale.invoiceNo}</td>
                                    <td>${sale.product_name}</td>
                                    <td>${sale.hsn_code}</td>
                                    <td>${sale.staff_name ? sale.staff_name : sale.customer_name}</td>
                                    <td>${sale.staff_phone ? sale.staff_phone : sale.customer_phone}</td>
                                    <td>${sale.paymentType}</td>
                                    <td>${sale.total_amt}</td>
                                </tr>
                            `;
                        });

                        $('#reportData').html(html);
                    } else {
                        alert('No data found for the selected store and month.');
                    }
                },
                error: function() {
                    alert('An error occurred while generating the report.');
                }
            });
        });
    });
    function printModalContent() {
        var printContent = document.getElementById("printArea").innerHTML;
        var originalContent = document.body.innerHTML;

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

    document.getElementById("printButton").addEventListener("click", function() {
        printModalContent();
    });
</script>
@endsection

                    