<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Waumini Link - Login</title>

<!-- Bootstrap CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
    :root {
        --primary-color: #940000;
        --secondary-color: #b30000;
        --accent-color: #f8f9fa;
        --text-color: #333;
        --dark-overlay: rgba(0,0,0,0.5);
        --error-color: #dc3545;
        --success-color: #28a745;
    }
    
    body, html {
        height: 100%;
        margin: 0;
        font-family: "Century Gothic", "CenturyGothic", "AppleGothic", Arial, sans-serif;
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
        transition: all 0.3s ease;
    }
    .navbar-custom.scrolled {
        background: rgba(0,0,0,0.9);
        padding: 8px 30px;
    }
    .navbar-custom .navbar-brand img {
        height: 50px;
        transition: height 0.3s ease;
    }
    .navbar-custom.scrolled .navbar-brand img {
        height: 40px;
    }

    .login-container {
        display: flex;
        min-height: 100vh;
        padding-top: 70px; /* to avoid overlap with navbar */
    }

    /* Left side background */
    .login-left {
        flex: 1;
        background: url('{{ asset("assets/images/church.jpg") }}') no-repeat center center;
        background-size: cover;
        position: relative;
        color: #fff;
        transition: all 0.5s ease;
    }
    .login-left::after {
        content: "";
        position: absolute;
        top:0; left:0;
        width:100%; height:100%;
        background: linear-gradient(135deg, rgba(148, 0, 0, 0.3), rgba(0,0,0,0.7));
        transition: background-color 0.5s ease;
    }
    .login-left:hover::after {
        background: linear-gradient(135deg, rgba(148, 0, 0, 0.4), rgba(0,0,0,0.6));
    }
    .login-left-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s ease;
    }
    .login-left-content h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    .login-left-content p {
        font-size: 1.2rem;
        line-height: 1.5;
        max-width: 80%;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }
    .scripture-reference {
        font-style: italic;
        margin-top: 20px;
        font-size: 1rem;
        opacity: 0.9;
    }

    /* Right side form */
    .login-right {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 40px;
        background-color: #fff;
        box-shadow: -5px 0 15px rgba(0,0,0,0.05);
        position: relative;
    }

    .login-form {
        width: 100%;
        max-width: 400px;
        animation: fadeIn 0.8s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .login-form .logo {
        display: block;
        margin: 0 auto 20px;
        height: 70px;
        transition: all 0.3s ease;
    }
    .login-form .logo:hover {
        transform: scale(1.05) rotate(2deg);
    }

    .login-form h2 {
        font-weight: bold;
        margin-bottom: 30px;
        text-align: center;
        color: var(--primary-color);
        position: relative;
    }
    .login-form h2::after {
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
        transition: all 0.3s ease;
        z-index: 2;
        font-size: 16px;
        width: 16px;
        height: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .form-control {
        padding-left: 48px;
        padding-right: 48px;
        height: 48px;
        border-radius: 10px;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
        position: relative;
        font-size: 15px;
        background-color: #fafbfc;
    }
    .form-control:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(37, 215, 27, 0.1);
        background-color: #ffffff;
        outline: none;
    }
    .form-control:focus + i {
        color: var(--secondary-color);
        transform: translateY(-50%) scale(1.1);
    }
    .form-control:hover {
        border-color: #ced4da;
        background-color: #ffffff;
    }
    .form-control:hover + i {
        color: var(--primary-color);
    }

    .password-toggle {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        transition: all 0.3s ease;
        z-index: 2;
        background: transparent;
        border: none;
        outline: none;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        font-size: 14px;
    }
    .password-toggle:hover {
        color: var(--primary-color);
        background-color: rgba(148, 0, 0, 0.1);
        transform: translateY(-50%) scale(1.1);
    }
    .password-toggle:active {
        transform: translateY(-50%) scale(0.95);
    }

    .btn-login {
        width: 100%;
        padding: 14px;
        background: linear-gradient(135deg, var(--primary-color), #7a0000);
        color: #fff;
        font-weight: 600;
        font-size: 16px;
        border-radius: 10px;
        border: none;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(148, 0, 0, 0.2);
        letter-spacing: 0.5px;
    }
    .btn-login::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: all 0.5s ease;
    }
    .btn-login:hover {
        background: linear-gradient(135deg, var(--secondary-color), #1fb115);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(179, 0, 0, 0.4);
    }
    .btn-login:hover::before {
        left: 100%;
    }
    .btn-login:active {
        transform: translateY(0);
    }

    .forgot-password {
        color: #dc3545;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        position: relative;
        display: inline-block;
    }
    .forgot-password:hover {
        color: #c82333;
        text-decoration: none;
        transform: translateY(-1px);
    }
    .forgot-password::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -2px;
        left: 0;
        background-color: #dc3545;
        transition: width 0.3s ease;
    }
    .forgot-password:hover::after {
        width: 100%;
    }

    .register-link {
        margin-top: 20px;
        text-align: center;
        font-size: 0.9rem;
        color: #666;
    }
    .register-link a {
        color: var(--primary-color);
        font-weight: 600;
        transition: color 0.3s ease;
        text-decoration: none;
        position: relative;
    }
    .register-link a::after {
        content: '';
        position: absolute;
        width: 0;
        height: 2px;
        bottom: -2px;
        left: 0;
        background-color: var(--secondary-color);
        transition: width 0.3s ease;
    }
    .register-link a:hover {
        color: var(--secondary-color);
    }
    .register-link a:hover::after {
        width: 100%;
    }

    /* Form validation styles */
    .is-invalid {
        border-color: var(--error-color) !important;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' stroke='%23dc3545' viewBox='0 0 12 12'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .is-valid {
        border-color: var(--success-color) !important;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' width='8' height='8' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    .invalid-feedback, .valid-feedback {
        display: none;
        font-size: 0.85rem;
        margin-top: 5px;
    }
    .invalid-feedback {
        color: var(--error-color);
    }
    .valid-feedback {
        color: var(--success-color);
    }
    .is-invalid ~ .invalid-feedback,
    .is-valid ~ .valid-feedback {
        display: block;
    }

    /* Password strength meter */
    .password-strength {
        height: 5px;
        margin-top: 8px;
        border-radius: 3px;
        background: #eee;
        overflow: hidden;
    }
    .password-strength-bar {
        height: 100%;
        width: 0;
        border-radius: 3px;
        transition: width 0.3s ease, background 0.3s ease;
    }

    /* Loading spinner */
    .btn-loading {
        position: relative;
        color: transparent !important;
        pointer-events: none;
        overflow: hidden;
    }
    
    .btn-loading .btn-text {
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .btn-loading::after {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        width: 26px;
        height: 26px;
        margin: -13px 0 0 -13px;
        border: 3px solid rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        border-top-color: #ffffff;
        border-right-color: #ffffff;
        animation: spinner-rotate 0.8s linear infinite;
        z-index: 10;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.3);
    }
    
    .btn-loading::before {
        content: 'Authenticating, please wait...';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
        z-index: 5;
        text-shadow: 0 1px 3px rgba(0,0,0,0.4);
        letter-spacing: 0.3px;
        opacity: 0.95;
    }
    
    @keyframes spinner-rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Footer */
    footer {
        background-color: #111;
        color: #fff;
        padding: 20px;
    }
    footer .footer-text,
    footer .footer-text *,
    footer .emca-powered,
    footer .emca-powered * {
        color: #940000 !important;
    }
    .footer-bar {
        height: 5px;
        background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
        width: 100%;
        margin-bottom: 15px;
    }
    .footer-content {
        max-width: 1200px;
        margin: auto;
    }
    .footer-content .d-flex {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }
    .footer-text {
        font-size: 0.9rem;
        color: #940000 !important;
    }
    footer .footer-text {
        color: #940000 !important;
    }
    footer .footer-text,
    footer .footer-text *,
    footer .emca-powered,
    footer .emca-powered *,
    .footer-text.emca-powered,
    .footer-text.emca-powered * {
        color: #940000 !important;
    }
    .footer-logo img {
        display: block;
        height: 30px;
        opacity: 0.8;
        transition: opacity 0.3s ease;
    }
    .footer-logo img:hover {
        opacity: 1;
    }

    /* Toast notification */
    .toast-container {
        position: fixed;
        top: 90px;
        right: 20px;
        z-index: 9999;
    }
    .toast {
        background-color: rgba(255,255,255,0.95);
        border-left: 4px solid;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-radius: 6px;
    }
    .toast-success {
        border-left-color: var(--secondary-color);
    }
    .toast-error {
        border-left-color: var(--error-color);
    }

    /* Background pattern */
    .background-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        opacity: 0.03;
        background-image: url("data:image/svg+xml,%3Csvg width='20' height='20' viewBox='0 0 20 20' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23940000' fill-opacity='1' fill-rule='evenodd'%3E%3Ccircle cx='3' cy='3' r='3'/%3E%3Ccircle cx='13' cy='13' r='3'/%3E%3C/g%3E%3C/svg%3E");
        pointer-events: none;
    }

    /* Alert improvements */
    .alert {
        border: none;
        border-left: 4px solid;
        border-radius: 6px;
        padding: 12px 15px;
    }
    .alert-success {
        border-left-color: var(--secondary-color);
        background-color: rgba(40, 167, 69, 0.1);
    }
    .alert-info {
        border-left-color: var(--primary-color);
        background-color: rgba(148, 0, 0, 0.05);
    }
    .alert-danger {
        border-left-color: var(--error-color);
        background-color: rgba(220, 53, 69, 0.1);
    }
    .alert-dismissible .btn-close {
        padding: 0.75rem;
    }

    /* Responsive */
    @media (max-width: 992px) {
        .login-container {
            flex-direction: column;
        }
        .login-left, .login-right {
            flex: none;
            width: 100%;
        }
        .login-left {
            height: 250px;
        }
        .login-left-content h1 {
            font-size: 1.8rem;
        }
        .login-left-content p {
            font-size: 1rem;
        }
        .login-right {
            padding: 30px 20px;
        }
        .navbar-custom {
            padding: 10px 15px;
        }
        .navbar-custom .navbar-brand img {
            height: 40px;
        }
    }

    @media (max-width: 768px) {
        .login-form .logo {
            display: none; /* Hide logo in form on mobile */
        }
        .login-form h2 {
            font-size: 1.8rem;
        }
        .footer-content {
            text-align: center;
        }
        .footer-logo {
            margin-top: 10px;
        }
        .footer-content .d-flex {
            justify-content: center;
        }
        .footer-text {
            margin-bottom: 10px;
        }
    }

    @media (max-width: 480px) {
        .login-left-content h1 {
            font-size: 1.5rem;
        }
        .login-left-content p {
            font-size: 0.9rem;
        }
        .login-form h2 {
            font-size: 1.5rem;
        }
        .form-group i {
            left: 14px;
        }
        .password-toggle {
            right: 14px;
        }
        .form-control {
            padding-left: 44px;
            padding-right: 44px;
            height: 46px;
        }
        .btn-login {
            padding: 12px;
            font-size: 15px;
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

<!-- Toast container for notifications -->
<div class="toast-container"></div>

<div class="login-container">
    <!-- Left Side Background with Welcome Text -->
    <div class="login-left">
        <div class="login-left-content">
            <h1>{{ autoTranslate('Welcome Back') }}</h1>
            <p>"{{ autoTranslate('For where two or three gather in my name, there am I with them.') }}"</p>
            <div class="scripture-reference">Matthew 18:20</div>
        </div>
    </div>

    <!-- Right Side Form -->
    <div class="login-right">
        <div class="background-pattern"></div>
        <form class="login-form" id="loginForm" method="POST" action="{{ route('login.post') }}">
            @csrf
            <h2>{{ autoTranslate('Login') }}</h2>

            <!-- Display success message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Display info message -->
            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-info me-2"></i>
                    {{ session('info') }}
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

            <!-- Display role error specifically -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="form-group">
                <i class="fa-solid fa-envelope"></i>
                <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email" placeholder="{{ autoTranslate('Email or Member ID') }}" value="{{ old('email') }}" required>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="invalid-feedback">{{ autoTranslate('Please provide your email or member ID.') }}</div>
                    <div class="valid-feedback">{{ autoTranslate('Looks good!') }}</div>
                @enderror
            </div>

            <div class="form-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ autoTranslate('Password') }}" required>
                <button type="button" class="password-toggle" id="passwordToggle">
                    <i class="fa-solid fa-eye"></i>
                </button>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @else
                    <div class="invalid-feedback">{{ autoTranslate('Please enter your password.') }}</div>
                    <div class="valid-feedback">{{ autoTranslate('Looks good!') }}</div>
                @enderror
                <div class="password-strength">
                    <div class="password-strength-bar" id="passwordStrengthBar"></div>
                </div>
            </div>

            <div class="form-group form-check">
                <input type="checkbox" class="form-check-input" id="rememberMe" name="remember">
                <label class="form-check-label" for="rememberMe">{{ autoTranslate('Remember me') }}</label>
            </div>


            <button type="submit" class="btn btn-login mt-3" id="loginButton">
                <span class="btn-text">{{ autoTranslate('Login') }}</span>
            </button>

            <div class="register-link">
               <span>{{ autoTranslate('Forgot Password?') }}</span> <a href="{{ route('password.request') }}" class="forgot-password">{{ autoTranslate('Click here') }}</a>
            </div>
        </form>
    </div>
</div>

<!-- Footer -->
<footer>
    <div class="footer-bar"></div>
    <div class="footer-content">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="footer-text">
                <span style="color: #ffffff !important;">Powered by</span> <a href="https://emca.tech/#" class="text-decoration-none fw-bold" style="color: #940000 !important;">EmCa Technologies</a> &copy; 2025
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-dismiss error messages after 5 seconds
        const errorAlerts = document.querySelectorAll('.alert-danger');
        errorAlerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        // Auto-dismiss other alerts after 7 seconds
        const successAlerts = document.querySelectorAll('.alert-success, .alert-info');
        successAlerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 7000);
        });

        // Navbar scroll effect
        const navbar = document.querySelector('.navbar-custom');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Password visibility toggle
        const passwordToggle = document.getElementById('passwordToggle');
        const passwordInput = document.getElementById('password');
        
        passwordToggle.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle eye icon
            const eyeIcon = this.querySelector('i');
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });

        // Real-time validation
        const emailInput = document.getElementById('email');
        const passwordStrengthBar = document.getElementById('passwordStrengthBar');
        
        // Email validation
        emailInput.addEventListener('input', function() {
            validateEmail();
        });
        
        // Password validation
        passwordInput.addEventListener('input', function() {
            validatePassword();
            updatePasswordStrength(this.value);
        });
        
        // Form validation on submit
        const loginForm = document.getElementById('loginForm');
        const loginButton = document.getElementById('loginButton');
        const btnText = loginButton.querySelector('.btn-text');
        
        loginForm.addEventListener('submit', function(e) {
            // Validate all fields
            const isEmailValid = validateEmail();
            const isPasswordValid = validatePassword();
            
            if (isEmailValid && isPasswordValid) {
                // Show loading state
                loginButton.classList.add('btn-loading');
                
                // Allow the form to submit normally to Laravel
                // The loading state will be handled by the page reload
            } else {
                e.preventDefault();
                showToast('Please check your inputs and try again.', 'error');
            }
        });
        
        // Input validation on change
        const inputs = loginForm.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.classList.contains('is-invalid')) {
                    this.classList.remove('is-invalid');
                }
                if (this.classList.contains('is-valid')) {
                    this.classList.remove('is-valid');
                }
            });
        });
        
        // Email or Member ID validation function
        function validateEmail() {
            const emailValue = emailInput.value.trim();
            
            if (emailValue === '') {
                emailInput.classList.remove('is-valid');
                emailInput.classList.remove('is-invalid');
                return false;
            }
            
            // Accept any non-empty value - server will validate if it's email or member ID
            emailInput.classList.remove('is-invalid');
            emailInput.classList.add('is-valid');
            return true;
        }
        
        // Password validation function
        function validatePassword() {
            const passwordValue = passwordInput.value;
            const isValid = passwordValue.length > 0; // Just check if password is not empty
            
            if (passwordValue === '') {
                passwordInput.classList.remove('is-valid');
                passwordInput.classList.remove('is-invalid');
                return false;
            }
            
            if (isValid) {
                passwordInput.classList.remove('is-invalid');
                passwordInput.classList.add('is-valid');
                return true;
            } else {
                passwordInput.classList.remove('is-valid');
                passwordInput.classList.add('is-invalid');
                return false;
            }
        }
        
        // Email format validation function
        function isValidEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Password strength indicator (optional, doesn't block login)
        function updatePasswordStrength(password) {
            let strength = 0;
            
            if (password.length > 0) strength += 20;
            if (password.length >= 4) strength += 20;
            if (password.length >= 6) strength += 20;
            if (/[A-Z]/.test(password)) strength += 20;
            if (/[0-9]/.test(password)) strength += 20;
            if (/[^A-Za-z0-9]/.test(password)) strength += 20;
            
            passwordStrengthBar.style.width = strength + '%';
            
            // Update color based on strength
            if (strength < 40) {
                passwordStrengthBar.style.background = '#dc3545'; // Red
            } else if (strength < 80) {
                passwordStrengthBar.style.background = '#ffc107'; // Yellow
            } else {
                passwordStrengthBar.style.background = '#28a745'; // Green
            }
        }
        
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `toast toast-${type} align-items-center`;
            toast.setAttribute('role', 'alert');
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;
            
            document.querySelector('.toast-container').appendChild(toast);
            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();
            
            // Remove toast from DOM after it's hidden
            toast.addEventListener('hidden.bs.toast', function() {
                toast.remove();
            });
        }
        
        // Clear authentication flag when on login page
        if (sessionStorage.getItem('isAuthenticated')) {
            sessionStorage.removeItem('isAuthenticated');
        }
        
        // Prevent back navigation to login page after login
        // Clear browser history when page loads
        if (window.history && window.history.pushState) {
            window.history.pushState(null, null, window.location.href);
            window.addEventListener('popstate', function(event) {
                window.history.pushState(null, null, window.location.href);
            });
        }
        
        // Prevent page from being cached
        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                // Page was loaded from cache, reload it
                window.location.reload();
            }
        });
    });
</script>
</body>
</html>