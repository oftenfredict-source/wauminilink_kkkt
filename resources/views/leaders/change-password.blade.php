@extends('layouts.index')

@section('content')
<style>
    /* Password Toggle Icon Styles */
    .password-toggle-wrapper {
        position: relative;
    }
    
    .password-toggle-wrapper .form-control {
        padding-right: 45px;
    }
    
    .password-toggle-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #6c757d;
        z-index: 10;
        padding: 0.375rem;
        transition: color 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
    }
    
    .password-toggle-icon:hover {
        color: #007bff;
    }
    
    .password-toggle-icon:active {
        color: #0056b3;
    }
    
    .password-toggle-icon i {
        font-size: 1rem;
    }
    
    .password-toggle-icon .fa-eye-slash {
        display: none;
    }
    
    .password-toggle-wrapper input[type="text"] ~ .password-toggle-icon .fa-eye {
        display: none;
    }
    
    .password-toggle-wrapper input[type="text"] ~ .password-toggle-icon .fa-eye-slash {
        display: inline-block;
    }
    
    .password-toggle-wrapper input[type="password"] ~ .password-toggle-icon .fa-eye {
        display: inline-block;
    }
    
    .password-toggle-wrapper input[type="password"] ~ .password-toggle-icon .fa-eye-slash {
        display: none;
    }
    
    /* Password Strength Indicator */
    .password-strength {
        margin-top: 0.5rem;
        height: 4px;
        background-color: #e9ecef;
        border-radius: 2px;
        overflow: hidden;
        display: none;
    }
    
    .password-strength.active {
        display: block;
    }
    
    .password-strength-bar {
        height: 100%;
        width: 0%;
        transition: all 0.3s ease;
        border-radius: 2px;
    }
    
    .password-strength-bar.weak {
        width: 33%;
        background-color: #dc3545;
    }
    
    .password-strength-bar.medium {
        width: 66%;
        background-color: #ffc107;
    }
    
    .password-strength-bar.strong {
        width: 100%;
        background-color: #28a745;
    }
    
    .password-strength-text {
        margin-top: 0.5rem;
        font-size: 0.875rem;
        font-weight: 500;
    }
    
    .password-strength-text.weak {
        color: #dc3545;
    }
    
    .password-strength-text.medium {
        color: #ffc107;
    }
    
    .password-strength-text.strong {
        color: #28a745;
    }
    
    .password-requirements {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }
    
    .password-requirements ul {
        margin-bottom: 0;
        padding-left: 1.25rem;
    }
    
    .password-requirements li {
        margin-bottom: 0.25rem;
    }
    
    .password-requirements li.valid {
        color: #28a745;
    }
    
    .password-requirements li.valid::marker {
        content: "✓ ";
    }
    
    .password-requirements li.invalid {
        color: #6c757d;
    }
    
    /* Mobile Responsive */
    @media (max-width: 767.98px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .d-flex.justify-content-end {
            flex-direction: column;
        }
        
        .d-flex.justify-content-end .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }
</style>

<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2" style="width:48px; height:48px; background:rgba(0,123,255,.1);">
                                <i class="fas fa-key text-primary"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold text-dark">Change Password</h5>
                                <small class="text-muted">Update your account password</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('leader.password.update') }}" id="changePasswordForm">
                        @csrf

                        <div class="mb-4">
                            <label for="current_password" class="form-label fw-bold">
                                <i class="fas fa-lock me-2"></i>Current Password <span class="text-danger">*</span>
                            </label>
                            <div class="password-toggle-wrapper">
                                <input type="password" 
                                       class="form-control @error('current_password') is-invalid @enderror" 
                                       id="current_password" 
                                       name="current_password" 
                                       placeholder="Enter your current password"
                                       required>
                                <span class="password-toggle-icon" onclick="togglePassword('current_password', this)">
                                    <i class="fas fa-eye"></i>
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password" class="form-label fw-bold">
                                <i class="fas fa-key me-2"></i>New Password <span class="text-danger">*</span>
                            </label>
                            <div class="password-toggle-wrapper">
                                <input type="password" 
                                       class="form-control @error('new_password') is-invalid @enderror" 
                                       id="new_password" 
                                       name="new_password" 
                                       placeholder="Enter your new password (minimum 6 characters)"
                                       required
                                       oninput="checkPasswordStrength(this.value)">
                                <span class="password-toggle-icon" onclick="togglePassword('new_password', this)">
                                    <i class="fas fa-eye"></i>
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>
                            
                            <!-- Password Strength Indicator -->
                            <div class="password-strength" id="passwordStrength">
                                <div class="password-strength-bar" id="passwordStrengthBar"></div>
                            </div>
                            <div class="password-strength-text" id="passwordStrengthText"></div>
                            
                            <!-- Password Requirements -->
                            <div class="password-requirements" id="passwordRequirements">
                                <ul>
                                    <li id="req-length" class="invalid">At least 6 characters</li>
                                    <li id="req-uppercase" class="invalid">One uppercase letter</li>
                                    <li id="req-lowercase" class="invalid">One lowercase letter</li>
                                    <li id="req-number" class="invalid">One number</li>
                                    <li id="req-special" class="invalid">One special character (!@#$%^&*)</li>
                                </ul>
                            </div>
                            
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label fw-bold">
                                <i class="fas fa-check-circle me-2"></i>Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <div class="password-toggle-wrapper">
                                <input type="password" 
                                       class="form-control" 
                                       id="new_password_confirmation" 
                                       name="new_password_confirmation" 
                                       placeholder="Confirm your new password"
                                       required
                                       oninput="checkPasswordMatch()">
                                <span class="password-toggle-icon" onclick="togglePassword('new_password_confirmation', this)">
                                    <i class="fas fa-eye"></i>
                                    <i class="fas fa-eye-slash"></i>
                                </span>
                            </div>
                            <small class="text-muted" id="passwordMatchText">Re-enter your new password to confirm.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            @php
                                $dashboardRoute = 'dashboard';
                                if (auth()->user()->isPastor()) {
                                    $dashboardRoute = 'dashboard.pastor';
                                } elseif (auth()->user()->isTreasurer()) {
                                    $dashboardRoute = 'finance.dashboard';
                                } elseif (auth()->user()->isEvangelismLeader()) {
                                    $dashboardRoute = 'evangelism-leader.dashboard';
                                } elseif (auth()->user()->isAdmin()) {
                                    $dashboardRoute = 'admin.dashboard';
                                }
                            @endphp
                            <a href="{{ route($dashboardRoute) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Change Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Tips -->
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-shield-alt me-2 text-primary"></i>Password Security Tips</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>Use at least 6 characters (longer is better)</li>
                        <li>Include a mix of letters, numbers, and special characters</li>
                        <li>Don't use personal information like your name or email</li>
                        <li>Don't share your password with anyone</li>
                        <li>Change your password regularly for better security</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Toggle Password Visibility
    function togglePassword(inputId, iconElement) {
        const input = document.getElementById(inputId);
        
        if (input.type === 'password') {
            input.type = 'text';
        } else {
            input.type = 'password';
        }
        // CSS will handle the icon visibility based on input type
    }
    
    // Check Password Strength
    function checkPasswordStrength(password) {
        const strengthBar = document.getElementById('passwordStrengthBar');
        const strengthText = document.getElementById('passwordStrengthText');
        const strengthIndicator = document.getElementById('passwordStrength');
        
        if (!password) {
            strengthIndicator.classList.remove('active');
            strengthText.textContent = '';
            updatePasswordRequirements('');
            return;
        }
        
        strengthIndicator.classList.add('active');
        
        let strength = 0;
        let strengthLabel = '';
        let strengthClass = '';
        
        // Check requirements
        const hasLength = password.length >= 6;
        const hasUppercase = /[A-Z]/.test(password);
        const hasLowercase = /[a-z]/.test(password);
        const hasNumber = /[0-9]/.test(password);
        const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
        
        // Calculate strength
        if (hasLength) strength++;
        if (hasUppercase) strength++;
        if (hasLowercase) strength++;
        if (hasNumber) strength++;
        if (hasSpecial) strength++;
        
        // Determine strength level
        if (strength <= 2) {
            strengthLabel = 'Weak';
            strengthClass = 'weak';
        } else if (strength <= 3) {
            strengthLabel = 'Medium';
            strengthClass = 'medium';
        } else {
            strengthLabel = 'Strong';
            strengthClass = 'strong';
        }
        
        // Update UI
        strengthBar.className = 'password-strength-bar ' + strengthClass;
        strengthText.className = 'password-strength-text ' + strengthClass;
        strengthText.textContent = 'Password Strength: ' + strengthLabel;
        
        // Update requirements
        updatePasswordRequirements(password);
    }
    
    // Update Password Requirements
    function updatePasswordRequirements(password) {
        const requirements = {
            'req-length': password.length >= 6,
            'req-uppercase': /[A-Z]/.test(password),
            'req-lowercase': /[a-z]/.test(password),
            'req-number': /[0-9]/.test(password),
            'req-special': /[!@#$%^&*(),.?":{}|<>]/.test(password)
        };
        
        Object.keys(requirements).forEach(function(reqId) {
            const reqElement = document.getElementById(reqId);
            if (reqElement) {
                if (requirements[reqId]) {
                    reqElement.classList.remove('invalid');
                    reqElement.classList.add('valid');
                } else {
                    reqElement.classList.remove('valid');
                    reqElement.classList.add('invalid');
                }
            }
        });
    }
    
    // Check Password Match
    function checkPasswordMatch() {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('new_password_confirmation').value;
        const matchText = document.getElementById('passwordMatchText');
        
        if (!confirmPassword) {
            matchText.textContent = 'Re-enter your new password to confirm.';
            matchText.className = 'text-muted';
            return;
        }
        
        if (newPassword === confirmPassword) {
            matchText.textContent = '✓ Passwords match';
            matchText.className = 'text-success';
        } else {
            matchText.textContent = '✗ Passwords do not match';
            matchText.className = 'text-danger';
        }
    }
    
    // Form Validation
    document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
        const newPassword = document.getElementById('new_password').value;
        const confirmPassword = document.getElementById('new_password_confirmation').value;
        
        if (newPassword !== confirmPassword) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Mismatch',
                text: 'New password and confirmation do not match!'
            });
            return false;
        }
        
        if (newPassword.length < 6) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Password Too Short',
                text: 'New password must be at least 6 characters long!'
            });
            return false;
        }
    });
</script>
@endsection

