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
    #actionsBody {
        transition: all 0.3s ease;
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
    
    /* Filter Section Styles */
    #filtersForm .card-header {
        transition: background-color 0.2s ease;
    }
    #filterBody {
        transition: all 0.3s ease;
    }
    
    /* Desktop: Always show filters */
    @media (min-width: 769px) {
        .filter-header {
            cursor: default !important;
            pointer-events: none !important;
        }
        .filter-header .fa-chevron-down {
            display: none !important;
        }
        #filterBody {
            display: block !important;
        }
    }
    
    /* Mobile: Collapsible */
    @media (max-width: 768px) {
        .filter-header {
            cursor: pointer !important;
            pointer-events: auto !important;
        }
        #filterBody {
            display: none;
        }
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        #filtersForm .card-body {
            padding: 1rem 0.75rem !important;
        }
        #filtersForm .form-label {
            font-size: 0.75rem !important;
            margin-bottom: 0.25rem !important;
        }
        #filtersForm .form-control,
        #filtersForm .form-select {
            font-size: 0.875rem !important;
            padding: 0.375rem 0.5rem !important;
        }
        #filtersForm .btn-sm {
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
        }
    }
    
    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.25rem !important;
            padding-right: 0.25rem !important;
        }
        #filtersForm .card-body {
            padding: 0.75rem 0.5rem !important;
        }
        #filtersForm .row.g-2 > [class*="col-"] {
            padding-left: 0.375rem !important;
            padding-right: 0.375rem !important;
            margin-bottom: 0.5rem !important;
        }
        #filtersForm .form-label {
            font-size: 0.7rem !important;
        }
        #filtersForm .form-control,
        #filtersForm .form-select {
            font-size: 0.8125rem !important;
            padding: 0.25rem 0.5rem !important;
        }
        .table {
            font-size: 0.75rem;
        }
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
        }
    }
</style>

<div class="container-fluid px-4">
    <!-- Page Title and Quick Actions - Compact Collapsible -->
    <div class="card border-0 shadow-sm mb-3 actions-card">
        <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header" onclick="toggleActions()">
            <div class="d-flex align-items-center gap-2">
                <h1 class="mb-0 mt-2" style="font-size: 1.5rem;">{{ autoTranslate('Weekly Assignments') }}</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('leaders.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>
                    <span class="d-none d-sm-inline">{{ autoTranslate('Back to Leaders') }}</span>
                    <span class="d-sm-none">{{ autoTranslate('Back') }}</span>
                </a>
                @if(auth()->user()->canManageLeadership())
                    <a href="{{ route('weekly-assignments.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>
                        <span class="d-none d-sm-inline">{{ autoTranslate('New Assignment') }}</span>
                        <span class="d-sm-none">{{ autoTranslate('New') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('weekly-assignments.index') }}" class="card mb-3 border-0 shadow-sm" id="filtersForm">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter text-primary"></i>
                    <span class="fw-semibold">{{ autoTranslate('Filters') }}</span>
                    @if(request('position') || request('status') || request('from') || request('to'))
                        <span class="badge bg-primary rounded-pill" id="activeFiltersCount">{{ (request('position') ? 1 : 0) + (request('status') && request('status') != 'active' ? 1 : 0) + (request('from') ? 1 : 0) + (request('to') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-muted d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">{{ autoTranslate('Position') }}</label>
                    <select name="position" class="form-select form-select-sm">
                        <option value="">{{ autoTranslate('All Positions') }}</option>
                        @foreach($positions as $key => $label)
                            <option value="{{ $key }}" {{ request('position') == $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">{{ autoTranslate('Status') }}</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>{{ autoTranslate('All Assignments') }}</option>
                        <option value="active" {{ request('status') == 'active' || !request('status') ? 'selected' : '' }}>{{ autoTranslate('Active Only') }}</option>
                        <option value="current" {{ request('status') == 'current' ? 'selected' : '' }}>{{ autoTranslate('Current Week') }}</option>
                        <option value="past" {{ request('status') == 'past' ? 'selected' : '' }}>{{ autoTranslate('Past') }}</option>
                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>{{ autoTranslate('Upcoming') }}</option>
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">{{ autoTranslate('From Date') }}</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">{{ autoTranslate('To Date') }}</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
            </div>
            
            <!-- Action Buttons - Compact -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="fas fa-filter me-1"></i>{{ autoTranslate('Apply') }}
                </button>
                <a href="{{ route('weekly-assignments.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-redo me-1"></i>{{ autoTranslate('Reset') }}
                </a>
            </div>
        </div>
    </form>

    <!-- Assignments Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>{{ autoTranslate('Week') }}</th>
                            <th>{{ autoTranslate('Leader') }}</th>
                            <th>{{ autoTranslate('Position') }}</th>
                            <th>{{ autoTranslate('Duties') }}</th>
                            <th>{{ autoTranslate('Status') }}</th>
                            <th>{{ autoTranslate('Assigned By') }}</th>
                            <th class="text-end">{{ autoTranslate('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $assignment->week_start_date->format('M d') }} - {{ $assignment->week_end_date->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $assignment->week_start_date->format('l') }} to {{ $assignment->week_end_date->format('l') }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $assignment->leader->member->full_name }}</div>
                                    <small class="text-muted">{{ $assignment->leader->member->member_id }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $assignment->position_display }}</span>
                                </td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $assignment->duties }}">
                                        {{ $assignment->duties ? \Illuminate\Support\Str::limit($assignment->duties, 50) : '—' }}
                                    </div>
                                </td>
                                <td>
                                    @if($assignment->is_active)
                                        @php
                                            $today = now()->toDateString();
                                            $isCurrent = $assignment->week_start_date <= $today && $assignment->week_end_date >= $today;
                                            $isPast = $assignment->week_end_date < $today;
                                            $isFuture = $assignment->week_start_date > $today;
                                        @endphp
                                        @if($isCurrent)
                                            <span class="badge bg-success">{{ autoTranslate('Current') }}</span>
                                        @elseif($isPast)
                                            <span class="badge bg-secondary">{{ autoTranslate('Past') }}</span>
                                        @else
                                            <span class="badge bg-info">{{ autoTranslate('Upcoming') }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-danger">{{ autoTranslate('Inactive') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $assignment->assignedBy->name ?? 'System' }}</small>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('weekly-assignments.show', $assignment) }}" class="btn btn-outline-info" title="{{ autoTranslate('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->canManageLeadership())
                                            <a href="{{ route('weekly-assignments.edit', $assignment) }}" class="btn btn-outline-primary" title="{{ autoTranslate('Edit') }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('weekly-assignments.destroy', $assignment) }}" method="POST" class="d-inline delete-assignment-form" data-assignment-id="{{ $assignment->id }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger" title="{{ autoTranslate('Delete') }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                        <p>{{ autoTranslate('No weekly assignments found.') }}</p>
                                        @if(auth()->user()->canManageLeadership())
                                            <a href="{{ route('weekly-assignments.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus me-2"></i>{{ autoTranslate('Create First Assignment') }}
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($assignments->hasPages())
            <div class="card-footer">
                {{ $assignments->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>

<script>
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

// Toggle Filters Function
function toggleFilters() {
    // Only toggle on mobile devices
    if (window.innerWidth > 768) {
        return; // Don't toggle on desktop
    }
    
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (!filterBody || !filterIcon) return;
    
    // Check computed style to see if it's visible
    const computedStyle = window.getComputedStyle(filterBody);
    const isVisible = computedStyle.display !== 'none';
    
    if (isVisible) {
        filterBody.style.display = 'none';
        filterIcon.classList.remove('fa-chevron-up');
        filterIcon.classList.add('fa-chevron-down');
    } else {
        filterBody.style.display = 'block';
        filterIcon.classList.remove('fa-chevron-down');
        filterIcon.classList.add('fa-chevron-up');
    }
}

// Handle window resize
window.addEventListener('resize', function() {
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (window.innerWidth > 768) {
        // Always show on desktop
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'block';
            actionsIcon.style.display = 'none';
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'block';
            filterIcon.style.display = 'none';
        }
    } else {
        // On mobile, show chevrons
        if (actionsIcon) actionsIcon.style.display = 'block';
        if (filterIcon) filterIcon.style.display = 'block';
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Initialize actions and filters
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (window.innerWidth <= 768) {
        // Mobile: start collapsed
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'none';
            actionsIcon.classList.remove('fa-chevron-up');
            actionsIcon.classList.add('fa-chevron-down');
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'none';
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
        }
    } else {
        // Desktop: always show
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'block';
            actionsIcon.style.display = 'none';
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'block';
            filterIcon.style.display = 'none';
        }
    }
    
    // Show filters if any are active
    @if(request('position') || request('status') || request('from') || request('to'))
        if (window.innerWidth <= 768 && filterBody && filterIcon) {
            toggleFilters(); // Expand if filters are active
        }
    @endif
    // Show success message with SweetAlert
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: '{{ autoTranslate('Success!') }}',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false
        });
    @endif

    // Handle delete confirmation with SweetAlert
    document.querySelectorAll('.delete-assignment-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: '{{ autoTranslate('Delete Assignment?') }}',
                text: '{{ autoTranslate('This action cannot be undone. Are you sure you want to delete this weekly assignment?') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ autoTranslate('Yes, delete it') }}',
                cancelButtonText: '{{ autoTranslate('Cancel') }}',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: '{{ autoTranslate('Deleting...') }}',
                        text: '{{ autoTranslate('Please wait') }}',
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
</script>
@endsection

