@extends('layouts.index')

@section('content')
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
                            <input type="password" 
                                   class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" 
                                   name="current_password" 
                                   placeholder="Enter your current password"
                                   required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password" class="form-label fw-bold">
                                <i class="fas fa-key me-2"></i>New Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Enter your new password (minimum 6 characters)"
                                   required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                <small class="text-muted">Password must be at least 6 characters long.</small>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="new_password_confirmation" class="form-label fw-bold">
                                <i class="fas fa-check-circle me-2"></i>Confirm New Password <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control" 
                                   id="new_password_confirmation" 
                                   name="new_password_confirmation" 
                                   placeholder="Confirm your new password"
                                   required>
                            <small class="text-muted">Re-enter your new password to confirm.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            @php
                                $dashboardRoute = 'dashboard';
                                if (auth()->user()->isPastor()) {
                                    $dashboardRoute = 'dashboard.pastor';
                                } elseif (auth()->user()->isTreasurer()) {
                                    $dashboardRoute = 'finance.dashboard';
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
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('new_password_confirmation').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('New password and confirmation do not match!');
        return false;
    }
    
    if (newPassword.length < 6) {
        e.preventDefault();
        alert('New password must be at least 6 characters long!');
        return false;
    }
});
</script>
@endsection

