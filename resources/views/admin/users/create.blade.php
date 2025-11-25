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
    
    .account-type-card {
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid #dee2e6;
    }
    
    .account-type-card:hover {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.15);
    }
    
    .account-type-card.active {
        border-color: #667eea;
        background-color: rgba(102, 126, 234, 0.05);
    }
    
    .leader-info {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-top: 10px;
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
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">Register New User</h5>
                                <small style="color: white !important;">Create account for a leader (must be a member first) or administrator</small>
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
                <i class="fas fa-user-plus me-2"></i>User Registration Form
            </h6>
        </div>
        <div class="card-body">
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>Error:</strong> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.users.store') }}" id="userCreateForm" onsubmit="return validateForm(event)">
                @csrf
                
                <!-- Account Type Selection -->
                <div class="row mb-4">
                    <div class="col-12">
                        <label class="form-label mb-3">
                            <i class="fas fa-user-tag me-1"></i>Account Type <span class="text-danger">*</span>
                        </label>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card account-type-card" onclick="selectAccountType('leader')" id="leaderCard">
                                    <div class="card-body text-center">
                                        <input type="radio" name="account_type" value="leader" id="account_type_leader" checked onchange="toggleAccountType()">
                                        <i class="fas fa-user-tie fa-3x text-primary mb-3 d-block"></i>
                                        <h5>Create for Leader</h5>
                                        <p class="text-muted mb-0">Select a member who is appointed as a leader (Pastor, Secretary, Treasurer)</p>
                                    </div>
                                </div>
                            </div>
                            @if($allowAdminCreation)
                            <div class="col-md-6 mb-3">
                                <div class="card account-type-card" onclick="selectAccountType('admin')" id="adminCard">
                                    <div class="card-body text-center">
                                        <input type="radio" name="account_type" value="admin" id="account_type_admin" onchange="toggleAccountType()">
                                        <i class="fas fa-shield-alt fa-3x text-danger mb-3 d-block"></i>
                                        <h5>Create Administrator</h5>
                                        <p class="text-muted mb-0">Create an administrator account (not tied to a member)</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Leader Selection Section -->
                <div id="leaderSection">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="leader_id" class="form-label">
                                <i class="fas fa-users me-1"></i>Select Leader <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('leader_id') is-invalid @enderror" 
                                    id="leader_id" 
                                    name="leader_id"
                                    onchange="loadLeaderInfo()">
                                <option value="">-- Select a Leader --</option>
                                @foreach($leaders as $leader)
                                    <option value="{{ $leader['id'] }}" 
                                            data-member-name="{{ $leader['member_name'] }}"
                                            data-member-email="{{ $leader['member_email'] }}"
                                            data-member-phone="{{ $leader['member_phone'] }}"
                                            data-position="{{ $leader['position'] }}"
                                            data-position-display="{{ $leader['position_display'] }}"
                                            data-role="{{ $leader['role'] }}">
                                        {{ $leader['member_name'] }} - {{ $leader['position_display'] }} ({{ ucfirst($leader['role']) }})
                                    </option>
                                @endforeach
                            </select>
                            @if($leaders->isNotEmpty())
                                <small class="form-text text-muted mt-1">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Showing {{ $leaders->count() }} available leader(s) without user accounts.
                                </small>
                            @endif
                            @error('leader_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            @if($leaders->isEmpty())
                                <div class="alert alert-warning mt-2">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <strong>No available leaders found.</strong> 
                                    <br>To create a user account for a leader:
                                    <ol class="mb-0 mt-2">
                                        <li>First, register the person as a member</li>
                                        <li>Then, appoint them as a leader (Pastor, Secretary, or Treasurer) and make sure the leader is <strong>Active</strong></li>
                                        <li>Come back here to create their user account</li>
                                    </ol>
                                    <hr>
                                    <strong>Common reasons a leader might not appear:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li><strong>The leader is marked as Inactive</strong> - Go to <a href="{{ route('leaders.index') }}" target="_blank">Leadership Management</a> and make sure the leader's status is "Active"</li>
                                        <li><strong>The leader already has a user account</strong> - Check the <a href="{{ route('admin.users') }}" target="_blank">User Management</a> page to see if an account already exists</li>
                                        <li><strong>The member relationship is missing</strong> - Verify the leader is properly linked to a member in the Leadership Management page</li>
                                        <li><strong>Position mismatch</strong> - Only Pastors, Secretaries, and Treasurers (and their assistants) can have user accounts</li>
                                    </ul>
                                    <div class="mt-3">
                                        <a href="{{ route('leaders.index') }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-external-link-alt me-1"></i> Go to Leadership Management
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Leader Info Display -->
                    <div id="leaderInfo" class="leader-info" style="display: none;">
                        <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Leader Information</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <strong>Name:</strong>
                                <p id="leaderName" class="mb-0">-</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Position:</strong>
                                <p id="leaderPosition" class="mb-0">-</p>
                            </div>
                            <div class="col-md-4">
                                <strong>Role:</strong>
                                <p id="leaderRole" class="mb-0">-</p>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-md-6">
                                <strong>Email:</strong>
                                <p id="leaderEmail" class="mb-0">-</p>
                            </div>
                            <div class="col-md-6">
                                <strong>Phone:</strong>
                                <p id="leaderPhone" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>

                    <!-- Email field (always visible and required) -->
                    <div class="row mt-3">
                        <div class="col-md-12 mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email Address <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}"
                                   placeholder="Enter email address"
                                   required
                                   autocomplete="email">
                            <small class="form-text text-muted" id="emailHelpText">
                                <i class="fas fa-info-circle me-1"></i>
                                Email will be used as the username for login. This field is required.
                            </small>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Admin Creation Section -->
                <div id="adminSection" style="display: none;">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="admin_name" class="form-label">
                                <i class="fas fa-user me-1"></i>Full Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="admin_name" 
                                   name="name" 
                                   value="{{ old('name') }}">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="admin_email" class="form-label">
                                <i class="fas fa-envelope me-1"></i>Email (Username) <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="admin_email" 
                                   name="email" 
                                   value="{{ old('email') }}">
                            <small class="form-text text-muted">Email will be used as the username for login</small>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="admin_phone_number" class="form-label">
                                <i class="fas fa-phone me-1"></i>Phone Number
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">+255</span>
                                <input type="text" 
                                       class="form-control @error('phone_number') is-invalid @enderror" 
                                       id="admin_phone_number" 
                                       name="phone_number" 
                                       value="{{ old('phone_number') }}"
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
                </div>

                <!-- Password Info -->
                <div class="row mt-4">
                    <div class="col-md-12 mb-3">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Automatic Password Generation</strong>
                            <p class="mb-0 mt-2">
                                A strong password will be automatically generated for this user account. 
                                The password and username (email) will be sent via SMS to the provided phone number.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Create User Account
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

<script>
function validateForm(event) {
    const accountType = document.querySelector('input[name="account_type"]:checked')?.value;
    
    if (accountType === 'leader') {
        const leaderId = document.getElementById('leader_id').value;
        const emailField = document.getElementById('email');
        const email = emailField ? emailField.value : '';
        
        console.log('Validating leader form:', { leaderId, email, accountType });
        
        if (!leaderId) {
            alert('Please select a leader from the dropdown.');
            event.preventDefault();
            return false;
        }
        
        if (!email || email.trim() === '') {
            alert('Please enter an email address for the user account.');
            if (emailField) {
                emailField.focus();
                emailField.classList.add('is-invalid');
            }
            event.preventDefault();
            return false;
        }
        
        // Ensure email field is enabled and will be submitted
        if (emailField) {
            emailField.disabled = false;
            emailField.removeAttribute('disabled');
        }
    } else if (accountType === 'admin') {
        const name = document.getElementById('admin_name').value;
        const email = document.getElementById('admin_email').value;
        if (!name || !email) {
            alert('Please fill in all required fields for administrator account.');
            event.preventDefault();
            return false;
        }
    }
    
    return true;
}

function selectAccountType(type) {
    document.getElementById('account_type_' + type).checked = true;
    toggleAccountType();
}

function toggleAccountType() {
    const accountType = document.querySelector('input[name="account_type"]:checked').value;
    const leaderSection = document.getElementById('leaderSection');
    const adminSection = document.getElementById('adminSection');
    const leaderCard = document.getElementById('leaderCard');
    const adminCard = document.getElementById('adminCard');
    
    // Get all form fields
    const leaderIdField = document.getElementById('leader_id');
    const emailField = document.getElementById('email'); // Leader email
    const adminNameField = document.getElementById('admin_name');
    const adminEmailField = document.getElementById('admin_email'); // Admin email
    
    if (accountType === 'leader') {
        leaderSection.style.display = 'block';
        adminSection.style.display = 'none';
        leaderCard.classList.add('active');
        if (adminCard) adminCard.classList.remove('active');
        
        // Enable and require leader fields
        if (leaderIdField) {
            leaderIdField.required = true;
            leaderIdField.disabled = false;
        }
        if (emailField) {
            emailField.required = true;
            emailField.disabled = false;
        }
        
        // Disable admin fields so they don't interfere with form submission
        if (adminNameField) {
            adminNameField.required = false;
            adminNameField.disabled = true;
        }
        if (adminEmailField) {
            adminEmailField.required = false;
            adminEmailField.disabled = true;
        }
    } else {
        leaderSection.style.display = 'none';
        adminSection.style.display = 'block';
        if (adminCard) adminCard.classList.add('active');
        leaderCard.classList.remove('active');
        
        // Disable leader fields so they don't interfere with form submission
        if (leaderIdField) {
            leaderIdField.required = false;
            leaderIdField.disabled = true;
        }
        if (emailField) {
            emailField.required = false;
            emailField.disabled = true;
        }
        
        // Enable and require admin fields
        if (adminNameField) {
            adminNameField.required = true;
            adminNameField.disabled = false;
        }
        if (adminEmailField) {
            adminEmailField.required = true;
            adminEmailField.disabled = false;
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleAccountType();
});

function loadLeaderInfo() {
    const select = document.getElementById('leader_id');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('leaderInfo');
    const emailOverrideSection = document.getElementById('emailOverrideSection');
    
    if (select.value) {
        const memberName = selectedOption.getAttribute('data-member-name');
        const memberEmail = selectedOption.getAttribute('data-member-email');
        const memberPhone = selectedOption.getAttribute('data-member-phone');
        const position = selectedOption.getAttribute('data-position-display');
        const role = selectedOption.getAttribute('data-role');
        
        document.getElementById('leaderName').textContent = memberName;
        document.getElementById('leaderPosition').textContent = position;
        document.getElementById('leaderRole').textContent = role.charAt(0).toUpperCase() + role.slice(1);
        document.getElementById('leaderEmail').textContent = memberEmail || 'Not provided';
        document.getElementById('leaderPhone').textContent = memberPhone || 'Not provided';
        
        infoDiv.style.display = 'block';
        
        // Set email field value and help text based on member's email
        const emailField = document.getElementById('email');
        const emailHelpText = document.getElementById('emailHelpText');
        
        if (emailField) {
            if (!memberEmail || memberEmail.trim() === '' || memberEmail === 'null' || memberEmail === 'undefined') {
                // Member doesn't have email - field is required and empty
                emailField.value = '';
                emailField.required = true;
                if (emailHelpText) {
                    emailHelpText.innerHTML = '<i class="fas fa-exclamation-triangle me-1 text-warning"></i><strong>Required:</strong> Member doesn\'t have an email. Please provide one for the user account.';
                }
            } else {
                // Member has email - pre-fill but allow override
                emailField.value = memberEmail;
                emailField.required = true;
                if (emailHelpText) {
                    emailHelpText.innerHTML = '<i class="fas fa-info-circle me-1"></i>Email will be used as the username for login. You can change this if needed.';
                }
            }
        }
    } else {
        infoDiv.style.display = 'none';
        emailOverrideSection.style.display = 'none';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleAccountType();
});
</script>

@endsection
