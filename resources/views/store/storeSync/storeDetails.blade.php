@extends('layouts.dashboard')

@section('title', 'Store Details')

@section('content')
<style>
    /* body {
        font-family: Arial, sans-serif;
        background-color: #d3d3d3;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
    } */
    .container {
        background-color: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 400px;
    }
    .container h1 {
        margin: 0 0 20px;
    }
    .details {
        margin-bottom: 20px;
    }
    .details div {
        margin-bottom: 10px;
    }
    .details div span:first-child {
        display: inline-block;
        width: 150px;
        font-weight: bold;
    }
    .change-password {
        text-align: right;
    }
    .change-password button {
        background-color: #f0f0f0;
        border: 1px solid #ccc;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
    }
    .change-password button:hover {
        background-color: #e0e0e0;
    }
</style>

<div class="container">
    <h1>Store Details</h1>
    <div class="details" id="store-details">
        {{-- <div><span>Store ID</span> 789654523</div>
        <div><span>Store Name</span> Store 1</div>
        <div><span>Store Address</span> Lorem Lorem LoremLorem</div>
        <div><span>Store Start Year</span> 1/2/2023</div>
        <div><span>Store Email</span> store1@gmail.com</div> --}}
    </div>
    <div class="change-password">
        {{-- <button>Request to Change Password</button> --}}
    </div>
</div>
<script>
    $(document).ready(function () {
        ajaxGetData(`/verify/store`, (response)=>{
            $('#store-details').html(`
                    <div><span>Store ID</span> ${response?.data[0]?.store_meta_id}</div>
                    <div><span>Store Name</span> ${response?.data[0]?.name}</div>
                    <div><span>Store Address</span> ${response?.data[0]?.store_address}</div>
                    <div><span>Store Start Year</span> ${response?.data[0]?.store_start_date}</div>
                    <div><span>Store Email</span> ${response?.data[0]?.store_mail}</div>
                `);
        })
    });
</script>
@endsection
