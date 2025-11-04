@extends('layouts.dashboard')

@section('title', 'Store Dashboard')

@section('content')
    <style>
        .container-fluid {
            min-height: calc(100vh - 100px); /* Adjust 100px based on header/footer height */
            padding: 20px;
            margin-top: 15px;
        }
    </style>
    <div class="row">
        <!-- Today's Sale Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Today's Sale</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="todaySale">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Yesterday's Sale Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Yesterday's Sale</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="yesterdaySale">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Monthly Earnings Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Monthly Earnings</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="monthlyEarnings">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expired Medicines Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Expired Medicines</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="expiredMedicines">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-pills fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock 0 Medicine Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Stock 0 Medicine</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="zeroStockMedicine">Loading...</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-prescription-bottle-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
        <script>
            $(document).ready(function() {
                // Fetch Today's Sale
                $.ajax({
                    url: '/api/today-sale',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#todaySale').text('₹' + response.data.toLocaleString());
                        } else {
                            $('#todaySale').text('Error');
                        }
                    },
                    error: function() {
                        $('#todaySale').text('Error');
                    }
                });
    
                // Fetch Yesterday's Sale
                $.ajax({
                    url: '/api/yesterday-sale',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#yesterdaySale').text('₹' + response.data.toLocaleString());
                        } else {
                            $('#yesterdaySale').text('Error');
                        }
                    },
                    error: function() {
                        $('#yesterdaySale').text('Error');
                    }
                });
    
                // Fetch Monthly Earnings
                $.ajax({
                    url: '/api/monthly-earnings',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#monthlyEarnings').text('₹' + response.data.toLocaleString());
                        } else {
                            $('#monthlyEarnings').text('Error');
                        }
                    },
                    error: function() {
                        $('#monthlyEarnings').text('Error');
                    }
                });
    
                // Fetch Expired Medicines
                $.ajax({
                    url: '/api/expiry-report',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 200) {
                            const today = new Date();
                            let expiredCount = 0;
                            response.data.forEach(item => {
                                const expiryDate = new Date(item.exp_date);
                                if (expiryDate < today) {
                                    expiredCount++;
                                }
                            });
                            $('#expiredMedicines').text(expiredCount);
                        } else {
                            $('#expiredMedicines').text('Error');
                        }
                    },
                    error: function() {
                        $('#expiredMedicines').text('Error');
                    }
                });
    
                // Fetch Stock 0 Medicine
                $.ajax({
                    url: '/api/zero-stock-medicine',
                    method: 'GET',
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#zeroStockMedicine').text(response.data.count);
                        } else {
                            $('#zeroStockMedicine').text('Error');
                        }
                    },
                    error: function() {
                        $('#zeroStockMedicine').text('Error');
                    }
                });
            });
        </script>
    @endsection