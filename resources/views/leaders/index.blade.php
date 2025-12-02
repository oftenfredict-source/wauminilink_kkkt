@extends('layouts.index')

@section('content')
<style>
    /* Actions Card Styles */
    .actions-card {
        transition: all 0.3s ease;
    }
    .actions-card .card-header {
        user-select: none;
        transition: background-color 0.2s ease;
    }
    .actions-card .card-header:hover {
        background-color: #f8f9fa !important;
    }
    .actions-card .card-header i {
        transition: transform 0.3s ease;
    }
    #actionsBody {
        transition: all 0.3s ease;
    }
    #actionsBody .btn-sm {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    /* Desktop: Always show actions, make header non-clickable */
    @media (min-width: 769px) {
        .actions-header {
            cursor: default !important;
            pointer-events: none !important;
        }
        .actions-header .fa-chevron-down {
            display: none !important;
        }
        #actionsBody {
            display: block !important;
        }
    }
    
    /* Mobile: Collapsible */
    @media (max-width: 768px) {
        .actions-header {
            cursor: pointer !important;
            pointer-events: auto !important;
        }
        #actionsBody {
            display: none;
        }
        #actionsToggleIcon {
            display: block !important;
        }
    }
    
    /* Mobile Responsive Styles for Leaders Page */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        
        /* Actions card improvements */
        .actions-card {
            border-radius: 0.5rem !important;
            margin-bottom: 1rem !important;
        }
        .actions-card .card-header {
            padding: 0.75rem 1rem !important;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        .actions-card .card-header h1 {
            font-size: 1.15rem !important;
            margin: 0 !important;
            font-weight: 600 !important;
        }
        .actions-card .card-body {
            padding: 0.875rem !important;
        }
        #actionsBody {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem !important;
        }
        #actionsBody .btn-sm {
            font-size: 0.8rem !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: 0.375rem !important;
            flex: 1 1 calc(50% - 0.25rem);
            min-width: calc(50% - 0.25rem);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.375rem;
        }
        #actionsBody .btn-sm i {
            font-size: 0.875rem !important;
        }
        
        /* Overview cards improvements */
        .row.mb-4 {
            margin-bottom: 1rem !important;
        }
        .col-xl-3.col-md-6 {
            margin-bottom: 0.75rem !important;
            padding-left: 0.375rem !important;
            padding-right: 0.375rem !important;
        }
        .card.bg-primary,
        .card.bg-success,
        .card.bg-info,
        .card.bg-warning {
            border-radius: 0.5rem !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }
        .card-body .h4 {
            font-size: 1.75rem !important;
            font-weight: 700 !important;
            margin: 0.25rem 0 !important;
        }
        .card-body .small {
            font-size: 0.75rem !important;
            font-weight: 500 !important;
            opacity: 0.9;
        }
        .card-body .fa-2x {
            font-size: 1.75rem !important;
            opacity: 0.8;
        }
        
        /* Alerts improvements */
        .alert {
            border-radius: 0.5rem !important;
            padding: 0.75rem 1rem !important;
            font-size: 0.875rem !important;
            margin-bottom: 1rem !important;
        }
        
        /* Card header improvements */
        .card {
            border-radius: 0.5rem !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.08) !important;
            margin-bottom: 1rem !important;
        }
        .card-header {
            flex-direction: row !important;
            align-items: center !important;
            justify-content: space-between !important;
            padding: 0.625rem 0.875rem !important;
            min-height: auto !important;
            border-radius: 0.5rem 0.5rem 0 0 !important;
        }
        .card-header h5 {
            font-size: 0.9rem !important;
            margin-bottom: 0 !important;
            line-height: 1.4 !important;
            font-weight: 600 !important;
            flex: 1;
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.375rem;
        }
        .card-header h5 i {
            font-size: 0.875rem !important;
        }
        .card-header .badge {
            font-size: 0.7rem !important;
            padding: 0.25rem 0.5rem !important;
            margin-left: 0 !important;
            font-weight: 600 !important;
        }
        .card-header .dropdown {
            margin-top: 0 !important;
            align-self: center !important;
        }
        .card-header .dropdown .btn {
            padding: 0.25rem 0.5rem !important;
            font-size: 0.75rem !important;
            border-radius: 0.25rem !important;
        }
        
        /* Leader cards improvements */
        .card-body {
            padding: 0.875rem !important;
        }
        .row > .col-12.col-md-6.col-lg-4 {
            margin-bottom: 0.875rem !important;
            padding-left: 0.375rem !important;
            padding-right: 0.375rem !important;
        }
        .card.h-100 {
            border-radius: 0.5rem !important;
            border-left-width: 3px !important;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .card.h-100:active {
            transform: scale(0.98);
        }
        .card.h-100 .card-body {
            padding: 0.875rem 0.75rem !important;
        }
        .card-title {
            font-size: 0.95rem !important;
            font-weight: 600 !important;
            margin-bottom: 0.5rem !important;
            line-height: 1.3 !important;
        }
        .card-text {
            font-size: 0.8rem !important;
            line-height: 1.5 !important;
            margin-bottom: 0.5rem !important;
        }
        .card-text small {
            font-size: 0.75rem !important;
        }
        .card-text i {
            font-size: 0.75rem !important;
            width: 16px;
            text-align: center;
        }
        .badge {
            font-size: 0.7rem !important;
            padding: 0.25rem 0.5rem !important;
            border-radius: 0.25rem !important;
        }
        .dropdown-menu {
            font-size: 0.875rem !important;
            border-radius: 0.375rem !important;
        }
        .dropdown-item {
            padding: 0.5rem 0.75rem !important;
            font-size: 0.875rem !important;
        }
        .dropdown-item i {
            width: 18px;
        }
    }
    
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        /* Actions card - extra small */
        .actions-card .card-header {
            padding: 0.625rem 0.875rem !important;
        }
        .actions-card .card-header h1 {
            font-size: 1.05rem !important;
        }
        .actions-card .card-body {
            padding: 0.75rem !important;
        }
        #actionsBody {
            gap: 0.5rem !important;
        }
        #actionsBody .btn-sm {
            font-size: 0.75rem !important;
            padding: 0.5rem 0.625rem !important;
            flex: 1 1 100%;
            min-width: 100%;
        }
        
        /* Overview cards - extra small */
        .col-xl-3.col-md-6 {
            margin-bottom: 0.625rem !important;
            padding-left: 0.25rem !important;
            padding-right: 0.25rem !important;
        }
        .card-body .h4 {
            font-size: 1.5rem !important;
        }
        .card-body .small {
            font-size: 0.7rem !important;
        }
        .card-body .fa-2x {
            font-size: 1.5rem !important;
        }
        
        /* Card header - extra small */
        .card-header {
            padding: 0.5rem 0.75rem !important;
        }
        .card-header h5 {
            font-size: 0.85rem !important;
        }
        .card-header h5 i {
            font-size: 0.8rem !important;
        }
        .card-header .badge {
            font-size: 0.65rem !important;
            padding: 0.2rem 0.4rem !important;
        }
        .card-header .dropdown .btn {
            padding: 0.2rem 0.4rem !important;
            font-size: 0.7rem !important;
        }
        
        /* Leader cards - extra small */
        .row > .col-12.col-md-6.col-lg-4 {
            margin-bottom: 0.75rem !important;
            padding-left: 0.25rem !important;
            padding-right: 0.25rem !important;
        }
        .card.h-100 .card-body {
            padding: 0.75rem 0.625rem !important;
        }
        .card-title {
            font-size: 0.9rem !important;
        }
        .card-text {
            font-size: 0.75rem !important;
            margin-bottom: 0.375rem !important;
        }
        .card-text small {
            font-size: 0.7rem !important;
        }
        .badge {
            font-size: 0.65rem !important;
            padding: 0.2rem 0.4rem !important;
        }
    }
    
    /* Additional mobile enhancements */
    @media (max-width: 768px) {
        /* Better spacing between sections */
        .card.mb-4 {
            margin-bottom: 1.25rem !important;
        }
        
        /* Improve leader card layout */
        .card.h-100 .d-flex.justify-content-between {
            margin-bottom: 0.625rem !important;
        }
        
        /* Better visual separation */
        .card-text:not(:last-child) {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 0.5rem;
        }
        
        /* Improve dropdown positioning */
        .dropdown-menu {
            min-width: 180px !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
        }
        
        /* Empty state improvements */
        .card-body.text-center {
            padding: 2rem 1rem !important;
        }
        .card-body.text-center .fa-3x {
            font-size: 2.5rem !important;
            margin-bottom: 1rem !important;
        }
        .card-body.text-center h5 {
            font-size: 1.1rem !important;
            margin-bottom: 0.75rem !important;
        }
        .card-body.text-center p {
            font-size: 0.875rem !important;
            margin-bottom: 1rem !important;
        }
    }
</style>

<div class="container-fluid px-4">
    <!-- Page Title and Quick Actions - Compact Collapsible -->
    <div class="card border-0 shadow-sm mb-3 actions-card">
        <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header" onclick="toggleActions()">
            <div class="d-flex align-items-center gap-2">
                <h1 class="mb-0 mt-2" style="font-size: 1.5rem;">Church Leadership</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('weekly-assignments.index') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-calendar-week"></i>
                    <span class="d-none d-sm-inline">Weekly Assignments</span>
                    <span class="d-sm-none">Assignments</span>
                </a>
                <a href="{{ route('leaders.reports') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-chart-bar"></i>
                    <span class="d-none d-sm-inline">Reports</span>
                    <span class="d-sm-none">Reports</span>
                </a>
                <a href="{{ route('leaders.identity-cards.bulk') }}" class="btn btn-success btn-sm" target="_blank">
                    <i class="fas fa-id-card"></i>
                    <span class="d-none d-sm-inline">All ID Cards</span>
                    <span class="d-sm-none">ID Cards</span>
                </a>
                @if(auth()->user()->canManageLeadership())
                    <a href="{{ route('leaders.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i>
                        <span class="d-none d-sm-inline">Assign Position</span>
                        <span class="d-sm-none">Assign</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Leadership Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Active Leaders</div>
                            <div class="h4">{{ $leaders->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Pastoral Team</div>
                            <div class="h4">{{ $leadersByPosition->get('pastor', collect())->count() + $leadersByPosition->get('assistant_pastor', collect())->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cross fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Ministry Leaders</div>
                            <div class="h4">{{ $leadersByPosition->filter(function($group, $position) {
                                return in_array($position, ['youth_leader', 'children_leader', 'worship_leader', 'choir_leader', 'usher_leader', 'evangelism_leader', 'prayer_leader']);
                            })->flatten()->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-hands-helping fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Administrative</div>
                            <div class="h4">{{ $leadersByPosition->get('secretary', collect())->count() + $leadersByPosition->get('treasurer', collect())->count() + $leadersByPosition->get('elder', collect())->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-cogs fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leadership Positions by Category -->
    @foreach($leadersByPosition as $position => $positionLeaders)
        <div class="card mb-4">
            <div class="card-header bg-primary text-white d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <h5 class="mb-0 text-white mb-2 mb-md-0">
                    <i class="fas fa-{{ $position === 'pastor' ? 'cross' : ($position === 'secretary' ? 'file-alt' : ($position === 'treasurer' ? 'dollar-sign' : 'user-tie')) }} me-2"></i>
                    {{ $positionLeaders->first()->position_display }}
                    <span class="badge bg-white text-primary ms-2 fw-bold">{{ $positionLeaders->count() }}</span>
                </h5>
                <div class="dropdown">
                    <button class="btn btn-sm btn-white text-primary dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" style="border: 1px solid rgba(255,255,255,0.3);">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('leaders.create') }}?position={{ $position }}">
                            <i class="fas fa-plus me-2"></i>Add {{ $positionLeaders->first()->position_display }}
                        </a></li>
                    </ul>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($positionLeaders as $leader)
                        <div class="col-12 col-md-6 col-lg-4 mb-3 mb-md-3">
                            <div class="card h-100 border-start border-4 border-primary">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0">{{ $leader->member->full_name }}</h6>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="{{ route('leaders.show', $leader) }}">
                                                    <i class="fas fa-eye me-2"></i>View Details
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('leaders.identity-card', $leader) }}" target="_blank">
                                                    <i class="fas fa-id-card me-2"></i>Generate ID Card
                                                </a></li>
                                                @if(auth()->user()->canManageLeadership())
                                                    <li><a class="dropdown-item" href="{{ route('leaders.edit', $leader) }}">
                                                        <i class="fas fa-edit me-2"></i>Edit
                                                    </a></li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form action="{{ route('leaders.deactivate', $leader) }}" method="POST" class="d-inline deactivate-leader-form">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item text-warning">
                                                                <i class="fas fa-pause me-2"></i>Deactivate
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('leaders.destroy', $leader) }}" method="POST" class="d-inline delete-leader-form">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="fas fa-trash me-2"></i>Remove
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    <p class="card-text small text-muted mb-2">
                                        <i class="fas fa-id-card me-1"></i>{{ $leader->member->member_id }}
                                    </p>
                                    
                                    @if($leader->position_title)
                                        <p class="card-text small mb-2">
                                            <strong>Title:</strong> {{ $leader->position_title }}
                                        </p>
                                    @endif
                                    
                                    <p class="card-text small mb-2">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        Appointed: {{ $leader->appointment_date->format('M d, Y') }}
                                    </p>
                                    
                                    @if($leader->end_date)
                                        <p class="card-text small mb-2">
                                            <i class="fas fa-calendar-times me-1"></i>
                                            Term Ends: {{ $leader->end_date->format('M d, Y') }}
                                        </p>
                                    @endif
                                    
                                    @if($leader->appointed_by)
                                        <p class="card-text small mb-2">
                                            <i class="fas fa-user-check me-1"></i>
                                            Appointed by: {{ $leader->appointed_by }}
                                        </p>
                                    @endif
                                    
                                    @if($leader->description)
                                        <p class="card-text small text-muted">
                                            {{ Str::limit($leader->description, 100) }}
                                        </p>
                                    @endif
                                    
                                    <div class="mt-2">
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Active
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    @if($leaders->isEmpty())
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No Leadership Positions Assigned</h5>
                <p class="text-muted">Start by assigning leadership positions to church members.</p>
                <a href="{{ route('leaders.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Assign First Leadership Position
                </a>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert for success messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end',
            timerProgressBar: true
        });
    @endif

    // SweetAlert for error messages
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            timer: 4000,
            showConfirmButton: true,
            toast: true,
            position: 'top-end',
            timerProgressBar: true
        });
    @endif

    // Handle deactivate confirmation with SweetAlert
    document.querySelectorAll('.deactivate-leader-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const leaderName = form.closest('.card').querySelector('.card-title')?.textContent || 'this leader';
            
            Swal.fire({
                title: 'Deactivate Leadership Position?',
                html: `Are you sure you want to deactivate the leadership position for <strong>${leaderName}</strong>?<br><br>This will mark the position as inactive but will not delete the record.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, deactivate it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Deactivating...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit the form
                    form.submit();
                }
            });
        });
    });

    // Handle delete confirmation with SweetAlert
    document.querySelectorAll('.delete-leader-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const leaderName = form.closest('.card').querySelector('.card-title')?.textContent || 'this leader';
            
            Swal.fire({
                title: 'Remove Leadership Position?',
                html: `Are you sure you want to permanently remove the leadership position for <strong>${leaderName}</strong>?<br><br><span class="text-danger">This action cannot be undone!</span>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Removing...',
                        text: 'Please wait',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Submit the form
                    form.submit();
                }
            });
        });
    });
});

// Toggle Actions Function
function toggleActions() {
    // Only toggle on mobile devices
    if (window.innerWidth > 768) {
        return; // Don't toggle on desktop
    }
    
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    
    if (!actionsBody || !actionsIcon) return;
    
    // Check computed style to see if it's visible
    const computedStyle = window.getComputedStyle(actionsBody);
    const isVisible = computedStyle.display !== 'none';
    
    if (isVisible) {
        actionsBody.style.display = 'none';
        actionsIcon.classList.remove('fa-chevron-up');
        actionsIcon.classList.add('fa-chevron-down');
    } else {
        actionsBody.style.display = 'block';
        actionsIcon.classList.remove('fa-chevron-down');
        actionsIcon.classList.add('fa-chevron-up');
    }
}

// Handle window resize
window.addEventListener('resize', function() {
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    
    if (!actionsBody || !actionsIcon) return;
    
    if (window.innerWidth > 768) {
        // Always show on desktop
        actionsBody.style.display = 'block';
        actionsIcon.style.display = 'none';
    } else {
        // On mobile, show chevron
        actionsIcon.style.display = 'block';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    
    if (!actionsBody || !actionsIcon) return;
    
    if (window.innerWidth <= 768) {
        // Mobile: start collapsed
        actionsBody.style.display = 'none';
        actionsIcon.classList.remove('fa-chevron-up');
        actionsIcon.classList.add('fa-chevron-down');
    } else {
        // Desktop: always show
        actionsBody.style.display = 'block';
        actionsIcon.style.display = 'none';
    }
});
</script>
@endsection
