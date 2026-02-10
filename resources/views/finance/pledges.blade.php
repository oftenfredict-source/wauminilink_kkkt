@extends('layouts.index')

@section('content')
<style>
    .is-invalid {
        border-color: #dc3545 !important;
    }
    #due_date_error {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
    
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
        
        /* Filter Section */
        #filtersForm .card-header {
            transition: all 0.2s ease;
        }
        .filter-header:hover {
            opacity: 0.9;
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
        }
        #filtersForm .form-control,
        #filtersForm .form-select {
            font-size: 0.8125rem !important;
            padding: 0.4rem 0.5rem !important;
            border-radius: 6px !important;
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
        
        /* Buttons - Icon Only on Mobile */
        .btn-group .btn {
            padding: 0.375rem 0.5rem !important;
        }
        .btn-group .btn i {
            margin: 0 !important;
        }
        .btn-group .btn span {
            display: none !important;
        }
        
        /* Header adjustments */
        h1 {
            font-size: 1.25rem !important;
        }
        
        /* Badge adjustments */
        .badge-sm {
            font-size: 0.65rem;
            padding: 0.25rem 0.4rem;
        }
        
        /* Progress Bar - Smaller on Mobile */
        .progress {
            height: 16px !important;
            font-size: 0.7rem !important;
        }
        
        /* Modal Full Screen on Mobile */
        @media (max-width: 576px) {
            .modal-fullscreen-sm-down {
                margin: 0;
                max-width: 100%;
                height: 100vh;
            }
            .modal-fullscreen-sm-down .modal-content {
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
                <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-handshake me-2"></i>Pledges Management</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPledgeModal">
                    <i class="fas fa-plus me-1"></i>
                    <span class="d-none d-sm-inline">Add Pledge</span>
                    <span class="d-sm-none">Add</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('finance.pledges') }}" class="card mb-4 border-0 shadow-sm" id="filtersForm">
        <!-- Filter Header -->
        <div class="card-header bg-primary text-white p-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter me-1"></i>
                    <span class="fw-semibold">Filters</span>
                    @if(request('member_id') || request('pledge_type') || request('status'))
                        <span class="badge bg-white text-primary rounded-pill ms-2" id="activeFiltersCount">{{ (request('member_id') ? 1 : 0) + (request('pledge_type') ? 1 : 0) + (request('status') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-2">
                <!-- Member - Full Width on Mobile -->
                <div class="col-12 col-md-3">
                    <label for="member_id" class="form-label small text-muted mb-1">
                        <i class="fas fa-user me-1 text-primary"></i>Member
                    </label>
                    <select class="form-select form-select-sm select2-member" id="member_id" name="member_id">
                        <option value="">All Members</option>
                        @foreach($members as $member)
                            <option value="{{ $member->id }}" {{ request('member_id') == $member->id ? 'selected' : '' }} data-envelope="{{ $member->envelope_number }}">
                                {{ $member->full_name }} ({{ $member->member_id }}) @if($member->envelope_number) [Env: {{ $member->envelope_number }}] @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Pledge Type - Full Width on Mobile -->
                <div class="col-6 col-md-3">
                    <label for="pledge_type" class="form-label small text-muted mb-1">
                        <i class="fas fa-tags me-1 text-info"></i>Pledge Type
                    </label>
                    <select class="form-select form-select-sm" id="pledge_type" name="pledge_type">
                        <option value="">All Types</option>
                        <option value="building" {{ request('pledge_type') == 'building' ? 'selected' : '' }}>Building Fund</option>
                        <option value="mission" {{ request('pledge_type') == 'mission' ? 'selected' : '' }}>Mission</option>
                        <option value="special" {{ request('pledge_type') == 'special' ? 'selected' : '' }}>Special Project</option>
                        <option value="general" {{ request('pledge_type') == 'general' ? 'selected' : '' }}>General</option>
                    </select>
                </div>
                
                <!-- Status - Full Width on Mobile -->
                <div class="col-6 col-md-3">
                    <label for="status" class="form-label small text-muted mb-1">
                        <i class="fas fa-info-circle me-1 text-success"></i>Status
                    </label>
                    <select class="form-select form-select-sm" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                    </select>
                </div>
                
                <!-- Action Buttons - Full Width on Mobile -->
                <div class="col-12 col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-search me-1"></i>
                        <span class="d-none d-sm-inline">Filter</span>
                        <span class="d-sm-none">Apply</span>
                    </button>
                    <a href="{{ route('finance.pledges') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>
                        <span class="d-none d-sm-inline">Clear</span>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Pledges Table -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-table me-1"></i><strong>Pledges List</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">Member</th>
                            <th class="d-table-cell d-md-none">Pledge</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th class="d-none d-lg-table-cell">Paid</th>
                            <th>Progress</th>
                            <th class="d-none d-xl-table-cell">Due Date</th>
                            <th>Status</th>
                            <th class="d-none d-lg-table-cell">Approval</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pledges as $pledge)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $pledge->member->full_name ?? 'Unknown' }}</div>
                                <div class="d-md-none">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-money-bill-wave me-1"></i>Paid: TZS {{ number_format($pledge->amount_paid, 0) }}
                                    </small>
                                    @if($pledge->due_date)
                                        <small class="text-muted d-block">
                                            <i class="fas fa-calendar me-1"></i>Due: {{ \Carbon\Carbon::parse($pledge->due_date)->format('M d, Y') }}
                                        </small>
                                    @endif
                                    <div class="mt-1">
                                        @if($pledge->approval_status == 'approved')
                                            <span class="badge bg-success badge-sm">
                                                <i class="fas fa-check me-1"></i>Approved
                                            </span>
                                        @elseif($pledge->approval_status == 'rejected')
                                            <span class="badge bg-danger badge-sm">
                                                <i class="fas fa-times me-1"></i>Rejected
                                            </span>
                                        @else
                                            <span class="badge bg-warning badge-sm">
                                                <i class="fas fa-clock me-1"></i>Pending
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($pledge->pledge_type) }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-primary">TZS {{ number_format($pledge->pledge_amount, 0) }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell text-end">TZS {{ number_format($pledge->amount_paid, 0) }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $pledge->progress_percentage >= 100 ? 'bg-success' : ($pledge->progress_percentage >= 75 ? 'bg-info' : 'bg-warning') }}" 
                                         style="width: {{ $pledge->progress_percentage }}%">
                                        {{ $pledge->progress_percentage }}%
                                    </div>
                                </div>
                            </td>
                            <td class="d-none d-xl-table-cell">{{ $pledge->due_date ? \Carbon\Carbon::parse($pledge->due_date)->format('M d, Y') : '-' }}</td>
                            <td>
                                @if($pledge->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($pledge->status == 'overdue')
                                    <span class="badge bg-danger">Overdue</span>
                                @else
                                    <span class="badge bg-primary">Active</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">
                                @if($pledge->approval_status == 'approved')
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Approved
                                    </span>
                                @elseif($pledge->approval_status == 'rejected')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times me-1"></i>Rejected
                                    </span>
                                @else
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary text-white"
                                        onclick="viewPledge(this)"
                                        data-id="{{ $pledge->id }}"
                                        data-member="{{ $pledge->member->full_name ?? 'Unknown' }}"
                                        data-type="{{ ucfirst($pledge->pledge_type) }}"
                                        data-amount="{{ number_format($pledge->pledge_amount, 2) }}"
                                        data-paid="{{ number_format($pledge->amount_paid, 2) }}"
                                        data-remaining="{{ number_format($pledge->remaining_amount, 2) }}"
                                        data-amount-raw="{{ $pledge->pledge_amount }}"
                                        data-paid-raw="{{ $pledge->amount_paid }}"
                                        data-progress="{{ $pledge->progress_percentage }}"
                                        data-due="{{ $pledge->due_date ? \Carbon\Carbon::parse($pledge->due_date)->format('M d, Y') : '-' }}"
                                        data-status="{{ ucfirst($pledge->status) }}"
                                        data-purpose="{{ $pledge->purpose }}"
                                        data-notes="{{ $pledge->notes }}"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                        <span class="d-none d-sm-inline ms-1">View</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-success text-white" onclick="addPayment({{ $pledge->id }})" title="Add Payment">
                                        <i class="fas fa-plus"></i>
                                        <span class="d-none d-sm-inline ms-1">Pay</span>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-warning text-white" onclick="editPledge({{ $pledge->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                        <span class="d-none d-sm-inline ms-1">Edit</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No pledges found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $pledges->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Pledge Modal -->
<div class="modal fade" id="addPledgeModal" tabindex="-1" aria-labelledby="addPledgeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPledgeModalLabel">Add New Pledge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('finance.pledges.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="envelope_lookup" class="form-label">Search by Envelope Number</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white"><i class="fas fa-envelope-open-text"></i></span>
                                    <input type="text" class="form-control" id="envelope_lookup" placeholder="Enter Envelope #">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="modal_member_id" class="form-label">Member *</label>
                                <select class="form-select select2-member-modal" id="modal_member_id" name="member_id" required>
                                    <option value="">Select Member</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}" data-envelope="{{ $member->envelope_number }}">
                                            {{ $member->full_name }} ({{ $member->member_id }}) @if($member->envelope_number) [Env: {{ $member->envelope_number }}] @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pledge_type" class="form-label">Pledge Type *</label>
                                <select class="form-select" id="pledge_type" name="pledge_type" required>
                                    <option value="">Select Type</option>
                                    <option value="building">Building Fund</option>
                                    <option value="mission">Mission</option>
                                    <option value="special">Special Project</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pledge_amount" class="form-label">Pledge Amount *</label>
                                <input type="number" class="form-control" id="pledge_amount" name="pledge_amount" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="payment_frequency" class="form-label">Payment Frequency *</label>
                                <select class="form-select" id="payment_frequency" name="payment_frequency" required>
                                    <option value="">Select Frequency</option>
                                    <option value="monthly">Monthly</option>
                                    <option value="quarterly">Quarterly</option>
                                    <option value="annually">Annually</option>
                                    <option value="one_time">One Time</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pledge_date" class="form-label">Pledge Date *</label>
                                <input type="date" class="form-control" id="pledge_date" name="pledge_date" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" class="form-control" id="due_date" name="due_date">
                                <div id="due_date_error" class="text-danger small mt-1" style="display: none;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="purpose" class="form-label">Purpose</label>
                        <input type="text" class="form-control" id="purpose" name="purpose">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Pledge</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Payment Modal -->
<div class="modal fade" id="addPaymentModal" tabindex="-1" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPaymentModalLabel">Add Payment to Pledge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addPaymentForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_amount" class="form-label">Payment Amount *</label>
                        <input type="number" class="form-control" id="payment_amount" name="payment_amount" step="0.01" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Payment Date *</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Add Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Pledge Modal -->
<div class="modal fade" id="viewPledgeModal" tabindex="-1" aria-labelledby="viewPledgeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-fullscreen-sm-down">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="viewPledgeModalLabel">
                    <i class="fas fa-handshake me-2"></i>Pledge Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="viewPledgeBody">
                <!-- Content will be populated by JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
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

// Initialize Select2 for member dropdowns
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
    @if(request('member_id') || request('pledge_type') || request('status'))
        if (window.innerWidth <= 768 && filterBody && filterIcon) {
            toggleFilters(); // Expand if filters are active
            const filterHeader = document.querySelector('.filter-header');
            if (filterHeader) filterHeader.classList.add('active');
        }
    @endif
    // Initialize Select2 for filter dropdown
    $('.select2-member').select2({
        placeholder: 'Search for a member...',
        allowClear: true,
        width: '100%'
    });
    
    // Initialize Select2 for modal dropdown
    $('.select2-member-modal').select2({
        placeholder: 'Search for a member...',
        allowClear: true,
        width: '100%',
        dropdownParent: $('#addPledgeModal')
    });
    
    // Reset validation when modal is opened
    $('#addPledgeModal').on('show.bs.modal', function() {
        const dueDate = document.getElementById('due_date');
        const dueDateError = document.getElementById('due_date_error');
        if (dueDate) {
            dueDate.classList.remove('is-invalid');
        }
        if (dueDateError) {
            dueDateError.style.display = 'none';
            dueDateError.textContent = '';
        }
    });
    
    // Validation for payment frequency and due date
    const paymentFrequency = document.getElementById('payment_frequency');
    const pledgeDate = document.getElementById('pledge_date');
    const dueDate = document.getElementById('due_date');
    const dueDateError = document.getElementById('due_date_error');
    const addPledgeForm = document.querySelector('#addPledgeModal form');
    
    // Function to validate due date based on payment frequency
    function validateDueDate() {
        const frequency = paymentFrequency.value;
        const pledgeDateValue = pledgeDate.value;
        const dueDateValue = dueDate.value;
        
        // Clear previous error
        dueDateError.style.display = 'none';
        dueDateError.textContent = '';
        dueDate.classList.remove('is-invalid');
        
        // Only validate if frequency is monthly and both dates are provided
        if (frequency === 'monthly' && pledgeDateValue && dueDateValue) {
            const pledgeDateObj = new Date(pledgeDateValue);
            const dueDateObj = new Date(dueDateValue);
            
            // Calculate the difference in days
            const timeDifference = dueDateObj.getTime() - pledgeDateObj.getTime();
            const daysDifference = Math.ceil(timeDifference / (1000 * 60 * 60 * 24));
            
            // Check if due date is within 30 days from pledge date
            if (daysDifference < 0) {
                // Due date is before pledge date
                dueDateError.textContent = 'The due date cannot be before the pledge date.';
                dueDateError.style.display = 'block';
                dueDate.classList.add('is-invalid');
                return false;
            } else if (daysDifference > 30) {
                // Due date exceeds 30 days from pledge date
                const maxDueDate = new Date(pledgeDateObj);
                maxDueDate.setDate(maxDueDate.getDate() + 30);
                const maxDueDateStr = maxDueDate.toLocaleDateString('en-US', { 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                
                dueDateError.textContent = `For monthly payment frequency, the due date must be within 30 days from the pledge date (maximum due date: ${maxDueDateStr}).`;
                dueDateError.style.display = 'block';
                dueDate.classList.add('is-invalid');
                return false;
            }
        }
        
        return true;
    }
    
    // Add event listeners
    if (paymentFrequency) {
        paymentFrequency.addEventListener('change', function() {
            validateDueDate();
        });
    }
    
    if (pledgeDate) {
        pledgeDate.addEventListener('change', function() {
            validateDueDate();
        });
    }
    
    if (dueDate) {
        dueDate.addEventListener('change', function() {
            validateDueDate();
        });
    }
    
    // Prevent form submission if validation fails
    if (addPledgeForm) {
        addPledgeForm.addEventListener('submit', function(e) {
            if (!validateDueDate()) {
                e.preventDefault();
                e.stopPropagation();
                
                // Show SweetAlert error
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please correct the due date. For monthly payment frequency, the due date must be within 30 days from the pledge date.',
                    confirmButtonText: 'OK'
                });
                
                return false;
            }
        });
    }

    // Envelope Number Lookup Logic
    const envelopeLookup = document.getElementById('envelope_lookup');
    const memberSelect = $('#modal_member_id');

    if (envelopeLookup) {
        envelopeLookup.addEventListener('change', function() {
            const val = this.value.trim();
            if (!val) return;

            let found = false;
            memberSelect.find('option').each(function() {
                const env = $(this).data('envelope');
                if (env && env.toString() === val) {
                    memberSelect.val($(this).val()).trigger('change');
                    found = true;
                    return false; // break
                }
            });

            if (found) {
                envelopeLookup.classList.remove('is-invalid');
                envelopeLookup.classList.add('is-valid');
            } else {
                envelopeLookup.classList.add('is-invalid');
                envelopeLookup.classList.remove('is-valid');
            }
        });
    }
});

function viewPledge(button) {
    if (!button) return;
    const data = button.dataset;

    // Calculate remaining amount
    let remainingDisplay = data.remaining;
    if (!remainingDisplay) {
        const amountRaw = parseFloat(data.amountRaw || data.amountRaw === '0' ? data.amountRaw : (data.amount || '0').toString().replace(/,/g, ''));
        const paidRaw = parseFloat(data.paidRaw || data.paidRaw === '0' ? data.paidRaw : (data.paid || '0').toString().replace(/,/g, ''));
        const rem = (isNaN(amountRaw) ? 0 : amountRaw) - (isNaN(paidRaw) ? 0 : paidRaw);
        remainingDisplay = rem.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    // Calculate progress
    const progress = parseFloat(data.progress || '0');
    const pct = isNaN(progress) ? 0 : Math.max(0, Math.min(100, progress));
    const progressClass = pct >= 100 ? 'success' : (pct >= 75 ? 'info' : 'warning');

    // Status styling
    const statusClass = data.status.toLowerCase() === 'completed' ? 'success' : 
                       data.status.toLowerCase() === 'active' ? 'primary' : 
                       data.status.toLowerCase() === 'overdue' ? 'danger' : 'secondary';

    // Pledge type styling
    const typeClass = data.type.toLowerCase() === 'building' ? 'primary' :
                     data.type.toLowerCase() === 'mission' ? 'info' :
                     data.type.toLowerCase() === 'special' ? 'warning' :
                     data.type.toLowerCase() === 'general' ? 'success' : 'secondary';

    const html = `
        <div class="row g-4">
            <!-- Pledge Overview Cards -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-handshake fa-2x mb-2"></i>
                                <h6 class="card-title">Pledge Amount</h6>
                                <h4 class="mb-0">TZS ${data.amount || '0.00'}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h6 class="card-title">Amount Paid</h6>
                                <h4 class="mb-0">TZS ${data.paid || '0.00'}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-piggy-bank fa-2x mb-2"></i>
                                <h6 class="card-title">Remaining</h6>
                                <h4 class="mb-0">TZS ${remainingDisplay}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-${progressClass} text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-pie fa-2x mb-2"></i>
                                <h6 class="card-title">Progress</h6>
                                <h4 class="mb-0">${pct.toFixed(1)}%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Pledge Progress</h6>
                    </div>
                    <div class="card-body">
                        <div class="progress" style="height: 30px;">
                            <div class="progress-bar bg-${progressClass}" style="width: ${pct}%" role="progressbar">
                                <span class="fw-bold">${pct.toFixed(1)}% Complete</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Pledge Details -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Pledge Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted">Member</small>
                                        <div class="fw-bold">${data.member || '-'}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tag text-${typeClass} me-3"></i>
                                    <div>
                                        <small class="text-muted">Pledge Type</small>
                                        <div class="fw-bold">
                                            <span class="badge bg-${typeClass}">${data.type || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar text-warning me-3"></i>
                                    <div>
                                        <small class="text-muted">Due Date</small>
                                        <div class="fw-bold">${data.due || '-'}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-flag text-${statusClass} me-3"></i>
                                    <div>
                                        <small class="text-muted">Status</small>
                                        <div class="fw-bold">
                                            <span class="badge bg-${statusClass}">${data.status || '-'}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Purpose and Notes -->
            ${(data.purpose && data.purpose !== '-') || (data.notes && data.notes !== '-') ? `
            <div class="col-12">
                <div class="row g-3">
                    ${data.purpose && data.purpose !== '-' ? `
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Purpose</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">${data.purpose}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    ${data.notes && data.notes !== '-' ? `
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">${data.notes}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
            </div>
            ` : ''}
        </div>
    `;
    
    document.getElementById('viewPledgeBody').innerHTML = html;
    const modal = new bootstrap.Modal(document.getElementById('viewPledgeModal'));
    modal.show();
}

function addPayment(id) {
    document.getElementById('addPaymentForm').action = `/finance/pledges/${id}/payment`;
    const modal = new bootstrap.Modal(document.getElementById('addPaymentModal'));
    modal.show();
}

function editPledge(id) {
    // Implementation for editing pledge
    console.log('Edit pledge:', id);
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// SweetAlert for success messages
@if(session('success'))
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: '{{ session('success') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });
@endif

@if(session('error'))
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: '{{ session('error') }}',
            timer: 3000,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });
@endif
</script>
@endsection
