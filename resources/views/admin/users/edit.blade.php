@extends('layouts.index')

@section('content')
<style>
    .form-control:focus, .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .dashboard-header {
            margin-bottom: 15px !important;
        }

        .dashboard-header .card-body {
            padding: 12px 15px !important;
        }

        .dashboard-header .rounded-circle {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 1rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 12px !important;
        }

        .dashboard-header h5 {
            font-size: 1.1rem !important;
            line-height: 1.3 !important;
            margin-bottom: 2px !important;
        }

        .dashboard-header small {
            font-size: 0.8rem !important;
            line-height: 1.2 !important;
            display: block !important;
        }

        .dashboard-header .btn {
            margin-top: 12px !important;
            padding: 8px 16px !important;
            font-size: 0.875rem !important;
            border-radius: 6px !important;
            white-space: nowrap !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            align-items: flex-start !important;
        }

        .dashboard-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .card-body {
            padding: 15px !important;
        }

        .card-header {
            padding: 10px 15px !important;
        }

        .row .col-md-6,
        .row .col-md-4 {
            width: 100%;
            margin-bottom: 15px;
        }

        .btn {
            width: 100%;
            margin-bottom: 10px;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .dashboard-header {
            margin-bottom: 10px !important;
            border-radius: 10px !important;
        }

        .dashboard-header .card-body {
            padding: 10px 12px !important;
        }

        .dashboard-header .rounded-circle {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.9rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 10px !important;
        }

        .dashboard-header h5 {
            font-size: 0.95rem !important;
            line-height: 1.25 !important;
            margin-bottom: 1px !important;
        }

        .dashboard-header small {
            font-size: 0.72rem !important;
            line-height: 1.15 !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            width: auto !important;
            min-width: fit-content !important;
            padding: 7px 12px !important;
            font-size: 0.8rem !important;
            flex: 0 0 auto !important;
        }

        /* Stack on very small screens */
        @media (max-width: 400px) {
            .dashboard-header .d-flex.justify-content-between {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .dashboard-header .btn {
                width: 100% !important;
                margin-top: 8px !important;
            }
        }
    }
</style>

<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:#17082d;">
                <div class="card-body text-white py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-2" style="width:48px; height:48px; background:rgba(255,255,255,.15);">
                                <i class="fas fa-user-edit text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">Edit User</h5>
                                <small style="color: white !important;">Update user account information</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.users') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-user-edit me-2"></i>Edit User Form
            </h6>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">
                            <i class="fas fa-user me-1"></i>Full Name <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $user->name) }}" 
                               required 
                               autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope me-1"></i>Email (Username) <span class="text-danger">*</span>
                        </label>
                        <input type="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               id="email" 
                               name="email" 
                               value="{{ old('email', $user->email) }}" 
                               required>
                        <small class="form-text text-muted">Email will be used as the username for login</small>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">
                            <i class="fas fa-user-tag me-1"></i>Role <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('role') is-invalid @enderror" 
                                id="role" 
                                name="role" 
                                required>
                            <option value="">Select Role</option>
                            @foreach($roles as $key => $label)
                                <option value="{{ $key }}" {{ old('role', $user->role) == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone_number" class="form-label">
                            <i class="fas fa-phone me-1"></i>Phone Number
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">+255</span>
                            @php
                                $phoneValue = old('phone_number', $user->phone_number);
                                // Remove +255 prefix if present for display
                                if ($phoneValue) {
                                    if (str_starts_with($phoneValue, '+255')) {
                                        $phoneValue = substr($phoneValue, 4);
                                    }
                                    // Remove leading zeros
                                    $phoneValue = ltrim($phoneValue, '0');
                                } else {
                                    $phoneValue = '';
                                }
                            @endphp
                            <input type="text" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ $phoneValue }}"
                                   placeholder="712345678"
                                   pattern="[0-9]{9}"
                                   maxlength="9">
                        </div>
                        <small class="form-text text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Enter 9 digits (e.g., 712345678) - +255 will be added automatically.
                            <br><i class="fas fa-exclamation-triangle me-1 text-warning"></i>
                            <strong>Note:</strong> Each user must have a unique phone number.
                        </small>
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="can_approve_finances" 
                                   name="can_approve_finances" 
                                   value="1"
                                   {{ old('can_approve_finances', $user->can_approve_finances) ? 'checked' : '' }}>
                            <label class="form-check-label" for="can_approve_finances">
                                <i class="fas fa-check-circle me-1"></i>Can Approve Finances
                            </label>
                            <small class="form-text text-muted d-block">Allow this user to approve financial transactions</small>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Update User Account
                        </button>
                        <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

