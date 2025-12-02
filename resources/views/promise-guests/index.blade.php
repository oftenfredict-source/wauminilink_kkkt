@extends('layouts.index')

@section('content')
<style>
    .table.interactive-table tbody tr:hover { background-color: #f8f9ff; }
    .table.interactive-table tbody tr td:first-child { border-left: 4px solid #5b2a86; }
    
    .status-badge {
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
    }
    .status-pending { background-color: #ffc107; color: #000; }
    .status-notified { background-color: #17a2b8; color: #fff; }
    .status-attended { background-color: #28a745; color: #fff; }
    .status-cancelled { background-color: #dc3545; color: #fff; }
    
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        /* Actions Card */
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
            display: none;
        }
        .actions-header {
            cursor: pointer !important;
        }
        #actionsToggleIcon {
            display: block !important;
        }
        
        /* Statistics Cards */
        .stats-card {
            margin-bottom: 0.75rem;
        }
        .stats-card .card-body {
            padding: 0.75rem !important;
        }
        .stats-card h3 {
            font-size: 1.5rem !important;
        }
        .stats-card h6 {
            font-size: 0.75rem !important;
        }
        .stats-card i {
            font-size: 1.5rem !important;
        }
        
        /* Filter Section */
        #filtersForm {
            border-radius: 8px !important;
            overflow: hidden;
        }
        #filtersForm .card-header {
            transition: all 0.2s ease;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
            border-bottom: 2px solid #e9ecef !important;
        }
        .filter-header:hover {
            background: linear-gradient(135deg, #f0f0f0 0%, #f8f9fa 100%) !important;
        }
        #filterBody {
            transition: all 0.3s ease;
            display: none;
            background: #fafbfc;
        }
        .filter-header {
            cursor: pointer !important;
        }
        #filterToggleIcon {
            display: block !important;
            transition: transform 0.3s ease;
        }
        .filter-header.active #filterToggleIcon {
            transform: rotate(180deg);
        }
        #filtersForm .card-body {
            padding: 0.75rem 0.5rem !important;
        }
        #filtersForm .form-label {
            font-size: 0.7rem !important;
            margin-bottom: 0.2rem !important;
            font-weight: 600 !important;
            color: #495057 !important;
        }
        #filtersForm .form-control,
        #filtersForm .form-select {
            font-size: 0.8125rem !important;
            padding: 0.4rem 0.5rem !important;
            border-radius: 6px !important;
            border: 1.5px solid #dee2e6 !important;
        }
        #filtersForm .form-control:focus,
        #filtersForm .form-select:focus {
            border-color: #667eea !important;
            box-shadow: 0 0 0 0.15rem rgba(102, 126, 234, 0.15) !important;
        }
        #filtersForm .btn-sm {
            padding: 0.4rem 0.75rem !important;
            font-size: 0.8125rem !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
        }
        #filtersForm .row.g-2 > [class*="col-"] {
            padding-left: 0.375rem !important;
            padding-right: 0.375rem !important;
            margin-bottom: 0.5rem !important;
        }
        #filtersForm .row.g-2 {
            margin-left: -0.375rem !important;
            margin-right: -0.375rem !important;
        }
        
        /* Table Responsive */
        .table {
            font-size: 0.75rem;
        }
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
        }
        .table th:first-child,
        .table td:first-child {
            position: sticky;
            left: 0;
            background: white;
            z-index: 1;
        }
        
        /* Buttons - Icon Only on Mobile */
        .btn-group .btn {
            padding: 0.375rem 0.5rem !important;
        }
        .btn-group .btn i {
            margin: 0 !important;
        }
        
        /* Modal Full Screen on Mobile */
        @media (max-width: 576px) {
            .modal-dialog {
                margin: 0;
                max-width: 100%;
                height: 100vh;
            }
            .modal-content {
                height: 100vh;
                border-radius: 0 !important;
            }
            #filtersForm .card-body {
                padding: 0.5rem 0.375rem !important;
            }
            #filtersForm .form-label {
                font-size: 0.65rem !important;
            }
            #filtersForm .form-control,
            #filtersForm .form-select {
                font-size: 0.75rem !important;
                padding: 0.35rem 0.45rem !important;
            }
        }
    }
    
    /* Desktop: Always show actions and filters */
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
</style>

<div class="container-fluid px-4">
    <!-- Page Title and Quick Actions - Compact Collapsible -->
    <div class="card border-0 shadow-sm mb-3 actions-card">
        <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header" onclick="toggleActions()">
            <div class="d-flex align-items-center gap-2">
                <h2 class="mb-0 mt-2" style="font-size: 1.5rem;">Promise Guests</h2>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPromiseGuestModal">
                    <i class="fas fa-plus me-1"></i>
                    <span class="d-none d-sm-inline">Add Promise Guest</span>
                    <span class="d-sm-none">Add Guest</span>
                </button>
                <a href="{{ route('promise-guests.create') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i>
                    <span class="d-none d-sm-inline">Full Form</span>
                    <span class="d-sm-none">Form</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 stats-card">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 stats-card">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-dark-50 mb-1">Pending</h6>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 stats-card">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Notified</h6>
                            <h3 class="mb-0">{{ $stats['notified'] }}</h3>
                        </div>
                        <i class="fas fa-bell fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-6 stats-card">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Attended</h6>
                            <h3 class="mb-0">{{ $stats['attended'] }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('promise-guests.index') }}" class="card mb-3 border-0 shadow-sm" id="filtersForm">
        <!-- Filter Header -->
        <div class="card-header bg-white border-bottom p-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter text-primary"></i>
                    <span class="fw-semibold">Filters</span>
                    @if(request('search') || request('status') || request('service_date') || request('from') || request('to'))
                        <span class="badge bg-primary rounded-pill" id="activeFiltersCount">{{ (request('search') ? 1 : 0) + (request('status') ? 1 : 0) + (request('service_date') ? 1 : 0) + (request('from') ? 1 : 0) + (request('to') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-muted d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-2">
                <!-- Search Field - Full Width on Mobile -->
                <div class="col-12 col-md-3">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-search me-1 text-primary"></i>Search
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Name or phone number">
                </div>
                
                <!-- Status - Full Width on Mobile -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-info-circle me-1 text-info"></i>Status
                    </label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="notified" {{ request('status') == 'notified' ? 'selected' : '' }}>Notified</option>
                        <option value="attended" {{ request('status') == 'attended' ? 'selected' : '' }}>Attended</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                
                <!-- Service Date - Full Width on Mobile -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar-alt me-1 text-success"></i>Service Date
                    </label>
                    <input type="date" name="service_date" value="{{ request('service_date') }}" class="form-control form-control-sm">
                </div>
                
                <!-- Date Range - Side by Side on Mobile -->
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar me-1 text-warning"></i>From
                    </label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar-check me-1 text-warning"></i>To
                    </label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                </div>
                
                <!-- Apply Button - Full Width on Mobile -->
                <div class="col-12 col-md-1">
                    <label class="form-label small text-muted mb-1 d-block">&nbsp;</label>
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-filter me-1"></i>
                        <span class="d-none d-sm-inline">Apply</span>
                    </button>
                </div>
            </div>
            
            <!-- Action Buttons - Compact, Full Width on Mobile -->
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('promise-guests.index') }}" class="btn btn-outline-secondary btn-sm flex-fill flex-md-grow-0">
                    <i class="fas fa-redo me-1"></i>
                    <span class="d-none d-sm-inline">Reset</span>
                    <span class="d-sm-none">Clear</span>
                </a>
            </div>
        </div>
    </form>

    <!-- Error/Success Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{!! htmlspecialchars_decode(session('success'), ENT_QUOTES) !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any() || session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            @if($errors->any())
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
            @elseif(session('error'))
                {{ session('error') }}
            @endif
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Promise Guests Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="d-none d-md-table-cell">Name</th>
                            <th class="d-table-cell d-md-none">Guest</th>
                            <th class="d-none d-lg-table-cell">Phone</th>
                            <th>Service Date</th>
                            <th>Status</th>
                            <th class="d-none d-xl-table-cell">Notified At</th>
                            <th class="d-none d-lg-table-cell">Created By</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($promiseGuests as $guest)
                            <tr>
                                <td>
                                    <strong>{{ $guest->name }}</strong>
                                    @if($guest->email)
                                        <br><small class="text-muted d-none d-md-inline">{{ $guest->email }}</small>
                                    @endif
                                    <div class="d-md-none">
                                        <small class="text-muted d-block">
                                            <i class="fas fa-phone me-1"></i>{{ $guest->phone_number }}
                                        </small>
                                        @if($guest->email)
                                            <small class="text-muted d-block">
                                                <i class="fas fa-envelope me-1"></i>{{ $guest->email }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                <td class="d-none d-lg-table-cell">{{ $guest->phone_number }}</td>
                                <td>
                                    {{ $guest->promised_service_date->format('d/m/Y') }}
                                    @if($guest->service)
                                        <br><small class="text-muted d-none d-md-inline">
                                            @if($guest->service->theme)
                                                {{ $guest->service->theme }}
                                            @endif
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $guest->status }}">
                                        {{ ucfirst($guest->status) }}
                                    </span>
                                </td>
                                <td class="d-none d-xl-table-cell">
                                    @if($guest->notified_at)
                                        {{ $guest->notified_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="d-none d-lg-table-cell">
                                    @if($guest->creator)
                                        {{ $guest->creator->name }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @if($guest->status == 'pending')
                                            <form action="{{ route('promise-guests.send-notification', $guest) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-info btn-sm text-white" title="Send Notification">
                                                    <i class="fas fa-bell"></i>
                                                    <span class="d-none d-sm-inline ms-1">Notify</span>
                                                </button>
                                            </form>
                                        @endif
                                        @if($guest->status != 'attended')
                                            <form action="{{ route('promise-guests.mark-attended', $guest) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm text-white" title="Mark as Attended">
                                                    <i class="fas fa-check"></i>
                                                    <span class="d-none d-sm-inline ms-1">Attend</span>
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('promise-guests.edit', $guest) }}" class="btn btn-primary btn-sm text-white" title="Edit">
                                            <i class="fas fa-edit"></i>
                                            <span class="d-none d-sm-inline ms-1">Edit</span>
                                        </a>
                                        <form action="{{ route('promise-guests.destroy', $guest) }}" method="POST" class="d-inline delete-promise-guest-form" data-guest-name="{{ $guest->name }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm text-white" title="Delete">
                                                <i class="fas fa-trash"></i>
                                                <span class="d-none d-sm-inline ms-1">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">No promise guests found.</p>
                                    <a href="{{ route('promise-guests.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Add Promise Guest
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($promiseGuests->hasPages())
            <div class="card-footer">
                {{ $promiseGuests->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Add Promise Guest Modal -->
<div class="modal fade" id="addPromiseGuestModal" tabindex="-1" aria-labelledby="addPromiseGuestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPromiseGuestModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Add Promise Guest
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('promise-guests.store') }}" method="POST" id="addPromiseGuestForm">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modal_name" class="form-label">Guest Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="modal_name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="modal_phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">+255</span>
                                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                                       id="modal_phone_number" name="phone_number" 
                                       placeholder="712345678" 
                                       value="{{ old('phone_number') }}" 
                                       pattern="[0-9]{9,15}" 
                                       maxlength="15"
                                       required>
                            </div>
                            <small class="text-muted">Enter phone number without +255 (e.g., 712345678)</small>
                            @error('phone_number')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modal_email" class="form-label">Email (Optional)</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="modal_email" name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="modal_promised_service_date" class="form-label">Promised Service Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('promised_service_date') is-invalid @enderror" 
                                   id="modal_promised_service_date" name="promised_service_date" 
                                   value="{{ old('promised_service_date') }}" 
                                   min="{{ date('Y-m-d') }}" required>
                            @error('promised_service_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="modal_service_id" class="form-label">Sunday Service (Optional)</label>
                            <select class="form-select @error('service_id') is-invalid @enderror" 
                                    id="modal_service_id" name="service_id">
                                <option value="">Select a service (or leave blank)</option>
                                @foreach($upcomingServices as $service)
                                    <option value="{{ $service->id }}" 
                                            data-date="{{ $service->service_date->format('Y-m-d') }}"
                                            {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->service_date->format('d/m/Y') }}
                                        @if($service->theme)
                                            - {{ $service->theme }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">If selected, service date will be automatically set</small>
                            @error('service_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="modal_notes" class="form-label">Notes (Optional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                      id="modal_notes" name="notes" rows="3">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Promise Guest
                    </button>
                </div>
            </form>
        </div>
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
        const filterHeader = document.querySelector('.filter-header');
        
        if (!filterBody || !filterIcon) return;
        
        // Check computed style to see if it's visible
        const computedStyle = window.getComputedStyle(filterBody);
        const isVisible = computedStyle.display !== 'none';
        
        if (isVisible) {
            filterBody.style.display = 'none';
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
            if (filterHeader) filterHeader.classList.remove('active');
        } else {
            filterBody.style.display = 'block';
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
            if (filterHeader) filterHeader.classList.add('active');
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

    // Auto-format phone number with +255 prefix
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
        @if(request('search') || request('status') || request('service_date') || request('from') || request('to'))
            if (window.innerWidth <= 768 && filterBody && filterIcon) {
                toggleFilters(); // Expand if filters are active
                const filterHeader = document.querySelector('.filter-header');
                if (filterHeader) filterHeader.classList.add('active');
            }
        @endif
        const phoneInput = document.getElementById('modal_phone_number');
        const phoneForm = document.getElementById('addPromiseGuestForm');
        
        // Format phone number on input (only allow digits)
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                // Remove any non-digit characters
                this.value = this.value.replace(/\D/g, '');
            });

            // Add +255 prefix before form submission
            if (phoneForm) {
                phoneForm.addEventListener('submit', function(e) {
                    const phoneValue = phoneInput.value.replace(/\s+/g, '');
                    if (phoneValue && /^[0-9]{9,15}$/.test(phoneValue)) {
                        phoneInput.value = '+255' + phoneValue;
                    } else if (phoneValue) {
                        e.preventDefault();
                        alert('Please enter a valid phone number (9-15 digits without +255)');
                        return false;
                    }
                });
            }
        }

        // Auto-update service date when a service is selected in modal
        const modalServiceSelect = document.getElementById('modal_service_id');
        const modalDateInput = document.getElementById('modal_promised_service_date');
        
        if (modalServiceSelect && modalDateInput) {
            modalServiceSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value && selectedOption.dataset.date) {
                    modalDateInput.value = selectedOption.dataset.date;
                }
            });

            // Auto-update service selection when date changes
            modalDateInput.addEventListener('change', function() {
                const selectedDate = this.value;
                
                // Try to find matching service
                for (let option of modalServiceSelect.options) {
                    if (option.dataset.date === selectedDate) {
                        modalServiceSelect.value = option.value;
                        break;
                    } else {
                        modalServiceSelect.value = '';
                    }
                }
            });
        }

        // Reset modal form when closed
        const modal = document.getElementById('addPromiseGuestModal');
        if (modal) {
            modal.addEventListener('hidden.bs.modal', function() {
                document.getElementById('addPromiseGuestForm').reset();
                // Clear any validation errors
                const invalidInputs = modal.querySelectorAll('.is-invalid');
                invalidInputs.forEach(input => input.classList.remove('is-invalid'));
            });
        }
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // SweetAlert for success messages
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: {!! json_encode(session('success')) !!},
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    @endif

    // SweetAlert for error messages
    @if($errors->any())
        @foreach($errors->all() as $error)
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: {!! json_encode($error) !!},
                confirmButtonColor: '#dc3545'
            });
        @endforeach
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: {!! json_encode(session('error')) !!},
            confirmButtonColor: '#dc3545'
        });
    @endif

    // Handle delete confirmation with SweetAlert
    document.querySelectorAll('.delete-promise-guest-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const guestName = this.getAttribute('data-guest-name') || 'this promise guest';
            
            Swal.fire({
                title: 'Delete Promise Guest?',
                html: `Are you sure you want to delete <strong>${guestName}</strong>?<br><br>This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    Swal.fire({
                        title: 'Deleting...',
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

    // Handle add promise guest form submission with SweetAlert
    const addPromiseGuestForm = document.getElementById('addPromiseGuestForm');
    if (addPromiseGuestForm) {
        addPromiseGuestForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const phoneInput = document.getElementById('modal_phone_number');
            const phoneValue = phoneInput.value.replace(/\s+/g, '').replace(/^\+255/, ''); // Remove +255 if already present
            
            // Validate phone number format
            if (!phoneValue || phoneValue.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Phone Number Required',
                    text: 'Please enter a phone number',
                    confirmButtonColor: '#dc3545'
                });
                phoneInput.focus();
                return false;
            }
            
            if (!/^[0-9]{9,15}$/.test(phoneValue)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Phone Number',
                    text: 'Please enter a valid phone number (9-15 digits without +255).\nExample: 712345678',
                    confirmButtonColor: '#dc3545'
                });
                phoneInput.focus();
                return false;
            }
            
            // Add +255 prefix to phone number
            const fullPhoneNumber = '+255' + phoneValue;
            phoneInput.value = fullPhoneNumber;
            
            // Create FormData with updated phone number
            const formData = new FormData(this);
            formData.set('phone_number', fullPhoneNumber);

            // Show loading
            Swal.fire({
                title: 'Adding Promise Guest...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit form via fetch to handle response
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
                },
                redirect: 'manual' // Don't follow redirects automatically
            })
            .then(async response => {
                // Check if response is JSON
                const contentType = response.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) {
                    const data = await response.json();
                    return { ...data, status: response.status };
                } else if (response.status === 302 || response.redirected) {
                    // If redirected, it means success (Laravel redirects on success)
                    return { success: true, redirected: true };
                } else if (response.status === 422) {
                    // Validation error - try to parse as JSON
                    try {
                        const data = await response.json();
                        return { ...data, status: response.status };
                    } catch {
                        return { success: false, message: 'Validation error occurred', status: response.status };
                    }
                } else {
                    // Try to get error message from response
                    const text = await response.text();
                    try {
                        const data = JSON.parse(text);
                        return { ...data, status: response.status };
                    } catch {
                        return { success: false, message: 'An error occurred', status: response.status };
                    }
                }
            })
            .then(data => {
                if (data && data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addPromiseGuestModal'));
                    if (modal) {
                        modal.hide();
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Promise guest added successfully!',
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    }).then(() => {
                        location.reload();
                    });
                } else if (data && data.redirected) {
                    // Handle redirect
                    location.reload();
                } else {
                    // Handle validation errors
                    let errorMessage = 'Failed to add promise guest';
                    let errorTitle = 'Error';
                    
                    if (data && data.errors) {
                        // Laravel validation errors
                        errorTitle = 'Validation Error';
                        const errors = Object.values(data.errors).flat();
                        errorMessage = errors.join('<br>');
                    } else if (data && data.message) {
                        errorMessage = data.message;
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: errorTitle,
                        html: errorMessage,
                        confirmButtonColor: '#dc3545'
                    });
                    
                    // Reset phone input to show only digits (remove +255 for display)
                    phoneInput.value = phoneValue;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while adding the promise guest. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
                
                // Reset phone input to show only digits (remove +255 for display)
                phoneInput.value = phoneValue;
            });
        });
    }
});
</script>

@endsection

