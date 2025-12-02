@extends('layouts.index')

@section('content')
@if(session('success'))
    <script>
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
    </script>
@endif

@if(session('error'))
    <script>
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
    </script>
@endif

<style>
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
                <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-wallet me-2"></i>Budgets Management</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addBudgetModal">
                    <i class="fas fa-plus me-1"></i>
                    <span class="d-none d-sm-inline">Add Budget</span>
                    <span class="d-sm-none">Add</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('finance.budgets') }}" class="card mb-4 border-0 shadow-sm" id="filtersForm">
        <!-- Filter Header -->
        <div class="card-header bg-primary text-white p-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter me-1"></i>
                    <span class="fw-semibold">Filters</span>
                    @if(request('fiscal_year') || request('budget_type') || request('status'))
                        <span class="badge bg-white text-primary rounded-pill ms-2" id="activeFiltersCount">{{ (request('fiscal_year') ? 1 : 0) + (request('budget_type') ? 1 : 0) + (request('status') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-2">
                <!-- Fiscal Year - Full Width on Mobile -->
                <div class="col-6 col-md-3">
                    <label for="fiscal_year" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar me-1 text-primary"></i>Fiscal Year
                    </label>
                    <select class="form-select form-select-sm" id="fiscal_year" name="fiscal_year">
                        <option value="">All Years</option>
                        @for($year = date('Y') - 2; $year <= date('Y') + 2; $year++)
                            <option value="{{ $year }}" {{ request('fiscal_year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endfor
                    </select>
                </div>
                
                <!-- Budget Type - Full Width on Mobile -->
                <div class="col-6 col-md-3">
                    <label for="budget_type" class="form-label small text-muted mb-1">
                        <i class="fas fa-tags me-1 text-info"></i>Budget Type
                    </label>
                    <select class="form-select form-select-sm" id="budget_type" name="budget_type">
                        <option value="">All Types</option>
                        <option value="operational" {{ request('budget_type') == 'operational' ? 'selected' : '' }}>Operational</option>
                        <option value="capital" {{ request('budget_type') == 'capital' ? 'selected' : '' }}>Capital</option>
                        <option value="program" {{ request('budget_type') == 'program' ? 'selected' : '' }}>Program</option>
                        <option value="special" {{ request('budget_type') == 'special' ? 'selected' : '' }}>Special</option>
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
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                
                <!-- Action Buttons - Full Width on Mobile -->
                <div class="col-6 col-md-3 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-search me-1"></i>
                        <span class="d-none d-sm-inline">Filter</span>
                        <span class="d-sm-none">Apply</span>
                    </button>
                    <a href="{{ route('finance.budgets') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-times me-1"></i>
                        <span class="d-none d-sm-inline">Clear</span>
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Budgets Table -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-table me-1"></i><strong>Budgets List</strong>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">Budget Name</th>
                            <th class="d-table-cell d-md-none">Budget</th>
                            <th>Type</th>
                            <th class="d-none d-lg-table-cell">Fiscal Year</th>
                            <th>Total Budget</th>
                            <th class="d-none d-xl-table-cell">Spent</th>
                            <th class="d-none d-xl-table-cell">Remaining</th>
                            <th>Utilization</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($budgets as $budget)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $budget->budget_name }}</div>
                                <div class="d-md-none">
                                    <small class="text-muted d-block">
                                        <i class="fas fa-calendar me-1"></i>FY: {{ $budget->fiscal_year }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-money-bill-wave me-1"></i>Spent: TZS {{ number_format($budget->spent_amount, 0) }}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="fas fa-piggy-bank me-1"></i>Remaining: TZS {{ number_format($budget->remaining_amount, 0) }}
                                    </small>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($budget->budget_type) }}</span>
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $budget->fiscal_year }}</td>
                            <td class="text-end">
                                <span class="fw-bold text-primary">TZS {{ number_format($budget->total_budget, 0) }}</span>
                            </td>
                            <td class="d-none d-xl-table-cell text-end">TZS {{ number_format($budget->spent_amount, 0) }}</td>
                            <td class="d-none d-xl-table-cell text-end">TZS {{ number_format($budget->remaining_amount, 0) }}</td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar {{ $budget->is_over_budget ? 'bg-danger' : ($budget->is_near_limit ? 'bg-warning' : 'bg-success') }}" 
                                         style="width: {{ $budget->utilization_percentage }}%">
                                        {{ $budget->utilization_percentage }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($budget->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($budget->status == 'completed')
                                    <span class="badge bg-primary">Completed</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-primary text-white"
                                        onclick="viewBudget(this)"
                                        data-id="{{ $budget->id }}"
                                        data-name="{{ $budget->budget_name }}"
                                        data-type="{{ ucfirst($budget->budget_type) }}"
                                        data-fy="{{ $budget->fiscal_year }}"
                                        data-total="{{ number_format($budget->total_budget, 2) }}"
                                        data-spent="{{ number_format($budget->spent_amount, 2) }}"
                                        data-remaining="{{ number_format($budget->remaining_amount, 2) }}"
                                        data-utilization="{{ $budget->utilization_percentage }}"
                                        data-status="{{ ucfirst($budget->status) }}"
                                        data-start="{{ $budget->start_date }}"
                                        data-end="{{ $budget->end_date }}"
                                        data-description="{{ $budget->description ?? '-' }}"
                                        title="View Details"
                                    >
                                        <i class="fas fa-eye"></i>
                                        <span class="d-none d-sm-inline ms-1">View</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        class="btn btn-sm btn-outline-warning text-white"
                                        onclick="editBudget(this)"
                                        data-id="{{ $budget->id }}"
                                        data-name="{{ $budget->budget_name }}"
                                        data-type="{{ $budget->budget_type }}"
                                        data-fy="{{ $budget->fiscal_year }}"
                                        data-total="{{ $budget->total_budget }}"
                                        data-start="{{ $budget->start_date }}"
                                        data-end="{{ $budget->end_date }}"
                                        data-status="{{ $budget->status }}"
                                        data-description="{{ $budget->description ?? '' }}"
                                        title="Edit"
                                    >
                                        <i class="fas fa-edit"></i>
                                        <span class="d-none d-sm-inline ms-1">Edit</span>
                                    </button>
                                    <form class="d-inline" onsubmit="return confirmDeleteBudget(event, {{ $budget->id }})" method="POST" action="{{ route('finance.budgets.destroy', $budget) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger text-white" title="Delete">
                                            <i class="fas fa-trash"></i>
                                            <span class="d-none d-sm-inline ms-1">Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No budgets found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $budgets->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Add Budget Modal -->
<div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content budget-modal-content">
            <div class="modal-header budget-modal-header">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-wrapper me-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="addBudgetModalLabel">Add New Budget</h5>
                        <small class="text-white-50">Create a new budget for your financial planning</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('finance.budgets.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="budget_name" class="form-label">Budget Name *</label>
                                <input type="text" class="form-control" id="budget_name" name="budget_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="budget_type" class="form-label">Budget Type *</label>
                                <select class="form-select" id="budget_type" name="budget_type" required>
                                    <option value="">Select Type</option>
                                    <option value="operational">Operational</option>
                                    <option value="capital">Capital</option>
                                    <option value="program">Program</option>
                                    <option value="special">Special</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Custom Budget Type Field (shown when "Other" is selected) -->
                    <div class="row" id="custom_budget_type_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_budget_type" class="form-label">Custom Budget Type *</label>
                                <input type="text" class="form-control" id="custom_budget_type" name="custom_budget_type" placeholder="Enter custom budget type">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Custom Purpose Field (shown when "Other" is selected) -->
                    <div class="row" id="custom_purpose_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_purpose" class="form-label">Custom Purpose *</label>
                                <input type="text" class="form-control" id="custom_purpose" name="custom_purpose" placeholder="e.g., Christmas, Easter, etc.">
                                <small class="text-muted">This will be used to match with custom offering types</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="purpose" class="form-label">Purpose *</label>
                                <select class="form-select" id="purpose" name="purpose" required>
                                    <option value="">Select Purpose</option>
                                    <option value="building">Building Fund</option>
                                    <option value="ministry">Ministry</option>
                                    <option value="operations">Operations</option>
                                    <option value="special_events">Special Events</option>
                                    <option value="thanksgiving">Thanksgiving</option>
                                    <option value="missions">Missions</option>
                                    <option value="youth">Youth Ministry</option>
                                    <option value="children">Children Ministry</option>
                                    <option value="worship">Worship</option>
                                    <option value="outreach">Outreach</option>
                                    <option value="other">Other (Custom)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Primary Funding Source</label>
                                <div class="form-control-plaintext" id="primary_funding_display">
                                    <small class="text-muted">Will be automatically selected based on purpose</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Fund Summary Section (shows when purpose is selected) -->
                    <div id="budget_fund_summary_section" style="display: none;">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-chart-pie me-1"></i>Fund Summary
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="text-center p-3 border rounded bg-success bg-opacity-10">
                                            <h5 class="text-success mb-1" id="budget_fund_total_income">TZS 0</h5>
                                            <small class="text-muted">Total Income</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 border rounded bg-warning bg-opacity-10">
                                            <h5 class="text-warning mb-1" id="budget_fund_used_amount">TZS 0</h5>
                                            <small class="text-muted">Used Amount</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="text-center p-3 border rounded" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                            <h5 class="mb-1" id="budget_fund_available_amount" style="color: #fff; font-weight: bold;">TZS 0</h5>
                                            <small style="color: rgba(255, 255, 255, 0.9);">Available Amount</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fiscal_year" class="form-label">Fiscal Year *</label>
                                <select class="form-select" id="fiscal_year" name="fiscal_year" required>
                                    <option value="">Select Year</option>
                                    @for($year = date('Y') - 1; $year <= date('Y') + 2; $year++)
                                        <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="total_budget" class="form-label">Total Budget *</label>
                                <input type="number" class="form-control" id="total_budget" name="total_budget" step="0.01" min="0" required>
                                <small class="text-muted" id="total_budget_hint">Enter total budget amount</small>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Budget Line Items Section (for Special Events and Celebrations) -->
                    <div id="budget_line_items_section" style="display: none;">
                        <div class="card mt-3">
                            <div class="card-header">
                                <h6 class="mb-0">
                                    <i class="fas fa-list me-1"></i>Budget Breakdown
                                    <button type="button" class="btn btn-sm btn-success float-end" id="addLineItemBtn">
                                        <i class="fas fa-plus me-1"></i>Add Item
                                    </button>
                                </h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small mb-3">Break down your budget into items with responsible persons. The total will be calculated automatically.</p>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="width: 35%;">Item Name *</th>
                                                <th style="width: 15%;">Amount (TZS) *</th>
                                                <th style="width: 25%;">Responsible Person *</th>
                                                <th style="width: 20%;">Notes</th>
                                                <th style="width: 5%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lineItemsContainer">
                                            <!-- Line items will be added here dynamically -->
                                        </tbody>
                                        <tfoot class="table-info">
                                            <tr>
                                                <th colspan="4" class="text-end">Total:</th>
                                                <th class="text-end" id="lineItemsTotal">TZS 0.00</th>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted" id="lineItemsTotalHint">Add items to calculate total</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="start_date" class="form-label">Start Date *</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="end_date" class="form-label">End Date *</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer budget-modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary budget-submit-btn">
                        <i class="fas fa-check me-1"></i>Add Budget
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Budget Modal -->
<div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
        <div class="modal-content budget-modal-content">
            <div class="modal-header budget-modal-header">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-wrapper me-3">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title mb-0" id="editBudgetModalLabel">Edit Budget</h5>
                        <small class="text-white-50">Update budget information</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editBudgetForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_budget_name" class="form-label">Budget Name *</label>
                                <input type="text" class="form-control" id="eb_budget_name" name="budget_name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_budget_type" class="form-label">Budget Type *</label>
                                <select class="form-select" id="eb_budget_type" name="budget_type" required>
                                    <option value="operational">Operational</option>
                                    <option value="capital">Capital</option>
                                    <option value="program">Program</option>
                                    <option value="special">Special</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Custom Budget Type Field for Edit Modal (shown when "Other" is selected) -->
                    <div class="row" id="eb_custom_budget_type_row" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_custom_budget_type" class="form-label">Custom Budget Type *</label>
                                <input type="text" class="form-control" id="eb_custom_budget_type" name="custom_budget_type" placeholder="Enter custom budget type">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_fiscal_year" class="form-label">Fiscal Year *</label>
                                <input type="number" class="form-control" id="eb_fiscal_year" name="fiscal_year" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_total_budget" class="form-label">Total Budget *</label>
                                <input type="number" class="form-control" id="eb_total_budget" name="total_budget" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_start_date" class="form-label">Start Date *</label>
                                <input type="date" class="form-control" id="eb_start_date" name="start_date" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_end_date" class="form-label">End Date *</label>
                                <input type="date" class="form-control" id="eb_end_date" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_status" class="form-label">Status *</label>
                                <select class="form-select" id="eb_status" name="status" required>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="eb_description" class="form-label">Description</label>
                                <textarea class="form-control" id="eb_description" name="description" rows="2"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer budget-modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary budget-submit-btn">
                        <i class="fas fa-save me-1"></i>Save Changes
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

function viewBudget(button) {
    if (!button) return;
    var d = button.dataset;
    var statusClass = d.status.toLowerCase() === 'active' ? 'success' : 
                     d.status.toLowerCase() === 'completed' ? 'primary' : 'secondary';
    var utilizationClass = parseFloat(d.utilization) > 80 ? 'danger' : 
                          parseFloat(d.utilization) > 60 ? 'warning' : 'success';
    
    var html = `
        <div class="row g-4">
            <!-- Budget Overview Cards -->
            <div class="col-12">
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-wallet fa-2x mb-2"></i>
                                <h6 class="card-title">Total Budget</h6>
                                <h4 class="mb-0">TZS ${d.total}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-chart-line fa-2x mb-2"></i>
                                <h6 class="card-title">Amount Spent</h6>
                                <h4 class="mb-0">TZS ${d.spent}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-piggy-bank fa-2x mb-2"></i>
                                <h6 class="card-title">Remaining</h6>
                                <h4 class="mb-0">TZS ${d.remaining}</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-${utilizationClass} text-white h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-percentage fa-2x mb-2"></i>
                                <h6 class="card-title">Utilization</h6>
                                <h4 class="mb-0">${d.utilization}%</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Budget Details -->
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Budget Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-tag text-primary me-3"></i>
                                    <div>
                                        <small class="text-muted">Budget Name</small>
                                        <div class="fw-bold">${d.name}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-layer-group text-info me-3"></i>
                                    <div>
                                        <small class="text-muted">Budget Type</small>
                                        <div class="fw-bold">${d.type}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-alt text-warning me-3"></i>
                                    <div>
                                        <small class="text-muted">Fiscal Year</small>
                                        <div class="fw-bold">${d.fy}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-flag text-${statusClass} me-3"></i>
                                    <div>
                                        <small class="text-muted">Status</small>
                                        <div class="fw-bold">
                                            <span class="badge bg-${statusClass}">${d.status}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-play-circle text-success me-3"></i>
                                    <div>
                                        <small class="text-muted">Start Date</small>
                                        <div class="fw-bold">${d.start}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-stop-circle text-danger me-3"></i>
                                    <div>
                                        <small class="text-muted">End Date</small>
                                        <div class="fw-bold">${d.end}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            ${d.description && d.description !== '-' ? `
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-align-left me-2"></i>Description</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${d.description}</p>
                    </div>
                </div>
            </div>
            ` : ''}
            
            <!-- Budget Line Items (for celebrations/events) -->
            <div class="col-12" id="budgetLineItemsSection">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0"><i class="fas fa-list me-2"></i>Budget Breakdown</h6>
                    </div>
                    <div class="card-body">
                        <div id="lineItemsLoading" class="text-center py-3">
                            <i class="fas fa-spinner fa-spin me-2"></i>Loading items...
                        </div>
                        <div id="lineItemsContent" style="display: none;">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th class="text-end">Amount</th>
                                            <th>Responsible Person</th>
                                            <th>Notes</th>
                                        </tr>
                                    </thead>
                                    <tbody id="lineItemsTableBody">
                                        <!-- Line items will be loaded here -->
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-info">
                                            <th>Total</th>
                                            <th class="text-end" id="lineItemsTotalFooter">TZS 0.00</th>
                                            <th colspan="2"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div id="lineItemsEmpty" style="display: none;">
                            <p class="text-muted text-center mb-0">No line items for this budget.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    // Create modal if not exists
    var modal = document.getElementById('viewBudgetModal');
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'viewBudgetModal';
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg modal-fullscreen-sm-down">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Budget Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body" id="vb_body">
                        ${html}
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(modal);
    }
    document.getElementById('vb_body').innerHTML = html;
    
    // Show modal and load line items after modal is shown
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    // Load line items after modal is fully shown
    modal.addEventListener('shown.bs.modal', function() {
        loadBudgetLineItems(d.id);
    }, { once: true });
}

function loadBudgetLineItems(budgetId) {
    const loadingDiv = document.getElementById('lineItemsLoading');
    const contentDiv = document.getElementById('lineItemsContent');
    const emptyDiv = document.getElementById('lineItemsEmpty');
    const tableBody = document.getElementById('lineItemsTableBody');
    const totalFooter = document.getElementById('lineItemsTotalFooter');
    
    if (!loadingDiv || !contentDiv || !emptyDiv || !tableBody || !totalFooter) return;
    
    fetch(`/finance/budgets/${budgetId}/line-items`)
        .then(response => response.json())
        .then(data => {
            loadingDiv.style.display = 'none';
            
            if (data.success && data.line_items && data.line_items.length > 0) {
                contentDiv.style.display = 'block';
                emptyDiv.style.display = 'none';
                
                tableBody.innerHTML = '';
                let total = 0;
                
                data.line_items.forEach(item => {
                    total += parseFloat(item.amount) || 0;
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${item.item_name}</td>
                        <td class="text-end">TZS ${parseFloat(item.amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}</td>
                        <td><span class="badge bg-info">${item.responsible_person}</span></td>
                        <td>${item.notes || '-'}</td>
                    `;
                    tableBody.appendChild(row);
                });
                
                totalFooter.textContent = 'TZS ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
            } else {
                contentDiv.style.display = 'none';
                emptyDiv.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading line items:', error);
            loadingDiv.style.display = 'none';
            contentDiv.style.display = 'none';
            emptyDiv.style.display = 'block';
        });
}

function editBudget(button) {
    if (!button) return;
    var d = button.dataset;
    var form = document.getElementById('editBudgetForm');
    if (!form) return;
    form.action = '/finance/budgets/' + d.id;
    document.getElementById('eb_budget_name').value = d.name || '';
    
    // Handle budget type - check if it's a standard type or custom
    var budgetType = d.type || '';
    var standardTypes = ['operational', 'capital', 'program', 'special'];
    var ebBudgetTypeSelect = document.getElementById('eb_budget_type');
    var ebCustomBudgetTypeRow = document.getElementById('eb_custom_budget_type_row');
    var ebCustomBudgetTypeInput = document.getElementById('eb_custom_budget_type');
    
    if (standardTypes.includes(budgetType.toLowerCase())) {
        ebBudgetTypeSelect.value = budgetType.toLowerCase();
        if (ebCustomBudgetTypeRow) {
            ebCustomBudgetTypeRow.style.display = 'none';
        }
        if (ebCustomBudgetTypeInput) {
            ebCustomBudgetTypeInput.value = '';
            ebCustomBudgetTypeInput.required = false;
        }
    } else if (budgetType) {
        // It's a custom type
        ebBudgetTypeSelect.value = 'other';
        if (ebCustomBudgetTypeRow) {
            ebCustomBudgetTypeRow.style.display = 'flex';
        }
        if (ebCustomBudgetTypeInput) {
            // Convert underscore format back to readable format
            ebCustomBudgetTypeInput.value = budgetType.replace(/_/g, ' ').replace(/\b\w/g, function(l) {
                return l.toUpperCase();
            });
            ebCustomBudgetTypeInput.required = true;
        }
    } else {
        ebBudgetTypeSelect.value = '';
    }
    
    document.getElementById('eb_fiscal_year').value = d.fy || '';
    document.getElementById('eb_total_budget').value = d.total || 0;
    document.getElementById('eb_start_date').value = d.start || '';
    document.getElementById('eb_end_date').value = d.end || '';
    document.getElementById('eb_status').value = d.status ? d.status.toLowerCase() : 'active';
    document.getElementById('eb_description').value = d.description || '';
    new bootstrap.Modal(document.getElementById('editBudgetModal')).show();
}

function confirmDeleteBudget(e, id) {
    e.preventDefault();
    
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this action!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        customClass: {
            confirmButton: 'btn btn-danger',
            cancelButton: 'btn btn-secondary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) {
            // Submit the form
            e.target.closest('form').submit();
        }
    });
    
    return false;
}

// Auto-hide alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        const bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);

// Funding allocation functionality
const offeringTypeMapping = {
    'building': 'building_fund',
    'ministry': 'general',
    'operations': 'general',
    'special_events': 'special',
    'thanksgiving': 'thanksgiving',
    'missions': 'general',
    'youth': 'general',
    'children': 'general',
    'worship': 'general',
    'outreach': 'general'
};

// Funding allocation functionality removed - budgets no longer require additional funding during planning
// Additional funding is only available when creating expenses

// Handle custom budget type field using event delegation
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
    @if(request('fiscal_year') || request('budget_type') || request('status'))
        if (window.innerWidth <= 768 && filterBody && filterIcon) {
            toggleFilters(); // Expand if filters are active
            const filterHeader = document.querySelector('.filter-header');
            if (filterHeader) filterHeader.classList.add('active');
        }
    @endif
    // Use event delegation to handle budget type changes
    document.addEventListener('change', function(e) {
        // Handle Add Budget Modal - Budget Type
        if (e.target && e.target.id === 'budget_type') {
            const customBudgetTypeRow = document.getElementById('custom_budget_type_row');
            const customBudgetTypeInput = document.getElementById('custom_budget_type');
            
            if (customBudgetTypeRow && customBudgetTypeInput) {
                if (e.target.value === 'other') {
                    customBudgetTypeRow.style.display = 'block';
                    customBudgetTypeInput.required = true;
                } else {
                    customBudgetTypeRow.style.display = 'none';
                    customBudgetTypeInput.required = false;
                    customBudgetTypeInput.value = '';
                }
            }
        }
        
        // Handle Add Budget Modal - Purpose
        if (e.target && e.target.id === 'purpose') {
            const customPurposeRow = document.getElementById('custom_purpose_row');
            const customPurposeInput = document.getElementById('custom_purpose');
            const primaryFundingDisplay = document.getElementById('primary_funding_display');
            const lineItemsSection = document.getElementById('budget_line_items_section');
            
            if (customPurposeRow && customPurposeInput) {
                if (e.target.value === 'other') {
                    customPurposeRow.style.display = 'block';
                    customPurposeInput.required = true;
                    if (primaryFundingDisplay) {
                        primaryFundingDisplay.innerHTML = '<small class="text-muted">Enter custom purpose to see suggested offering type</small>';
                    }
                    // Show line items section for custom purposes (like Christmas, Easter)
                    if (lineItemsSection) {
                        lineItemsSection.style.display = 'block';
                    }
                    // Hide fund summary until custom purpose is entered
                    hideBudgetFundSummary();
                } else if (e.target.value === 'special_events') {
                    customPurposeRow.style.display = 'none';
                    customPurposeInput.required = false;
                    customPurposeInput.value = '';
                    // Show line items section for special events
                    if (lineItemsSection) {
                        lineItemsSection.style.display = 'block';
                    }
                    
                    // Update primary funding display
                    if (primaryFundingDisplay) {
                        const purpose = e.target.value;
                        const primaryType = offeringTypeMapping[purpose] || 'general';
                        primaryFundingDisplay.innerHTML = `<span class="badge bg-primary">${primaryType.replace('_', ' ').toUpperCase()}</span>`;
                    }
                    
                    // Fetch and display fund summary for this offering type
                    fetchOfferingTypeFundSummary(offeringTypeMapping[e.target.value] || 'general');
                } else {
                    customPurposeRow.style.display = 'none';
                    customPurposeInput.required = false;
                    customPurposeInput.value = '';
                    // Hide line items section for other purposes
                    if (lineItemsSection) {
                        lineItemsSection.style.display = 'none';
                        // Clear line items
                        const container = document.getElementById('lineItemsContainer');
                        if (container) {
                            container.innerHTML = '';
                            updateLineItemsTotal();
                        }
                    }
                    
                    // Update primary funding display
                    if (primaryFundingDisplay) {
                        const purpose = e.target.value;
                        const primaryType = offeringTypeMapping[purpose] || 'general';
                        primaryFundingDisplay.innerHTML = `<span class="badge bg-primary">${primaryType.replace('_', ' ').toUpperCase()}</span>`;
                    }
                    
                    // Fetch and display fund summary for this offering type
                    fetchOfferingTypeFundSummary(offeringTypeMapping[e.target.value] || 'general');
                }
            }
        }
        
        // Handle custom purpose input - show line items for celebrations
        if (e.target && e.target.id === 'custom_purpose') {
            const primaryFundingDisplay = document.getElementById('primary_funding_display');
            const lineItemsSection = document.getElementById('budget_line_items_section');
            const fundSummarySection = document.getElementById('budget_fund_summary_section');
            
            if (primaryFundingDisplay && e.target.value.trim()) {
                const customPurpose = e.target.value.trim().toLowerCase().replace(/[^a-z0-9]/g, '_');
                primaryFundingDisplay.innerHTML = `<span class="badge bg-info">Will match: ${customPurpose}</span>`;
                
                // Fetch and display fund summary for custom offering type
                fetchOfferingTypeFundSummary(customPurpose);
            }
            
            // Show line items section for custom purposes (assumed to be celebrations/events)
            if (lineItemsSection && e.target.value.trim()) {
                lineItemsSection.style.display = 'block';
            }
            
            // Show fund summary section
            if (fundSummarySection && e.target.value.trim()) {
                fundSummarySection.style.display = 'block';
            }
        }
        
        // Handle Edit Budget Modal
        if (e.target && e.target.id === 'eb_budget_type') {
            const ebCustomBudgetTypeRow = document.getElementById('eb_custom_budget_type_row');
            const ebCustomBudgetTypeInput = document.getElementById('eb_custom_budget_type');
            
            if (ebCustomBudgetTypeRow && ebCustomBudgetTypeInput) {
                if (e.target.value === 'other') {
                    ebCustomBudgetTypeRow.style.display = 'block';
                    ebCustomBudgetTypeInput.required = true;
                } else {
                    ebCustomBudgetTypeRow.style.display = 'none';
                    ebCustomBudgetTypeInput.required = false;
                    ebCustomBudgetTypeInput.value = '';
                }
            }
        }
    });
    
    // Budget Line Items Management
    let lineItemIndex = 0;
    
    // Add Line Item Button - use event delegation since button is in modal
    document.addEventListener('click', function(e) {
        if (e.target && (e.target.id === 'addLineItemBtn' || e.target.closest('#addLineItemBtn'))) {
            e.preventDefault();
            addLineItem();
        }
        
        // Handle remove line item buttons
        if (e.target && e.target.closest('.remove-line-item')) {
            e.preventDefault();
            const btn = e.target.closest('.remove-line-item');
            const row = btn.closest('.line-item-row');
            if (row) {
                row.remove();
                updateLineItemsTotal();
            }
        }
    });
    
    function addLineItem(itemName = '', amount = '', responsiblePerson = '', notes = '') {
        const container = document.getElementById('lineItemsContainer');
        if (!container) return;
        
        // Create table row
        const row = document.createElement('tr');
        row.className = 'line-item-row';
        row.dataset.index = lineItemIndex;
        row.innerHTML = `
            <td>
                <input type="text" class="form-control form-control-sm line-item-name" name="line_items[${lineItemIndex}][item_name]" 
                       value="${itemName}" placeholder="e.g., Rice, Cooking Oil" required>
            </td>
            <td>
                <input type="number" class="form-control form-control-sm line-item-amount text-end" name="line_items[${lineItemIndex}][amount]" 
                       value="${amount}" step="0.01" min="0" placeholder="0.00" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm line-item-person" name="line_items[${lineItemIndex}][responsible_person]" 
                       value="${responsiblePerson}" placeholder="e.g., Often, Gee" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm line-item-notes" name="line_items[${lineItemIndex}][notes]" 
                       value="${notes}" placeholder="Optional">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-line-item" title="Remove">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        container.appendChild(row);
        lineItemIndex++;
        
        // Add event listener for amount input to update total
        const amountInput = row.querySelector('.line-item-amount');
        if (amountInput) {
            amountInput.addEventListener('input', updateLineItemsTotal);
        }
        
        updateLineItemsTotal();
    }
    
    function updateLineItemsTotal() {
        const container = document.getElementById('lineItemsContainer');
        const totalElement = document.getElementById('lineItemsTotal');
        const totalBudgetInput = document.getElementById('total_budget');
        const totalBudgetHint = document.getElementById('total_budget_hint');
        
        if (!container || !totalElement) return;
        
        let total = 0;
        const amountInputs = container.querySelectorAll('.line-item-amount');
        amountInputs.forEach(input => {
            const value = parseFloat(input.value) || 0;
            total += value;
        });
        
        totalElement.textContent = 'TZS ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Auto-fill total budget if line items exist
        if (totalBudgetInput && amountInputs.length > 0) {
            totalBudgetInput.value = total.toFixed(2);
            if (totalBudgetHint) {
                totalBudgetHint.textContent = 'Auto-calculated from line items';
                totalBudgetHint.className = 'text-success';
            }
        } else if (totalBudgetHint && amountInputs.length === 0) {
            totalBudgetHint.textContent = 'Enter total budget amount';
            totalBudgetHint.className = 'text-muted';
        }
    }
    
    // Use event delegation for dynamically added line items amount inputs
    document.addEventListener('input', function(e) {
        if (e.target && e.target.classList.contains('line-item-amount')) {
            updateLineItemsTotal();
        }
    });
    
    // Function to fetch and display fund summary for an offering type
    function fetchOfferingTypeFundSummary(offeringType) {
        if (!offeringType) {
            hideBudgetFundSummary();
            return;
        }
        
        const fundSummarySection = document.getElementById('budget_fund_summary_section');
        if (!fundSummarySection) return;
        
        // Show loading state
        fundSummarySection.style.display = 'block';
        document.getElementById('budget_fund_total_income').textContent = 'Loading...';
        document.getElementById('budget_fund_used_amount').textContent = 'Loading...';
        document.getElementById('budget_fund_available_amount').textContent = 'Loading...';
        
        fetch(`/finance/budgets/offering-type-fund-summary?offering_type=${encodeURIComponent(offeringType)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.fund_summary) {
                    displayBudgetFundSummary(data.fund_summary);
                } else {
                    hideBudgetFundSummary();
                }
            })
            .catch(error => {
                console.error('Error fetching fund summary:', error);
                hideBudgetFundSummary();
            });
    }
    
    function displayBudgetFundSummary(summary) {
        const fundSummarySection = document.getElementById('budget_fund_summary_section');
        if (!fundSummarySection) return;
        
        fundSummarySection.style.display = 'block';
        
        document.getElementById('budget_fund_total_income').textContent = 'TZS ' + summary.total_income.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('budget_fund_used_amount').textContent = 'TZS ' + summary.total_committed.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('budget_fund_available_amount').textContent = 'TZS ' + summary.available_amount.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    
    function hideBudgetFundSummary() {
        const fundSummarySection = document.getElementById('budget_fund_summary_section');
        if (fundSummarySection) {
            fundSummarySection.style.display = 'none';
        }
    }
    
    // Handle form submission for Add Budget Modal
    const addBudgetForm = document.querySelector('#addBudgetModal form');
    if (addBudgetForm) {
        addBudgetForm.addEventListener('submit', function(e) {
            const budgetTypeSelect = document.getElementById('budget_type');
            const customBudgetTypeInput = document.getElementById('custom_budget_type');
            const purposeSelect = document.getElementById('purpose');
            const customPurposeInput = document.getElementById('custom_purpose');
            
            // Handle custom budget type
            if (budgetTypeSelect && budgetTypeSelect.value === 'other') {
                const customType = customBudgetTypeInput ? customBudgetTypeInput.value.trim() : '';
                if (!customType) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter a custom budget type.'
                    });
                    return false;
                }
                // Create a hidden input to send the custom type as budget_type
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'budget_type';
                hiddenInput.value = customType.toLowerCase().replace(/\s+/g, '_');
                this.appendChild(hiddenInput);
                // Disable the select so it doesn't send "other"
                budgetTypeSelect.disabled = true;
            }
            
            // Handle custom purpose
            if (purposeSelect && purposeSelect.value === 'other') {
                const customPurpose = customPurposeInput ? customPurposeInput.value.trim() : '';
                if (!customPurpose) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter a custom purpose.'
                    });
                    return false;
                }
                // The custom purpose will be handled in the controller
            }
        });
    }
    
    // Handle form submission for Edit Budget Modal
    const editBudgetForm = document.getElementById('editBudgetForm');
    if (editBudgetForm) {
        editBudgetForm.addEventListener('submit', function(e) {
            const ebBudgetTypeSelect = document.getElementById('eb_budget_type');
            const ebCustomBudgetTypeInput = document.getElementById('eb_custom_budget_type');
            
            if (ebBudgetTypeSelect && ebBudgetTypeSelect.value === 'other') {
                const customType = ebCustomBudgetTypeInput ? ebCustomBudgetTypeInput.value.trim() : '';
                if (!customType) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Please enter a custom budget type.'
                    });
                    return false;
                }
                // Create a hidden input to send the custom type as budget_type
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'budget_type';
                hiddenInput.value = customType.toLowerCase().replace(/\s+/g, '_');
                this.appendChild(hiddenInput);
                // Disable the select so it doesn't send "other"
                ebBudgetTypeSelect.disabled = true;
            }
        });
    }
    
    // Reset custom fields when modals are closed
    const addModal = document.getElementById('addBudgetModal');
    if (addModal) {
        addModal.addEventListener('hidden.bs.modal', function() {
            // Get elements inside the handler to avoid undefined variable errors
            const customBudgetTypeRow = document.getElementById('custom_budget_type_row');
            const customBudgetTypeInput = document.getElementById('custom_budget_type');
            const budgetTypeSelect = document.getElementById('budget_type');
            
            if (customBudgetTypeRow) {
                customBudgetTypeRow.style.display = 'none';
            }
            if (customBudgetTypeInput) {
                customBudgetTypeInput.value = '';
                customBudgetTypeInput.required = false;
            }
            if (budgetTypeSelect) {
                budgetTypeSelect.disabled = false;
                budgetTypeSelect.value = '';
            }
            
            // Reset line items section
            const lineItemsSection = document.getElementById('budget_line_items_section');
            const lineItemsContainer = document.getElementById('lineItemsContainer');
            const totalBudgetInput = document.getElementById('total_budget');
            const totalBudgetHint = document.getElementById('total_budget_hint');
            
            if (lineItemsSection) {
                lineItemsSection.style.display = 'none';
            }
            if (lineItemsContainer) {
                lineItemsContainer.innerHTML = '';
                lineItemIndex = 0;
            }
            if (totalBudgetInput) {
                totalBudgetInput.value = '';
            }
            if (totalBudgetHint) {
                totalBudgetHint.textContent = 'Enter total budget amount';
                totalBudgetHint.className = 'text-muted';
            }
            updateLineItemsTotal();
        });
    }
    
    const editModal = document.getElementById('editBudgetModal');
    if (editModal) {
        editModal.addEventListener('hidden.bs.modal', function() {
            // Get elements inside the handler to avoid undefined variable errors
            const ebCustomBudgetTypeRow = document.getElementById('eb_custom_budget_type_row');
            const ebCustomBudgetTypeInput = document.getElementById('eb_custom_budget_type');
            const ebBudgetTypeSelect = document.getElementById('eb_budget_type');
            
            if (ebCustomBudgetTypeRow) {
                ebCustomBudgetTypeRow.style.display = 'none';
            }
            if (ebCustomBudgetTypeInput) {
                ebCustomBudgetTypeInput.value = '';
                ebCustomBudgetTypeInput.required = false;
            }
            if (ebBudgetTypeSelect) {
                ebBudgetTypeSelect.disabled = false;
            }
        });
    }
});
</script>

<style>
/* Budget Modal Styling */
.budget-modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    overflow: hidden;
}

.budget-modal-header {
    background: linear-gradient(135deg, #5b2a86 0%, #1f2b6c 100%);
    color: white;
    padding: 1.5rem;
    border-bottom: none;
}

.budget-modal-header .modal-title {
    color: white;
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-icon-wrapper {
    width: 48px;
    height: 48px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
    backdrop-filter: blur(10px);
}

.budget-modal-content .modal-body {
    padding: 2rem;
    background: #f8f9fa;
    max-height: calc(100vh - 250px);
    overflow-y: auto;
    overflow-x: hidden;
}

.budget-modal-content .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
    font-size: 0.9rem;
}

.budget-modal-content .form-control,
.budget-modal-content .form-select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.budget-modal-content .form-control:focus,
.budget-modal-content .form-select:focus {
    border-color: #5b2a86;
    box-shadow: 0 0 0 0.2rem rgba(91, 42, 134, 0.15);
    outline: none;
}

.budget-modal-content .form-control:hover,
.budget-modal-content .form-select:hover {
    border-color: #ced4da;
}

/* Custom Budget Type Row Animation */
#custom_budget_type_row,
#eb_custom_budget_type_row {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Funding Allocation Card Styling */
.budget-modal-content .card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    margin-top: 1.5rem;
}

.budget-modal-content .card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 2px solid #dee2e6;
    padding: 1rem 1.25rem;
    border-radius: 10px 10px 0 0;
}

.budget-modal-content .card-header h6 {
    color: #495057;
    font-weight: 600;
    margin: 0;
}

.budget-modal-content .card-body {
    padding: 1.25rem;
}

/* Modal Footer Styling */
.budget-modal-footer {
    background: white;
    border-top: 1px solid #e9ecef;
    padding: 1.25rem 2rem;
    border-radius: 0 0 12px 12px;
}

.budget-submit-btn {
    background: linear-gradient(135deg, #5b2a86 0%, #1f2b6c 100%);
    border: none;
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(91, 42, 134, 0.3);
}

.budget-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(91, 42, 134, 0.4);
    background: linear-gradient(135deg, #6b3a96 0%, #2f3b7c 100%);
}

.budget-submit-btn:active {
    transform: translateY(0);
}

.budget-modal-footer .btn-light {
    border: 2px solid #e9ecef;
    padding: 0.75rem 2rem;
    font-weight: 600;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.budget-modal-footer .btn-light:hover {
    background: #f8f9fa;
    border-color: #dee2e6;
    transform: translateY(-1px);
}

/* Input Group Styling */
.budget-modal-content .input-group-text {
    background: #f8f9fa;
    border: 2px solid #e9ecef;
    border-right: none;
    color: #6c757d;
    font-weight: 500;
}

.budget-modal-content .input-group .form-control {
    border-left: none;
}

.budget-modal-content .input-group .form-control:focus {
    border-left: 2px solid #5b2a86;
}

/* Textarea Styling */
.budget-modal-content textarea.form-control {
    resize: vertical;
    min-height: 100px;
}

/* Select Dropdown Styling */
.budget-modal-content .form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}

/* Alert Styling in Modal */
.budget-modal-content .alert {
    border-radius: 8px;
    border: none;
    padding: 1rem 1.25rem;
}

.budget-modal-content .alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
}

/* Funding allocation styles removed - no longer used in budget planning */

/* Modal Animation */
.modal.fade .budget-modal-content {
    transform: scale(0.9);
    transition: transform 0.3s ease-out;
}

.modal.show .budget-modal-content {
    transform: scale(1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .budget-modal-content .modal-body {
        padding: 1.5rem;
    }
    
    .budget-modal-header {
        padding: 1.25rem;
    }
    
    .modal-icon-wrapper {
        width: 40px;
        height: 40px;
        font-size: 1.25rem;
    }
    
    .budget-modal-footer {
        padding: 1rem 1.5rem;
    }
    
    .budget-submit-btn,
    .budget-modal-footer .btn-light {
        padding: 0.625rem 1.5rem;
        font-size: 0.9rem;
    }
}

/* Full Screen Modal on Small Devices */
@media (max-width: 576px) {
    .modal-fullscreen-sm-down {
        margin: 0;
        max-width: 100%;
        height: 100vh;
    }
    .modal-fullscreen-sm-down .modal-content {
        height: 100vh;
        border-radius: 0 !important;
        display: flex;
        flex-direction: column;
    }
    .modal-fullscreen-sm-down .modal-body {
        flex: 1;
        overflow-y: auto;
        max-height: calc(100vh - 120px);
    }
}

/* Required Field Indicator */
.budget-modal-content .form-label:has(+ .form-control[required]):after,
.budget-modal-content .form-label:has(+ .form-select[required]):after {
    content: " *";
    color: #dc3545;
    font-weight: bold;
}

/* Smooth Transitions */
.budget-modal-content * {
    transition: all 0.2s ease;
}

/* Prevent body scroll when modal is open */
body.modal-open {
    overflow: hidden !important;
    padding-right: 0 !important;
}

/* Custom scrollbar for modal body */
.budget-modal-content .modal-body::-webkit-scrollbar {
    width: 8px;
}

.budget-modal-content .modal-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.budget-modal-content .modal-body::-webkit-scrollbar-thumb {
    background: #5b2a86;
    border-radius: 10px;
}

.budget-modal-content .modal-body::-webkit-scrollbar-thumb:hover {
    background: #4a2175;
}
</style>
@endsection


