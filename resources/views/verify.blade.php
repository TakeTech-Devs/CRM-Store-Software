<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Verification Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .container h2 {
            margin-bottom: 20px;
            font-size: 28px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 10px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .form-group input[type="button"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group input[type="button"]:hover {
            background-color: #45a049;
        }
    </style>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

<div class="container">
    <h2>Store Verification</h2>
        <div class="form-group">
            <label for="storeId">Store ID</label>
            <input type="text" id="storeId" name="storeId" >
        </div>
        <div class="form-group">
            <label for="storePassKey">Store Pass Key</label>
            <input type="password" id="storePassKey" name="storePassKey" >
        </div>
        <div class="form-group">
            <label for="storeMail">Store Email</label>
            <input type="email" id="storeMail" name="storeMail" >
        </div>
        <div class="form-group">
            <input type="button" id="verify" value="Verify">
        </div>
</div>

</body>
<script src="{{ url('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{asset('assets/js/master.js')}}"></script>

<script>
    $(document).ready(function () {
        verifyStatus()
        $(document).on('click', '#verify', function () {
            let bodyData = {
                storeId: $('#storeId').val(),
                storePassKey: $('#storePassKey').val(),
                storeMail: $('#storeMail').val(),
            }
            let csrfToken = $('meta[name="csrf-token"]').attr('content');
            ajaxPostData('http://127.0.0.1:8000/api/verify-store', bodyData, csrfToken, (response) =>{
                if (response?.resStatus) {
                    ajaxPostData('http://127.0.0.1:8001/store', response?.data, csrfToken, (responseData) =>{
                        window.location.href = '/login-page';
                    })
                }
            })
        });
    });

    function verifyStatus(){
        ajaxGetData("/verify/store", (res)=>{
            if (res.resStatus) {
                window.location.href = '/login-page';
            }

        })
    }
</script>
</html>
