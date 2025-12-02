<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Waumini Link - Verify OTP</title>

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
        --dark-overlay: rgba(0,0,0,0.5);
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

    .otp-container {
        display: flex;
        min-height: 100vh;
        padding-top: 70px; /* to avoid overlap with navbar */
    }

    /* Left side background */
    .otp-left {
        flex: 1;
        background: url('{{ asset("assets/images/church.jpg") }}') no-repeat center center;
        background-size: cover;
        position: relative;
        color: #fff;
        transition: all 0.5s ease;
    }
    .otp-left::after {
        content: "";
        position: absolute;
        top:0; left:0;
        width:100%; height:100%;
        background: linear-gradient(135deg, rgba(0, 132, 214, 0.3), rgba(0,0,0,0.7));
        transition: background-color 0.5s ease;
    }
    .otp-left:hover::after {
        background: linear-gradient(135deg, rgba(0, 132, 214, 0.4), rgba(0,0,0,0.6));
    }
    .otp-left-content {
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
    .otp-left-content h1 {
        font-size: 2.5rem;
        margin-bottom: 15px;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
    }
    .otp-left-content p {
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
    .otp-right {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        padding: 40px;
        background-color: #fff;
        box-shadow: -5px 0 15px rgba(0,0,0,0.05);
        position: relative;
    }
    
    .otp-form-container {
        width: 100%;
        max-width: 400px;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .otp-form {
        width: 100%;
        animation: fadeIn 0.8s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .otp-form .logo {
        display: none !important;
        margin: 0 auto 20px;
        height: 70px;
        transition: all 0.3s ease;
    }

    .otp-form h2 {
        font-weight: bold;
        margin-bottom: 30px;
        text-align: center;
        color: var(--primary-color);
        position: relative;
    }
    .otp-form h2::after {
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

    .otp-form p {
        text-align: center;
        color: #666;
        margin-bottom: 30px;
        font-size: 0.95rem;
    }

    .otp-input-group {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-bottom: 25px;
    }

    .otp-input {
        width: 50px;
        height: 60px;
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s ease;
        background-color: #fafbfc;
    }

    .otp-input:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 3px rgba(37, 215, 27, 0.1);
        background-color: #ffffff;
        outline: none;
    }

    .otp-input.error {
        border-color: var(--error-color);
        animation: shake 0.5s;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    .countdown {
        text-align: center;
        color: #666;
        font-size: 0.9rem;
        margin-top: 10px;
        margin-bottom: 25px;
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
        margin: 0;
        position: relative;
        overflow: hidden;
        letter-spacing: 0.5px;
    }
    .btn-submit::before {
        content: "";
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: all 0.5s ease;
    }
    .btn-submit:hover {
        background: linear-gradient(135deg, var(--secondary-color), #1fb115);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(37, 215, 27, 0.4);
    }
    .btn-submit:hover::before {
        left: 100%;
    }
    .btn-submit:active {
        transform: translateY(0);
    }
    .btn-submit:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .resend-form {
        width: 100%;
        margin: 12px 0 0 0;
        padding: 0;
        display: flex;
        flex-direction: column;
    }

    .btn-resend {
        width: 100%;
        padding: 12px 16px;
        background: rgba(0, 132, 214, 0.1);
        color: var(--primary-color);
        font-weight: 500;
        font-size: 14px;
        border-radius: 8px;
        border: 1px solid rgba(0, 132, 214, 0.3);
        transition: all 0.3s ease;
        margin: 0;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-resend:hover:not(:disabled) {
        background: var(--primary-color);
        color: #fff;
        border-color: var(--primary-color);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 132, 214, 0.3);
    }
    .btn-resend:disabled {
        opacity: 0.8;
        cursor: not-allowed;
        background: rgba(0, 132, 214, 0.08);
        color: var(--primary-color);
        border-color: rgba(0, 132, 214, 0.2);
    }
    .btn-resend i {
        font-size: 14px;
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
        border-left-color: var(--secondary-color);
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
    .alert-dismissible .btn-close {
        padding: 0.75rem;
    }

    /* Background pattern */
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
        .otp-container {
            flex-direction: column;
        }
        .otp-left, .otp-right {
            flex: none;
            width: 100%;
        }
        .otp-left {
            height: 250px;
        }
        .otp-left-content h1 {
            font-size: 1.8rem;
        }
        .otp-left-content p {
            font-size: 1rem;
        }
        .otp-right {
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
        .otp-form .logo {
            display: none; /* Hide logo in form on mobile */
        }
        .otp-form h2 {
            font-size: 1.8rem;
        }
        .otp-input {
            width: 45px;
            height: 55px;
            font-size: 20px;
        }
        .otp-input-group {
            gap: 8px;
            margin-bottom: 20px;
        }
    }

    @media (max-width: 480px) {
        .otp-left-content h1 {
            font-size: 1.5rem;
        }
        .otp-left-content p {
            font-size: 0.9rem;
        }
        .otp-form h2 {
            font-size: 1.5rem;
        }
        .otp-input {
            width: 42px;
            height: 50px;
            font-size: 18px;
        }
        .otp-input-group {
            gap: 6px;
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
    <a class="navbar-brand" href="{{ route('login') }}">
        <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo">
    </a>
</nav>

<div class="otp-container">
    <!-- Left Side Background with Welcome Text -->
    <div class="otp-left">
        <div class="otp-left-content">
            <h1>Verify Your Identity</h1>
            <p>"Trust in the Lord with all your heart and lean not on your own understanding."</p>
            <div class="scripture-reference">Proverbs 3:5</div>
        </div>
    </div>

    <!-- Right Side Form -->
    <div class="otp-right">
        <div class="background-pattern"></div>
        <div class="otp-form-container">
            <form class="otp-form" id="otpForm" method="POST" action="{{ route('login.otp.verify.post') }}">
                @csrf
                <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo">
                <h2>Enter OTP</h2>
                <p>We've sent a 6-digit code to your phone number. Please enter it below to complete your login.</p>

                <!-- Display validation errors -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        @foreach($errors->all() as $error)
                            <p class="mb-1"><i class="fa-solid fa-circle-exclamation me-1"></i> {{ $error }}</p>
                        @endforeach
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="otp-input-group">
                    <input type="text" class="otp-input" id="otp1" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" id="otp2" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" id="otp3" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" id="otp4" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" id="otp5" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                    <input type="text" class="otp-input" id="otp6" maxlength="1" pattern="[0-9]" inputmode="numeric" autocomplete="off">
                </div>
                <input type="hidden" name="otp" id="otpValue">

                <div class="countdown" id="countdown">
                    <i class="fa-solid fa-clock me-1"></i>
                    <span id="countdownText">OTP expires in <span id="timer">5:00</span></span>
                </div>

                <button type="submit" class="btn btn-submit" id="submitBtn" disabled>
                    <i class="fa-solid fa-check me-2"></i>
                    Verify OTP
                </button>
            </form>

            <!-- Resend OTP Button - Positioned directly below Verify OTP button -->
            <form method="POST" action="{{ route('login.otp.resend') }}" id="resendForm" class="resend-form">
                @csrf
                <button type="submit" class="btn btn-resend" id="resendBtn">
                    <i class="fa-solid fa-redo me-2"></i>
                    Resend OTP
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Navbar scroll effect
        const navbar = document.querySelector('.navbar-custom');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Auto-dismiss alerts
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }, 5000);
        });

        const otpInputs = document.querySelectorAll('.otp-input');
        const otpValueInput = document.getElementById('otpValue');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('otpForm');
        const resendBtn = document.getElementById('resendBtn');
        
        // Auto-focus first input
        otpInputs[0].focus();
        
        // Handle input
        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function(e) {
                const value = e.target.value.replace(/[^0-9]/g, '');
                e.target.value = value;
                
                // Update hidden input
                updateOtpValue();
                
                // Move to next input if value entered
                if (value && index < otpInputs.length - 1) {
                    otpInputs[index + 1].focus();
                }
            });
            
            input.addEventListener('keydown', function(e) {
                // Handle backspace
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
                
                // Handle paste
                if (e.key === 'v' && (e.ctrlKey || e.metaKey)) {
                    e.preventDefault();
                    navigator.clipboard.readText().then(text => {
                        const digits = text.replace(/[^0-9]/g, '').slice(0, 6);
                        digits.split('').forEach((digit, i) => {
                            if (otpInputs[i]) {
                                otpInputs[i].value = digit;
                            }
                        });
                        updateOtpValue();
                        if (digits.length === 6) {
                            submitBtn.focus();
                        } else {
                            otpInputs[digits.length].focus();
                        }
                    });
                }
            });
            
            input.addEventListener('paste', function(e) {
                e.preventDefault();
                const text = (e.clipboardData || window.clipboardData).getData('text');
                const digits = text.replace(/[^0-9]/g, '').slice(0, 6);
                digits.split('').forEach((digit, i) => {
                    if (otpInputs[i]) {
                        otpInputs[i].value = digit;
                    }
                });
                updateOtpValue();
                if (digits.length === 6) {
                    submitBtn.focus();
                } else {
                    otpInputs[digits.length].focus();
                }
            });
        });
        
        function updateOtpValue() {
            const otp = Array.from(otpInputs).map(input => input.value).join('');
            otpValueInput.value = otp;
            
            // Enable/disable submit button
            submitBtn.disabled = otp.length !== 6;
            
            // Remove error class
            otpInputs.forEach(input => input.classList.remove('error'));
        }
        
        // Form submission
        form.addEventListener('submit', function(e) {
            const otp = otpValueInput.value;
            if (otp.length !== 6) {
                e.preventDefault();
                otpInputs.forEach(input => input.classList.add('error'));
                return false;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Verifying...';
        });
        
        // Countdown timer
        @if(isset($otp_expires_at))
            const expiresAt = new Date('{{ $otp_expires_at->toIso8601String() }}').getTime();
            const countdownElement = document.getElementById('timer');
            const countdownText = document.getElementById('countdownText');
            let isOtpExpired = false;
            
            // Initially disable resend button if timer is active
            resendBtn.disabled = true;
            
            function updateCountdown() {
                const now = new Date().getTime();
                const distance = expiresAt - now;
                
                if (distance < 0) {
                    // Timer expired - enable resend button
                    isOtpExpired = true;
                    countdownElement.textContent = 'Expired';
                    countdownText.innerHTML = '<span style="color: var(--error-color); font-weight: 600;">Code has expired. Please request a new one.</span>';
                    submitBtn.disabled = true;
                    
                    // Enable resend button
                    resendBtn.disabled = false;
                    resendBtn.innerHTML = '<i class="fa-solid fa-paper-plane me-2"></i>Resend Code';
                    return;
                }
                
                // Timer still active - keep resend button disabled
                isOtpExpired = false;
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                countdownElement.textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
                
                // Keep resend button disabled while timer is active
                resendBtn.disabled = true;
                resendBtn.innerHTML = '<i class="fa-solid fa-clock me-2"></i>Wait for timer to expire';
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        @else
            // If no expiration time set, allow resend immediately
            let isOtpExpired = true;
        @endif
        
        // Resend OTP - only allow if timer has expired
        document.getElementById('resendForm').addEventListener('submit', function(e) {
            // Check if OTP timer is still active
            @if(isset($otp_expires_at))
                if (!isOtpExpired) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'info',
                        title: 'Please Wait',
                        text: 'You can only resend the code after the current timer expires. Please wait for the countdown to finish.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#0084d6',
                        customClass: {
                            popup: 'rounded-lg'
                        }
                    });
                    return false;
                }
            @endif
            
            // Allow resend if timer expired
            resendBtn.disabled = true;
            resendBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Sending...';
        });
        
        // Show SweetAlert for success messages
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: {!! json_encode(session('success')) !!},
                timer: 2500, // 2.5 seconds
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
        
        // Show SweetAlert for info messages (OTP sent notification)
        @if(session('info'))
            Swal.fire({
                icon: 'info',
                title: 'OTP Sent',
                text: {!! json_encode(session('info')) !!},
                timer: 180000, // 3 minutes (180000ms)
                timerProgressBar: true,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });
        @endif
    });
</script>
</body>
</html>

