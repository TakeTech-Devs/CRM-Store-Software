@extends('layouts.dashboard')

@section('title', 'Store Details')

@section('content')
<style>
    .container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 600px;
    }
    .container h1 {
        margin: 0 0 20px;
    }
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table, th, td {
        border: 1px solid #ccc;
    }
    th, td {
        padding: 10px;
        text-align: left;
    }
    th {
        background-color: #f0f0f0;
    }
</style>

<div class="container">
    <h3 class="text-uppercase text-center">Store Details</h3>
    <table>
        <tbody id="store-details">
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        ajaxGetData(`/verify/store`, (response) => {
            if (response && response.data && response.data.length > 0) {
                $('#store-details').html(`
                    <tr>
                        <th>Store ID</th>
                        <td>${response.data[0].store_meta_id}</td>
                    </tr>
                    <tr>
                        <th>Store Name</th>
                        <td>${response.data[0].name}</td>
                    </tr>
                    <tr>
                        <th>Store Address</th>
                        <td>${response.data[0].store_address}</td>
                    </tr>
                    <tr>
                        <th>Store Start Year</th>
                        <td>${response.data[0].store_start_date}</td>
                    </tr>
                    <tr>
                        <th>Store Email</th>
                        <td>${response.data[0].store_mail}</td>
                    </tr>
                `);
            } else {
                $('#store-details').html('<tr><td colspan="2">No store details available.</td></tr>');
            }
        });
    });
</script>
@endsection
