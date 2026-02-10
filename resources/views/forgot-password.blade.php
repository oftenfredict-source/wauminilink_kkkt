<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waumini Link - Forgot Password</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css"
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --primary-color: #940000;
            --secondary-color: #b30000;
            --accent-color: #f8f9fa;
            --text-color: #333;
            --error-color: #dc3545;
            --success-color: #28a745;
            --dark-overlay: rgba(0, 0, 0, 0.5);
        }

        body,
        html {
            height: 100%;
            margin: 0;
            font-family: "Century Gothic", "CenturyGothic", "AppleGothic", Arial, sans-serif;
            background-color: var(--accent-color);
            color: var(--text-color);
        }

        /* Navbar */
        .navbar-custom {
            background: rgba(0, 0, 0, 0.7);
            padding: 10px 30px;
            position: fixed;
            width: 100%;
            z-index: 10;
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
        }

        .navbar-custom.scrolled {
            background: rgba(0, 0, 0, 0.9);
            padding: 8px 30px;
        }

        .navbar-custom .navbar-brand img {
            height: 50px;
            transition: height 0.3s ease;
        }

        .navbar-custom.scrolled .navbar-brand img {
            height: 40px;
        }

        .forgot-password-container {
            display: flex;
            min-height: 100vh;
            padding-top: 70px;
            /* to avoid overlap with navbar */
        }

        /* Left side background */
        .forgot-password-left {
            flex: 1;
            background: url('{{ asset("assets/images/church.jpg") }}') no-repeat center center;
            background-size: cover;
            position: relative;
            color: #fff;
            transition: all 0.5s ease;
        }

        .forgot-password-left::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(148, 0, 0, 0.3), rgba(0, 0, 0, 0.7));
            transition: background-color 0.5s ease;
        }

        .forgot-password-left:hover::after {
            background: linear-gradient(135deg, rgba(148, 0, 0, 0.4), rgba(0, 0, 0, 0.6));
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
            transition: transform 0.3s ease;
        }

        .forgot-password-left-content h1 {
            font-size: 2.5rem;
            margin-bottom: 15px;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .forgot-password-left-content p {
            font-size: 1.2rem;
            line-height: 1.5;
            max-width: 80%;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }

        .scripture-reference {
            font-style: italic;
            margin-top: 20px;
            font-size: 1rem;
            opacity: 0.9;
        }

        /* Right side form */
        .forgot-password-right {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
            background-color: #fff;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.05);
            position: relative;
        }

        .forgot-password-form {
            width: 100%;
            max-width: 400px;
            animation: fadeIn 0.8s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .forgot-password-form .logo {
            display: block;
            margin: 0 auto 20px;
            height: 70px;
            transition: all 0.3s ease;
        }

        .forgot-password-form .logo:hover {
            transform: scale(1.05) rotate(2deg);
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
            padding-right: 16px;
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

        .form-control:focus+i {
            color: var(--secondary-color);
            transform: translateY(-50%) scale(1.1);
        }

        .form-control:hover {
            border-color: #ced4da;
            background-color: #ffffff;
        }

        .form-control:hover+i {
            color: var(--primary-color);
        }

        .btn-submit {
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

        .btn-submit::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
        }

        .btn-submit:hover {
            background: linear-gradient(135deg, var(--secondary-color), #1fb115);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(179, 0, 0, 0.4);
        }

        .btn-submit:hover::before {
            left: 100%;
        }

        .btn-submit:active {
            transform: translateY(0);
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
            position: relative;
        }

        .back-to-login a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: var(--secondary-color);
            transition: width 0.3s ease;
        }

        .back-to-login a:hover {
            color: var(--secondary-color);
        }

        .back-to-login a:hover::after {
            width: 100%;
        }

        /* Alert improvements */
        .alert {
            border: none;
            border-left: 4px solid;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 20px;
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

        .alert-warning {
            border-left-color: #ffc107;
            background-color: rgba(255, 193, 7, 0.1);
        }

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

        /* Responsive */
        @media (max-width: 992px) {
            .forgot-password-container {
                flex-direction: column;
            }

            .forgot-password-left,
            .forgot-password-right {
                flex: none;
                width: 100%;
            }

            .forgot-password-left {
                height: 250px;
            }

            .forgot-password-left-content h1 {
                font-size: 1.8rem;
            }

            .forgot-password-left-content p {
                font-size: 1rem;
            }

            .forgot-password-right {
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
            .forgot-password-form .logo {
                display: none;
            }

            .forgot-password-form h2 {
                font-size: 1.8rem;
            }

            .footer-content {
                text-align: center;
            }

            .footer-content .d-flex {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .forgot-password-left-content h1 {
                font-size: 1.5rem;
            }

            .forgot-password-form h2 {
                font-size: 1.5rem;
            }

            .form-control {
                padding-left: 44px;
                padding-right: 44px;
                height: 46px;
            }

            .btn-submit {
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

    <div class="forgot-password-container">
        <!-- Left Side Background -->
        <div class="forgot-password-left">
            <div class="forgot-password-left-content">
                <h1>Reset Your Password</h1>
                <p>"Cast all your anxiety on him because he cares for you."</p>
                <div class="scripture-reference">1 Peter 5:7</div>
            </div>
        </div>

        <!-- Right Side Form -->
        <div class="forgot-password-right">
            <div class="background-pattern"></div>
            <form class="forgot-password-form" method="POST" action="{{ route('password.email') }}">
                @csrf
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
                            <p class="mt-2 mb-0"><small>Or copy this URL: <code
                                        style="font-size: 0.8rem; word-break: break-all;">{{ session('reset_url') }}</code></small>
                            </p>
                        @else
                            <p class="mb-2">Reset token: <code>{{ session('reset_token') }}</code></p>
                            <a href="{{ route('password.reset', ['token' => session('reset_token')]) }}"
                                class="btn btn-sm btn-primary" target="_blank">
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
                    <input type="text" class="form-control @error('email') is-invalid @enderror" id="email" name="email"
                        placeholder="Email or Member ID" value="{{ old('email') }}" required autofocus>
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

    <!-- Footer -->
    <footer>
        <div class="footer-bar"></div>
        <div class="footer-content">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="footer-text">
                    <span style="color: #ffffff !important;">Powered by</span> <a href="https://emca.tech/#"
                        class="text-decoration-none fw-bold" style="color: #940000 !important;">EmCa Technologies</a>
                    &copy; 2025
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Auto-dismiss alerts after 7 seconds
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 7000);
            });

            // Navbar scroll effect
            const navbar = document.querySelector('.navbar-custom');
            window.addEventListener('scroll', function () {
                if (window.scrollY > 50) {
                    navbar.classList.add('scrolled');
                } else {
                    navbar.classList.remove('scrolled');
                }
            });
        });
    </script>
</body>

</html>