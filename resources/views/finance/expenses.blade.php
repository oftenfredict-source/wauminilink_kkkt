@extends('layouts.index')

@section('content')
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
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
            document.addEventListener('DOMContentLoaded', function () {
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

    @if(session('warning'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: '{{ session('warning') }}',
                    timer: 5000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            });
        </script>
    @endif

    @if(session('info'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                Swal.fire({
                    icon: 'info',
                    title: 'Information',
                    text: '{{ session('info') }}',
                    timer: 5000,
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

            #filtersForm .row.g-2>[class*="col-"] {
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

            /* Expense Modal Styles */
            .bg-light-success {
                background-color: #e8f5e9 !important;
            }

            .bg-light-warning {
                background-color: #fff3e0 !important;
            }

            .bg-light-primary {
                background-color: #e3f2fd !important;
            }

            .text-warning-700 {
                color: #f57c00 !important;
            }

            .uppercase-xs {
                font-size: 0.65rem;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .tracking-wider {
                letter-spacing: 0.05em;
            }

            /* Premium Modal Content */
            .modal-content.premium-modal {
                border: none;
                border-radius: 16px;
                box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
                overflow: hidden;
            }

            .premium-modal-header {
                background: #fff;
                padding: 1.5rem 2rem;
                border-bottom: 1px solid #f0f0f0;
            }

            .premium-modal-title {
                font-weight: 700;
                color: #2c3e50;
                font-size: 1.25rem;
                letter-spacing: -0.5px;
            }

            .premium-modal-subtitle {
                font-size: 0.85rem;
                color: #95a5a6;
                margin-top: 4px;
            }

            .premium-modal-footer {
                padding: 1.25rem 2rem;
                background: #fcfcfc;
                border-top: 1px solid #f0f0f0;
            }

            .section-header {
                font-size: 0.75rem;
                font-weight: 800;
                color: #94a3b8;
                text-transform: uppercase;
                letter-spacing: 1px;
                margin-bottom: 1.25rem;
                display: flex;
                align-items: center;
                width: 100%;
            }

            .section-header::after {
                content: "";
                flex: 1;
                height: 1px;
                background: #f1f5f9;
                margin-left: 1rem;
            }

            .section-header i {
                color: #b02a37;
                margin-right: 0.75rem;
                font-size: 0.9rem;
            }

            .modal-icon-wrapper {
                width: 48px;
                height: 48px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 12px;
                font-size: 1.25rem;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            }

            .budget-submit-btn {
                background: #b02a37;
                color: white !important;
                font-weight: 600;
                padding: 0.6rem 2rem;
                border-radius: 10px;
                transition: all 0.2s;
                border: none;
            }

            .budget-submit-btn:hover {
                background: #8b1e29;
                transform: translateY(-1px);
                box-shadow: 0 6px 15px rgba(176, 42, 55, 0.3);
            }

            .budget-submit-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 6px 15px rgba(176, 42, 55, 0.3);
            }
    </style>
    <div class="container-fluid px-4">
        <!-- Page Title and Quick Actions - Compact Collapsible -->
        <div class="card border-0 shadow-sm mb-3 actions-card">
            <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header"
                onclick="toggleActions()">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-receipt me-2"></i>Expenses Management
                    </h1>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
                </div>
            </div>
            <div class="card-body p-3" id="actionsBody">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addExpenseModal">
                        <i class="fas fa-plus me-1"></i>
                        <span class="d-none d-sm-inline">Add Expense</span>
                        <span class="d-sm-none">Add</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters & Search - Collapsible on Mobile -->
        <form method="GET" action="{{ route('finance.expenses') }}" class="card mb-4 border-0 shadow-sm" id="filtersForm">
            <!-- Filter Header -->
            <div class="card-header bg-primary text-white p-2 px-3 filter-header" onclick="toggleFilters()">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-filter me-1"></i>
                        <span class="fw-semibold">Filters</span>
                        @if(request('expense_category') || request('status') || request('date_from') || request('date_to'))
                            <span class="badge bg-white text-primary rounded-pill ms-2"
                                id="activeFiltersCount">{{ (request('expense_category') ? 1 : 0) + (request('status') ? 1 : 0) + (request('date_from') ? 1 : 0) + (request('date_to') ? 1 : 0) }}</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
                </div>
            </div>

            <!-- Filter Body - Collapsible on Mobile -->
            <div class="card-body p-3" id="filterBody">
                <div class="row g-2 mb-2">
                    <!-- Category - Full Width on Mobile -->
                    <div class="col-6 col-md-3">
                        <label for="expense_category" class="form-label small text-muted mb-1">
                            <i class="fas fa-tags me-1 text-primary"></i>Category
                        </label>
                        <select class="form-select form-select-sm" id="expense_category" name="expense_category">
                            <option value="">All Categories</option>
                            <option value="utilities" {{ request('expense_category') == 'utilities' ? 'selected' : '' }}>
                                Utilities</option>
                            <option value="maintenance" {{ request('expense_category') == 'maintenance' ? 'selected' : '' }}>
                                Maintenance</option>
                            <option value="supplies" {{ request('expense_category') == 'supplies' ? 'selected' : '' }}>
                                Supplies</option>
                            <option value="transport" {{ request('expense_category') == 'transport' ? 'selected' : '' }}>
                                Transport</option>
                            <option value="communication" {{ request('expense_category') == 'communication' ? 'selected' : '' }}>Communication</option>
                            <option value="other" {{ request('expense_category') == 'other' ? 'selected' : '' }}>Other
                            </option>
                        </select>
                    </div>

                    <!-- Status - Full Width on Mobile -->
                    <div class="col-6 col-md-3">
                        <label for="status" class="form-label small text-muted mb-1">
                            <i class="fas fa-info-circle me-1 text-success"></i>Status
                        </label>
                        <select class="form-select form-select-sm" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>

                    <!-- From Date - Full Width on Mobile -->
                    <div class="col-6 col-md-3">
                        <label for="date_from" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-info"></i>From Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="date_from" name="date_from"
                            value="{{ request('date_from') }}">
                    </div>

                    <!-- To Date - Full Width on Mobile -->
                    <div class="col-6 col-md-3">
                        <label for="date_to" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-warning"></i>To Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="date_to" name="date_to"
                            value="{{ request('date_to') }}">
                    </div>

                    <!-- Action Buttons - Full Width on Mobile -->
                    <div class="col-12 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-search me-1"></i>
                            <span class="d-none d-sm-inline">Filter</span>
                            <span class="d-sm-none">Apply</span>
                        </button>
                        <a href="{{ route('finance.expenses') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times me-1"></i>
                            <span class="d-none d-sm-inline">Clear</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <!-- Expenses Table -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-table me-1"></i><strong>Expenses List</strong>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell">Date</th>
                                <th class="d-table-cell d-md-none">Expense</th>
                                <th>Name</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th class="d-none d-lg-table-cell">Vendor</th>
                                <th class="d-none d-xl-table-cell">Budget</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expenses as $expense)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $expense->expense_name }}</div>
                                        <div class="d-md-none">
                                            <small class="text-muted d-block">
                                                <i
                                                    class="fas fa-calendar me-1"></i>{{ $expense->expense_date->format('M d, Y') }}
                                            </small>
                                            @if($expense->vendor)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-store me-1"></i>{{ $expense->vendor }}
                                                </small>
                                            @endif
                                            @if($expense->budget)
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-wallet me-1"></i>{{ $expense->budget->budget_name }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="d-none d-md-table-cell">{{ $expense->expense_date->format('M d, Y') }}</td>
                                    <td class="d-md-none">{{ $expense->expense_name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($expense->expense_category) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-primary">TZS {{ number_format($expense->amount, 0) }}</span>
                                    </td>
                                    <td class="d-none d-lg-table-cell">{{ $expense->vendor ?? '-' }}</td>
                                    <td class="d-none d-xl-table-cell">{{ $expense->budget->budget_name ?? '-' }}</td>
                                    <td>
                                        @if($expense->approval_status == 'approved' && $expense->status == 'paid')
                                            <span class="badge bg-success">Paid</span>
                                        @elseif($expense->approval_status == 'approved')
                                            <span class="badge bg-primary">Approved</span>
                                        @elseif($expense->approval_status == 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-sm btn-primary" onclick="viewExpense(this)"
                                                data-id="{{ $expense->id }}"
                                                data-date="{{ $expense->expense_date->format('M d, Y') }}"
                                                data-name="{{ $expense->expense_name }}"
                                                data-category="{{ ucfirst($expense->expense_category) }}"
                                                data-amount="{{ number_format($expense->amount, 2) }}"
                                                data-vendor="{{ $expense->vendor ?? '-' }}"
                                                data-budget="{{ $expense->budget->budget_name ?? '-' }}"
                                                data-method="{{ ucfirst($expense->payment_method) }}"
                                                data-reference="{{ $expense->reference_number ?? '-' }}"
                                                data-receipt="{{ $expense->receipt_number ?? '-' }}"
                                                data-status="{{ ucfirst($expense->status) }}"
                                                data-description="{{ $expense->description ?? '-' }}"
                                                data-notes="{{ $expense->notes ?? '-' }}" title="View Details">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-sm-inline ms-1">View</span>
                                            </button>
                                            @if($expense->status == 'paid')
                                                <button type="button" class="btn btn-sm btn-success" disabled title="Already Paid">
                                                    <i class="fas fa-check-circle"></i>
                                                    <span class="d-none d-sm-inline ms-1">Paid</span>
                                                </button>
                                            @elseif($expense->approval_status == 'approved')
                                                <button type="button" class="btn btn-sm btn-success"
                                                    onclick="markPaid({{ $expense->id }})" title="Mark as Paid">
                                                    <i class="fas fa-money-bill-wave"></i>
                                                    <span class="d-none d-sm-inline ms-1">Pay</span>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-sm btn-secondary" disabled
                                                    title="Awaiting Approval">
                                                    <i class="fas fa-clock"></i>
                                                    <span class="d-none d-sm-inline ms-1">Pending</span>
                                                </button>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-warning" onclick="editExpense(this)"
                                                data-id="{{ $expense->id }}"
                                                data-date="{{ $expense->expense_date->format('Y-m-d') }}"
                                                data-name="{{ $expense->expense_name }}"
                                                data-category="{{ $expense->expense_category }}"
                                                data-amount="{{ $expense->amount }}" data-vendor="{{ $expense->vendor ?? '' }}"
                                                data-budget-id="{{ $expense->budget_id ?? '' }}"
                                                data-method="{{ $expense->payment_method }}"
                                                data-reference="{{ $expense->reference_number ?? '' }}"
                                                data-receipt="{{ $expense->receipt_number ?? '' }}"
                                                data-status="{{ $expense->status }}"
                                                data-description="{{ $expense->description ?? '' }}"
                                                data-notes="{{ $expense->notes ?? '' }}" title="Edit">
                                                <i class="fas fa-edit"></i>
                                                <span class="d-none d-sm-inline ms-1">Edit</span>
                                            </button>
                                            <form class="d-inline"
                                                onsubmit="return confirmDeleteExpense(this, {{ $expense->id }})" method="POST"
                                                action="{{ route('finance.expenses.destroy', $expense) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                    <span class="d-none d-sm-inline ms-1">Delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">No expenses found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content premium-modal">
                <div class="modal-header premium-modal-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon-wrapper me-3 bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h5 class="premium-modal-title mb-0" id="addExpenseModalLabel">Add New Expense</h5>
                            <div class="premium-modal-subtitle">Record a new expense transaction</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('finance.expenses.store') }}" method="POST" id="addExpenseForm"
                    onsubmit="return validateExpenseForm(event)">
                    @csrf
                    <div class="modal-body p-0">
                        {{-- Section 1: Maelezo ya Matumizi --}}
                        <div class="p-4 bg-white">
                            <div class="section-header">
                                <i class="fas fa-info-circle"></i>Expense Details
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="budget_id" name="budget_id">
                                            <option value="">Select Budget (Optional)</option>
                                            @foreach($budgets as $budget)
                                                <option value="{{ $budget->id }}" data-total="{{ $budget->total_budget }}"
                                                    data-spent="{{ $budget->spent_amount }}"
                                                    data-pending="{{ $budget->pending_expenses_amount ?? 0 }}"
                                                    data-remaining="{{ $budget->remaining_with_pending ?? ($budget->total_budget - $budget->spent_amount) }}"
                                                    data-funded="{{ $budget->isFullyFunded() ? '1' : '0' }}"
                                                    data-funding-percent="{{ $budget->funding_percentage }}"
                                                    data-purpose="{{ $budget->purpose }}"
                                                    data-line-items="{{ json_encode($budget->lineItems) }}">
                                                    {{ $budget->budget_name }} ({{ $budget->fiscal_year }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="budget_id">Budget Source</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="expense_name" name="expense_name"
                                            placeholder="Expense Name" list="expenseLineItems" required>
                                        <label for="expense_name">Expense Name *</label>
                                        <datalist id="expenseLineItems"></datalist>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="expense_category" name="expense_category" required>
                                            <option value="">Select Category</option>
                                            @if(isset($expenseCategories))
                                                <optgroup label="A. INJILI">
                                                    @foreach($expenseCategories['injili'] as $code => $name)
                                                        <option value="{{ strtolower(str_replace([' ', '/', '.'], '_', $name)) }}"
                                                            title="{{ $code }} - {{ $name }}">{{ $name }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="B. UMOJA NA IDARA">
                                                    @foreach($expenseCategories['idara'] as $code => $name)
                                                        <option value="{{ strtolower(str_replace([' ', '/', '.'], '_', $name)) }}"
                                                            title="{{ $code }} - {{ $name }}">{{ $name }}</option>
                                                    @endforeach
                                                </optgroup>
                                                <optgroup label="C. MAJENGO">
                                                    @foreach($expenseCategories['majengo'] as $code => $name)
                                                        <option value="{{ strtolower(str_replace([' ', '/', '.'], '_', $name)) }}"
                                                            title="{{ $code }} - {{ $name }}">{{ $name }}</option>
                                                    @endforeach
                                                </optgroup>
                                            @else
                                                <optgroup label="Categories Loaded Failed">
                                                    <option value="other">Other</option>
                                                </optgroup>
                                            @endif
                                            <optgroup label="D. ZINGINEZO">
                                                <option value="other">Other (Custom Purpose)</option>
                                            </optgroup>
                                        </select>
                                        <label for="expense_category">Category *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="expense_date" name="expense_date"
                                            value="{{ date('Y-m-d') }}" required>
                                        <label for="expense_date">Expense Date *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="amount" name="amount" step="0.01"
                                            min="0" placeholder="0.00" required>
                                        <label for="amount">Amount (TZS) *</label>
                                        <small class="text-muted" id="amountHelpText"></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Budget Info (Dynamic) --}}
                        <div class="px-4 pb-2 bg-white" id="budgetInfo" style="display: none;">
                            <div class="section-header mt-0">
                                <i class="fas fa-wallet"></i>Budget Context
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="card border-0 shadow-sm overflow-hidden mb-3">
                                        <div class="card-body p-0">
                                            <div class="row g-0">
                                                <div class="col-4 border-end">
                                                    <div class="p-3 text-center">
                                                        <small class="text-muted d-block mb-1">Total Budget</small>
                                                        <span class="fw-bold text-primary" id="budgetTotal">TZS 0</span>
                                                    </div>
                                                </div>
                                                <div class="col-4 border-end">
                                                    <div class="p-3 text-center">
                                                        <small class="text-muted d-block mb-1">Spent</small>
                                                        <span class="fw-bold text-dark" id="budgetSpent">TZS 0</span>
                                                    </div>
                                                </div>
                                                <div class="col-4 bg-success bg-opacity-10">
                                                    <div class="p-3 text-center">
                                                        <small class="text-success d-block mb-1">Remaining</small>
                                                        <span class="fw-bold text-success" id="budgetRemaining">TZS 0</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="px-3 pb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted fw-bold uppercase-xs">Budget
                                                        Utilization</small>
                                                    <small class="fw-bold" id="budgetProgressText">0%</small>
                                                </div>
                                                <div class="progress" style="height: 6px; border-radius: 3px;">
                                                    <div class="progress-bar bg-danger" id="budgetProgressBar"
                                                        role="progressbar" style="width: 0%"></div>
                                                </div>
                                                <div id="budgetFundingStatus" class="small mt-2">-</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="budgetAlert" class="alert small p-2 mb-3" style="display: none;">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <span id="budgetAlertText"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Fund Summary Mini-Card -->
                        <div class="card border-0 shadow-sm overflow-hidden mb-3" id="fundSummaryCard"
                            style="display: none;">
                            <div class="card-body p-0">
                                <div class="row g-0">
                                    <div class="col-4 border-end bg-light-success">
                                        <div class="p-3 text-center">
                                            <small class="text-muted d-block mb-1">Total Income</small>
                                            <span class="fw-bold text-success" id="totalIncome">TZS 0</span>
                                        </div>
                                    </div>
                                    <div class="col-4 border-end">
                                        <div class="p-3 text-center">
                                            <small class="text-muted d-block mb-1">Used</small>
                                            <span class="fw-bold text-warning" id="usedAmount">TZS 0</span>
                                        </div>
                                    </div>
                                    <div class="col-4 bg-primary text-white">
                                        <div class="p-3 text-center">
                                            <small class="text-white-50 d-block mb-1">Available Funds</small>
                                            <span class="fw-bold" id="availableAmount">TZS 0</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="px-3 py-2 bg-light">
                                    <div class="progress" style="height: 4px;">
                                        <div class="progress-bar bg-success" id="incomeProgress" style="width: 0%">
                                        </div>
                                        <div class="progress-bar bg-warning" id="usedProgress" style="width: 0%">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Insufficient Fund --}}
                        <div class="card border-0 shadow-sm bg-light-warning mb-3" id="insufficientFundCard"
                            style="display: none;">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-3 text-warning-700">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    <h6 class="mb-0 fw-bold">Additional Funding Required</h6>
                                </div>
                                <div class="alert alert-danger p-2 small mb-3 border-0 shadow-sm" id="shortfallSummary">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><i class="fas fa-minus-circle me-1"></i>Shortfall:</span>
                                        <span id="shortfallAmount" class="fw-bold">TZS 0</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <small class="fw-bold text-muted uppercase-xs d-block mb-2">Available
                                        Funds:</small>
                                    <div id="availableFundsDisplay" class="bg-white border rounded p-2 small"
                                        style="max-height: 120px; overflow-y: auto;"></div>
                                </div>
                                <div id="manualFundingSection">
                                    <small class="fw-bold text-muted uppercase-xs d-block mb-2">Select Allocation
                                        Sources:</small>
                                    <div id="additionalFundingInputs" class="row g-2"></div>
                                    <button type="button" class="btn btn-sm btn-outline-warning mt-2 w-100"
                                        id="addFundingSourceBtn">
                                        <i class="fas fa-plus me-1"></i>Add Funding Source
                                    </button>
                                    <div class="mt-3 p-2 bg-white rounded border d-flex justify-content-between">
                                        <small class="fw-bold">Total Allocated:</small>
                                        <span id="totalAllocatedAmount" class="fw-bold text-primary">TZS 0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Malipo & Nyaraka --}}
                        <div class="p-4 bg-white border-top">
                            <div class="section-header">
                                <i class="fas fa-credit-card"></i>Payment & Documentation
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="payment_method" name="payment_method" required>
                                            <option value="">Select Method</option>
                                            <option value="cash">Cash</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="check">Check</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <label for="payment_method">Payment Method *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="vendor" name="vendor"
                                            placeholder="Vendor Name">
                                        <label for="vendor">Vendor/Payee</label>
                                    </div>
                                </div>
                                <div class="col-md-6" id="expense_reference_group">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="reference_number"
                                            name="reference_number" placeholder="Ref Number">
                                        <label for="reference_number">Reference Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="receipt_number" name="receipt_number"
                                            placeholder="Receipt Number">
                                        <label for="receipt_number">Receipt Number</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Maelezo ya Ziada --}}
                        <div class="p-4 bg-white border-top">
                            <div class="section-header">
                                <i class="fas fa-comment-alt"></i>Additional Notes
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" id="description" name="description"
                                            style="height: 100px" placeholder="Details"></textarea>
                                        <label for="description">Expense Details/Description</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" id="notes" name="notes" style="height: 100px"
                                            placeholder="Internal Notes"></textarea>
                                        <label for="notes">Internal Working Notes</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="premium-modal-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light fw-bold px-4 border"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="budget-submit-btn">
                            <i class="fas fa-paper-plane me-2"></i>Record Expense
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Expense Modal -->
    <div class="modal fade" id="viewExpenseModal" tabindex="-1" aria-labelledby="viewExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="viewExpenseModalLabel">
                        <i class="fas fa-receipt me-2"></i>Expense Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body" id="viewExpenseBody">
                    <!-- Content will be populated by JavaScript -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Expense Modal -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content premium-modal">
                <div class="modal-header premium-modal-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon-wrapper me-3 bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h5 class="premium-modal-title mb-0" id="editExpenseModalLabel">Edit Expense</h5>
                            <div class="premium-modal-subtitle">Modify existing expense details</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="editExpenseForm" method="POST">
                    @csrf
                    <div class="modal-body p-0">
                        {{-- Section 1: Maelezo ya Matumizi --}}
                        <div class="p-4 bg-white">
                            <div class="section-header">
                                <i class="fas fa-info-circle"></i>Expense Details
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="ex_expense_name" name="expense_name"
                                            placeholder="Expense Name" required>
                                        <label for="ex_expense_name">Expense Name *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="ex_expense_category" name="expense_category"
                                            required>
                                            <option value="utilities">Utilities</option>
                                            <option value="maintenance">Maintenance</option>
                                            <option value="supplies">Supplies</option>
                                            <option value="transport">Transport</option>
                                            <option value="communication">Communication</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <label for="ex_expense_category">Category *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="ex_expense_date" name="expense_date"
                                            required>
                                        <label for="ex_expense_date">Expense Date *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="ex_amount" name="amount" step="0.01"
                                            min="0" placeholder="0.00" required>
                                        <label for="ex_amount">Amount (TZS) *</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Budget context & Status --}}
                        <div class="p-4 bg-white border-top">
                            <div class="section-header">
                                <i class="fas fa-wallet"></i>Budget & Status
                            </div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="ex_budget_id" name="budget_id">
                                            <option value="">Select Budget (Optional)</option>
                                            @foreach($budgets as $budget)
                                                <option value="{{ $budget->id }}">
                                                    {{ $budget->budget_name }} ({{ $budget->fiscal_year }})
                                                </option>
                                            @endforeach
                                        </select>
                                        <label for="ex_budget_id">Budget Source</label>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="ex_status" name="status" required>
                                            <option value="pending">Pending</option>
                                            <option value="approved">Approved</option>
                                            <option value="paid">Paid</option>
                                        </select>
                                        <label for="ex_status">Payment Status *</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Malipo & Nyaraka --}}
                        <div class="p-4 bg-white border-top">
                            <div class="section-header">
                                <i class="fas fa-credit-card"></i>Payment & Documentation
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="ex_payment_method" name="payment_method" required>
                                            <option value="cash">Cash</option>
                                            <option value="bank_transfer">Bank Transfer</option>
                                            <option value="mobile_money">Mobile Money</option>
                                            <option value="check">Check</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <label for="ex_payment_method">Payment Method *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="ex_vendor" name="vendor"
                                            placeholder="Vendor Name">
                                        <label for="ex_vendor">Vendor/Payee</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="ex_reference_number"
                                            name="reference_number" placeholder="Ref Number">
                                        <label for="ex_reference_number">Reference Number</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="ex_receipt_number" name="receipt_number"
                                            placeholder="Receipt Number">
                                        <label for="ex_receipt_number">Receipt Number</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Maelezo ya Ziada --}}
                        <div class="p-4 bg-white border-top">
                            <div class="section-header">
                                <i class="fas fa-comment-alt"></i>Additional Notes
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" id="ex_description" name="description"
                                            style="height: 100px" placeholder="Details"></textarea>
                                        <label for="ex_description">Expense Details/Description</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control" id="ex_notes" name="notes" style="height: 100px"
                                            placeholder="Internal Notes"></textarea>
                                        <label for="ex_notes">Internal Working Notes</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="premium-modal-footer d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light fw-bold px-4 border"
                            data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="budget-submit-btn">
                            <i class="fas fa-save me-2"></i>Update Expense
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
        window.addEventListener('resize', function () {
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

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
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
            @if(request('expense_category') || request('status') || request('date_from') || request('date_to'))
                if (window.innerWidth <= 768 && filterBody && filterIcon) {
                    toggleFilters(); // Expand if filters are active
                    const filterHeader = document.querySelector('.filter-header');
                    if (filterHeader) filterHeader.classList.add('active');
                }
            @endif
                                                                                                });

        function viewExpense(button) {
            if (!button) return;
            var d = button.dataset;

            var statusClass = d.status.toLowerCase() === 'paid' ? 'success' :
                d.status.toLowerCase() === 'approved' ? 'primary' :
                    d.status.toLowerCase() === 'pending' ? 'warning' : 'secondary';

            var categoryClass = d.category.toLowerCase() === 'utilities' ? 'info' :
                d.category.toLowerCase() === 'maintenance' ? 'warning' :
                    d.category.toLowerCase() === 'supplies' ? 'success' :
                        d.category.toLowerCase() === 'transport' ? 'primary' :
                            d.category.toLowerCase() === 'communication' ? 'danger' : 'secondary';

            var html = `
                                                                                                        <div class="row g-4">
                                                                                                            <!-- Expense Overview Cards -->
                                                                                                            <div class="col-12">
                                                                                                                <div class="row g-3">
                                                                                                                    <div class="col-md-4">
                                                                                                                        <div class="card bg-primary text-white h-100">
                                                                                                                            <div class="card-body text-center">
                                                                                                                                <i class="fas fa-dollar-sign fa-2x mb-2"></i>
                                                                                                                                <h6 class="card-title">Amount</h6>
                                                                                                                                <h4 class="mb-0">TZS ${d.amount || '0.00'}</h4>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="col-md-4">
                                                                                                                        <div class="card bg-${statusClass} text-white h-100">
                                                                                                                            <div class="card-body text-center">
                                                                                                                                <i class="fas fa-flag fa-2x mb-2"></i>
                                                                                                                                <h6 class="card-title">Status</h6>
                                                                                                                                <h4 class="mb-0">${d.status || '-'}</h4>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    <div class="col-md-4">
                                                                                                                        <div class="card bg-${categoryClass} text-white h-100">
                                                                                                                            <div class="card-body text-center">
                                                                                                                                <i class="fas fa-tag fa-2x mb-2"></i>
                                                                                                                                <h6 class="card-title">Category</h6>
                                                                                                                                <h4 class="mb-0">${d.category || '-'}</h4>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>

                                                                                                            <!-- Expense Details -->
                                                                                                            <div class="col-12">
                                                                                                                <div class="card">
                                                                                                                    <div class="card-header bg-light">
                                                                                                                        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Expense Information</h6>
                                                                                                                    </div>
                                                                                                                    <div class="card-body">
                                                                                                                        <div class="row g-3">
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <i class="fas fa-calendar text-primary me-3"></i>
                                                                                                                                    <div>
                                                                                                                                        <small class="text-muted">Date</small>
                                                                                                                                        <div class="fw-bold">${d.date || '-'}</div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <i class="fas fa-file-invoice text-info me-3"></i>
                                                                                                                                    <div>
                                                                                                                                        <small class="text-muted">Expense Name</small>
                                                                                                                                        <div class="fw-bold">${d.name || '-'}</div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <i class="fas fa-building text-warning me-3"></i>
                                                                                                                                    <div>
                                                                                                                                        <small class="text-muted">Vendor</small>
                                                                                                                                        <div class="fw-bold">${d.vendor || '-'}</div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <i class="fas fa-wallet text-success me-3"></i>
                                                                                                                                    <div>
                                                                                                                                        <small class="text-muted">Budget</small>
                                                                                                                                        <div class="fw-bold">${d.budget || '-'}</div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <i class="fas fa-credit-card text-danger me-3"></i>
                                                                                                                                    <div>
                                                                                                                                        <small class="text-muted">Payment Method</small>
                                                                                                                                        <div class="fw-bold">${d.method || '-'}</div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <i class="fas fa-hashtag text-secondary me-3"></i>
                                                                                                                                    <div>
                                                                                                                                        <small class="text-muted">Reference Number</small>
                                                                                                                                        <div class="fw-bold">${d.reference || '-'}</div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <i class="fas fa-receipt text-primary me-3"></i>
                                                                                                                                    <div>
                                                                                                                                        <small class="text-muted">Receipt Number</small>
                                                                                                                                        <div class="fw-bold">${d.receipt || '-'}</div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                            <div class="col-md-6">
                                                                                                                                <div class="d-flex align-items-center">
                                                                                                                                    <i class="fas fa-flag text-${statusClass} me-3"></i>
                                                                                                                                    <div>
                                                                                                                                        <small class="text-muted">Status</small>
                                                                                                                                        <div class="fw-bold">
                                                                                                                                            <span class="badge bg-${statusClass}">${d.status || '-'}</span>
                                                                                                                                        </div>
                                                                                                                                    </div>
                                                                                                                                </div>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>

                                                                                                            <!-- Description and Notes -->
                                                                                                            ${(d.description && d.description !== '-') || (d.notes && d.notes !== '-') ? `
                                                                                                            <div class="col-12">
                                                                                                                <div class="row g-3">
                                                                                                                    ${d.description && d.description !== '-' ? `
                                                                                                                    <div class="col-md-6">
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
                                                                                                                    ${d.notes && d.notes !== '-' ? `
                                                                                                                    <div class="col-md-6">
                                                                                                                        <div class="card">
                                                                                                                            <div class="card-header bg-light">
                                                                                                                                <h6 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                                                                                                                            </div>
                                                                                                                            <div class="card-body">
                                                                                                                                <p class="mb-0">${d.notes}</p>
                                                                                                                            </div>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                    ` : ''}
                                                                                                                </div>
                                                                                                            </div>
                                                                                                            ` : ''}
                                                                                                        </div>
                                                                                                    `;

            document.getElementById('viewExpenseBody').innerHTML = html;
            new bootstrap.Modal(document.getElementById('viewExpenseModal')).show();
        }

        function markPaid(expenseId) {
            Swal.fire({
                title: 'Mark as Paid?',
                text: 'Are you sure you want to mark this expense as paid?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Mark as Paid',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/finance/expenses/${expenseId}/pay`;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = '{{ csrf_token() }}';
                    form.appendChild(csrfInput);

                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);

                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function editExpense(button) {
            if (!button) return;
            var d = button.dataset;
            var form = document.getElementById('editExpenseForm');
            form.action = '/finance/expenses/' + d.id;
            document.getElementById('ex_expense_name').value = d.name || '';
            document.getElementById('ex_amount').value = d.amount || 0;
            document.getElementById('ex_expense_date').value = d.date || '';
            document.getElementById('ex_expense_category').value = d.category || '';
            document.getElementById('ex_budget_id').value = d.budgetId || '';
            document.getElementById('ex_payment_method').value = d.method || '';
            document.getElementById('ex_vendor').value = d.vendor || '';
            document.getElementById('ex_reference_number').value = d.reference || '';
            document.getElementById('ex_receipt_number').value = d.receipt || '';
            document.getElementById('ex_status').value = d.status || 'pending';
            document.getElementById('ex_description').value = d.description || '';
            document.getElementById('ex_notes').value = d.notes || '';
            new bootstrap.Modal(document.getElementById('editExpenseModal')).show();
        }

        function confirmDeleteExpense(form, id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this action! This will permanently delete the expense record.",
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
                    form.submit();
                }
            });

            return false;
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function () {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Toggle reference number visibility based on payment method
        document.addEventListener('DOMContentLoaded', function () {
            var expenseModal = document.getElementById('addExpenseModal');
            if (expenseModal) {
                var methodEl = expenseModal.querySelector('#payment_method');
                var refGroup = expenseModal.querySelector('#expense_reference_group');
                var refInput = expenseModal.querySelector('#reference_number');
                function updateExpenseRefVisibility() {
                    var method = methodEl ? methodEl.value : '';
                    var hide = method === 'cash' || method === '';
                    if (refGroup) {
                        refGroup.style.display = hide ? 'none' : '';
                    }
                    if (refInput) {
                        refInput.required = !hide;
                        if (hide) refInput.value = '';
                    }
                }
                if (methodEl) {
                    methodEl.addEventListener('change', updateExpenseRefVisibility);
                }
                expenseModal.addEventListener('shown.bs.modal', function () {
                    updateExpenseRefVisibility();
                    // Refresh fund summary when modal opens (in case expenses were added)
                    const budgetSelect = document.getElementById('budget_id');
                    if (budgetSelect && budgetSelect.value) {
                        fetchFundSummary(budgetSelect.value);
                        fetchBudgetInfo(budgetSelect.value);
                    }
                });
                // Initialize on load
                updateExpenseRefVisibility();
            }
        });

        // Budget validation functionality
        document.addEventListener('DOMContentLoaded', function () {
            const budgetSelect = document.getElementById('budget_id');
            const amountInput = document.getElementById('amount');
            const budgetInfo = document.getElementById('budgetInfo');
            const budgetAlert = document.getElementById('budgetAlert');
            const budgetAlertText = document.getElementById('budgetAlertText');

            let currentBudget = null;

            // Budget selection change handler
            budgetSelect.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const amountHelpText = document.getElementById('amountHelpText');

                if (this.value) {
                    currentBudget = {
                        id: this.value,
                        total: parseFloat(selectedOption.dataset.total) || 0,
                        spent: parseFloat(selectedOption.dataset.spent) || 0,
                        pendingExpenses: parseFloat(selectedOption.dataset.pending) || 0,
                        totalCommitted: parseFloat(selectedOption.dataset.spent) + parseFloat(selectedOption.dataset.pending || 0),
                        remaining: parseFloat(selectedOption.dataset.remaining) || 0,
                        funded: selectedOption.dataset.funded === '1',
                        fundingPercent: parseFloat(selectedOption.dataset.fundingPercent) || 0,
                        purpose: selectedOption.dataset.purpose || '',
                        lineItems: JSON.parse(selectedOption.dataset.lineItems || '[]')
                    };

                    // Update expense name suggestions
                    const datalist = document.getElementById('expenseLineItems');
                    if (datalist) {
                        datalist.innerHTML = '';
                        currentBudget.lineItems.forEach(item => {
                            const option = document.createElement('option');
                            option.value = item.item_name;
                            datalist.appendChild(option);
                        });
                    }

                    // Auto-select category if budget has a purpose
                    const categorySelect = document.getElementById('expense_category');
                    if (categorySelect && currentBudget.purpose) {
                        categorySelect.value = currentBudget.purpose;
                    }

                    // Set max attribute on amount input to prevent exceeding budget total
                    if (currentBudget.total > 0) {
                        amountInput.setAttribute('max', currentBudget.total);
                        if (amountHelpText) {
                            amountHelpText.textContent = `Maximum allowed: TZS ${currentBudget.total.toLocaleString()} (Budget Total)`;
                            amountHelpText.className = 'text-info';
                        }
                    }

                    updateBudgetDisplay();
                    budgetInfo.style.display = 'block';

                    // Fetch and display fund summary (with updated budget info)
                    fetchBudgetInfo(this.value);
                    fetchFundSummary(this.value);
                } else {
                    currentBudget = null;
                    budgetInfo.style.display = 'none';
                    amountInput.removeAttribute('max');
                    if (amountHelpText) {
                        amountHelpText.textContent = '';
                    }
                    hideBudgetAlert();
                    hideInsufficientFundBreakdown();
                    hideFundSummary();
                    const datalist = document.getElementById('expenseLineItems');
                    if (datalist) datalist.innerHTML = '';
                }

                validateExpenseAmount();
            });

            // Amount input change handler
            amountInput.addEventListener('input', function () {
                validateExpenseAmount();
            });

            function updateBudgetDisplay() {
                if (!currentBudget) return;

                // Update budget information display
                const pendingExpenses = currentBudget.pendingExpenses || 0;
                const totalCommitted = currentBudget.totalCommitted || (currentBudget.spent + pendingExpenses);

                document.getElementById('budgetTotal').textContent = 'TZS ' + currentBudget.total.toLocaleString();
                document.getElementById('budgetSpent').textContent = 'TZS ' + currentBudget.spent.toLocaleString();

                // Show pending expenses if any
                const budgetRemainingEl = document.getElementById('budgetRemaining');
                if (pendingExpenses > 0) {
                    budgetRemainingEl.innerHTML = `TZS ${currentBudget.remaining.toLocaleString()} <small class="text-warning">(Pending: TZS ${pendingExpenses.toLocaleString()})</small>`;
                } else {
                    budgetRemainingEl.textContent = 'TZS ' + currentBudget.remaining.toLocaleString();
                }

                // Update funding status
                const fundingStatus = document.getElementById('budgetFundingStatus');
                if (currentBudget.funded) {
                    fundingStatus.textContent = '100% Funded';
                    fundingStatus.className = 'h5 text-success';
                } else {
                    fundingStatus.textContent = currentBudget.fundingPercent.toFixed(1) + '% Funded';
                    fundingStatus.className = 'h5 text-warning';
                }

                // Update progress bar (use total committed including pending)
                const utilizationPercent = (totalCommitted / currentBudget.total) * 100;
                const progressBar = document.getElementById('budgetProgressBar');
                const progressText = document.getElementById('budgetProgressText');

                progressBar.style.width = utilizationPercent + '%';
                progressText.textContent = utilizationPercent.toFixed(1) + '%';

                if (utilizationPercent >= 100) {
                    progressBar.className = 'progress-bar bg-danger';
                } else if (utilizationPercent >= 90) {
                    progressBar.className = 'progress-bar bg-warning';
                } else {
                    progressBar.className = 'progress-bar bg-success';
                }
            }

            function validateExpenseAmount() {
                if (!currentBudget || !amountInput.value) {
                    hideBudgetAlert();
                    hideInsufficientFundBreakdown();
                    return;
                }

                const expenseAmount = parseFloat(amountInput.value) || 0;
                // Use total_committed (spent + pending) instead of just spent
                const totalCommitted = (currentBudget.totalCommitted || currentBudget.spent) + (currentBudget.pendingExpenses || 0);
                const newTotalCommitted = totalCommitted + expenseAmount;
                const remainingAfterExpense = currentBudget.remaining - expenseAmount;

                // Get available amount from fund summary
                const availableAmountEl = document.getElementById('availableAmount');
                const availableAmount = availableAmountEl ? parseFloat(availableAmountEl.textContent.replace(/[^\d.]/g, '')) || 0 : 0;

                // Clear previous validation
                amountInput.classList.remove('is-invalid', 'is-valid', 'is-warning');
                hideBudgetAlert();

                if (expenseAmount <= 0) {
                    hideInsufficientFundBreakdown();
                    return;
                }

                // Check if expense amount itself exceeds budget total
                if (expenseAmount > currentBudget.total) {
                    showBudgetAlert('danger', `Expense amount (TZS ${expenseAmount.toLocaleString()}) cannot exceed the budget total amount (TZS ${currentBudget.total.toLocaleString()}).`);
                    amountInput.classList.add('is-invalid');
                    amountInput.classList.remove('is-valid', 'is-warning');
                    amountInput.setCustomValidity(`Expense amount cannot exceed budget total of TZS ${currentBudget.total.toLocaleString()}`);

                    // Update help text
                    const amountHelpText = document.getElementById('amountHelpText');
                    if (amountHelpText) {
                        amountHelpText.textContent = `❌ Amount exceeds budget total! Maximum: TZS ${currentBudget.total.toLocaleString()}`;
                        amountHelpText.className = 'text-danger fw-bold';
                    }

                    hideInsufficientFundBreakdown();
                    return;
                }

                // Check if expense would exceed budget (accounting for pending expenses)
                if (newTotalCommitted > currentBudget.total) {
                    showBudgetAlert('danger', `Expense would exceed budget limit! Budget total: TZS ${currentBudget.total.toLocaleString()}. Available budget: TZS ${currentBudget.remaining.toLocaleString()}`);
                    amountInput.classList.add('is-invalid');
                    amountInput.setCustomValidity(`Expense would exceed budget limit. Maximum allowed: TZS ${currentBudget.remaining.toLocaleString()}`);
                    hideInsufficientFundBreakdown();
                    return;
                }

                // Clear any previous custom validity
                amountInput.setCustomValidity('');

                // Check if expense exceeds available amount from fund summary
                if (availableAmount > 0 && expenseAmount > availableAmount) {
                    const shortfall = expenseAmount - availableAmount;
                    showBudgetAlert('warning', `Expense amount (TZS ${expenseAmount.toLocaleString()}) exceeds available funds (TZS ${availableAmount.toLocaleString()}). Shortfall: TZS ${shortfall.toLocaleString()}`);
                    amountInput.classList.add('is-warning');
                    showInsufficientFundBreakdown(expenseAmount, availableAmount);
                    return;
                }

                // Check if expense exceeds remaining available amount (fallback if fund summary not loaded)
                if (expenseAmount > currentBudget.remaining) {
                    const shortfall = expenseAmount - currentBudget.remaining;
                    showBudgetAlert('warning', `Expense amount exceeds available budget! Available: TZS ${currentBudget.remaining.toLocaleString()}. Shortfall: TZS ${shortfall.toLocaleString()}`);
                    amountInput.classList.add('is-warning');
                    showInsufficientFundBreakdown(expenseAmount, currentBudget.remaining);
                    return;
                }

                // Check if budget is not fully funded - but only show additional funding if funds are insufficient
                if (!currentBudget.funded) {
                    // Check if available amount is sufficient
                    const effectiveAvailable = availableAmount > 0 ? availableAmount : currentBudget.remaining;
                    if (expenseAmount > effectiveAvailable) {
                        const shortfall = expenseAmount - effectiveAvailable;
                        showBudgetAlert('warning', `Warning: This budget is only ${currentBudget.fundingPercent.toFixed(1)}% funded. Expense amount exceeds available funds. Shortfall: TZS ${shortfall.toLocaleString()}`);
                        amountInput.classList.add('is-warning');
                        showInsufficientFundBreakdown(expenseAmount, effectiveAvailable);
                        return;
                    } else {
                        // Budget not fully funded but funds are sufficient - just show warning, no additional funding needed
                        showBudgetAlert('warning', `Warning: This budget is only ${currentBudget.fundingPercent.toFixed(1)}% funded. Expense may not be payable until budget is fully funded.`);
                        amountInput.classList.add('is-warning');
                        hideInsufficientFundBreakdown();
                        return;
                    }
                }

                // Check if remaining amount is getting low
                if (remainingAfterExpense < (currentBudget.total * 0.1)) { // Less than 10% remaining
                    showBudgetAlert('warning', `Warning: This expense will leave only TZS ${remainingAfterExpense.toLocaleString()} (${((remainingAfterExpense / currentBudget.total) * 100).toFixed(1)}%) remaining in budget.`);
                    amountInput.classList.add('is-warning');
                    hideInsufficientFundBreakdown();
                    return;
                }

                // All good - show fund allocation preview
                amountInput.classList.add('is-valid');
                amountInput.classList.remove('is-invalid', 'is-warning');
                amountInput.setCustomValidity(''); // Clear any validation errors

                // Update help text
                const amountHelpText = document.getElementById('amountHelpText');
                if (amountHelpText && currentBudget) {
                    amountHelpText.textContent = `✓ Valid amount. Maximum: TZS ${currentBudget.total.toLocaleString()}`;
                    amountHelpText.className = 'text-success';
                }

                hideInsufficientFundBreakdown();
                showFundAllocationPreview(expenseAmount);
                showBudgetAlert('success', `Valid expense. Remaining budget after this expense: TZS ${remainingAfterExpense.toLocaleString()}`);
            }

            function showBudgetAlert(type, message) {
                budgetAlert.className = `alert alert-${type}`;
                budgetAlertText.textContent = message;
                budgetAlert.style.display = 'block';
            }

            function hideBudgetAlert() {
                budgetAlert.style.display = 'none';
            }

            function showInsufficientFundBreakdown(expenseAmount, availableAmount) {
                if (!currentBudget) return;

                const insufficientFundCard = document.getElementById('insufficientFundCard');
                if (!insufficientFundCard) {
                    console.error('Insufficient fund card element not found');
                    return;
                }

                insufficientFundCard.style.display = 'block';

                // Calculate shortfall
                const shortfall = expenseAmount - availableAmount;

                // Update shortfall display (will be updated after fetching fund breakdown)
                const shortfallAmountEl = document.getElementById('shortfallAmount');
                if (shortfallAmountEl) {
                    shortfallAmountEl.textContent = 'TZS ' + shortfall.toLocaleString();
                }

                // Fetch real fund breakdown from server to get available offering types
                fetch(`/finance/budgets/${currentBudget.id}/fund-breakdown?amount=${expenseAmount}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update shortfall to use remaining_shortfall (after primary offering is used)
                            const actualShortfall = data.remaining_shortfall || data.shortfall || shortfall;

                            // Update shortfall display with remaining shortfall
                            if (shortfallAmountEl) {
                                shortfallAmountEl.textContent = 'TZS ' + actualShortfall.toLocaleString();
                            }

                            // Show primary offering usage info
                            if (data.primary_offering_type && data.primary_offering_used > 0) {
                                showPrimaryOfferingUsage(data.primary_offering_type, data.primary_offering_used, data.primary_offering_available);
                            }

                            if (data.available_offering_types) {
                                displayAvailableFunds(data.available_offering_types);
                                showManualFundingSelection(data.available_offering_types, actualShortfall, data.primary_offering_type, data.primary_offering_used);
                            } else {
                                // Fallback: use fund summary data or calculate from budget
                                const fallbackTypes = calculateAvailableOfferingTypes();
                                displayAvailableFunds(fallbackTypes);
                                showManualFundingSelection(fallbackTypes, actualShortfall);
                            }
                        } else {
                            // Fallback: use fund summary data or calculate from budget
                            const fallbackTypes = calculateAvailableOfferingTypes();
                            displayAvailableFunds(fallbackTypes);
                            showManualFundingSelection(fallbackTypes, shortfall);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching fund breakdown:', error);
                        // Fallback: use fund summary data or calculate from budget
                        const fallbackTypes = calculateAvailableOfferingTypes();
                        displayAvailableFunds(fallbackTypes);
                        showManualFundingSelection(fallbackTypes, shortfall);
                    });
            }

            function displayAvailableFunds(availableTypes) {
                const availableFundsDisplay = document.getElementById('availableFundsDisplay');
                if (!availableFundsDisplay) {
                    console.error('Available funds display element not found');
                    return;
                }

                availableFundsDisplay.innerHTML = '';

                if (!availableTypes || Object.keys(availableTypes).length === 0) {
                    availableFundsDisplay.innerHTML = '<div class="text-muted text-center">No additional funds available</div>';
                    return;
                }

                let totalAvailable = 0;
                Object.entries(availableTypes).forEach(([type, amount]) => {
                    const amountValue = parseFloat(amount) || 0;
                    totalAvailable += amountValue;

                    const fundDiv = document.createElement('div');
                    fundDiv.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-white';
                    fundDiv.innerHTML = `
                                                                                                                <div>
                                                                                                                    <i class="fas fa-coins me-2 text-primary"></i>
                                                                                                                    <strong>${formatOfferingType(type)}</strong>
                                                                                                                </div>
                                                                                                                <span class="text-success fw-bold">TZS ${amountValue.toLocaleString()}</span>
                                                                                                            `;
                    availableFundsDisplay.appendChild(fundDiv);
                });

                // Add total
                const totalDiv = document.createElement('div');
                totalDiv.className = 'd-flex justify-content-between align-items-center mt-2 p-2 border rounded bg-light fw-bold';
                totalDiv.innerHTML = `
                                                                                                            <span>TOTAL AVAILABLE</span>
                                                                                                            <span class="text-primary">TZS ${totalAvailable.toLocaleString()}</span>
                                                                                                        `;
                availableFundsDisplay.appendChild(totalDiv);
            }

            function formatOfferingType(type) {
                return type.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            }

            function calculateAvailableOfferingTypes() {
                // Fallback calculation - in real implementation, this would come from the server
                // For now, we'll use a simple calculation based on budget remaining
                const remaining = currentBudget.remaining || 0;
                return {
                    'general': remaining * 0.3,
                    'building_fund': remaining * 0.25,
                    'special': remaining * 0.2,
                    'thanksgiving': remaining * 0.15,
                    'missions': remaining * 0.1
                };
            }

            function displayFundBreakdown(data, primaryFundsDiv, additionalFundingDiv) {
                // Display primary funding source
                primaryFundsDiv.innerHTML = '';

                if (data.primary_offering_type && data.current_funds[data.primary_offering_type]) {
                    const primaryAmount = data.current_funds[data.primary_offering_type];
                    const div = document.createElement('div');
                    div.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-primary bg-opacity-10';
                    div.innerHTML = `
                                                                                                                <span class="fw-bold">${data.primary_offering_type.replace('_', ' ').toUpperCase()} <span class="badge bg-primary">Primary</span></span>
                                                                                                                <span class="text-primary fw-bold">TZS ${primaryAmount.toLocaleString()}</span>
                                                                                                            `;
                    primaryFundsDiv.appendChild(div);

                    // Show how much will be used from primary
                    if (data.fund_allocation && data.fund_allocation.length > 0) {
                        const primaryAllocation = data.fund_allocation.find(a => a.is_primary);
                        if (primaryAllocation) {
                            const usedDiv = document.createElement('div');
                            usedDiv.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-info bg-opacity-10';
                            usedDiv.innerHTML = `
                                                                                                                        <span>Amount to be used:</span>
                                                                                                                        <span class="text-info fw-bold">TZS ${primaryAllocation.amount.toLocaleString()}</span>
                                                                                                                    `;
                            primaryFundsDiv.appendChild(usedDiv);
                        }
                    }
                }

                // Display additional funding options
                additionalFundingDiv.innerHTML = '';

                if (data.shortfall > 0) {
                    // Show shortfall amount
                    const shortfallDiv = document.createElement('div');
                    shortfallDiv.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-danger bg-opacity-25 fw-bold';
                    shortfallDiv.innerHTML = `
                                                                                                                <span>SHORTFALL AMOUNT</span>
                                                                                                                <span class="text-danger">TZS ${data.shortfall.toLocaleString()}</span>
                                                                                                            `;
                    additionalFundingDiv.appendChild(shortfallDiv);

                    // Show available offering types for selection
                    if (data.available_offering_types && Object.keys(data.available_offering_types).length > 0) {
                        const availableDiv = document.createElement('div');
                        availableDiv.className = 'mt-2';
                        availableDiv.innerHTML = '<h6 class="mb-2">Available Offering Types:</h6>';

                        Object.entries(data.available_offering_types).forEach(([type, amount]) => {
                            const typeDiv = document.createElement('div');
                            typeDiv.className = 'd-flex justify-content-between align-items-center mb-1 p-2 border rounded';
                            typeDiv.innerHTML = `
                                                                                                                        <span>${type.replace('_', ' ').toUpperCase()}</span>
                                                                                                                        <span class="text-success">TZS ${amount.toLocaleString()}</span>
                                                                                                                    `;
                            availableDiv.appendChild(typeDiv);
                        });

                        additionalFundingDiv.appendChild(availableDiv);

                        // Show manual funding selection
                        showManualFundingSelection(data.available_offering_types, data.shortfall);
                    }
                } else {
                    additionalFundingDiv.innerHTML = '<div class="text-center text-success"><i class="fas fa-check-circle"></i> No additional funding needed</div>';
                }
            }

            function showPrimaryOfferingUsage(primaryType, usedAmount, availableAmount) {
                // Display info about primary offering being used
                const insufficientFundCard = document.getElementById('insufficientFundCard');
                if (!insufficientFundCard) return;

                // Check if primary offering info already exists
                let primaryInfoDiv = document.getElementById('primaryOfferingUsageInfo');
                if (!primaryInfoDiv) {
                    primaryInfoDiv = document.createElement('div');
                    primaryInfoDiv.id = 'primaryOfferingUsageInfo';
                    primaryInfoDiv.className = 'alert alert-info mb-3';

                    // Insert before the shortfall summary
                    const shortfallSummary = document.getElementById('shortfallSummary');
                    if (shortfallSummary && shortfallSummary.parentNode) {
                        shortfallSummary.parentNode.insertBefore(primaryInfoDiv, shortfallSummary);
                    }
                }

                primaryInfoDiv.innerHTML = `
                                                                                                            <div class="d-flex align-items-center">
                                                                                                                <i class="fas fa-info-circle me-2"></i>
                                                                                                                <div>
                                                                                                                    <strong>Primary Offering (${formatOfferingType(primaryType)}) will be used:</strong>
                                                                                                                    <span class="ms-2 fw-bold text-primary">TZS ${usedAmount.toLocaleString()}</span>
                                                                                                                    <span class="text-muted ms-2">(Available: TZS ${availableAmount.toLocaleString()})</span>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        `;
            }

            function showManualFundingSelection(availableTypes, shortfall, primaryOfferingType, primaryOfferingUsed) {
                const manualSection = document.getElementById('manualFundingSection');
                const inputsContainer = document.getElementById('additionalFundingInputs');

                if (!manualSection || !inputsContainer) {
                    console.error('Manual funding section elements not found');
                    return;
                }

                // Clear previous inputs
                inputsContainer.innerHTML = '';

                // Show the manual section
                manualSection.style.display = 'block';

                // If primary offering is being used, automatically add it as a hidden/read-only field
                if (primaryOfferingType && primaryOfferingUsed > 0) {
                    addPrimaryOfferingInput(primaryOfferingType, primaryOfferingUsed);
                }

                // Only show additional funding inputs if there's still a shortfall
                if (shortfall > 0) {
                    // Create first additional funding input
                    addFundingInput(availableTypes, shortfall);
                }

                // Update remaining shortfall display
                updateRemainingShortfall();

                // Add event listener for add button
                const addBtn = document.getElementById('addFundingSourceBtn');
                if (addBtn) {
                    addBtn.onclick = () => {
                        addFundingInput(availableTypes, shortfall);
                    };
                }
            }

            function addPrimaryOfferingInput(primaryType, amount) {
                const inputsContainer = document.getElementById('additionalFundingInputs');
                const inputIndex = inputsContainer.children.length;

                const inputDiv = document.createElement('div');
                inputDiv.className = 'col-md-12 mb-3';
                inputDiv.innerHTML = `
                                                                                                            <div class="card border border-primary">
                                                                                                                <div class="card-body">
                                                                                                                    <div class="row align-items-center">
                                                                                                                        <div class="col-md-5">
                                                                                                                            <label class="form-label">Primary Offering Type</label>
                                                                                                                            <input type="text" class="form-control" value="${formatOfferingType(primaryType)}" readonly style="background-color: #e7f3ff;">
                                                                                                                            <input type="hidden" name="additional_funding[${inputIndex}][offering_type]" value="${primaryType}">
                                                                                                                        </div>
                                                                                                                        <div class="col-md-5">
                                                                                                                            <label class="form-label">Amount (TZS)</label>
                                                                                                                            <input type="number" class="form-control funding-amount-input" 
                                                                                                                                   name="additional_funding[${inputIndex}][amount]" 
                                                                                                                                   value="${amount}" 
                                                                                                                                   readonly 
                                                                                                                                   style="background-color: #e7f3ff;">
                                                                                                                            <small class="text-muted"><i class="fas fa-info-circle"></i> Automatically used from primary offering</small>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2">
                                                                                                                            <span class="badge bg-primary">Primary</span>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        `;

                inputsContainer.appendChild(inputDiv);
            }

            function addFundingInput(availableTypes, shortfall) {
                const inputsContainer = document.getElementById('additionalFundingInputs');
                const inputIndex = inputsContainer.children.length;

                const inputDiv = document.createElement('div');
                inputDiv.className = 'col-md-12 mb-3';
                inputDiv.innerHTML = `
                                                                                                            <div class="card border">
                                                                                                                <div class="card-body">
                                                                                                                    <div class="row align-items-end">
                                                                                                                        <div class="col-md-5">
                                                                                                                            <label class="form-label">Offering Type *</label>
                                                                                                                            <select class="form-select funding-type-select" name="additional_funding[${inputIndex}][offering_type]" required>
                                                                                                                                <option value="">Select Offering Type</option>
                                                                                                                                ${Object.keys(availableTypes).map(type => {
                    const available = parseFloat(availableTypes[type]) || 0;
                    return `<option value="${type}" data-available="${available}">${formatOfferingType(type)} (Available: TZS ${available.toLocaleString()})</option>`;
                }).join('')}
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-5">
                                                                                                                            <label class="form-label">Amount (TZS) *</label>
                                                                                                                            <input type="number" class="form-control funding-amount-input" 
                                                                                                                                   name="additional_funding[${inputIndex}][amount]" 
                                                                                                                                   min="0" step="0.01" 
                                                                                                                                   placeholder="Enter amount"
                                                                                                                                   required>
                                                                                                                            <small class="text-muted">Max available: <span class="max-available">TZS 0</span></small>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-2">
                                                                                                                            <button type="button" class="btn btn-outline-danger w-100" onclick="removeFundingInput(this)" title="Remove">
                                                                                                                                <i class="fas fa-trash"></i>
                                                                                                                            </button>
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        `;

                inputsContainer.appendChild(inputDiv);

                // Add change events
                const typeSelect = inputDiv.querySelector('.funding-type-select');
                const amountInput = inputDiv.querySelector('.funding-amount-input');
                const maxAvailableSpan = inputDiv.querySelector('.max-available');

                // Update max available when type changes
                typeSelect.addEventListener('change', function () {
                    const selectedOption = this.options[this.selectedIndex];
                    const available = parseFloat(selectedOption.dataset.available) || 0;
                    maxAvailableSpan.textContent = 'TZS ' + available.toLocaleString();
                    amountInput.setAttribute('max', available);

                    // Update remaining shortfall
                    updateRemainingShortfall();
                });

                // Update remaining shortfall when amount changes
                amountInput.addEventListener('input', function () {
                    const selectedOption = typeSelect.options[typeSelect.selectedIndex];
                    const available = parseFloat(selectedOption.dataset.available) || 0;
                    const enteredAmount = parseFloat(this.value) || 0;

                    // Validate against available amount
                    if (enteredAmount > available) {
                        this.setCustomValidity(`Amount cannot exceed available: TZS ${available.toLocaleString()}`);
                        this.classList.add('is-invalid');
                    } else {
                        this.setCustomValidity('');
                        this.classList.remove('is-invalid');
                    }

                    updateRemainingShortfall();
                });

                // Initialize max available if type is pre-selected
                if (typeSelect.value) {
                    typeSelect.dispatchEvent(new Event('change'));
                }
            }

            function updateRemainingShortfall() {
                // Check if insufficient fund card is visible
                const insufficientFundCard = document.getElementById('insufficientFundCard');
                if (!insufficientFundCard || insufficientFundCard.style.display === 'none') {
                    return; // Exit early if card is not visible
                }

                const amountInputs = document.querySelectorAll('.funding-amount-input');
                let totalAllocated = 0;

                amountInputs.forEach(input => {
                    const amount = parseFloat(input.value) || 0;
                    totalAllocated += amount;
                });

                // Get expense amount
                const amountInput = document.getElementById('amount');
                const expenseAmount = amountInput ? (parseFloat(amountInput.value) || 0) : 0;

                // Get primary offering used amount (from readonly input if exists)
                let primaryOfferingUsed = 0;
                const primaryOfferingInput = document.querySelector('input[name*="additional_funding"][readonly]');
                if (primaryOfferingInput) {
                    primaryOfferingUsed = parseFloat(primaryOfferingInput.value) || 0;
                }

                // Calculate shortfall after using primary offering
                // Shortfall = Expense Amount - Primary Offering Used
                const shortfall = expenseAmount - primaryOfferingUsed;

                // Calculate remaining shortfall after all additional funding
                // Exclude primary offering from totalAllocated since it's already accounted for in shortfall
                const additionalFundingOnly = totalAllocated - primaryOfferingUsed;
                const remainingShortfall = shortfall - additionalFundingOnly;

                // Update total allocated display
                const totalAllocatedEl = document.getElementById('totalAllocatedAmount');
                if (totalAllocatedEl) {
                    totalAllocatedEl.textContent = 'TZS ' + totalAllocated.toLocaleString();
                    if (totalAllocated >= shortfall) {
                        totalAllocatedEl.className = 'fw-bold text-success';
                    } else {
                        totalAllocatedEl.className = 'fw-bold text-warning';
                    }
                }

                // Update remaining shortfall display
                const remainingShortfallDisplay = document.getElementById('remainingShortfallDisplay');
                if (remainingShortfallDisplay) {
                    if (remainingShortfall <= 0) {
                        remainingShortfallDisplay.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-1"></i>Fully Covered</span>';
                    } else {
                        remainingShortfallDisplay.innerHTML = `<span class="text-danger"><i class="fas fa-exclamation-circle me-1"></i>Remaining: TZS ${remainingShortfall.toLocaleString()}</span>`;
                    }
                }

                // Update shortfall summary alert
                const shortfallSummary = document.getElementById('shortfallSummary');
                if (shortfallSummary) {
                    if (remainingShortfall <= 0) {
                        shortfallSummary.className = 'alert alert-success mb-3';
                    } else {
                        shortfallSummary.className = 'alert alert-danger mb-3';
                    }
                }
            }

            // Make removeFundingInput available globally
            window.removeFundingInput = function (button) {
                button.closest('.col-md-12').remove();
                updateRemainingShortfall();
            };

            function displayMockFundBreakdown(expenseAmount, currentFundsDiv, requiredFundsDiv) {
                // Fallback to mock data
                const currentFunds = calculateCurrentAvailableFunds();
                const totalAvailable = Object.values(currentFunds).reduce((sum, amount) => sum + amount, 0);
                const shortfall = expenseAmount - totalAvailable;

                // Display current available funds
                currentFundsDiv.innerHTML = '';
                Object.entries(currentFunds).forEach(([type, amount]) => {
                    const div = document.createElement('div');
                    div.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded';
                    div.innerHTML = `
                                                                                                                <span class="fw-bold">${type.replace('_', ' ').toUpperCase()}</span>
                                                                                                                <span class="text-success">TZS ${amount.toLocaleString()}</span>
                                                                                                            `;
                    currentFundsDiv.appendChild(div);
                });

                // Add total
                const totalDiv = document.createElement('div');
                totalDiv.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-light fw-bold';
                totalDiv.innerHTML = `
                                                                                                            <span>TOTAL AVAILABLE</span>
                                                                                                            <span class="text-primary">TZS ${totalAvailable.toLocaleString()}</span>
                                                                                                        `;
                currentFundsDiv.appendChild(totalDiv);

                // Calculate and display required additional funds
                const requiredFunds = calculateRequiredAdditionalFunds(shortfall);
                requiredFundsDiv.innerHTML = '';

                if (requiredFunds.length > 0) {
                    requiredFunds.forEach(fund => {
                        const div = document.createElement('div');
                        div.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-warning bg-opacity-25';
                        div.innerHTML = `
                                                                                                                    <span class="fw-bold">${fund.offering_type.replace('_', ' ').toUpperCase()}</span>
                                                                                                                    <span class="text-warning fw-bold">TZS ${fund.amount.toLocaleString()}</span>
                                                                                                                `;
                        requiredFundsDiv.appendChild(div);
                    });
                }

                // Add shortfall total
                const shortfallDiv = document.createElement('div');
                shortfallDiv.className = 'd-flex justify-content-between align-items-center mb-2 p-2 border rounded bg-danger bg-opacity-25 fw-bold';
                shortfallDiv.innerHTML = `
                                                                                                            <span>TOTAL SHORTFALL</span>
                                                                                                            <span class="text-danger">TZS ${shortfall.toLocaleString()}</span>
                                                                                                        `;
                requiredFundsDiv.appendChild(shortfallDiv);
            }

            function calculateCurrentAvailableFunds() {
                // This would normally be calculated from the server, but for demo purposes we'll simulate
                const mockAvailableFunds = {
                    'building_fund': currentBudget.remaining * 0.4, // 40% of remaining budget
                    'general': currentBudget.remaining * 0.3,        // 30% of remaining budget
                    'special': currentBudget.remaining * 0.2,        // 20% of remaining budget
                    'thanksgiving': currentBudget.remaining * 0.1    // 10% of remaining budget
                };

                return mockAvailableFunds;
            }

            function calculateRequiredAdditionalFunds(shortfall) {
                // This would normally be calculated from available offering funds, but for demo purposes we'll simulate
                const mockAdditionalFunds = [
                    { offering_type: 'general', amount: shortfall * 0.6 },
                    { offering_type: 'building_fund', amount: shortfall * 0.4 }
                ];

                return mockAdditionalFunds;
            }

            function hideInsufficientFundBreakdown() {
                const insufficientFundCard = document.getElementById('insufficientFundCard');
                insufficientFundCard.style.display = 'none';
            }

            function fetchBudgetInfo(budgetId) {
                if (!budgetId) return;

                fetch(`/finance/budgets/${budgetId}/info`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.budget) {
                            // Update currentBudget with latest info including pending expenses
                            if (currentBudget) {
                                currentBudget.spent = parseFloat(data.budget.spent_amount) || 0;
                                currentBudget.pendingExpenses = parseFloat(data.budget.pending_expenses_amount) || 0;
                                currentBudget.totalCommitted = parseFloat(data.budget.total_committed) || 0;
                                currentBudget.remaining = parseFloat(data.budget.remaining_amount) || 0;

                                // Update the budget select option data attributes
                                const budgetSelect = document.getElementById('budget_id');
                                const selectedOption = budgetSelect.options[budgetSelect.selectedIndex];
                                if (selectedOption) {
                                    selectedOption.dataset.spent = currentBudget.spent;
                                    selectedOption.dataset.remaining = currentBudget.remaining;
                                }

                                // Update display
                                updateBudgetDisplay();
                                // Re-validate expense amount
                                validateExpenseAmount();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching budget info:', error);
                    });
            }

            function fetchFundSummary(budgetId) {
                if (!budgetId) {
                    hideFundSummary();
                    return;
                }

                const fundSummaryCard = document.getElementById('fundSummaryCard');

                // Show loading state
                fundSummaryCard.style.display = 'block';
                document.getElementById('totalIncome').textContent = 'Loading...';
                document.getElementById('usedAmount').textContent = 'Loading...';
                document.getElementById('availableAmount').textContent = 'Loading...';

                fetch(`/finance/budgets/${budgetId}/fund-summary`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            console.log('Fund Summary Data:', data.fund_summary); // Debug log
                            displayFundSummary(data.fund_summary);
                        } else {
                            console.error('Failed to fetch fund summary:', data.message);
                            hideFundSummary();
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching fund summary:', error);
                        hideFundSummary();
                    });
            }

            function displayFundSummary(summary) {
                const fundSummaryCard = document.getElementById('fundSummaryCard');

                console.log('Displaying Fund Summary:', summary); // Debug log

                // Update amounts
                document.getElementById('totalIncome').textContent = 'TZS ' + summary.total_income.toLocaleString();
                document.getElementById('usedAmount').textContent = 'TZS ' + summary.used_amount.toLocaleString();
                document.getElementById('availableAmount').textContent = 'TZS ' + summary.available_amount.toLocaleString();

                // Update progress bars
                const incomeProgress = document.getElementById('incomeProgress');
                const usedProgress = document.getElementById('usedProgress');

                // Income progress (always 100% of total)
                incomeProgress.style.width = '100%';

                // Used progress (percentage of total income)
                usedProgress.style.width = summary.used_percentage + '%';

                // Update available amount styling (already has gradient background, just ensure text is white)
                const availableElement = document.getElementById('availableAmount');
                availableElement.style.color = '#fff';
                availableElement.style.fontWeight = 'bold';

                // Show the fund summary card
                fundSummaryCard.style.display = 'block';
            }

            function hideFundSummary() {
                const fundSummaryCard = document.getElementById('fundSummaryCard');
                fundSummaryCard.style.display = 'none';
            }


            function showFundAllocationPreview(expenseAmount) {
                if (!currentBudget || !currentBudget.funded) return;

                // Simulate fund allocation calculation (in real implementation, this would be an AJAX call)
                const mockAllocations = calculateMockFundAllocation(expenseAmount);

                if (mockAllocations.length > 0) {
                    let previewHtml = '<div class="mt-2"><strong>Fund Allocation Preview:</strong><ul class="list-unstyled mt-1">';
                    mockAllocations.forEach(allocation => {
                        const primaryIndicator = allocation.is_primary ? ' <span class="badge bg-primary">Primary</span>' : '';
                        previewHtml += `<li>• ${allocation.offering_type.replace('_', ' ').toUpperCase()}${primaryIndicator}: TZS ${allocation.amount.toLocaleString()}</li>`;
                    });
                    previewHtml += '</ul></div>';

                    // Add preview to budget alert
                    const existingPreview = document.getElementById('fundAllocationPreview');
                    if (existingPreview) {
                        existingPreview.remove();
                    }

                    const previewDiv = document.createElement('div');
                    previewDiv.id = 'fundAllocationPreview';
                    previewDiv.innerHTML = previewHtml;
                    budgetAlert.appendChild(previewDiv);
                }
            }

            function calculateMockFundAllocation(expenseAmount) {
                // This is a mock calculation - in real implementation, this would be calculated server-side
                const allocations = [];
                let remaining = expenseAmount;

                // Simulate primary offering type allocation first
                if (currentBudget.primary_offering_type) {
                    const primaryAmount = Math.min(remaining, currentBudget.remaining * 0.6); // Assume 60% from primary
                    if (primaryAmount > 0) {
                        allocations.push({
                            offering_type: currentBudget.primary_offering_type,
                            amount: primaryAmount,
                            is_primary: true
                        });
                        remaining -= primaryAmount;
                    }
                }

                // Simulate secondary offering type allocation
                if (remaining > 0) {
                    allocations.push({
                        offering_type: 'general',
                        amount: remaining,
                        is_primary: false
                    });
                }

                return allocations;
            }
        });
    </script>

    <style>
        /* Expense Modal Styling - Matching Budget Modal */
        .expense-modal-content {
            border: none;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .expense-modal-header {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
            padding: 1.5rem;
            border-bottom: none;
        }

        .expense-modal-header .modal-title {
            color: white;
            font-weight: 600;
            font-size: 1.25rem;
        }

        .expense-modal-content .modal-icon-wrapper {
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

        .expense-modal-content .modal-body {
            padding: 2rem;
            background: #f8f9fa;
            max-height: calc(100vh - 250px);
            overflow-y: auto;
            overflow-x: hidden;
        }

        .expense-modal-content .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .expense-modal-content .form-control,
        .expense-modal-content .form-select {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .expense-modal-content .form-control:focus,
        .expense-modal-content .form-select:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
            outline: none;
        }

        .expense-modal-content .form-control:hover,
        .expense-modal-content .form-select:hover {
            border-color: #ced4da;
        }

        /* Budget Information Card Styling */
        .expense-modal-content .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            margin-top: 1.5rem;
        }

        .expense-modal-content .card-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-bottom: 2px solid #dee2e6;
            padding: 1rem 1.25rem;
            border-radius: 10px 10px 0 0;
        }

        .expense-modal-content .card-header h6 {
            color: #495057;
            font-weight: 600;
            margin: 0;
        }

        .expense-modal-content .card-body {
            padding: 1.25rem;
        }

        /* Modal Footer Styling */
        .expense-modal-footer {
            background: white;
            border-top: 1px solid #e9ecef;
            padding: 1.25rem 2rem;
            border-radius: 0 0 12px 12px;
        }

        .expense-submit-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
            color: white;
        }

        .expense-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
            background: linear-gradient(135deg, #ec4555 0%, #d83343 100%);
            color: white;
        }

        .expense-submit-btn:active {
            transform: translateY(0);
        }

        .expense-modal-footer .btn-light {
            border: 2px solid #e9ecef;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .expense-modal-footer .btn-light:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
            transform: translateY(-1px);
        }

        /* Textarea Styling */
        .expense-modal-content textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        /* Select Dropdown Styling */
        .expense-modal-content .form-select {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
        }

        /* Alert Styling in Modal */
        .expense-modal-content .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
        }

        .expense-modal-content .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
        }

        .expense-modal-content .alert-danger {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        .expense-modal-content .alert-warning {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }

        .expense-modal-content .alert-success {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        /* Modal Animation */
        .modal.fade .expense-modal-content {
            transform: scale(0.9);
            transition: transform 0.3s ease-out;
        }

        .modal.show .expense-modal-content {
            transform: scale(1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .expense-modal-content .modal-body {
                padding: 1.5rem;
            }

            .expense-modal-header {
                padding: 1.25rem;
            }

            .expense-modal-content .modal-icon-wrapper {
                width: 40px;
                height: 40px;
                font-size: 1.25rem;
            }

            .expense-modal-footer {
                padding: 1rem 1.5rem;
            }

            .expense-submit-btn,
            .expense-modal-footer .btn-light {
                padding: 0.625rem 1.5rem;
                font-size: 0.9rem;
            }
        }

        /* Required Field Indicator */
        .expense-modal-content .form-label:has(+ .form-control[required]):after,
        .expense-modal-content .form-label:has(+ .form-select[required]):after {
            content: " *";
            color: #dc3545;
            font-weight: bold;
        }

        /* Smooth Transitions */
        .expense-modal-content * {
            transition: all 0.2s ease;
        }

        /* Custom scrollbar for modal body */
        .expense-modal-content .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .expense-modal-content .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .expense-modal-content .modal-body::-webkit-scrollbar-thumb {
            background: #dc3545;
            border-radius: 10px;
        }

        .expense-modal-content .modal-body::-webkit-scrollbar-thumb:hover {
            background: #c82333;
        }

        /* Budget Info Card Enhancements */
        #budgetInfo {
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

        /* Progress Bar Styling */
        .expense-modal-content .progress {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
        }

        .expense-modal-content .progress-bar {
            transition: width 0.6s ease;
        }

        /* Additional Funding Input Styling */
        .expense-modal-content .funding-type-select,
        .expense-modal-content .funding-amount-input {
            border: 2px solid #e9ecef;
        }

        .expense-modal-content .funding-type-select:focus,
        .expense-modal-content .funding-amount-input:focus {
            border-color: #dc3545;
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.15);
        }

        .expense-modal-content .funding-amount-input.is-invalid {
            border-color: #dc3545;
        }
    </style>

    <script>
        // Form validation before submission
        document.addEventListener('DOMContentLoaded', function () {
            window.validateExpenseForm = function (event) {
                const amountInput = document.getElementById('amount');
                const budgetSelect = document.getElementById('budget_id');

                // Validate expense amount against budget total if budget is selected
                if (budgetSelect && budgetSelect.value && amountInput) {
                    const expenseAmount = parseFloat(amountInput.value) || 0;
                    const selectedOption = budgetSelect.options[budgetSelect.selectedIndex];

                    if (selectedOption && expenseAmount > 0) {
                        const budgetTotal = parseFloat(selectedOption.dataset.total) || 0;

                        // Check if expense amount exceeds budget total
                        if (expenseAmount > budgetTotal) {
                            event.preventDefault();
                            event.stopPropagation();
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Expense Amount',
                                html: `<div class="text-start">
                                                                                                                            <p><strong>The expense amount cannot exceed the budget total amount.</strong></p>
                                                                                                                            <ul class="text-start">
                                                                                                                                <li>Expense Amount: <strong class="text-danger">TZS ${expenseAmount.toLocaleString()}</strong></li>
                                                                                                                                <li>Budget Total: <strong class="text-info">TZS ${budgetTotal.toLocaleString()}</strong></li>
                                                                                                                                <li>Excess: <strong class="text-danger">TZS ${(expenseAmount - budgetTotal).toLocaleString()}</strong></li>
                                                                                                                            </ul>
                                                                                                                            <p class="mt-2">Please reduce the expense amount to be within the budget limit.</p>
                                                                                                                        </div>`,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545',
                                width: '500px'
                            });
                            amountInput.focus();
                            amountInput.select();
                            return false;
                        }

                        // Also check if expense would exceed budget when combined with already spent
                        const currentSpent = parseFloat(selectedOption.dataset.spent) || 0;
                        const pendingExpenses = parseFloat(selectedOption.dataset.pending) || 0;
                        const newTotalSpent = currentSpent + pendingExpenses + expenseAmount;

                        if (newTotalSpent > budgetTotal) {
                            const remainingBudget = budgetTotal - currentSpent - pendingExpenses;
                            event.preventDefault();
                            event.stopPropagation();
                            Swal.fire({
                                icon: 'error',
                                title: 'Would Exceed Budget Limit',
                                html: `<div class="text-start">
                                                                                                                            <p><strong>This expense would exceed the budget limit when combined with existing expenses.</strong></p>
                                                                                                                            <ul class="text-start">
                                                                                                                                <li>Expense Amount: <strong>TZS ${expenseAmount.toLocaleString()}</strong></li>
                                                                                                                                <li>Already Spent: <strong>TZS ${currentSpent.toLocaleString()}</strong></li>
                                                                                                                                <li>Pending Expenses: <strong>TZS ${pendingExpenses.toLocaleString()}</strong></li>
                                                                                                                                <li>Budget Total: <strong>TZS ${budgetTotal.toLocaleString()}</strong></li>
                                                                                                                                <li>Remaining Budget: <strong class="text-info">TZS ${remainingBudget.toLocaleString()}</strong></li>
                                                                                                                            </ul>
                                                                                                                            <p class="mt-2">Maximum expense amount allowed: <strong class="text-success">TZS ${remainingBudget.toLocaleString()}</strong></p>
                                                                                                                        </div>`,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545',
                                width: '500px'
                            });
                            amountInput.focus();
                            amountInput.select();
                            return false;
                        }
                    }
                }

                const insufficientFundCard = document.getElementById('insufficientFundCard');

                // If insufficient fund card is visible, validate that shortfall is covered
                if (insufficientFundCard && insufficientFundCard.style.display !== 'none') {
                    const remainingShortfallDisplay = document.getElementById('remainingShortfallDisplay');
                    if (remainingShortfallDisplay) {
                        const remainingText = remainingShortfallDisplay.textContent || '';
                        if (remainingText.includes('Remaining:') && !remainingText.includes('Fully Covered')) {
                            event.preventDefault();
                            Swal.fire({
                                icon: 'warning',
                                title: 'Incomplete Funding',
                                html: 'Please allocate additional funding sources to cover the remaining shortfall amount before submitting.',
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#dc3545'
                            });
                            return false;
                        }
                    }

                    // Validate that all funding inputs are filled
                    const fundingInputs = document.querySelectorAll('.funding-amount-input');
                    const fundingTypes = document.querySelectorAll('.funding-type-select');
                    let hasEmptyFields = false;

                    fundingInputs.forEach((input, index) => {
                        if (!input.value || parseFloat(input.value) <= 0) {
                            hasEmptyFields = true;
                            input.classList.add('is-invalid');
                        }
                        if (index < fundingTypes.length && !fundingTypes[index].value) {
                            hasEmptyFields = true;
                            fundingTypes[index].classList.add('is-invalid');
                        }
                    });

                    if (hasEmptyFields) {
                        event.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Incomplete Information',
                            html: 'Please fill in all additional funding source fields (offering type and amount).',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                        return false;
                    }
                }

                return true;
            };
        });
    </script>
@endsection