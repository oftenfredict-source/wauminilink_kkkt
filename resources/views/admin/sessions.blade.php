@extends('layouts.index')

@section('content')
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

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 0.25rem !important;
        }

        .dashboard-header {
            margin-bottom: 12px !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        }

        .dashboard-header .card-body {
            padding: 12px 14px !important;
        }

        .dashboard-header .rounded-circle {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-shrink: 0 !important;
            background: rgba(255,255,255,0.2) !important;
            border: 2px solid rgba(255,255,255,0.3) !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.95rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 12px !important;
            flex: 1 !important;
            min-width: 0 !important;
        }

        .dashboard-header .lh-sm {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        .dashboard-header h5 {
            font-size: 1rem !important;
            line-height: 1.3 !important;
            margin-bottom: 2px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header small {
            font-size: 0.75rem !important;
            line-height: 1.2 !important;
            display: block !important;
            opacity: 0.9 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            transition: all 0.2s ease !important;
        }

        .dashboard-header .btn:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15) !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            align-items: center !important;
            flex-wrap: nowrap !important;
        }

        .dashboard-header .d-flex.justify-content-between > div:first-child {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        /* Filter section improvements */
        .card.shadow:has(form) {
            margin-bottom: 1rem !important;
        }

        .card.shadow:has(form) .card-body {
            padding: 1rem !important;
        }

        /* Filter header on mobile */
        .card-header.d-md-none {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            padding: 0.75rem 1rem !important;
            user-select: none !important;
        }

        .card-header.d-md-none:hover {
            background-color: #e9ecef !important;
        }

        .card-header.d-md-none h6 {
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            margin: 0 !important;
        }

        .card-header.d-md-none i {
            font-size: 0.875rem !important;
            transition: transform 0.3s ease !important;
        }

        /* Filter form - stack on mobile */
        .card-body .row.g-3 > div {
            margin-bottom: 0.75rem !important;
        }

        .form-label {
            font-size: 0.875rem !important;
            margin-bottom: 0.375rem !important;
            font-weight: 500 !important;
        }

        .form-control,
        .form-select {
            width: 100% !important;
            padding: 0.5rem 0.75rem !important;
            font-size: 0.9rem !important;
        }

        /* Filter buttons on mobile */
        .card-body .btn {
            width: 100% !important;
            margin-bottom: 0.5rem !important;
        }

        .card-body .gap-2 {
            gap: 0.5rem !important;
        }

        /* Table responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            display: block;
            width: 100%;
        }

        .table {
            font-size: 0.85rem;
            min-width: 900px;
        }

        .table th,
        .table td {
            padding: 8px 4px !important;
        }

        /* Actions column */
        .table td:last-child .btn {
            width: 100%;
            margin-bottom: 5px;
            font-size: 0.75rem;
            padding: 4px 8px;
        }

        .card-header {
            padding: 10px 15px !important;
        }

        .card-body {
            padding: 15px !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            padding-top: 0.15rem !important;
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

        .table {
            font-size: 0.75rem;
            min-width: 800px;
        }

        .table th,
        .table td {
            padding: 6px 3px !important;
        }

        .btn-sm {
            font-size: 0.7rem;
            padding: 3px 6px;
        }

        /* Filter section improvements on extra small */
        .card.shadow:has(form) .card-body {
            padding: 0.75rem !important;
        }

        /* Filter header on extra small mobile */
        .card-header.d-md-none {
            padding: 0.625rem 0.75rem !important;
        }

        .card-header.d-md-none h6 {
            font-size: 0.85rem !important;
        }

        .card-header.d-md-none i {
            font-size: 0.8125rem !important;
        }

        .form-label {
            font-size: 0.8125rem !important;
        }

        .form-control,
        .form-select {
            font-size: 0.875rem !important;
            padding: 0.45rem 0.625rem !important;
        }

        .card-body .btn {
            font-size: 0.875rem !important;
            padding: 0.5rem 0.75rem !important;
        }
    }

    /* Desktop: Always show filters */
    @media (min-width: 769px) {
        #sessionsFilters {
            display: block !important;
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
                                <i class="fas fa-user-check text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">User Sessions</h5>
                                <small style="color: white !important;">Monitor and manage active sessions</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-md-none d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="toggleFilterSection('sessionsFilters')">
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filters</h6>
            <i class="fas fa-chevron-down" id="sessionsFiltersIcon"></i>
        </div>
        <div class="card-header py-3 d-none d-md-block">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body" id="sessionsFilters">
            <form method="GET" action="{{ route('admin.sessions') }}" class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">User</label>
                    <select name="user_id" class="form-control">
                        <option value="">All Users</option>
                        @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Status</label>
                    <select name="active_only" class="form-control">
                        <option value="">All Sessions</option>
                        <option value="1" {{ request('active_only') == '1' ? 'selected' : '' }}>Active Only (Last 24h)</option>
                    </select>
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end gap-2 flex-wrap">
                    <button type="submit" class="btn btn-primary flex-grow-1 flex-md-grow-0">
                        <i class="fas fa-filter me-1"></i><span class="d-none d-sm-inline">Apply Filters</span><span class="d-sm-none">Filter</span>
                    </button>
                    <a href="{{ route('admin.sessions') }}" class="btn btn-secondary flex-grow-1 flex-md-grow-0">
                        <i class="fas fa-times me-1"></i><span class="d-none d-sm-inline">Clear</span><span class="d-sm-none">Clear</span>
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Sessions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Sessions ({{ $sessions->count() }} total)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>IP Address</th>
                            <th>User Agent</th>
                            <th>Last Activity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sessions as $session)
                        <tr class="{{ $session->is_current ? 'table-info' : '' }}">
                            <td>
                                <strong>{{ $session->name }}</strong><br>
                                <small class="text-muted">{{ $session->email }}</small>
                                @if(isset($session->is_login_blocked) && $session->is_login_blocked)
                                <br><span class="badge badge-danger" style="font-size: 0.7em; margin-top: 2px;">
                                    <i class="fas fa-ban"></i> Login Blocked 
                                    @if(isset($session->remaining_block_time_formatted) && $session->remaining_block_time_formatted)
                                        ({{ $session->remaining_block_time_formatted }})
                                    @elseif(isset($session->remaining_block_time) && $session->remaining_block_time)
                                        ({{ $session->remaining_block_time }} min)
                                    @endif
                                </span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $roleBadgeClass = match(strtolower($session->role)) {
                                        'admin' => 'badge-danger',
                                        'pastor' => 'badge-warning',
                                        'secretary' => 'badge-info',
                                        'treasurer' => 'badge-secondary',
                                        default => 'badge-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $roleBadgeClass }}">
                                    {{ ucfirst($session->role) }}
                                </span>
                            </td>
                            <td><small>{{ $session->ip_address }}</small></td>
                            <td>
                                <small title="{{ $session->user_agent }}">
                                    {{ Str::limit($session->user_agent, 50) }}
                                </small>
                            </td>
                            <td>
                                <small>{{ $session->last_activity_formatted }}</small><br>
                                <small class="text-muted">{{ $session->last_activity_human }}</small>
                            </td>
                            <td>
                                @if($session->is_current)
                                    <span class="badge badge-success">Current Session</span>
                                @elseif($session->is_active)
                                    <span class="badge badge-info">Active</span>
                                @else
                                    <span class="badge badge-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if(!$session->is_current)
                                    @if(isset($session->is_login_blocked) && $session->is_login_blocked)
                                    <button type="button" class="btn btn-sm btn-success mb-1" onclick="unblockUser('{{ $session->user_id }}', '{{ $session->name }}')" title="Unblock user from logging in">
                                        <i class="fas fa-unlock"></i> Unblock
                                    </button>
                                    <br>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" onclick="revokeSession('{{ $session->id }}', '{{ $session->name }}')">
                                        <i class="fas fa-ban"></i> Revoke
                                    </button>
                                @else
                                <span class="text-muted">Current</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No sessions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle filter section on mobile
function toggleFilterSection(filterId) {
    const filterBody = document.getElementById(filterId);
    const filterIcon = document.getElementById(filterId + 'Icon');
    
    if (filterBody && filterIcon) {
        if (filterBody.style.display === 'none') {
            filterBody.style.display = 'block';
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
        } else {
            filterBody.style.display = 'none';
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
        }
    }
}

// Initialize filter sections on mobile
document.addEventListener('DOMContentLoaded', function() {
    function initializeFilters() {
        const filterSections = ['sessionsFilters'];
        if (window.innerWidth <= 768) {
            filterSections.forEach(function(filterId) {
                const filterBody = document.getElementById(filterId);
                if (filterBody && filterBody.style.display === '') {
                    filterBody.style.display = 'none';
                }
            });
        } else {
            filterSections.forEach(function(filterId) {
                const filterBody = document.getElementById(filterId);
                if (filterBody) {
                    filterBody.style.display = 'block';
                }
            });
        }
    }
    
    initializeFilters();
    
    // Handle window resize
    window.addEventListener('resize', function() {
        initializeFilters();
    });
});

function revokeSession(sessionId, userName) {
    // Show SweetAlert dialog to get the unblock date/time
    Swal.fire({
        title: 'Revoke Session',
        html: `
            <div class="text-start">
                <p class="mb-3">Are you sure you want to revoke the session for <strong>${userName}</strong>?</p>
                <div class="mb-3">
                    <label for="blocked-until" class="form-label"><strong>User can login again on:</strong></label>
                    <input type="datetime-local" 
                           id="blocked-until" 
                           class="form-control" 
                           required
                           min="${new Date().toISOString().slice(0, 16)}">
                    <small class="form-text text-muted">Select the date and time when the user will be able to login again.</small>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>The session will be terminated immediately, and the user will be blocked from logging in until the specified time.</small>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-ban me-1"></i>Revoke Session',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusConfirm: false,
        preConfirm: () => {
            const blockedUntil = document.getElementById('blocked-until').value;
            if (!blockedUntil) {
                Swal.showValidationMessage('Please select when the user can login again.');
                return false;
            }
            
            const selectedDate = new Date(blockedUntil);
            const now = new Date();
            if (selectedDate <= now) {
                Swal.showValidationMessage('The unblock time must be in the future.');
                return false;
            }
            
            return {
                blocked_until: blockedUntil
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Show loading state
            Swal.fire({
                title: 'Revoking Session...',
                text: 'Please wait while we revoke the session',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make the API call
            fetch(`{{ url('/admin/sessions') }}/${sessionId}/revoke`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Session Revoked!',
                        html: `
                            <p>${data.message}</p>
                            <p class="text-muted small mt-2">
                                <i class="fas fa-clock me-1"></i>
                                User can login again: <strong>${data.blocked_until}</strong>
                            </p>
                        `,
                        confirmButtonColor: '#198754',
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Revoke Failed',
                        text: data.message || 'Failed to revoke session. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while revoking the session. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

function unblockUser(userId, userName) {
    Swal.fire({
        title: 'Unblock User?',
        html: `
            <div class="text-start">
                <p class="mb-3">Are you sure you want to unblock <strong>${userName}</strong> from logging in?</p>
                <p class="text-muted small">This will allow the user to login immediately, even if the block time hasn't expired yet.</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-unlock me-1"></i>Yes, Unblock',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Unblocking User...',
                text: 'Please wait',
                allowOutsideClick: false,
                allowEscapeKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make the request
            fetch(`{{ url('/admin/users') }}/${userId}/unblock`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Unblocked!',
                        text: `${userName} has been unblocked and can now login.`,
                        confirmButtonColor: '#28a745',
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unblock Failed',
                        text: data.message || 'Failed to unblock user. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while unblocking the user. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}
</script>
@endsection

