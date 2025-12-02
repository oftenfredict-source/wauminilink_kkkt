<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Waumini Link - Forgot Password</title>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    :root {
        --primary-color: #0084d6;
        --secondary-color: #25D71B;
        --accent-color: #f8f9fa;
        --text-color: #333;
        --error-color: #dc3545;
        --success-color: #28a745;
    }
    
    body, html {
        height: 100%;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--accent-color);
        color: var(--text-color);
    }

    /* Navbar */
    .navbar-custom {
        background: rgba(0,0,0,0.7);
        padding: 10px 30px;
        position: fixed;
        width: 100%;
        z-index: 10;
        backdrop-filter: blur(5px);
    }
    .navbar-custom .navbar-brand img {
        height: 50px;
    }

    .forgot-password-container {
        display: flex;
        min-height: 100vh;
        padding-top: 70px;
    }

    /* Left side background */
    .forgot-password-left {
        flex: 1;
        background: url('{{ asset("assets/images/church.jpg") }}') no-repeat center center;
        background-size: cover;
        position: relative;
        color: #fff;
    }
    .forgot-password-left::after {
        content: "";
        position: absolute;
        top:0; left:0;
        width:100%; height:100%;
        background: linear-gradient(135deg, rgba(0, 132, 214, 0.3), rgba(0,0,0,0.7));
    }
    .forgot-password-left-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        padding: 20px;
        text-align: center;
    }
    .forgot-password-left-content h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    .forgot-password-left-content p {
        font-size: 1.2rem;
        line-height: 1.5;
        max-width: 80%;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    /* Right side form */
    .forgot-password-right {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
        background-color: #fff;
        box-shadow: -5px 0 15px rgba(0,0,0,0.05);
        position: relative;
    }

    .forgot-password-form {
        width: 100%;
        max-width: 400px;
        animation: fadeIn 0.8s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .forgot-password-form .logo {
        display: block;
        margin: 0 auto 20px;
        height: 70px;
    }

    .forgot-password-form h2 {
        font-weight: bold;
        margin-bottom: 10px;
        text-align: center;
        color: var(--primary-color);
        position: relative;
    }
    .forgot-password-form h2::after {
        content: "";
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        border-radius: 2px;
    }

    .forgot-password-form p {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
        font-size: 0.95rem;
    }

    .form-group {
        position: relative;
        margin-bottom: 25px;
    }
    .form-group i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-color);
        z-index: 2;
        font-size: 16px;
    }
    .form-control {
        padding-left: 48px;
        padding-right: 16px;
        height: 48px;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        font-size: 15px;
        background-color: #fafbfc;
    }
    .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(37, 215, 27, 0.1);
        background-color: #ffffff;
        outline: none;
    }

    .btn-submit {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, var(--primary-color), #006aad);
        color: #fff;
        font-weight: 600;
        font-size: 16px;
        border-radius: 10px;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 132, 214, 0.2);
    }
    .btn-submit:hover {
        background: linear-gradient(135deg, var(--secondary-color), #1fb115);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(37, 215, 27, 0.4);
    }

    .back-to-login {
        margin-top: 20px;
        text-align: center;
        font-size: 0.9rem;
        color: #666;
    }
    .back-to-login a {
        color: var(--primary-color);
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .back-to-login a:hover {
        color: var(--secondary-color);
    }

    .alert {
        border: none;
        border-left: 4px solid;
        border-radius: 6px;
        padding: 12px 15px;
        margin-bottom: 20px;
    }
    .alert-success {
        border-left-color: var(--success-color);
        background-color: rgba(40, 167, 69, 0.1);
    }
    .alert-info {
        border-left-color: var(--primary-color);
        background-color: rgba(0, 132, 214, 0.1);
    }
    .alert-danger {
        border-left-color: var(--error-color);
        background-color: rgba(220, 53, 69, 0.1);
    }

    .background-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.03;
        background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%230084d6' fill-opacity='1' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .forgot-password-container {
            flex-direction: column;
        }
        .forgot-password-left, .forgot-password-right {
            flex: none;
            width: 100%;
        }
        .forgot-password-left {
            height: 250px;
        }
        .forgot-password-left-content h1 {
            font-size: 1.8rem;
        }
    }
</style>
</head>
<body>

<!-- Navbar/Header -->
<nav class="navbar navbar-custom">
    <a class="navbar-brand" href="{{ route('landing_page') }}">
        <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo">
    </a>
</nav>

<div class="forgot-password-container">
    <!-- Left Side Background -->
    <div class="forgot-password-left">
        <div class="forgot-password-left-content">
            <h1>Reset Your Password</h1>
            <p>"Cast all your anxiety on him because he cares for you."</p>
            <div style="font-style: italic; margin-top: 20px; font-size: 1rem; opacity: 0.9;">1 Peter 5:7</div>
        </div>
    </div>

    <!-- Right Side Form -->
    <div class="forgot-password-right">
        <div class="background-pattern"></div>
        <form class="forgot-password-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo">
            <h2>Forgot Password</h2>
            <p>Enter your email or member ID and we'll send you a password reset link.</p>

            <!-- Display status message -->
            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Display general error message (e.g., page expired / CSRF) -->
            @if(session('error'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Display validation errors -->
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                        <p class="mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $error }}</p>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Show reset link if SMS failed -->
            @if(session('reset_token'))
                <div class="alert alert-info">
                    <strong><i class="fa-solid fa-info-circle me-2"></i>Password Reset Link:</strong><br>
                    @if(session('reset_url'))
                        <p class="mb-2">Click the link below to reset your password:</p>
                        <a href="{{ session('reset_url') }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fa-solid fa-key me-1"></i> Reset Password
                        </a>
                        <p class="mt-2 mb-0"><small>Or copy this URL: <code style="font-size: 0.8rem; word-break: break-all;">{{ session('reset_url') }}</code></small></p>
                    @else
                        <p class="mb-2">Reset token: <code>{{ session('reset_token') }}</code></p>
                        <a href="{{ route('password.reset', ['token' => session('reset_token')]) }}" class="btn btn-sm btn-primary" target="_blank">
                            <i class="fa-solid fa-key me-1"></i> Reset Password
                        </a>
                    @endif
                    @if(session('sms_error'))
                        <p class="mt-2 mb-0"><small><strong>Note:</strong> {{ session('sms_error') }}</small></p>
                    @endif
                </div>
            @endif

            <div class="form-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="text" 
                       class="form-control @error('email') is-invalid @enderror" 
                       id="email" 
                       name="email" 
                       placeholder="Email or Member ID" 
                       value="{{ old('email') }}" 
                       required
                       autofocus>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-submit">
                <i class="fa-solid fa-paper-plane me-2"></i>
                Send Reset Link
            </button>

            <div class="back-to-login">
                <a href="{{ route('login') }}">
                    <i class="fa-solid fa-arrow-left me-1"></i>
                    Back to Login
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss alerts after 7 seconds
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 7000);
        });
    });
</script>
</body>
</html>

