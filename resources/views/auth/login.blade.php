<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login &mdash; Student Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 45%, #084298 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        body::before {
            content: '';
            position: fixed;
            top: -80px; left: -80px;
            width: 320px; height: 320px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
            pointer-events: none;
        }
        body::after {
            content: '';
            position: fixed;
            bottom: -100px; right: -80px;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
            pointer-events: none;
        }
        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 440px;
            padding: 2.5rem 2.5rem 2rem;
            animation: slideUp 0.5s cubic-bezier(0.16,1,0.3,1) both;
        }
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .login-logo {
            width: 64px; height: 64px;
            background: linear-gradient(135deg, #0d6efd, #084298);
            border-radius: 16px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.25rem;
            box-shadow: 0 8px 20px rgba(13,110,253,0.35);
        }
        .login-logo i { font-size: 2rem; color: #fff; }
        .login-title {
            font-size: 1.5rem; font-weight: 700;
            color: #1a1a2e; text-align: center; margin-bottom: 0.25rem;
        }
        .login-subtitle {
            text-align: center; color: #6c757d;
            font-size: 0.875rem; margin-bottom: 1.75rem;
        }
        .form-label { font-weight: 500; font-size: 0.875rem; color: #374151; }
        .form-control {
            border-radius: 10px; border: 1.5px solid #e5e7eb;
            padding: 0.65rem 1rem; font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 3px rgba(13,110,253,0.15);
        }
        .input-group-text {
            background: #f8f9fa; border: 1.5px solid #e5e7eb;
            border-right: none; border-radius: 10px 0 0 10px; color: #6c757d;
        }
        .input-group .form-control { border-left: none; border-radius: 0 10px 10px 0; }
        .input-group .form-control:focus { border-left: none; }
        .toggle-password {
            border: 1.5px solid #e5e7eb; border-left: none;
            border-radius: 0 10px 10px 0; background: #f8f9fa;
            cursor: pointer; color: #6c757d; padding: 0 0.9rem; transition: color 0.2s;
        }
        .toggle-password:hover { color: #0d6efd; }
        .btn-login {
            width: 100%; padding: 0.75rem; border-radius: 10px;
            font-weight: 600; font-size: 1rem;
            background: linear-gradient(135deg, #0d6efd, #0a58ca);
            border: none; color: #fff;
            box-shadow: 0 4px 14px rgba(13,110,253,0.4);
            transition: transform 0.15s, box-shadow 0.15s;
        }
        .btn-login:hover {
            transform: translateY(-1px); color: #fff;
            box-shadow: 0 6px 20px rgba(13,110,253,0.5);
        }
        .btn-login:active { transform: translateY(0); }
        .alert-custom { border-radius: 10px; font-size: 0.875rem; border: none; }
        .footer-note { text-align: center; font-size: 0.78rem; color: #adb5bd; margin-top: 1.5rem; }
    </style>
</head>
<body>
<div class="login-card">

    <div class="login-logo">
        <i class="bi bi-mortarboard-fill"></i>
    </div>
    <h1 class="login-title">Welcome Back</h1>
    <p class="login-subtitle">Sign in to Student Management System</p>

    {{-- Session status (e.g. after logout) --}}
    @if (session('status'))
        <div class="alert alert-success alert-custom mb-3">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('status') }}
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-custom mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" id="loginForm">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" id="email" name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="admin@example.com"
                    value="{{ old('email') }}"
                    required autofocus autocomplete="email">
            </div>
        </div>

        {{-- Password --}}
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" id="password" name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter your password"
                    required autocomplete="current-password">
                <button type="button" class="toggle-password" id="togglePassword" tabindex="-1">
                    <i class="bi bi-eye" id="toggleIcon"></i>
                </button>
            </div>
        </div>

        {{-- Remember Me & Forgot Password --}}
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check mb-0">
                <input class="form-check-input" type="checkbox" name="remember" id="remember"
                    {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label text-muted small" for="remember">Remember me</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-decoration-none small text-primary">
                    Forgot password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-login" id="loginBtn">
            <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
        </button>
    </form>

    <p class="footer-note">
        &copy; {{ date('Y') }} Student Management System &mdash; All rights reserved.
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('togglePassword').addEventListener('click', function () {
        const pw = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        pw.type = pw.type === 'password' ? 'text' : 'password';
        icon.className = pw.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
    document.getElementById('loginForm').addEventListener('submit', function () {
        const btn = document.getElementById('loginBtn');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Signing in...';
        btn.disabled = true;
    });
</script>
</body>
</html>
