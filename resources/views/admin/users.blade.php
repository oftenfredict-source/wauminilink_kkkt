@extends('layouts.index')

@section('content')

@if(session('user_created'))
<div id="user-created-data" 
     data-name="{{ htmlspecialchars(session('user_name', ''), ENT_QUOTES, 'UTF-8') }}"
     data-email="{{ htmlspecialchars(session('user_email', ''), ENT_QUOTES, 'UTF-8') }}"
     data-password="{{ htmlspecialchars(session('user_password', ''), ENT_QUOTES, 'UTF-8') }}"
     data-role="{{ htmlspecialchars(session('user_role', ''), ENT_QUOTES, 'UTF-8') }}"
     data-sms-sent="{{ session('sms_sent') ? 'true' : 'false' }}"
     data-sms-error="{{ htmlspecialchars(session('sms_error', ''), ENT_QUOTES, 'UTF-8') }}"
     data-sms-reason="{{ htmlspecialchars(session('sms_reason', ''), ENT_QUOTES, 'UTF-8') }}"
     data-phone="{{ htmlspecialchars(session('phone_number', ''), ENT_QUOTES, 'UTF-8') }}"
     style="display: none;"></div>
@endif
<style>
    /* Ensure badge text is always visible with proper colors - works with Bootstrap 4 and 5 */
    .badge.badge-danger,
    .badge[class*="badge-danger"] {
        background-color: #dc3545 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-success,
    .badge[class*="badge-success"] {
        background-color: #198754 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-info,
    .badge[class*="badge-info"] {
        background-color: #0dcaf0 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-secondary,
    .badge[class*="badge-secondary"] {
        background-color: #6c757d !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-warning,
    .badge[class*="badge-warning"] {
        background-color: #ffc107 !important;
        color: #212529 !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    /* Fallback for any badge */
    .badge {
        display: inline-block !important;
        padding: 0.35em 0.65em !important;
        font-weight: 600 !important;
        border-radius: 0.25rem !important;
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
                                <i class="fas fa-users text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">User Management</h5>
                                <small style="color: white !important;">Manage leaders and administrators (Pastors, Secretaries, Treasurers, Admins)</small>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-user-plus"></i> Create New User
                            </a>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 font-weight-bold text-primary">Leaders & Administrators ({{ $users->count() }} total)</h6>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Only users with leadership roles or administrator access are shown here. 
                    Regular members are managed in the <a href="{{ route('members.view') }}" class="text-primary">Member Management</a> page.
                </small>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Can Approve Finances</th>
                            <th>Activities</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if($user->is_login_blocked)
                                <br><span class="badge badge-danger" style="font-size: 0.7em; margin-top: 2px;">
                                    <i class="fas fa-ban"></i> Login Blocked ({{ $user->remaining_block_time }} min)
                                </span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @php
                                    $roleBadgeClass = match($user->role) {
                                        'admin' => 'badge-danger',
                                        'pastor' => 'badge-warning',
                                        'secretary' => 'badge-info',
                                        'treasurer' => 'badge-secondary',
                                        default => 'badge-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $roleBadgeClass }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                @if($user->can_approve_finances)
                                    <span class="badge badge-success">Yes</span>
                                @else
                                    <span class="badge badge-secondary">No</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.user-activity', $user->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-list"></i> {{ $user->activity_logs_count }} Activities
                                </a>
                            </td>
                            <td>
                                <div class="d-flex gap-1 flex-wrap">
                                    <a href="{{ route('admin.user-activity', $user->id) }}" class="btn btn-sm btn-primary" title="View Activity">
                                        <i class="fas fa-eye"></i> View Activity
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-info" title="Edit User">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button class="btn btn-sm btn-success" onclick="resetPassword({{ $user->id }})" title="Reset Password">
                                        <i class="fas fa-key"></i> Reset Password
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->name) }}')" title="Delete User">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                    @if($user->is_login_blocked)
                                    <form action="{{ route('admin.users.unblock', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Unblock this user from logging in?');">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" class="btn btn-sm btn-warning" title="Unblock User">
                                            <i class="fas fa-unlock"></i> Unblock
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No users found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if(session('user_created'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Small delay to ensure DOM is fully loaded
    setTimeout(function() {
        // Get data from hidden div element (more reliable than session in blade)
        const dataDiv = document.getElementById('user-created-data');
        
        if (!dataDiv) {
            console.error('User created data div not found');
            // Fallback: try to show basic success message
            Swal.fire({
                icon: 'success',
                title: 'User Created!',
                text: 'User account has been created successfully. Please check the users list.',
                confirmButtonColor: '#667eea'
            });
            return;
        }
        
        const userName = dataDiv.getAttribute('data-name') || '';
        const userEmail = dataDiv.getAttribute('data-email') || '';
        const userPassword = dataDiv.getAttribute('data-password') || '';
        const userRole = dataDiv.getAttribute('data-role') || '';
        const smsSent = dataDiv.getAttribute('data-sms-sent') === 'true';
        const smsError = dataDiv.getAttribute('data-sms-error') || '';
        const smsReason = dataDiv.getAttribute('data-sms-reason') || '';
        const phoneNumber = dataDiv.getAttribute('data-phone') || '';
        
        console.log('User created data loaded:', {
            userName: userName,
            userEmail: userEmail,
            hasPassword: !!userPassword,
            userRole: userRole,
            smsSent: smsSent,
            phoneNumber: phoneNumber
        });
        
        // Check if we have required data
        if (!userName || !userEmail || !userPassword) {
            console.error('Missing user data:', { userName, userEmail, hasPassword: !!userPassword });
            Swal.fire({
                icon: 'warning',
                title: 'User Created',
                text: 'User account created but credentials could not be displayed. Please check the users list.',
                confirmButtonColor: '#667eea'
            });
            return;
        }
    
    // Create HTML content for the popup
    let htmlContent = `
        <div style="text-align: left;">
            <p style="margin-bottom: 15px;"><strong>User Account Created Successfully!</strong></p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                <p style="margin: 5px 0;"><strong>Name:</strong> ${userName}</p>
                <p style="margin: 5px 0;"><strong>Role:</strong> ${userRole.charAt(0).toUpperCase() + userRole.slice(1)}</p>
                <p style="margin: 5px 0;"><strong>Username (Email):</strong></p>
                <div style="background: white; padding: 8px; border-radius: 4px; margin: 5px 0; font-family: monospace; word-break: break-all;">
                    ${userEmail}
                </div>
                <p style="margin: 10px 0 5px 0;"><strong>Password:</strong></p>
                <div style="background: white; padding: 8px; border-radius: 4px; margin: 5px 0; font-family: monospace; font-size: 14px; position: relative;">
                    <span id="password-display">${userPassword}</span>
                    <button type="button" id="copy-password-btn" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #667eea; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                        <i class="fas fa-copy"></i> Copy
                    </button>
                </div>
            </div>
    `;
    
    if (smsSent) {
        htmlContent += `
            <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                <i class="fas fa-check-circle"></i> <strong>SMS Sent Successfully!</strong><br>
                Credentials have been sent to ${phoneNumber}
            </div>
        `;
    } else if (phoneNumber) {
        let errorMessage = 'Could not send SMS. Please provide credentials manually.';
        if (smsError) {
            errorMessage = smsError;
            if (smsReason === 'disabled') {
                errorMessage += '<br><small>Go to Settings → Notifications to enable SMS.</small>';
            } else if (smsReason === 'config_missing') {
                errorMessage += '<br><small>Go to Settings → Notifications to configure SMS credentials.</small>';
            }
        }
        htmlContent += `
            <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                <i class="fas fa-exclamation-triangle"></i> <strong>SMS Not Sent</strong><br>
                ${errorMessage}
            </div>
        `;
    } else {
        htmlContent += `
            <div style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                <i class="fas fa-exclamation-triangle"></i> <strong>No Phone Number</strong><br>
                Please provide credentials manually to the user.
            </div>
        `;
    }
    
    htmlContent += `
            <p style="margin-top: 15px; font-size: 12px; color: #6c757d;">
                <i class="fas fa-info-circle"></i> Please save these credentials securely. The user should change their password after first login.
            </p>
        </div>
    `;
    
    // Show the popup immediately
    Swal.fire({
        title: 'User Account Created!',
        html: htmlContent,
        icon: 'success',
        width: '600px',
        confirmButtonText: 'Got it!',
        confirmButtonColor: '#667eea',
        allowOutsideClick: false,
        showCloseButton: true,
        didOpen: () => {
            // Add copy functionality
            const copyBtn = document.getElementById('copy-password-btn');
            if (copyBtn) {
                copyBtn.addEventListener('click', function() {
                    navigator.clipboard.writeText(userPassword).then(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Copied!',
                            text: 'Password copied to clipboard',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    }).catch(function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Copy Failed',
                            text: 'Could not copy to clipboard',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    });
                });
            }
            
            // Also allow copying email
            const emailDisplay = document.querySelector('div[style*="font-family: monospace"]');
            if (emailDisplay && emailDisplay.textContent === userEmail) {
                emailDisplay.style.cursor = 'pointer';
                emailDisplay.title = 'Click to copy';
                emailDisplay.addEventListener('click', function() {
                    navigator.clipboard.writeText(userEmail).then(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Copied!',
                            text: 'Email copied to clipboard',
                            timer: 1500,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        });
                    });
                });
            }
        }
    });
    }, 100); // Small delay to ensure everything is loaded
});
</script>
@endif

@if(session('success') && !session('user_created'))
<script>
document.addEventListener('DOMContentLoaded', function() {
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: {!! json_encode(session('success')) !!},
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end'
    });
});
</script>
@endif

<script>
function resetPassword(userId) {
    Swal.fire({
        title: 'Reset Password',
        text: 'Are you sure you want to reset this user\'s password? A new password will be generated and sent via SMS if available.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Reset Password',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/admin/users/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Failed to reset password');
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            const data = result.value;
            let message = `Password reset successfully!\n\n`;
            message += `<strong>User:</strong> ${data.user_name} (${data.user_email})\n`;
            message += `<strong>Role:</strong> ${data.user_role.charAt(0).toUpperCase() + data.user_role.slice(1)}\n\n`;
            message += `<strong>New Password:</strong>\n`;
            
            // Create HTML content for the popup
            let htmlContent = `
                <div style="text-align: left;">
                    <p style="margin-bottom: 15px;"><strong>Password Reset Successful!</strong></p>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                        <p style="margin: 5px 0;"><strong>User:</strong> ${data.user_name}</p>
                        <p style="margin: 5px 0;"><strong>Email:</strong> ${data.user_email}</p>
                        <p style="margin: 5px 0;"><strong>Role:</strong> ${data.user_role.charAt(0).toUpperCase() + data.user_role.slice(1)}</p>
                        <p style="margin: 10px 0 5px 0;"><strong>New Password:</strong></p>
                        <div style="background: white; padding: 8px; border-radius: 4px; margin: 5px 0; font-family: monospace; font-size: 14px; position: relative;">
                            <span id="password-display">${data.password}</span>
                            <button type="button" id="copy-password-btn" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: #28a745; color: white; border: none; padding: 4px 8px; border-radius: 4px; cursor: pointer; font-size: 12px;">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                    </div>
            `;
            
            if (data.sms_sent) {
                htmlContent += `
                    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                        <i class="fas fa-check-circle"></i> <strong>SMS Sent Successfully!</strong><br>
                        Password has been sent to ${data.phone_number}
                    </div>
                `;
            } else if (data.phone_number) {
                let errorMessage = 'Could not send SMS. Please provide password manually.';
                if (data.sms_error) {
                    errorMessage = data.sms_error;
                    if (data.sms_reason === 'disabled') {
                        errorMessage += '<br><small>Go to Settings → Notifications to enable SMS.</small>';
                    } else if (data.sms_reason === 'config_missing') {
                        errorMessage += '<br><small>Go to Settings → Notifications to configure SMS credentials.</small>';
                    }
                }
                htmlContent += `
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>SMS Not Sent</strong><br>
                        ${errorMessage}
                    </div>
                `;
            } else {
                htmlContent += `
                    <div style="background: #fff3cd; color: #856404; padding: 10px; border-radius: 4px; margin-bottom: 10px;">
                        <i class="fas fa-exclamation-triangle"></i> <strong>No Phone Number</strong><br>
                        Please provide the password manually to the user.
                    </div>
                `;
            }
            
            htmlContent += `
                    <p style="margin-top: 15px; font-size: 12px; color: #6c757d;">
                        <i class="fas fa-info-circle"></i> Please save these credentials securely. The user should change their password after first login.
                    </p>
                </div>
            `;
            
            Swal.fire({
                title: 'Password Reset Successful!',
                html: htmlContent,
                icon: 'success',
                confirmButtonColor: '#28a745',
                confirmButtonText: 'OK',
                width: '600px'
            }).then(() => {
                // Add copy functionality after popup is shown
                const copyBtn = document.getElementById('copy-password-btn');
                if (copyBtn) {
                    copyBtn.addEventListener('click', function() {
                        const passwordText = document.getElementById('password-display').textContent;
                        navigator.clipboard.writeText(passwordText).then(() => {
                            const originalText = copyBtn.innerHTML;
                            copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                            copyBtn.style.background = '#198754';
                            setTimeout(() => {
                                copyBtn.innerHTML = originalText;
                                copyBtn.style.background = '#28a745';
                            }, 2000);
                        }).catch(err => {
                            console.error('Failed to copy password:', err);
                            Swal.fire({
                                icon: 'error',
                                title: 'Copy Failed',
                                text: 'Please manually copy the password.'
                            });
                        });
                    });
                }
            });
        }
    });
}

// Make function available globally
window.resetPassword = resetPassword;

function deleteUser(userId, userName) {
    Swal.fire({
        title: 'Delete User?',
        html: `Are you sure you want to delete <strong>${userName}</strong>?<br><br>This action cannot be undone and will permanently remove the user account.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete User',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: () => {
            return fetch(`/admin/users/${userId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Failed to delete user');
                }
                return data;
            })
            .catch(error => {
                Swal.showValidationMessage(`Request failed: ${error.message}`);
            });
        },
        allowOutsideClick: () => !Swal.isLoading()
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            Swal.fire({
                icon: 'success',
                title: 'User Deleted!',
                text: result.value.message || 'User has been deleted successfully.',
                confirmButtonColor: '#28a745',
                timer: 2000,
                showConfirmButton: true
            }).then(() => {
                // Reload the page to refresh the user list
                window.location.reload();
            });
        }
    });
}

// Make function available globally
window.deleteUser = deleteUser;
</script>

@endsection

