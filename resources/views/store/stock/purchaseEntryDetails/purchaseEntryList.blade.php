<!-- resources/views/admin/stocks/index.blade.php -->
@extends('layouts.dashboard')
@section('title', 'Purchase Entry List')

@section('content')
    <style>
        .pagination {
            margin-top: 10px;
        }
    </style>

        <div class="container-fluid">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="text-dark bold ">Purchase Entry List</h1>
                <div class="text-right">
                    <a href="{{ url('admin/purchase/create') }}" class="btn btn-secondary btn-sm">New Purchase Entry</a>
                </div>
            </div>
            <div class="form-row d-flex align-items-center justify-content-between my-3">
                <div class="form-group d-flex align-items-end justify-content-around">
                    <div class="form-group mx-1">
                        <label for="start_date_input">Start Date</label>
                        <input type="date" class="form-control" id="start_date_input" name="start_date_input">
                    </div>
                    <div class="form-group mx-1">

                        <label for="end_date_input">End Date</label>
                        <input type="date" class="form-control" id="end_date_input" name="end_date_input">
                    </div>
                    <div class="form-group">
                        <button type="button" class="btn btn-success btn-md mx-1 filterBtn">Find</button>
                    </div>
                </div>
            </div>
            <div class="form-row btn-group d-flex align-items-center justify-content-between" role="group" aria-label="Show Entries and Export">   
                    <div class="show-entries form-group d-flex align-items-baseline justify-content-between">
                        <label for="showEntries" class="d-inline-block">Show Entries: &nbsp;</label>
                        <select class="form-control form-control-sm d-inline-block" style="width: auto;" id="showEntries" onchange="updatePagination()">
                            <option>10</option>
                            <option>25</option>
                            <option>50</option>
                            <option>100</option>
                        </select>
                    </div>
                    <div class="download-buttons">
                        <div class="download-options d-flex align-items-baseline justify-content-between">
                            <form class="d-flex align-items-center justify-content-between">
                                <div class="form-group d-flex align-items-center justify-content-center mx-3">
                                    <label for="search">Search: </label> &nbsp;&nbsp;
                                    <input type="text" class="form-control" id="search" placeholder="Search Purchase Bill No.">
                                </div>  
                            </form>   
                            
                            <p>Export as : &nbsp;</p>
                            <button type="button" class="btn mx-1 btn-md btn-success">
                                <i class="fas fa-file-excel"></i>
                            </button>
                            <button type="button" class="btn mx-1 btn-md btn-primary">
                                <i class="fas fa-file-word"></i>
                            </button>
                            <button type="button" class="btn mx-1 btn-md btn-danger">
                                <i class="fas fa-file-pdf"></i>
                            </button>
                        </div>
                    </div>
                <!-- </div> -->
            </div>

            
            <div class="table-responsive border mt-3 mb-5">
                <table id="purchase-entry-table" class="table p-2 text-center">
                    <thead>
                        <tr>
                            <th class="text-dark">#</th>
                            <th class="text-dark">Purchase Bill No</th>
                            <th class="text-dark">SKU ID</th>
                            <th class="text-dark">SKU Date</th>
                            <th class="text-dark">Total Amount</th>
                            <th class="text-dark">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        
                    </tbody>
                </table>   
                <div id="noBrandFoundMessage" class="text-center mt-3" style="display: none;">No Purchase Entry found</div>
                     
            </div>
        </div>


<script>

    $(document).ready(function(){
        // STORE ASSIGN LIST 
        function api_for_purchaseEntryList() {
            ajaxGetData('/api/purchase/stock', (response) => {
                purchase_entry_list(response)
            });
        }

        
        function purchase_entry_list(response){
            // console.log(response);
            $('#purchase-entry-table tbody').empty();
            if (response && response.data.length > 0) {
                $.each(response.data, function(index, store) {
                    $('#purchase-entry-table tbody').append(`
                    <tr class="store-row" style="cursor:pointer;" data-id="${store.id}"> 
                    <td scope="row">${index + 1}</td>   
                    // <td>${store.purchase_bill_number}</td>
                    <td>${store.sku_id}</td>   
                    <td>${store.sku_date}</td>
                    <td>${store.total}</td> 
                    <td><button class="btn btn-sm btn-info" onclick="print()">Print</button></td>   
                    </tr>
                    `);
                });
            } else {
                $('#purchase-entry-table tbody').append(`
                <tr>
                <td class="text-center" colspan="9">No records found</td>
                </tr>
                `);
            }
        }
        api_for_purchaseEntryList()
    });
    
    // SEARCH FUNCATIONALITY 
    $(document).ready(function(){
        $('#search').on('input', function(){
            var searchText = $(this).val().toLowerCase();
            var found = false;
            
            $('.store-row').each(function(){
                var brandName = $(this).find('td:eq(1)').text().toLowerCase(); 
                if(brandName.includes(searchText)){
                    $(this).show();
                    found = true;
                } else {
                    $(this).hide();
                }
            });
            
            
            if (found) {
                $('#noBrandFoundMessage').hide();
            } else {
                $('#noBrandFoundMessage').show();
            }
        });
    });

    // FILTER FUNCTIONALITY
    
    $(document).on('click', '.filterBtn', function() {
    let startDate = $('#start_date_input').val();
    let endDate = $('#end_date_input').val();

    $.ajax({
        url: '/api/purchase/entry/filter',
        method: 'GET',
        data: {
            start_date: startDate,
            end_date: endDate
        },
        success: function(response) {
            console.log(response);
            // Assuming the response contains the filtered data in JSON format
            if (response.status === 200) {
                displayFilteredData(response.data);
            } else {
                console.error('Failed to fetch data:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching data:', error);
        }
    });
});

function displayFilteredData(data) {
    // Assuming 'data' is an array of filtered entries
    let tableBody = $('#purchase-entry-table tbody');
    tableBody.empty();

    if (data.length > 0) {
        data.forEach((entry, index) => {
            tableBody.append(`
                <tr>
                    <td>${index + 1}</td>
                    <td>${entry.purchase_bill_no}</td>
                    <td>${entry.sku_id}</td>
                    <td>${entry.created_at}</td>
                    <td>${entry.total_amount}</td>
                    <td>Actions</td>
                </tr>
            `);
        });
    } else {
        tableBody.append('<tr><td colspan="6">No records found</td></tr>');
    }
}

});


</script>
@endsection
