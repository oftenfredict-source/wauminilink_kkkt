@extends('layouts.index')

@section('content')
    <style>
        .logo-white-section {
            background-color: white !important;
            border-radius: 8px;
            margin: 8px 0;
            padding: 8px 16px !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .logo-white-section:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .navbar-brand .logo {
            transition: all 0.3s ease;
        }

        .navbar-brand .logo:hover {
            transform: scale(1.05);
        }

        .navbar-brand {
            min-height: 60px;
            display: flex !important;
            align-items: center !important;
        }

        .table.interactive-table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .table.interactive-table tbody tr td:first-child {
            border-left: 4px solid #5b2a86;
        }

        /* Celebration specific styles */
        .celebration-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .celebration-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .celebration-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .view-toggle-btn {
            border-radius: 20px;
            padding: 8px 16px;
            transition: all 0.3s ease;
        }

        .view-toggle-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
        }

        .view-toggle-btn:not(.active) {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .view-toggle-btn:not(.active):hover {
            background: #667eea;
            color: white;
        }

        /* Layout fixes */
        #layoutSidenav {
            display: flex;
        }

        #layoutSidenav_nav {
            flex-shrink: 0;
        }

        #layoutSidenav_content {
            flex: 1;
        }

        .sb-nav-fixed #layoutSidenav #layoutSidenav_nav {
            position: fixed;
            top: 56px;
            left: 0;
            width: 225px;
            height: calc(100vh - 56px);
            z-index: 1039;
        }

        .sb-nav-fixed #layoutSidenav #layoutSidenav_content {
            padding-left: 225px;
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

            /* Card View - Full Width on Mobile */
            #cardView .col-lg-4 {
                flex: 0 0 100%;
                max-width: 100%;
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
            <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header"
                onclick="toggleActions()">
                <div class="d-flex align-items-center gap-2">
                    <h2 class="mb-0 mt-2" style="font-size: 1.5rem;">{{ autoTranslate('Celebrations') }}</h2>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
                </div>
            </div>
            <div class="card-body p-3" id="actionsBody">
                <div class="d-flex flex-wrap gap-2">
                    <div class="btn-group btn-group-sm" role="group">
                        <button class="btn btn-outline-primary view-toggle-btn active" id="listViewBtn"
                            onclick="switchView('list')">
                            <i class="fas fa-list me-1"></i>
                            <span class="d-none d-sm-inline">{{ autoTranslate('List') }}</span>
                        </button>
                        <button class="btn btn-outline-primary view-toggle-btn" id="cardViewBtn"
                            onclick="switchView('card')">
                            <i class="fas fa-th-large me-1"></i>
                            <span class="d-none d-sm-inline">{{ autoTranslate('Card') }}</span>
                        </button>
                    </div>
                    <a href="{{ route('celebrations.export.csv', request()->query()) }}"
                        class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i>
                        <span class="d-none d-sm-inline">{{ autoTranslate('Export CSV') }}</span>
                        <span class="d-sm-none">{{ autoTranslate('Export') }}</span>
                    </a>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addCelebrationModal"
                        onclick="openAddCelebration()">
                        <i class="fas fa-plus me-1"></i>
                        <span class="d-none d-sm-inline">{{ autoTranslate('Add Celebration') }}</span>
                        <span class="d-sm-none">{{ autoTranslate('Add') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Filters & Search - Collapsible on Mobile -->
        <form method="GET" action="{{ route('celebrations.index') }}" class="card mb-3 border-0 shadow-sm" id="filtersForm">
            <!-- Filter Header -->
            <div class="card-header bg-white border-bottom p-2 px-3 filter-header" onclick="toggleFilters()">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-filter text-primary"></i>
                        <span class="fw-semibold">{{ autoTranslate('Filters') }}</span>
                        @if(request('search') || request('type') || request('from') || request('to'))
                            <span class="badge bg-primary rounded-pill"
                                id="activeFiltersCount">{{ (request('search') ? 1 : 0) + (request('type') ? 1 : 0) + (request('from') ? 1 : 0) + (request('to') ? 1 : 0) }}</span>
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
                            <i class="fas fa-search me-1 text-primary"></i>{{ autoTranslate('Search') }}
                        </label>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="form-control form-control-sm"
                            placeholder="{{ autoTranslate('Search title, celebrant, venue, type') }}">
                    </div>

                    <!-- Type - Full Width on Mobile -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-tags me-1 text-info"></i>{{ autoTranslate('Type') }}
                        </label>
                        <select name="type" class="form-select form-select-sm">
                            <option value="">{{ autoTranslate('All Types') }}</option>
                            <option value="Birthday" {{ request('type') == 'Birthday' ? 'selected' : '' }}>
                                {{ autoTranslate('Birthday') }}</option>
                            <option value="Anniversary" {{ request('type') == 'Anniversary' ? 'selected' : '' }}>
                                {{ autoTranslate('Anniversary') }}</option>
                            <option value="Wedding" {{ request('type') == 'Wedding' ? 'selected' : '' }}>
                                {{ autoTranslate('Wedding') }}</option>
                            <option value="Graduation" {{ request('type') == 'Graduation' ? 'selected' : '' }}>
                                {{ autoTranslate('Graduation') }}</option>
                            <option value="Other" {{ request('type') == 'Other' ? 'selected' : '' }}>
                                {{ autoTranslate('Other') }}</option>
                        </select>
                    </div>

                    <!-- Date Range - Side by Side on Mobile -->
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-warning"></i>{{ autoTranslate('From') }}
                        </label>
                        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar-check me-1 text-warning"></i>{{ autoTranslate('To') }}
                        </label>
                        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                    </div>

                    <!-- Apply Button - Full Width on Mobile -->
                    <div class="col-12 col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-filter me-1"></i>
                            <span class="d-none d-sm-inline">{{ autoTranslate('Apply') }}</span>
                            <span class="d-sm-none">{{ autoTranslate('Filter') }}</span>
                        </button>
                        <a href="{{ route('celebrations.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                </div>

                <!-- Action Buttons - Compact, Full Width on Mobile -->
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('celebrations.index') }}"
                        class="btn btn-outline-secondary btn-sm flex-fill flex-md-grow-0">
                        <i class="fas fa-redo me-1"></i>
                        <span class="d-none d-sm-inline">{{ autoTranslate('Reset') }}</span>
                        <span class="d-sm-none">{{ autoTranslate('Clear') }}</span>
                    </a>
                </div>
            </div>
        </form>

        <!-- List View -->
        <div id="listView">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="d-none d-md-table-cell">{{ autoTranslate('Title') }}</th>
                                    <th class="d-table-cell d-md-none">{{ autoTranslate('Celebration') }}</th>
                                    <th class="d-none d-lg-table-cell">{{ autoTranslate('Celebrant') }}</th>
                                    <th>{{ autoTranslate('Type') }}</th>
                                    <th>{{ autoTranslate('Date') }}</th>
                                    <th class="d-none d-xl-table-cell">{{ autoTranslate('Time') }}</th>
                                    <th class="d-none d-md-table-cell">{{ autoTranslate('Venue') }}</th>
                                    <th class="d-none d-lg-table-cell">{{ autoTranslate('Guests') }}</th>
                                    <th class="text-end">{{ autoTranslate('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($celebrations as $celebration)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $celebration->title }}</div>
                                            @if($celebration->description)
                                                <small
                                                    class="text-muted d-none d-md-inline">{{ Str::limit($celebration->description, 50) }}</small>
                                            @endif
                                            <div class="d-md-none">
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-user me-1"></i>{{ $celebration->celebrant_name ?? '—' }}
                                                </small>
                                                @if($celebration->venue)
                                                    <small class="text-muted d-block">
                                                        <i class="fas fa-map-marker-alt me-1"></i>{{ $celebration->venue }}
                                                    </small>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="d-none d-lg-table-cell">{{ $celebration->celebrant_name ?? '—' }}</td>
                                        <td>
                                            @if($celebration->type)
                                                <span class="celebration-type-badge">{{ $celebration->type }}</span>
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td>
                                            <span class="celebration-date">
                                                {{ $celebration->celebration_date ? $celebration->celebration_date->format('M d, Y') : '—' }}
                                            </span>
                                        </td>
                                        <td class="d-none d-xl-table-cell">
                                            @if($celebration->start_time && $celebration->end_time)
                                                {{ \Carbon\Carbon::parse($celebration->start_time)->format('g:i A') }} -
                                                {{ \Carbon\Carbon::parse($celebration->end_time)->format('g:i A') }}
                                            @elseif($celebration->start_time)
                                                {{ \Carbon\Carbon::parse($celebration->start_time)->format('g:i A') }}
                                            @else
                                                —
                                            @endif
                                        </td>
                                        <td class="d-none d-md-table-cell">{{ $celebration->venue ?? '—' }}</td>
                                        <td class="d-none d-lg-table-cell">{{ $celebration->expected_guests ?? '—' }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button class="btn btn-outline-info btn-sm text-white"
                                                    onclick="viewDetails({{ $celebration->id }})"
                                                    title="{{ autoTranslate('View Details') }}">
                                                    <i class="fas fa-eye"></i>
                                                    <span class="d-none d-sm-inline ms-1">{{ autoTranslate('View') }}</span>
                                                </button>
                                                <button class="btn btn-outline-primary btn-sm text-white"
                                                    onclick="openEdit({{ $celebration->id }})"
                                                    title="{{ autoTranslate('Edit Celebration') }}">
                                                    <i class="fas fa-edit"></i>
                                                    <span class="d-none d-sm-inline ms-1">{{ autoTranslate('Edit') }}</span>
                                                </button>
                                                <button class="btn btn-outline-danger btn-sm text-white"
                                                    onclick="confirmDelete({{ $celebration->id }})"
                                                    title="{{ autoTranslate('Delete Celebration') }}">
                                                    <i class="fas fa-trash"></i>
                                                    <span class="d-none d-sm-inline ms-1">{{ autoTranslate('Delete') }}</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-birthday-cake fa-3x mb-3"></i>
                                                <p>{{ autoTranslate('No celebrations found') }}</p>
                                                <button class="btn btn-primary" data-bs-toggle="modal"
                                                    data-bs-target="#addCelebrationModal" onclick="openAddCelebration()">
                                                    <i class="fas fa-plus me-2"></i>{{ autoTranslate('Add First Celebration') }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card View -->
        <div id="cardView" style="display: none;">
            <div class="row">
                @forelse($celebrations as $celebration)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card celebration-card h-100">
                            <div class="card-header celebration-header">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-bold">{{ $celebration->title }}</h6>
                                    @if($celebration->type)
                                        <span class="celebration-type-badge">{{ $celebration->type }}</span>
                                    @endif
                                </div>
                                @if($celebration->celebrant_name)
                                    <small class="opacity-75">{{ $celebration->celebrant_name }}</small>
                                @endif
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <span class="celebration-date">
                                        {{ $celebration->celebration_date ? $celebration->celebration_date->format('M d, Y') : '—' }}
                                    </span>
                                </div>
                                @if($celebration->start_time && $celebration->end_time)
                                    <p class="mb-2">
                                        <i class="fas fa-clock me-2 text-primary"></i>
                                        {{ \Carbon\Carbon::parse($celebration->start_time)->format('g:i A') }} -
                                        {{ \Carbon\Carbon::parse($celebration->end_time)->format('g:i A') }}
                                    </p>
                                @endif
                                @if($celebration->venue)
                                    <p class="mb-2">
                                        <i class="fas fa-map-marker-alt me-2 text-primary"></i>
                                        {{ $celebration->venue }}
                                    </p>
                                @endif
                                @if($celebration->expected_guests)
                                    <p class="mb-2">
                                        <i class="fas fa-users me-2 text-primary"></i>
                                        {{ $celebration->expected_guests }} {{ autoTranslate('guests') }}
                                    </p>
                                @endif
                                @if($celebration->budget)
                                    <p class="mb-2">
                                        <i class="fas fa-money-bill-wave me-2 text-primary"></i>
                                        TZS {{ number_format($celebration->budget, 2) }}
                                    </p>
                                @endif
                                @if($celebration->description)
                                    <p class="mb-3 text-muted small">{{ Str::limit($celebration->description, 100) }}</p>
                                @endif
                            </div>
                            <div class="card-footer bg-transparent">
                                <div class="btn-group w-100" role="group">
                                    <button class="btn btn-outline-info btn-sm" onclick="viewDetails({{ $celebration->id }})"
                                        title="{{ autoTranslate('View Details') }}">
                                        <i class="fas fa-eye me-1"></i>{{ autoTranslate('View') }}
                                    </button>
                                    <button class="btn btn-outline-primary btn-sm" onclick="openEdit({{ $celebration->id }})"
                                        title="{{ autoTranslate('Edit Celebration') }}">
                                        <i class="fas fa-edit me-1"></i>{{ autoTranslate('Edit') }}
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm"
                                        onclick="confirmDelete({{ $celebration->id }})"
                                        title="{{ autoTranslate('Delete Celebration') }}">
                                        <i class="fas fa-trash me-1"></i>{{ autoTranslate('Delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-birthday-cake fa-5x mb-4"></i>
                                <h4>{{ autoTranslate('No celebrations found') }}</h4>
                                <p>{{ autoTranslate('Start by adding your first celebration') }}</p>
                                <button class="btn btn-primary btn-lg" data-bs-toggle="modal"
                                    data-bs-target="#addCelebrationModal" onclick="openAddCelebration()">
                                    <i class="fas fa-plus me-2"></i>{{ autoTranslate('Add First Celebration') }}
                                </button>
                            </div>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        @if($celebrations->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $celebrations->withQueryString()->links() }}
            </div>
        @endif
    </div>

    <!-- Add Celebration Modal -->
    <div class="modal fade" id="addCelebrationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow-lg service-modal-content"
                style="border-radius: 20px; overflow: hidden;">
                <!-- Stylish Header -->
                <div class="modal-header border-0 service-modal-header"
                    style="background: linear-gradient(180deg, #17082d 0%, #17082ddd 100%); padding: 1.25rem 1.5rem;">
                    <div class="d-flex align-items-center">
                        <div class="service-icon-wrapper me-3">
                            <i class="fas fa-birthday-cake"></i>
                        </div>
                        <h5 class="modal-title mb-0 fw-bold text-white" id="celebrationModalTitle">
                            {{ autoTranslate('Create Celebration') }}
                        </h5>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <!-- Stylish Body -->
                <div class="modal-body service-modal-body" style="padding: 1.75rem; background: #f8f9fa;">
                    <form id="addCelebrationForm">
                        <input type="hidden" id="editing_celebration_id" value="">
                        <div class="row g-3">
                            <!-- Row 1: Title & Celebrant -->
                            <div class="col-md-6">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-star me-1 text-warning"></i>{{ autoTranslate('Celebration Title') }}
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control service-input" id="cel_title"
                                    placeholder="{{ autoTranslate('Enter celebration title') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-user me-1 text-primary"></i>{{ autoTranslate('Celebrant Name') }}
                                </label>
                                <input type="text" class="form-control service-input" id="cel_celebrant"
                                    placeholder="{{ autoTranslate('Enter celebrant name') }}">
                            </div>

                            <!-- Row 2: Type & Venue -->
                            <div class="col-md-6">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-tags me-1 text-primary"></i>{{ autoTranslate('Celebration Type') }}
                                </label>
                                <select class="form-select service-input" id="cel_type">
                                    <option value="">{{ autoTranslate('Select Type') }}</option>
                                    <option value="Birthday">{{ autoTranslate('Birthday') }}</option>
                                    <option value="Anniversary">{{ autoTranslate('Anniversary') }}</option>
                                    <option value="Wedding">{{ autoTranslate('Wedding') }}</option>
                                    <option value="Graduation">{{ autoTranslate('Graduation') }}</option>
                                    <option value="Other">{{ autoTranslate('Other') }}</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i>{{ autoTranslate('Venue') }}
                                </label>
                                <input type="text" class="form-control service-input" id="cel_venue"
                                    placeholder="{{ autoTranslate('Enter venue location') }}">
                            </div>

                            <!-- Row 3: Date & Time -->
                            <div class="col-md-4">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-calendar-alt me-1 text-info"></i>{{ autoTranslate('Date') }} <span
                                        class="text-danger">*</span>
                                </label>
                                <input type="date" class="form-control service-input" id="cel_date" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-clock me-1 text-success"></i>{{ autoTranslate('Start Time') }}
                                </label>
                                <input type="time" class="form-control service-input" id="cel_start">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-clock me-1 text-danger"></i>{{ autoTranslate('End Time') }}</label>
                                <input type="time" class="form-control service-input" id="cel_end">
                            </div>

                            <!-- Row 4: Guests & Budget -->
                            <div class="col-md-6">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-users me-1 text-info"></i>{{ autoTranslate('Expected Guests') }}
                                </label>
                                <input type="number" min="0" class="form-control service-input" id="cel_guests"
                                    placeholder="{{ autoTranslate('Number of guests') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label service-label mb-2">
                                    <i
                                        class="fas fa-money-bill-wave me-1 text-success"></i>{{ autoTranslate('Budget (TZS)') }}
                                </label>
                                <input type="number" min="0" step="0.01" class="form-control service-input" id="cel_budget"
                                    placeholder="{{ autoTranslate('Budget amount') }}">
                            </div>

                            <!-- Row 5: Description -->
                            <div class="col-12">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-file-alt me-1 text-primary"></i>{{ autoTranslate('Description') }}
                                </label>
                                <textarea class="form-control service-input" id="cel_description" rows="2"
                                    placeholder="{{ autoTranslate('Enter celebration description') }}"></textarea>
                            </div>

                            <!-- Row 6: Special Requests -->
                            <div class="col-12">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-gift me-1 text-warning"></i>{{ autoTranslate('Special Requests') }}
                                </label>
                                <textarea class="form-control service-input" id="cel_requests" rows="2"
                                    placeholder="{{ autoTranslate('Enter special requests') }}"></textarea>
                            </div>

                            <!-- Row 7: Notes -->
                            <div class="col-12">
                                <label class="form-label service-label mb-2">
                                    <i class="fas fa-sticky-note me-1 text-secondary"></i>{{ autoTranslate('Notes') }}
                                </label>
                                <textarea class="form-control service-input" id="cel_notes" rows="2"
                                    placeholder="{{ autoTranslate('Additional notes') }}"></textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="btn btn-outline-secondary service-btn-cancel"
                                data-bs-dismiss="modal">
                                <i class="fas fa-times me-1"></i>{{ autoTranslate('Cancel') }}
                            </button>
                            <button type="submit" class="btn service-btn-save" id="submitButton">
                                <i class="fas fa-save me-1"></i>{{ autoTranslate('Save Celebration') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- View Details Modal -->
    <div class="modal fade" id="celebrationDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%); border: none;">
                    <h5 class="modal-title d-flex align-items-center gap-2"><i
                            class="fas fa-birthday-cake"></i><span>{{ autoTranslate('Celebration Details') }}</span></h5>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light" id="celebrationDetailsBody">
                    <div class="text-center text-muted py-4">{{ autoTranslate('Loading...') }}</div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div class="small">
                        <span class="me-1">Powered by</span>
                        <a href="https://emca.tech/#" target="_blank" rel="noopener" class="emca-link fw-semibold"
                            style="color: #940000 !important;">EmCa Technologies</a>
                    </div>
                    <button type="button" class="btn btn-danger"
                        data-bs-dismiss="modal">{{ autoTranslate('Close') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
    <script>
        // View Toggle Functionality
        function switchView(view) {
            const listView = document.getElementById('listView');
            const cardView = document.getElementById('cardView');
            const listBtn = document.getElementById('listViewBtn');
            const cardBtn = document.getElementById('cardViewBtn');

            if (view === 'list') {
                listView.style.display = 'block';
                cardView.style.display = 'none';
                listBtn.classList.add('active');
                cardBtn.classList.remove('active');
                localStorage.setItem('celebrationView', 'list');
            } else {
                listView.style.display = 'none';
                cardView.style.display = 'block';
                listBtn.classList.remove('active');
                cardBtn.classList.add('active');
                localStorage.setItem('celebrationView', 'card');
            }
        }

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

        // Load saved view preference
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
            @if(request('search') || request('type') || request('from') || request('to'))
                if (window.innerWidth <= 768 && filterBody && filterIcon) {
                    toggleFilters(); // Expand if filters are active
                    const filterHeader = document.querySelector('.filter-header');
                    if (filterHeader) filterHeader.classList.add('active');
                }
            @endif

                const savedView = localStorage.getItem('celebrationView') || 'list';
            switchView(savedView);

            // Auto-open add modal if coming from dashboard
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('action') === 'add') {
                openAddCelebration();
            }
        });

        // Modal Functions
        function openAddCelebration() {
            document.getElementById('editing_celebration_id').value = '';
            const titleEl = document.getElementById('celebrationModalTitle');
            if (titleEl) titleEl.textContent = '{{ autoTranslate('Create Celebration') }}';
            const submitBtn = document.getElementById('submitButton');
            if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>{{ autoTranslate('Save Celebration') }}';
            document.getElementById('addCelebrationForm').reset();
        }

        function openEdit(id) {
            fetch(`/celebrations/${id}`, { headers: { 'Accept': 'application/json' } })
                .then(res => res.json())
                .then(data => {
                    document.getElementById('editing_celebration_id').value = id;
                    const titleEl = document.getElementById('celebrationModalTitle');
                    if (titleEl) titleEl.textContent = '{{ autoTranslate('Edit Celebration') }}';
                    const submitBtn = document.getElementById('submitButton');
                    if (submitBtn) submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>{{ autoTranslate('Update Celebration') }}';

                    document.getElementById('cel_title').value = data.title || '';
                    document.getElementById('cel_celebrant').value = data.celebrant_name || '';
                    document.getElementById('cel_type').value = data.type || '';
                    document.getElementById('cel_venue').value = data.venue || '';
                    document.getElementById('cel_date').value = data.celebration_date || '';
                    document.getElementById('cel_start').value = data.start_time || '';
                    document.getElementById('cel_end').value = data.end_time || '';
                    document.getElementById('cel_guests').value = data.expected_guests || '';
                    document.getElementById('cel_budget').value = data.budget || '';
                    document.getElementById('cel_description').value = data.description || '';
                    document.getElementById('cel_requests').value = data.special_requests || '';
                    document.getElementById('cel_notes').value = data.notes || '';

                    new bootstrap.Modal(document.getElementById('addCelebrationModal')).show();
                })
                .catch(() => {
                    Swal.fire('Error', 'Failed to load celebration details', 'error');
                });
        }

        function viewDetails(id) {
            fetch(`/celebrations/${id}`)
                .then(res => res.json())
                .then(data => {
                    // Time formatting function
                    const formatTime = (timeStr) => {
                        if (!timeStr || timeStr === 'TBD') return 'TBD';
                        try {
                            // Handle ISO format
                            if (timeStr.includes('T')) {
                                const time = new Date(timeStr);
                                return time.toLocaleTimeString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });
                            }
                            // Handle HH:MM:SS format
                            if (timeStr.includes(':')) {
                                const [hours, minutes] = timeStr.split(':');
                                const time = new Date();
                                time.setHours(parseInt(hours), parseInt(minutes), 0);
                                return time.toLocaleTimeString('en-US', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    hour12: true
                                });
                            }
                            return timeStr;
                        } catch (e) {
                            return 'TBD';
                        }
                    };

                    const body = document.getElementById('celebrationDetailsBody');
                    body.innerHTML = `
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>{{ autoTranslate('Basic Information') }}</h6>
                                    <p><strong>{{ autoTranslate('Title') }}:</strong> ${data.title || '—'}</p>
                                    <p><strong>{{ autoTranslate('Celebrant') }}:</strong> ${data.celebrant_name || '—'}</p>
                                    <p><strong>{{ autoTranslate('Type') }}:</strong> ${data.type ? `<span class="celebration-type-badge">${data.type}</span>` : '—'}</p>
                                    <p><strong>{{ autoTranslate('Venue') }}:</strong> ${data.venue || '—'}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-primary mb-3"><i class="fas fa-clock me-2"></i>{{ autoTranslate('Date & Time') }}</h6>
                                    <p><strong>{{ autoTranslate('Date') }}:</strong> ${data.celebration_date ? new Date(data.celebration_date).toLocaleDateString() : '—'}</p>
                                    <p><strong>{{ autoTranslate('Time') }}:</strong> ${data.start_time && data.end_time ? `${formatTime(data.start_time)} - ${formatTime(data.end_time)}` : data.start_time ? formatTime(data.start_time) : '—'}</p>
                                    <p><strong>{{ autoTranslate('Expected Guests') }}:</strong> ${data.expected_guests || '—'}</p>
                                    <p><strong>{{ autoTranslate('Budget') }}:</strong> ${data.budget ? `TZS ${parseFloat(data.budget).toLocaleString()}` : '—'}</p>
                                </div>
                            </div>
                            ${data.description ? `<div class="mt-4"><h6 class="text-primary mb-3"><i class="fas fa-file-alt me-2"></i>{{ autoTranslate('Description') }}</h6><p>${data.description}</p></div>` : ''}
                            ${data.special_requests ? `<div class="mt-4"><h6 class="text-primary mb-3"><i class="fas fa-gift me-2"></i>{{ autoTranslate('Special Requests') }}</h6><p>${data.special_requests}</p></div>` : ''}
                            ${data.notes ? `<div class="mt-4"><h6 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i>{{ autoTranslate('Notes') }}</h6><p>${data.notes}</p></div>` : ''}
                        `;
                    new bootstrap.Modal(document.getElementById('celebrationDetailsModal')).show();
                })
                .catch(err => {
                    Swal.fire('Error', 'Failed to load celebration details', 'error');
                });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: '{{ autoTranslate('Are you sure?') }}',
                text: "{{ autoTranslate("You won't be able to revert this!") }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ autoTranslate('Yes, delete it!') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/celebrations/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('{{ autoTranslate('Deleted!') }}', data.message, 'success').then(() => location.reload());
                            } else {
                                Swal.fire('{{ autoTranslate('Error') }}', data.message || '{{ autoTranslate('Failed to delete celebration') }}', 'error');
                            }
                        })
                        .catch(err => {
                            Swal.fire('{{ autoTranslate('Error') }}', '{{ autoTranslate('Failed to delete celebration') }}', 'error');
                        });
                }
            });
        }

        // Form Submission
        document.getElementById('addCelebrationForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const formData = new FormData();
            const editingId = document.getElementById('editing_celebration_id').value;

            formData.append('title', document.getElementById('cel_title').value);
            formData.append('celebrant_name', document.getElementById('cel_celebrant').value);
            formData.append('type', document.getElementById('cel_type').value);
            formData.append('venue', document.getElementById('cel_venue').value);
            formData.append('celebration_date', document.getElementById('cel_date').value);
            const startVal = document.getElementById('cel_start').value;
            const endVal = document.getElementById('cel_end').value;
            if (startVal) formData.append('start_time', startVal);
            if (endVal) formData.append('end_time', endVal);
            formData.append('expected_guests', document.getElementById('cel_guests').value);
            formData.append('budget', document.getElementById('cel_budget').value);
            formData.append('description', document.getElementById('cel_description').value);
            formData.append('special_requests', document.getElementById('cel_requests').value);
            formData.append('notes', document.getElementById('cel_notes').value);
            formData.append('is_public', '1');

            const submitBtn = document.getElementById('submitButton');
            const originalHtml = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>{{ autoTranslate('Saving...') }}';

            const url = editingId ? `/celebrations/${editingId}` : '/celebrations';
            if (editingId) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(async (res) => {
                    const contentType = res.headers.get('content-type') || '';
                    if (!res.ok) {
                        let message = `HTTP ${res.status}`;
                        if (contentType.includes('application/json')) {
                            const err = await res.json().catch(() => null);
                            if (err && err.message) message = err.message;
                        } else {
                            const text = await res.text().catch(() => '');
                            if (text) message = text.substring(0, 200);
                        }
                        throw new Error(message);
                    }
                    if (contentType.includes('application/json')) {
                        return res.json();
                    }
                    return { success: true, message: 'Saved' };
                })
                .then(data => {
                    if (data.success) {
                        document.getElementById('addCelebrationForm').reset();
                        document.getElementById('editing_celebration_id').value = '';
                        const titleEl = document.querySelector('#addCelebrationModal .modal-title');
                        if (titleEl) titleEl.textContent = '{{ autoTranslate('Create Celebration') }}';
                        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>{{ autoTranslate('Save Celebration') }}';

                        Swal.fire({
                            icon: 'success',
                            title: editingId ? '{{ autoTranslate('Updated') }}' : '{{ autoTranslate('Saved') }}',
                            text: data.message || '{{ autoTranslate('Celebration saved') }}',
                            timer: 1200,
                            showConfirmButton: false
                        }).then(() => location.reload());
                    } else {
                        Swal.fire('{{ autoTranslate('Error') }}', data.message || '{{ autoTranslate('Failed to save celebration') }}', 'error');
                    }
                })
                .catch((err) => {
                    Swal.fire('{{ autoTranslate('Error') }}', err?.message || '{{ autoTranslate('Failed to save celebration') }}', 'error');
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalHtml;
                });
        });
    </script>

    <style>
        .celebration-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .celebration-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .celebration-header {
            background: linear-gradient(135deg, #940000 0%, #667eea 50%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }

        .celebration-type-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .celebration-date {
            background: linear-gradient(135deg, #940000, #667eea);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
        }

        .view-toggle-btn {
            background: linear-gradient(135deg, #940000 0%, #667eea 100%);
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .view-toggle-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(148, 0, 0, 0.3);
        }

        .view-toggle-btn.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        /* Enhanced Modal Animations */
        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-50px) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Form Control Focus Effects */
        .form-control:focus {
            border-color: #940000 !important;
            box-shadow: 0 0 0 0.2rem rgba(148, 0, 0, 0.25) !important;
            transform: translateY(-2px);
        }

        .form-control:hover {
            border-color: #667eea !important;
            transform: translateY(-1px);
        }

        /* Button Hover Effects */
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        /* Card Hover Effects */
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1) !important;
        }

        /* Floating Label Animation */
        .form-floating>.form-control:focus~label,
        .form-floating>.form-control:not(:placeholder-shown)~label {
            color: #940000;
            font-weight: 600;
        }

        /* Modal Backdrop */
        .modal-backdrop {
            background: rgba(23, 8, 45, 0.4) !important;
        }

        .modal-backdrop.show {
            opacity: 1 !important;
        }

        /* Service Modal Styling (for Celebration Modal) */
        .service-modal-content {
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.95);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .service-modal-header {
            position: relative;
            overflow: hidden;
        }

        .service-modal-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
            pointer-events: none;
        }

        .service-icon-wrapper {
            width: 45px;
            height: 45px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .service-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: #495057;
            letter-spacing: 0.3px;
        }

        .service-input {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.5rem 0.75rem;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            background: white;
        }

        .service-input:focus {
            border-color: #17082d;
            box-shadow: 0 0 0 0.2rem rgba(23, 8, 45, 0.15);
            transform: translateY(-1px);
            background: white;
        }

        .service-input:hover {
            border-color: #ced4da;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .service-btn-save {
            background: linear-gradient(180deg, #17082d 0%, #17082ddd 100%);
            border: none;
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(23, 8, 45, 0.3);
        }

        .service-btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(23, 8, 45, 0.4);
            background: linear-gradient(180deg, #1f0d3d 0%, #1f0d3ddd 100%);
            color: white;
        }

        .service-btn-cancel {
            border-radius: 10px;
            padding: 0.5rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 2px solid #dee2e6;
        }

        .service-btn-cancel:hover {
            transform: translateY(-2px);
            background: #f8f9fa;
            border-color: #adb5bd;
        }

        .service-modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }

        .service-modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .service-modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .service-modal-body::-webkit-scrollbar-thumb {
            background: linear-gradient(180deg, #17082d 0%, #17082ddd 100%);
            border-radius: 10px;
        }

        .service-modal-body::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(180deg, #1f0d3d 0%, #1f0d3ddd 100%);
        }

        /* Form Select Styling */
        .service-input.form-select {
            cursor: pointer;
        }

        .service-input.form-select:focus {
            border-color: #17082d;
        }

        /* Textarea Styling */
        .service-input[rows] {
            resize: vertical;
            min-height: 60px;
        }
    </style>

@endsection

@section('scripts')
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
    <script src="{{ asset('js/scripts.js') }}"></script>
@endsection