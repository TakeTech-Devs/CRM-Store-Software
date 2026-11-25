<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — RightAid Store</title>
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
            width: 820px;
            min-height: 460px;
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
            width: 260px; height: 260px;
            border-radius: 50%;
            background: rgba(255,255,255,.06);
            top: -60px; right: -80px;
        }
        .brand-panel::after {
            content: '';
            position: absolute;
            width: 180px; height: 180px;
            border-radius: 50%;
            background: rgba(255,255,255,.05);
            bottom: -40px; left: -50px;
        }

        .brand-logo {
            background: #fff;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 24px;
        }
        .brand-logo img { width: 140px; display: block; }

        .brand-panel h2 {
            font-size: 1.2rem;
            font-weight: 700;
            text-align: center;
            margin-bottom: 8px;
        }
        .brand-panel p {
            font-size: .8rem;
            opacity: .75;
            text-align: center;
            line-height: 1.6;
        }

        .brand-divider {
            width: 40px;
            height: 3px;
            background: rgba(255,255,255,.35);
            border-radius: 2px;
            margin: 20px auto;
        }

        .brand-info {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 100%;
        }
        .brand-info-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: .78rem;
            opacity: .8;
        }
        .brand-info-item i {
            width: 28px; height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,.15);
            display: flex; align-items: center; justify-content: center;
            font-size: .7rem;
            flex-shrink: 0;
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

        .form-panel .greeting {
            font-size: .78rem;
            color: #A54217;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: 6px;
        }

        .form-panel h1 {
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .form-panel .subtitle {
            font-size: .82rem;
            color: #94a3b8;
            margin-bottom: 36px;
        }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: .72rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .input-wrap { position: relative; }

        .input-wrap i {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: .85rem;
            pointer-events: none;
        }

        .input-wrap input {
            width: 100%;
            padding: 12px 42px 12px 40px;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            font-size: .9rem;
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

        .input-wrap input::placeholder { color: #cbd5e1; }

        .toggle-pw {
            position: absolute;
            right: 0px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            cursor: pointer;
            font-size: .85rem;
            border: none;
            background: none;
            padding: 4px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 12%;
            height: 100%;
        }
        .toggle-pw:hover { color: #475569; }

        .alert-msg {
            display: none;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: .8rem;
            margin-bottom: 18px;
            font-weight: 600;
        }
        .alert-msg.error   { background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; }
        .alert-msg.success { background: #dcfce7; color: #16a34a; border: 1px solid #86efac; }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #A54217, #c7521e);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 6px;
            transition: opacity .2s, transform .1s;
            letter-spacing: .02em;
        }
        .btn-login:hover   { opacity: .92; }
        .btn-login:active  { transform: scale(.98); }
        .btn-login:disabled { opacity: .6; cursor: not-allowed; }

        @media (max-width: 640px) {
            .page-wrapper { flex-direction: column; width: 95%; }
            .brand-panel  { width: 100%; padding: 32px 24px; }
            .brand-info   { display: none; }
            .form-panel   { width: 100%; padding: 32px 24px; }
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
        <h2>Store Panel</h2>
        <p>Manage billing, stock, and reports for your store.</p>

        <div class="brand-divider"></div>

        <div class="brand-info">
            <div class="brand-info-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Customer & Staff Billing</span>
            </div>
            <div class="brand-info-item">
                <i class="fas fa-boxes"></i>
                <span>Stock & Expiry Management</span>
            </div>
            <div class="brand-info-item">
                <i class="fas fa-chart-bar"></i>
                <span>Sales Analytics & Reports</span>
            </div>
            <div class="brand-info-item">
                <i class="fas fa-sync-alt"></i>
                <span>Admin Sync — In & Out</span>
            </div>
        </div>
    </div>

    <!-- Right: Login Form -->
    <div class="form-panel">
        <div class="greeting">Welcome Back</div>
        <h1>Sign In</h1>
        <p class="subtitle">Enter your store credentials to continue</p>

        @if ($errors->any())
        <div class="alert-msg error" style="display:block;">
            {{ $errors->first() }}
        </div>
        @endif

        <div class="alert-msg" id="alertMsg"></div>

        <form action="/login" method="POST" id="loginForm">
            @csrf
            <div class="form-group">
                <label for="storeId">Store ID</label>
                <div class="input-wrap">
                    <i class="fas fa-store"></i>
                    <input type="text" name="storeId" id="storeId"
                           placeholder="RA_CRM_STORE-00001"
                           value="{{ old('storeId') }}"
                           autocomplete="username" required>
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="password"
                           placeholder="Enter your password"
                           autocomplete="current-password" required>
                    <button type="button" class="toggle-pw" id="togglePw" tabindex="-1">
                        <i class="fas fa-eye" id="togglePwIcon"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn-login" id="loginBtn">Sign In</button>
        </form>
    </div>
</div>

<script src="{{ url('assets/vendor/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/master.js') }}"></script>
<script>
    $(document).ready(function () {
        // Redirect to verify if store not set up
        ajaxGetData("/verify/store", function(res) {
            if (!res.resStatus) window.location.href = '/';
        });

        // Toggle password visibility
        $('#togglePw').on('click', function () {
            const input = $('#password');
            const icon  = $('#togglePwIcon');
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // Show loading state on submit
        $('#loginForm').on('submit', function () {
            $('#loginBtn').text('Signing in…').prop('disabled', true);
        });
    });
</script>
</body>
</html>
