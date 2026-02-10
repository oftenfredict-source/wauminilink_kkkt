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

        /* Premium Modal Styles */
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

        .section-header {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #7f8c8d;
            margin-bottom: 1.25rem;
            position: relative;
            padding-left: 1rem;
        }

        .section-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            height: 12px;
            width: 3px;
            background: #b02a37;
            /* Brand Red */
            border-radius: 2px;
        }

        /* Form Floating Customization */
        .form-floating>.form-control,
        .form-floating>.form-select {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            background-color: #fcfcfc;
            font-size: 0.95rem;
            font-weight: 500;
            color: #34495e;
            height: 3.5rem;
            line-height: normal;
        }

        .form-floating>.form-control:focus,
        .form-floating>.form-select:focus {
            border-color: #b02a37;
            background-color: #fff;
            box-shadow: 0 0 0 4px rgba(176, 42, 55, 0.1);
        }

        .form-floating>label {
            padding-left: 1rem;
            color: #95a5a6;
        }

        /* Fix for select padding to prevent overlap */
        .form-floating>.form-select {
            padding-top: 1.625rem;
            padding-bottom: 0.625rem;
        }

        .primary-funding-box {
            background: #fdf2f3;
            /* Very light red */
            border: 1px dashed #e6b0b6;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .primary-funding-box.active {
            background: #e8f5e9;
            /* Light green when active */
            border-color: #a5d6a7;
            border-style: solid;
        }
    </style>
    <div class="container-fluid px-4">
        <!-- Page Title and Quick Actions - Compact Collapsible -->
        <div class="card border-0 shadow-sm mb-3 actions-card">
            <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header"
                onclick="toggleActions()">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-wallet me-2"></i>Budgets Management
                    </h1>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
                </div>
            </div>
            <div class="card-body p-3" id="actionsBody">
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                        data-bs-target="#addBudgetModal">
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
                            <span class="badge bg-white text-primary rounded-pill ms-2"
                                id="activeFiltersCount">{{ (request('fiscal_year') ? 1 : 0) + (request('budget_type') ? 1 : 0) + (request('status') ? 1 : 0) }}</span>
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
                                <option value="{{ $year }}" {{ request('fiscal_year') == $year ? 'selected' : '' }}>{{ $year }}
                                </option>
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
                            <option value="operational" {{ request('budget_type') == 'operational' ? 'selected' : '' }}>
                                Operational</option>
                            <option value="capital" {{ request('budget_type') == 'capital' ? 'selected' : '' }}>Capital
                            </option>
                            <option value="program" {{ request('budget_type') == 'program' ? 'selected' : '' }}>Program
                            </option>
                            <option value="special" {{ request('budget_type') == 'special' ? 'selected' : '' }}>Special
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
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed
                            </option>
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

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-primary text-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-white-50 fw-bold text-uppercase">A. INJILI</small>
                            <i class="fas fa-bible text-white-50"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">TZS {{ number_format($categorySummaries['injili']['total'], 0) }}</h5>
                        <div class="mt-2 small text-white-50">
                            Budget Remaining: TZS {{ number_format($categorySummaries['injili']['remaining'], 0) }}
                        </div>
                        <div class="small fw-bold border-top border-white border-opacity-10 mt-1 pt-1">
                            Available for Allocation: TZS {{ number_format($categorySummaries['injili']['available'], 0) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-success text-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-white-50 fw-bold text-uppercase">B. UMOJA/IDARA</small>
                            <i class="fas fa-users text-white-50"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">TZS {{ number_format($categorySummaries['umoja']['total'], 0) }}</h5>
                        <div class="mt-2 small text-white-50">
                            Budget Remaining: TZS {{ number_format($categorySummaries['umoja']['remaining'], 0) }}
                        </div>
                        <div class="small fw-bold border-top border-white border-opacity-10 mt-1 pt-1">
                            Available for Allocation: TZS {{ number_format($categorySummaries['umoja']['available'], 0) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-warning text-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-white-50 fw-bold text-uppercase">C. MAJENGO</small>
                            <i class="fas fa-building text-white-50"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">TZS {{ number_format($categorySummaries['majengo']['total'], 0) }}</h5>
                        <div class="mt-2 small text-white-50">
                            Budget Remaining: TZS {{ number_format($categorySummaries['majengo']['remaining'], 0) }}
                        </div>
                        <div class="small fw-bold border-top border-white border-opacity-10 mt-1 pt-1">
                            Available for Allocation: TZS {{ number_format($categorySummaries['majengo']['available'], 0) }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm bg-info text-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-white-50 fw-bold text-uppercase">ZINGINEZO</small>
                            <i class="fas fa-folder-open text-white-50"></i>
                        </div>
                        <h5 class="mb-0 fw-bold">TZS {{ number_format($categorySummaries['other']['total'], 0) }}</h5>
                        <div class="mt-2 small text-white-50">
                            Budget Remaining: TZS {{ number_format($categorySummaries['other']['remaining'], 0) }}
                        </div>
                        <div class="small fw-bold border-top border-white border-opacity-10 mt-1 pt-1">
                            Available for Allocation: TZS {{ number_format($categorySummaries['other']['available'], 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Budgets Categorized Tabs -->
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-white border-bottom p-0">
                <ul class="nav nav-tabs border-0" id="budgetTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold text-uppercase py-3 px-4" id="all-tab" data-bs-toggle="tab"
                            data-bs-target="#all" type="button" role="tab">All Budgets</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-uppercase py-3 px-4 text-primary" id="injili-tab"
                            data-bs-toggle="tab" data-bs-target="#injili" type="button" role="tab">A. INJILI</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-uppercase py-3 px-4 text-success" id="umoja-tab"
                            data-bs-toggle="tab" data-bs-target="#umoja" type="button" role="tab">B. UMOJA/IDARA</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-uppercase py-3 px-4 text-warning" id="majengo-tab"
                            data-bs-toggle="tab" data-bs-target="#majengo" type="button" role="tab">C. MAJENGO</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-uppercase py-3 px-4 text-info" id="other-tab"
                            data-bs-toggle="tab" data-bs-target="#other" type="button" role="tab">D. ZINGINEZO</button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content" id="budgetTabsContent">
                    {{-- All Budgets Tab --}}
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        @include('finance.partials.budget_table', ['budgets' => $allBudgets, 'category' => 'All'])
                    </div>
                    {{-- Injili Tab --}}
                    <div class="tab-pane fade" id="injili" role="tabpanel">
                        @include('finance.partials.budget_table', ['budgets' => $injiliBudgets, 'category' => 'Injili'])
                    </div>
                    {{-- Umoja Tab --}}
                    <div class="tab-pane fade" id="umoja" role="tabpanel">
                        @include('finance.partials.budget_table', ['budgets' => $umojaBudgets, 'category' => 'Umoja/Idara'])
                    </div>
                    {{-- Majengo Tab --}}
                    <div class="tab-pane fade" id="majengo" role="tabpanel">
                        @include('finance.partials.budget_table', ['budgets' => $majengoBudgets, 'category' => 'Majengo'])
                    </div>
                    {{-- Other Tab --}}
                    <div class="tab-pane fade" id="other" role="tabpanel">
                        @include('finance.partials.budget_table', ['budgets' => $otherBudgets, 'category' => 'Zinginezo'])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Budget Modal -->
    <div class="modal fade" id="addBudgetModal" tabindex="-1" aria-labelledby="addBudgetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content premium-modal border-0">
                <div class="modal-header premium-modal-header align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="me-3 d-flex align-items-center justify-content-center bg-danger bg-opacity-10 rounded-circle"
                            style="width: 48px; height: 48px;">
                            <i class="fas fa-wallet text-danger fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="premium-modal-title mb-0" id="addBudgetModalLabel">New Budget</h5>
                            <div class="premium-modal-subtitle">Configure financial planning & allocation</div>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('finance.budgets.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-0">
                        {{-- Section 1: Budget Details --}}
                        <div class="p-4 bg-white">
                            <div class="section-header">Budget Details</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="budget_name" name="budget_name"
                                            placeholder="Enter budget name" required>
                                        <label for="budget_name">Budget Name *</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="budget_type" name="budget_type" required>
                                            <option value="">Select Type</option>
                                            <option value="operational">Operational</option>
                                            <option value="capital">Capital</option>
                                            <option value="program">Program</option>
                                            <option value="special">Special</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <label for="budget_type">Budget Type *</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="fiscal_year" name="fiscal_year" required>
                                            <option value="">Select Year</option>
                                            @for($year = date('Y') - 1; $year <= date('Y') + 2; $year++)
                                                <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                        <label for="fiscal_year">Fiscal Year *</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Budget Type Field -->
                            <div class="row d-none" id="custom_budget_type_row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="custom_budget_type"
                                            name="custom_budget_type" placeholder="Enter custom type">
                                        <label for="custom_budget_type">Custom Budget Type *</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Purpose & Funding --}}
                        <div class="p-4 bg-white">
                            <div class="section-header">Purpose & Funding</div>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="purpose" name="purpose" required>
                                            <option value="">Select Purpose</option>
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
                                        <label for="purpose">Budget Purpose *</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Custom Purpose Field -->
                            <div class="row d-none mt-2" id="custom_purpose_row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="custom_purpose" name="custom_purpose"
                                            placeholder="Custom Purpose">
                                        <label for="custom_purpose">Custom Purpose Name *</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Fund Summary Mini-Card -->
                            <div id="budget_fund_summary_section" class="mt-3 d-none">
                                <div class="card border-0 shadow-sm overflow-hidden">
                                    <div class="card-body p-0">
                                        <div class="row g-0">
                                            <div class="col-4 border-end">
                                                <div class="p-3 text-center">
                                                    <small class="text-muted d-block mb-1">Total Fund</small>
                                                    <span class="fw-bold text-success"
                                                        id="budget_fund_total_income">0</span>
                                                </div>
                                            </div>
                                            <div class="col-4 border-end">
                                                <div class="p-3 text-center">
                                                    <small class="text-muted d-block mb-1">Committed</small>
                                                    <span class="fw-bold text-warning" id="budget_fund_used_amount">0</span>
                                                </div>
                                            </div>
                                            <div class="col-4 bg-primary text-white">
                                                <div class="p-3 text-center">
                                                    <small class="text-white-50 d-block mb-1">Available</small>
                                                    <span class="fw-bold" id="budget_fund_available_amount">0</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Period and Amount --}}
                        <div class="p-4 bg-white">
                            <div class="section-header">Period & Allocations</div>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                                        <label for="start_date">Start Date *</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                                        <label for="end_date">End Date *</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="total_budget" name="total_budget"
                                            step="0.01" min="0" placeholder="0.00" required>
                                        <label for="total_budget">Total Budget *</label>
                                        <div id="total_budget_hint" class="small mt-1 text-muted">Amount in TZS</div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-0 mt-2">
                                <label for="description"
                                    class="form-label small fw-bold text-muted uppercase-xs">Description</label>
                                <textarea class="form-control bg-light border-0" id="description" name="description"
                                    rows="2" placeholder="Optional description..."></textarea>
                            </div>
                        </div>

                        {{-- Section 4: Breakdown --}}
                        <div class="p-4 bg-white" id="budget_line_items_section" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="section-header mb-0">Budget Breakdown</div>
                                <button type="button" class="btn btn-sm btn-outline-danger" id="addLineItemBtn">
                                    <i class="fas fa-plus me-1"></i>Add Item
                                </button>
                            </div>

                            <div class="card border-0 shadow-sm">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light small font-monospace">
                                            <tr>
                                                <th style="width: 40%;">Item Name</th>
                                                <th style="width: 25%;">Amount (TZS)</th>
                                                <th style="width: 25%;">Responsible</th>
                                                <th style="width: 10%;" class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="lineItemsContainer">
                                            <!-- Dynamic items -->
                                        </tbody>
                                        <tfoot id="lineItemsFooter" class="d-none">
                                            <tr class="bg-light fw-bold">
                                                <td class="text-end">Total Breakdown:</td>
                                                <td class="text-danger" id="lineItemsTotal">TZS 0.00</td>
                                                <td colspan="2"></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer premium-modal-footer bg-white border-top">
                        <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-danger budget-submit-btn px-4 shadow-sm">
                            <i class="fas fa-check-circle me-1"></i>Create Budget
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Budget Modal -->
    <div class="modal fade" id="editBudgetModal" tabindex="-1" aria-labelledby="editBudgetModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-fullscreen-sm-down">
            <div class="modal-content budget-modal-contentborder-0">
                <div class="modal-header budget-modal-header"
                    style="background: linear-gradient(135deg, #1f2b6c 0%, #151d4a 100%);">
                    <div class="d-flex align-items-center">
                        <div class="modal-icon-wrapper me-3" style="background: rgba(255, 255, 255, 0.2);">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0 text-white" id="editBudgetModalLabel">Edit Budget</h5>
                            <small class="text-white-50">Update budget information</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="editBudgetForm" method="POST">
                    @csrf
                    <div class="modal-body p-0">
                        {{-- Section 1: Budget Details --}}
                        <div class="p-4 bg-white border-bottom">
                            <h6 class="text-uppercase fw-bold text-primary mb-3 small tracking-wider">
                                <i class="fas fa-info-circle me-2"></i>Maelezo ya Bajeti
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control" id="eb_budget_name" name="budget_name"
                                            placeholder="Name" required>
                                        <label for="eb_budget_name">Budget Name *</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="eb_budget_type" name="budget_type" required>
                                            <option value="operational">Operational</option>
                                            <option value="capital">Capital</option>
                                            <option value="program">Program</option>
                                            <option value="special">Special</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <label for="eb_budget_type">Type *</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="eb_fiscal_year" name="fiscal_year"
                                            placeholder="Year" required>
                                        <label for="eb_fiscal_year">Fiscal Year *</label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-3 d-none" id="eb_custom_budget_type_row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="eb_custom_budget_type"
                                            name="custom_budget_type" placeholder="Custom type">
                                        <label for="eb_custom_budget_type">Custom Type *</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2: Timeline, Amount & Status --}}
                        <div class="p-4 bg-light border-bottom">
                            <h6 class="text-uppercase fw-bold text-primary mb-3 small tracking-wider">
                                <i class="fas fa-calendar-check me-2"></i>Kipindi na Kiasi
                            </h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="eb_start_date" name="start_date"
                                            required>
                                        <label for="eb_start_date">Start Date *</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control" id="eb_end_date" name="end_date" required>
                                        <label for="eb_end_date">End Date *</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control" id="eb_total_budget" name="total_budget"
                                            step="0.01" min="0" required>
                                        <label for="eb_total_budget">Total Budget *</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="eb_status" name="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                        <label for="eb_status">Status *</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-white border rounded h-100">
                                        <label for="eb_description"
                                            class="form-label small fw-bold text-muted uppercase-xs">Description</label>
                                        <textarea class="form-control border-0 p-0" id="eb_description" name="description"
                                            rows="1" placeholder="Optional notes..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer budget-modal-footer bg-white border-top">
                        <button type="button" class="btn btn-light px-4 border" data-bs-dismiss="modal">
                            <i class="fas fa-times me-1"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary budget-submit-btn px-4"
                            style="background: linear-gradient(135deg, #1f2b6c 0%, #151d4a 100%); box-shadow: 0 4px 12px rgba(31, 43, 108, 0.3);">
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

        function viewBudget(button) {
            if (!button) return;
            var d = button.dataset;
            var status = d.status ? d.status.toLowerCase() : '';
            var statusClass = status === 'active' ? 'success' :
                status === 'completed' ? 'primary' : 'secondary';
            var utilization = parseFloat(d.utilization) || 0;
            var utilizationClass = utilization > 80 ? 'danger' :
                utilization > 60 ? 'warning' : 'success';

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
            modal.addEventListener('shown.bs.modal', function () {
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
                                                                                                    <td class="text-end">TZS ${parseFloat(item.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                                                                                    <td><span class="badge bg-info">${item.responsible_person}</span></td>
                                                                                                    <td>${item.notes || '-'}</td>
                                                                                                `;
                            tableBody.appendChild(row);
                        });

                        totalFooter.textContent = 'TZS ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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

        function editBudget(data) {
            // If passed a button element, extract data from dataset
            let d;
            if (data instanceof HTMLElement) {
                d = data.dataset;
            } else {
                d = typeof data === 'string' ? JSON.parse(data) : data;
            }

            const form = document.getElementById('editBudgetForm');
            if (!form) return;
            form.action = `/finance/budgets/${d.id}`;

            document.getElementById('eb_budget_name').value = d.name || '';
            document.getElementById('eb_budget_type').value = d.type || 'operational';
            document.getElementById('eb_fiscal_year').value = d.fiscal_year || new Date().getFullYear();
            document.getElementById('eb_total_budget').value = d.total || 0;
            document.getElementById('eb_start_date').value = d.start || '';
            document.getElementById('eb_end_date').value = d.end || '';
            document.getElementById('eb_status').value = d.status ? d.status.toLowerCase() : 'active';
            document.getElementById('eb_description').value = d.description || '';

            // Handle custom budget type row visibility
            const customTypeRow = document.getElementById('eb_custom_budget_type_row');
            const customTypeInput = document.getElementById('eb_custom_budget_type');
            if (customTypeRow && customTypeInput) {
                if (d.type === 'other') {
                    customTypeRow.style.display = 'block';
                    customTypeInput.value = d.custom_type || '';
                } else {
                    customTypeRow.style.display = 'none';
                    customTypeInput.value = '';
                }
            }

            const modal = new bootstrap.Modal(document.getElementById('editBudgetModal'));
            modal.show();
        }

        function confirmDeleteBudget(form, id) {
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
            @if(request('fiscal_year') || request('budget_type') || request('status'))
                if (window.innerWidth <= 768 && filterBody && filterIcon) {
                    toggleFilters(); // Expand if filters are active
                    const filterHeader = document.querySelector('.filter-header');
                    if (filterHeader) filterHeader.classList.add('active');
                }
            @endif
            // Use event delegation to handle budget type changes
            document.addEventListener('change', function (e) {
                // Handle Add Budget Modal - Budget Type
                if (e.target && e.target.id === 'budget_type') {
                    const customBudgetTypeRow = document.getElementById('custom_budget_type_row');
                    const customBudgetTypeInput = document.getElementById('custom_budget_type');

                    if (customBudgetTypeRow && customBudgetTypeInput) {
                        if (e.target.value === 'other') {
                            customBudgetTypeRow.classList.remove('d-none');
                            customBudgetTypeInput.required = true;
                        } else {
                            customBudgetTypeRow.classList.add('d-none');
                            customBudgetTypeInput.required = false;
                            customBudgetTypeInput.value = '';
                        }
                    }
                }

                // Handle Fiscal Year change - Auto-fill dates
                if (e.target && e.target.id === 'fiscal_year') {
                    const year = e.target.value;
                    const startDateInput = document.getElementById('start_date');
                    const endDateInput = document.getElementById('end_date');
                    if (year && startDateInput && endDateInput) {
                        startDateInput.value = `${year}-01-01`;
                        endDateInput.value = `${year}-12-31`;
                    }
                }

                // Handle Add Budget Modal - Purpose
                if (e.target && e.target.id === 'purpose') {
                    const customPurposeRow = document.getElementById('custom_purpose_row');
                    const customPurposeInput = document.getElementById('custom_purpose');
                    const primaryFundingDisplay = document.getElementById('primary_funding_display');
                    const lineItemsSection = document.getElementById('budget_line_items_section');
                    const fundSummarySection = document.getElementById('budget_fund_summary_section');

                    if (customPurposeRow && customPurposeInput) {
                        if (e.target.value === 'other') {
                            customPurposeRow.classList.remove('d-none');
                            customPurposeInput.required = true;
                            if (primaryFundingDisplay) {
                                primaryFundingDisplay.innerHTML = '<small class="text-muted">Enter custom purpose...</small>';
                            }
                            // Show line items for custom purposes
                            if (lineItemsSection) {
                                lineItemsSection.style.display = 'block';
                            }
                            if (fundSummarySection) fundSummarySection.classList.add('d-none');
                        } else {
                            customPurposeRow.classList.add('d-none');
                            customPurposeInput.required = false;
                            customPurposeInput.value = '';

                            // Define purposes that show line items
                            const showLineItems = ['special_events', 'thanksgiving'];
                            if (lineItemsSection) {
                                lineItemsSection.style.display = showLineItems.includes(e.target.value) ? 'block' : 'none';
                                if (!showLineItems.includes(e.target.value)) {
                                    const container = document.getElementById('lineItemsContainer');
                                    if (container) {
                                        container.innerHTML = '';
                                        updateLineItemsTotal();
                                    }
                                }
                            }

                            // Update primary funding display
                            if (primaryFundingDisplay) {
                                const purpose = e.target.value;
                                const primaryType = offeringTypeMapping[purpose] || 'general';
                                primaryFundingDisplay.innerHTML = `<span class="badge bg-primary-soft text-primary border border-primary-soft uppercase-xs">${primaryType.replace('_', ' ')}</span>`;
                            }

                            // Fetch and display fund summary for this offering type
                            if (fundSummarySection) fundSummarySection.classList.remove('d-none');
                            fetchOfferingTypeFundSummary(offeringTypeMapping[e.target.value] || 'general');
                        }
                    }
                }

                // Handle custom purpose input
                if (e.target && e.target.id === 'custom_purpose') {
                    const primaryFundingDisplay = document.getElementById('primary_funding_display');
                    const fundSummarySection = document.getElementById('budget_fund_summary_section');

                    if (primaryFundingDisplay && e.target.value.trim()) {
                        const customPurpose = e.target.value.trim().toLowerCase().replace(/[^a-z0-9]/g, '_');
                        primaryFundingDisplay.innerHTML = `<span class="badge bg-info-soft text-info border border-info-soft uppercase-xs">Match: ${customPurpose}</span>`;

                        if (fundSummarySection) fundSummarySection.classList.remove('d-none');
                        fetchOfferingTypeFundSummary(customPurpose);
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
            document.addEventListener('click', function (e) {
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

            function addLineItem(itemName = '', amount = '', responsiblePerson = '') {
                const container = document.getElementById('lineItemsContainer');
                const footer = document.getElementById('lineItemsFooter');
                if (!container) return;

                // Show footer if first item
                if (footer) footer.classList.remove('d-none');

                // Create table row
                const row = document.createElement('tr');
                row.className = 'line-item-row';
                row.dataset.index = lineItemIndex;
                row.innerHTML = `
                                                                                        <td>
                                                                                            <input type="text" class="form-control form-control-sm border-0 bg-light" name="line_items[${lineItemIndex}][item_name]" 
                                                                                                   value="${itemName}" placeholder="e.g., Vyakula" required>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="number" class="form-control form-control-sm border-0 bg-light text-end fw-bold text-danger line-item-amount" name="line_items[${lineItemIndex}][amount]" 
                                                                                                   value="${amount}" step="0.01" min="0" placeholder="0.00" required>
                                                                                        </td>
                                                                                        <td>
                                                                                            <input type="text" class="form-control form-control-sm border-0 bg-light" name="line_items[${lineItemIndex}][responsible_person]" 
                                                                                                   value="${responsiblePerson}" placeholder="e.g., Often" required>
                                                                                        </td>
                                                                                        <td class="text-center">
                                                                                            <button type="button" class="btn btn-link text-danger p-0 remove-line-item" title="Remove">
                                                                                                <i class="fas fa-times-circle"></i>
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
                const footer = document.getElementById('lineItemsFooter');

                if (!container || !totalElement) return;

                let total = 0;
                const amountInputs = container.querySelectorAll('.line-item-amount');

                if (amountInputs.length === 0 && footer) {
                    footer.classList.add('d-none');
                }

                amountInputs.forEach(input => {
                    const value = parseFloat(input.value) || 0;
                    total += value;
                });

                totalElement.textContent = 'TZS ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

                // Auto-fill total budget if line items exist
                if (totalBudgetInput && amountInputs.length > 0) {
                    totalBudgetInput.value = total.toFixed(2);
                    if (totalBudgetHint) {
                        totalBudgetHint.textContent = 'Auto-calculated from breakdown items';
                        totalBudgetHint.classList.remove('text-muted');
                        totalBudgetHint.classList.add('text-danger', 'fw-bold');
                    }
                } else if (totalBudgetHint && amountInputs.length === 0) {
                    totalBudgetHint.textContent = 'Enter total budget amount';
                    totalBudgetHint.classList.remove('text-danger', 'fw-bold');
                    totalBudgetHint.classList.add('text-muted');
                }
            }

            // Use event delegation for dynamically added line items amount inputs
            document.addEventListener('input', function (e) {
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

                document.getElementById('budget_fund_total_income').textContent = 'TZS ' + summary.total_income.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('budget_fund_used_amount').textContent = 'TZS ' + summary.total_committed.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                document.getElementById('budget_fund_available_amount').textContent = 'TZS ' + summary.available_amount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
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
                addBudgetForm.addEventListener('submit', function (e) {
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
                editBudgetForm.addEventListener('submit', function (e) {
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
                addModal.addEventListener('hidden.bs.modal', function () {
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
                editModal.addEventListener('hidden.bs.modal', function () {
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
            background: linear-gradient(135deg, #b02a37 0%, #8b1e29 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(176, 42, 55, 0.3);
        }

        .budget-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(176, 42, 55, 0.4);
            background: linear-gradient(135deg, #c82333 0%, #a71d2a 100%);
        }

        .budget-submit-btn:active {
            transform: translateY(0);
        }

        .text-danger {
            color: #b02a37 !important;
        }

        .bg-danger {
            background-color: #b02a37 !important;
        }

        .btn-outline-danger {
            color: #b02a37;
            border-color: #b02a37;
        }

        .btn-outline-danger:hover {
            background-color: #b02a37;
            border-color: #b02a37;
        }

        .tracking-wider {
            letter-spacing: 0.1em;
        }

        .uppercase-xs {
            font-size: 0.65rem;
            text-transform: uppercase;
        }

        .text-muted-25 {
            color: rgba(0, 0, 0, 0.1) !important;
        }

        .form-floating>label {
            padding-left: 1.25rem;
            color: #6c757d;
        }

        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label,
        .form-floating>.form-select~label {
            color: #b02a37;
            opacity: 0.8;
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