<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store Verification</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="{{ asset('assets/vendor/fontawesome-free/css/all.min.css') }}" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-wrapper {
            display: flex;
            width: 860px;
            min-height: 480px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,.15);
        }

        /* Left brand panel */
        .brand-panel {
            width: 42%;
            background: linear-gradient(160deg, #A54217 0%, #7a3010 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px 36px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }

        .brand-panel::before {
            content: '';
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
            top: -60px;
            right: -80px;
        }
        .brand-panel::after {
            content: '';
            position: absolute;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
            bottom: -40px;
            left: -50px;
        }

        .brand-logo {
            background: #fff;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 28px;
        }

        .brand-logo img {
            width: 140px;
            display: block;
        }

        .brand-panel h2 {
            font-size: 1.25rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 10px;
            letter-spacing: .02em;
        }

        .brand-panel p {
            font-size: .82rem;
            opacity: .75;
            text-align: center;
            line-height: 1.6;
        }

        .brand-steps {
            margin-top: 32px;
            width: 100%;
        }

        .brand-step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
        }

        .step-num {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.4);
            font-size: .7rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .step-text {
            font-size: .78rem;
            opacity: .85;
            line-height: 1.5;
        }

        /* Right form panel */
        .form-panel {
            width: 58%;
            background: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 52px 48px;
        }

        .form-panel h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 6px;
        }

        .form-panel .subtitle {
            font-size: .82rem;
            color: #94a3b8;
            margin-bottom: 36px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: .78rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .input-wrap {
            position: relative;
        }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: .85rem;
        }

        .input-wrap input {
            width: 100%;
            padding: 11px 14px 11px 38px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: .88rem;
            color: #1e293b;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
            background: #f8fafc;
        }

        .input-wrap input:focus {
            border-color: #A54217;
            box-shadow: 0 0 0 3px rgba(165,66,23,.1);
            background: #fff;
        }

        .btn-verify {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #A54217, #c7521e);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 8px;
            transition: opacity .2s, transform .1s;
            letter-spacing: .02em;
        }

        .btn-verify:hover { opacity: .92; }
        .btn-verify:active { transform: scale(.98); }
        .btn-verify:disabled { opacity: .65; cursor: not-allowed; }

        .alert-msg {
            display: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: .8rem;
            margin-bottom: 18px;
            font-weight: 600;
        }
        .alert-msg.error { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .alert-msg.success { background: #dcfce7; color: #16a34a; border: 1px solid #86efac; }

        @media (max-width: 680px) {
            .page-wrapper { flex-direction: column; width: 95%; }
            .brand-panel { width: 100%; padding: 32px 24px; }
            .brand-steps { display: none; }
            .form-panel { width: 100%; padding: 32px 24px; }
        }
    </style>
</head>
<body>

<div class="page-wrapper">
    <!-- Left: Brand -->
    <div class="brand-panel">
        <div class="brand-logo">
            <img src="{{ asset('assets/img/logo.png') }}" alt="RightAid Logo">
        </div>
        <h2>Store Setup</h2>
        <p>Verify your store credentials to activate this device for billing and operations.</p>

        <div class="brand-steps">
            <div class="brand-step">
                <div class="step-num">1</div>
                <div class="step-text">Enter the Store ID provided by your administrator</div>
            </div>
            <div class="brand-step">
                <div class="step-num">2</div>
                <div class="step-text">Enter your store pass key and registered email</div>
            </div>
            <div class="brand-step">
                <div class="step-num">3</div>
                <div class="step-text">Click Verify — you'll be redirected to login</div>
            </div>
        </div>
    </div>

    <!-- Right: Form -->
    <div class="form-panel">
        <h1>Store Verification</h1>
        <p class="subtitle">One-time setup to activate your store panel</p>

        <div class="alert-msg" id="alertMsg"></div>

        <div class="form-group">
            <label for="storeId">Store ID</label>
            <div class="input-wrap">
                <i class="fas fa-store"></i>
                <input type="text" id="storeId" name="storeId" placeholder="e.g. RA_CRM_STORE-00001" autocomplete="off">
            </div>
        </div>

        <div class="form-group">
            <label for="storePassKey">Store Pass Key</label>
            <div class="input-wrap">
                <i class="fas fa-key"></i>
                <input type="password" id="storePassKey" name="storePassKey" placeholder="Enter pass key">
            </div>
        </div>

        <div class="form-group">
            <label for="storeMail">Store Email</label>
            <div class="input-wrap">
                <i class="fas fa-envelope"></i>
                <input type="email" id="storeMail" name="storeMail" placeholder="store@rightaid.com">
            </div>
        </div>

        <button class="btn-verify" id="verify">Verify Store</button>
    </div>
</div>

<script src="{{ url('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/master.js') }}"></script>
<script>
    function showAlert(msg, type) {
        $('#alertMsg').removeClass('error success').addClass(type).text(msg).show();
    }

    $(document).ready(function () {
        // If already verified, go straight to login
        ajaxGetData("/verify/store", function(res) {
            if (res.resStatus) window.location.href = '/login-page';
        });

        $('#verify').on('click', function () {
            const btn = $(this);
            const storeId = $('#storeId').val().trim();
            const passKey = $('#storePassKey').val().trim();
            const mail    = $('#storeMail').val().trim();

            if (!storeId || !passKey || !mail) {
                showAlert('Please fill in all fields.', 'error');
                return;
            }

            btn.text('Verifying…').prop('disabled', true);
            $('#alertMsg').hide();

            $.ajax({
                type: 'POST',
                url: '/api/verify-store',
                data: { storeId, storePassKey: passKey, storeMail: mail },
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (response) {
                    if (response?.success) {
                        showAlert('Verified! Redirecting to login…', 'success');
                        setTimeout(() => window.location.href = '/login-page', 800);
                    } else {
                        showAlert(response?.message || 'Verification failed.', 'error');
                        btn.text('Verify Store').prop('disabled', false);
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Verification failed. Please try again.';
                    showAlert(msg, 'error');
                    btn.text('Verify Store').prop('disabled', false);
                }
            });
        });

        // Allow Enter key to submit
        $('input').on('keydown', function(e) {
            if (e.key === 'Enter') $('#verify').click();
        });
    });
</script>
</body>
</html>
