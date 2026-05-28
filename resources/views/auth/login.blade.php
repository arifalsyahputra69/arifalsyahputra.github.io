<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login Kasir</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>

        @keyframes gradientMove {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        @keyframes float {
            0%,100% { transform: translateY(0px); }
            50%      { transform: translateY(-12px); }
        }
        @keyframes slideUp {
            from { opacity:0; transform:translateY(40px); }
            to   { opacity:1; transform:translateY(0); }
        }
        @keyframes fadeInDown {
            from { opacity:0; transform:translateY(-20px); }
            to   { opacity:1; transform:translateY(0); }
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(-45deg, #4e73df, #1cc88a, #36b9cc, #6f42c1);
            background-size: 400% 400%;
            animation: gradientMove 8s ease infinite;
            position: relative;
            overflow: hidden;
        }
        body::before, body::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            opacity: .12;
            background: white;
        }
        body::before {
            width: 400px; height: 400px;
            top: -100px; left: -100px;
            animation: float 6s ease-in-out infinite;
        }
        body::after {
            width: 300px; height: 300px;
            bottom: -80px; right: -80px;
            animation: float 8s ease-in-out infinite reverse;
        }

        .store-logo {
            text-align: center;
            margin-bottom: 28px;
            animation: fadeInDown .6s ease both;
        }
        .store-logo .logo-icon {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
            margin-bottom: 10px;
            box-shadow: 0 8px 20px rgba(78,115,223,.35);
        }
        .store-logo .store-name {
            font-size: 20px;
            font-weight: 800;
            color: #1a1a2e;
            letter-spacing: .5px;
        }
        .store-logo .store-sub {
            font-size: 12px;
            color: #64748b;
            font-weight: 500;
        }

        .login-card {
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(20px);
            padding: 40px;
            border-radius: 24px;
            box-shadow: 0 30px 60px rgba(0,0,0,.2);
            width: 100%;
            max-width: 420px;
            animation: slideUp .6s ease both;
            animation-delay: .1s;
            position: relative;
            z-index: 1;
        }
        .login-title {
            text-align: center;
            font-weight: 800;
            font-size: 18px;
            margin-bottom: 24px;
            color: #1a1a2e;
        }
        .login-title span {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            margin-top: 4px;
        }

        .input-wrap {
            position: relative;
            margin-bottom: 18px;
            animation: slideUp .5s ease both;
        }
        .input-wrap:nth-child(1) { animation-delay:.2s; }
        .input-wrap:nth-child(2) { animation-delay:.3s; }
        .input-wrap:nth-child(3) { animation-delay:.4s; }
        .input-wrap label {
            font-size: 12px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 6px;
            display: block;
        }

        .input-icon-wrap { position: relative; }
        .input-icon-wrap .input-icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 14px;
            transition: .2s;
        }
        .input-icon-wrap .form-control {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 11px 16px 11px 40px;
            font-size: 14px;
            transition: .25s;
            background: #fafafa;
        }
        .input-icon-wrap .form-control:focus {
            border-color: #4e73df;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(78,115,223,.1);
        }
        .input-icon-wrap .form-control:focus ~ .input-icon { color: #4e73df; }
        .input-icon-wrap .form-control.is-valid  { border-color: #1cc88a; }
        .input-icon-wrap .form-control.is-invalid { border-color: #e74a3b; }

        .toggle-password {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #94a3b8;
            font-size: 14px;
            transition: .2s;
        }
        .toggle-password:hover { color: #4e73df; }

        .form-check-input:checked {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        .form-check-label {
            font-size: 13px;
            color: #64748b;
        }

        .btn-login {
            border-radius: 12px;
            padding: 12px;
            font-weight: 700;
            font-size: 15px;
            background: linear-gradient(135deg, #4e73df, #1cc88a);
            border: none;
            color: white;
            transition: .3s;
            box-shadow: 0 4px 14px rgba(78,115,223,.35);
            animation: slideUp .5s ease both;
            animation-delay: .5s;
        }
        .btn-login:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(78,115,223,.4);
            color: white;
        }
        .btn-login:disabled { opacity: .6; cursor: not-allowed; }

        .alert-danger {
            border-radius: 12px;
            font-size: 13px;
            border: none;
            background: #fee2e2;
            color: #991b1b;
        }
        .text-danger.small { font-size: 11px; }

    </style>
</head>
<body>

<div class="login-card">

    <div class="store-logo">
        <div class="logo-icon">
            <i class="fas fa-store"></i>
        </div>
        <div class="store-name">Toko Hikfra Collection</div>
        <div class="store-sub">Sistem Kasir & Manajemen Stok</div>
    </div>

    <div class="login-title">
        Masuk ke Akun Anda
        <span>Silakan login untuk melanjutkan</span>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>
            Name/email atau password salah.
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- NAME / EMAIL -->
        <div class="input-wrap">
            <label>Name / Email</label>
            <div class="input-icon-wrap">
                <input type="text"
                       name="login"
                       id="login"
                       value="{{ old('login') }}"
                       class="form-control"
                       placeholder="Name atau Email"
                       required autofocus>
                <i class="fas fa-user input-icon"></i>
            </div>
        </div>

        <!-- PASSWORD -->
        <div class="input-wrap">
            <label>Password</label>
            <div class="input-icon-wrap">
                <input type="password"
                       name="password"
                       id="password"
                       class="form-control"
                       placeholder="Minimal 6 karakter"
                       required>
                <i class="fas fa-lock input-icon"></i>
                <i class="fas fa-eye toggle-password" id="togglePassword"></i>
            </div>
        </div>

        <!-- REMEMBER ME -->
        <div class="input-wrap">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember">
                <label class="form-check-label">Ingat Saya</label>
            </div>
        </div>

        <button type="submit" class="btn btn-login w-100" id="btn-login">
            <i class="fas fa-sign-in-alt me-2"></i> Login
        </button>

    </form>

</div>


<script>

    /* ===== TOGGLE PASSWORD ===== */
    document.getElementById("togglePassword").addEventListener("click", function(){
        const input = document.getElementById("password");
        const isPassword = input.type === "password";
        input.type = isPassword ? "text" : "password";
        this.classList.toggle("fa-eye", !isPassword);
        this.classList.toggle("fa-eye-slash", isPassword);
    });


    /* ===== VALIDASI FORM ===== */
    document.addEventListener("DOMContentLoaded", function(){

        const loginInput    = document.getElementById("login");
        const passwordInput = document.getElementById("password");
        const loginBtn      = document.getElementById("btn-login");

        const loginError = document.createElement("div");
        loginError.classList.add("text-danger", "small", "mt-1");
        loginInput.parentNode.appendChild(loginError);

        const passwordError = document.createElement("div");
        passwordError.classList.add("text-danger", "small", "mt-1");
        passwordInput.parentNode.parentNode.appendChild(passwordError);

        function validateLogin(){
            if(loginInput.value.length < 3){
                loginInput.classList.add("is-invalid");
                loginInput.classList.remove("is-valid");
                loginError.textContent = "Minimal 3 karakter";
                return false;
            }
            loginInput.classList.remove("is-invalid");
            loginInput.classList.add("is-valid");
            loginError.textContent = "";
            return true;
        }

        function validatePassword(){
            if(passwordInput.value.length < 6){
                passwordInput.classList.add("is-invalid");
                passwordInput.classList.remove("is-valid");
                passwordError.textContent = "Password minimal 6 karakter";
                return false;
            }
            passwordInput.classList.remove("is-invalid");
            passwordInput.classList.add("is-valid");
            passwordError.textContent = "";
            return true;
        }

        function validateForm(){
            loginBtn.disabled = !(validateLogin() && validatePassword());
        }

        loginInput.addEventListener("keyup", validateForm);
        passwordInput.addEventListener("keyup", validateForm);
        loginBtn.disabled = true;

    });

</script>

</body>
</html>