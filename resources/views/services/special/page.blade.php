@extends('layouts.index')

@section('content')
        <style>
            .table.interactive-table tbody tr:hover { background-color: #f8f9ff; }
            .table.interactive-table tbody tr td:first-child { border-left: 4px solid #5b2a86; }
            
            /* Card View Styles */
            .event-card {
                transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
                border-radius: 16px;
                overflow: hidden;
                background: #ffffff;
                border: 1px solid #e3e6f0;
                position: relative;
            }
            
            .event-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 4px;
                background: linear-gradient(90deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
                z-index: 1;
            }
            
            .event-card:hover {
                transform: translateY(-8px) scale(1.02);
                box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15), 0 8px 16px rgba(0,0,0,0.1) !important;
                border-color: #667eea;
            }
            
            /* Event Header Styles */
            .event-header {
                position: relative;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                border: none !important;
                padding: 1rem 1rem 0.75rem !important;
            }
            
            .event-title {
                font-size: 1.2rem;
                font-weight: 700;
                color: #ffffff;
                text-shadow: 0 2px 4px rgba(0,0,0,0.1);
                line-height: 1.3;
                margin-bottom: 0.5rem;
            }
            
            .event-date {
                background: rgba(255, 255, 255, 0.95) !important;
                color: #667eea !important;
                font-weight: 600;
                font-size: 0.8rem;
                border-radius: 15px;
                padding: 0.4rem 0.8rem !important;
                box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                border: 2px solid rgba(255, 255, 255, 0.3);
            }
            
            /* Event Details Styles */
            .event-details {
                background: #fafbfc;
                border-radius: 10px;
                padding: 1rem;
                margin: 0.75rem;
                border: 1px solid #e8ecf3;
            }
            
            .detail-item {
                padding: 0.5rem 0;
                border-bottom: 1px solid #e8ecf3;
                transition: all 0.2s ease;
            }
            
            .detail-item:last-child {
                border-bottom: none;
            }
            
            .detail-item:hover {
                background: rgba(102, 126, 234, 0.05);
                border-radius: 6px;
                padding: 0.5rem;
                margin: 0 -0.5rem;
            }
            
            .icon-wrapper {
                width: 32px;
                height: 32px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 0.9rem;
                font-weight: 600;
                flex-shrink: 0;
            }
            
            .detail-item:nth-child(1) .icon-wrapper {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
            }
            
            .detail-item:nth-child(2) .icon-wrapper {
                background: linear-gradient(135deg, #f093fb, #f5576c);
                color: white;
            }
            
            .detail-item:nth-child(3) .icon-wrapper {
                background: linear-gradient(135deg, #4facfe, #00f2fe);
                color: white;
            }
            
            .detail-item:nth-child(4) .icon-wrapper {
                background: linear-gradient(135deg, #43e97b, #38f9d7);
                color: white;
            }
            
            .detail-item:nth-child(5) .icon-wrapper {
                background: linear-gradient(135deg, #fa709a, #fee140);
                color: white;
            }
            
            .detail-label {
                font-size: 0.7rem;
                font-weight: 600;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 0.2rem;
            }
            
            .detail-value {
                font-size: 0.9rem;
                font-weight: 600;
                color: #2d3748;
                line-height: 1.3;
            }
            
            
            /* Card Footer Styles */
            .event-footer {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
                border: none !important;
                padding: 0.75rem 1rem !important;
                border-top: 1px solid #dee2e6;
            }
            
            .action-btn {
                border-radius: 6px !important;
                font-weight: 600;
                font-size: 0.75rem;
                padding: 0.4rem 0.6rem !important;
                transition: all 0.3s ease;
                border-width: 2px !important;
                margin: 0 1px;
            }
            
            .action-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            
            .action-btn:first-child {
                margin-left: 0;
            }
            
            .action-btn:last-child {
                margin-right: 0;
            }
            
            /* View Toggle Styles */
            .btn-group .btn.active {
                background-color: #6c757d;
                border-color: #6c757d;
                color: white;
            }
            
            .btn-group .btn:not(.active) {
                background-color: transparent;
                color: #6c757d;
            }
            
            .btn-group .btn:not(.active):hover {
                background-color: #e9ecef;
                border-color: #6c757d;
                color: #495057;
            }

            /* Event type badge styling */
            .event-type-badge {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                padding: 0.25rem 0.75rem;
                border-radius: 20px;
                font-size: 0.8rem;
                font-weight: 600;
            }
            
            /* Event date styling */
            .event-date {
                background: linear-gradient(135deg, #940000, #667eea);
                color: white;
                padding: 0.5rem 1rem;
                border-radius: 25px;
                font-weight: 600;
            }
            
            /* Action buttons styling */
            .btn-group .btn {
                border: none !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }
            
            .btn-group .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            }
            
            .btn-group .btn-info {
                background: linear-gradient(135deg, #17a2b8, #138496) !important;
            }
            
            .btn-group .btn-primary {
                background: linear-gradient(135deg, #007bff, #0056b3) !important;
            }
            
            .btn-group .btn-danger {
                background: linear-gradient(135deg, #dc3545, #c82333) !important;
            }
        </style>
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
                
                /* Active Filter Badge */
                #filtersForm .badge {
                    font-size: 0.7rem !important;
                    padding: 0.25rem 0.5rem !important;
                    font-weight: 600 !important;
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
                
                /* Better mobile filter layout */
                @media (max-width: 576px) {
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
                    #filtersForm .btn-sm {
                        padding: 0.35rem 0.65rem !important;
                        font-size: 0.75rem !important;
                    }
                }
                
                /* Table Responsive */
                .table {
                    font-size: 0.75rem;
                }
                .table th,
                .table td {
                    padding: 0.5rem 0.25rem;
                    white-space: nowrap;
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
                .btn-group .btn span,
                .btn span:not(.spinner-border) {
                    display: none !important;
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
                                    <h2 class="mb-0 mt-2" style="font-size: 1.5rem;">Special Events</h2>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
                                </div>
                            </div>
                            <div class="card-body p-3" id="actionsBody">
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('attendance.index', ['service_type' => 'special_event']) }}" class="btn btn-info btn-sm">
                                        <i class="fas fa-users me-1"></i>
                                        <span class="d-none d-sm-inline">Record Attendance</span>
                                        <span class="d-sm-none">Attendance</span>
                                    </a>
                                    <a href="{{ route('attendance.statistics') }}" class="btn btn-outline-info btn-sm">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        <span class="d-none d-sm-inline">Statistics</span>
                                        <span class="d-sm-none">Stats</span>
                                    </a>
                                    <div class="btn-group btn-group-sm" role="group" aria-label="View toggle">
                                        <button type="button" class="btn btn-outline-secondary active" id="listViewBtn" onclick="switchView('list')">
                                            <i class="fas fa-list me-1"></i>
                                            <span class="d-none d-sm-inline">List</span>
                                        </button>
                                        <button type="button" class="btn btn-outline-secondary" id="cardViewBtn" onclick="switchView('card')">
                                            <i class="fas fa-th-large me-1"></i>
                                            <span class="d-none d-sm-inline">Card</span>
                                        </button>
                                    </div>
                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addEventModal" onclick="openAddEvent()">
                                        <i class="fas fa-plus me-1"></i>
                                        <span class="d-none d-sm-inline">Add Event</span>
                                        <span class="d-sm-none">Add</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Filters & Search - Collapsible on Mobile -->
                        <form method="GET" action="{{ route('special.events.index') }}" class="card mb-3 border-0 shadow-sm" id="filtersForm">
                            <!-- Filter Header -->
                            <div class="card-header bg-white border-bottom p-2 px-3 filter-header" onclick="toggleFilters()">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fas fa-filter text-primary"></i>
                                        <span class="fw-semibold">Filters</span>
                                        @if(request('search') || request('from') || request('to'))
                                            <span class="badge bg-primary rounded-pill" id="activeFiltersCount">{{ (request('search') ? 1 : 0) + (request('from') ? 1 : 0) + (request('to') ? 1 : 0) }}</span>
                                        @endif
                                    </div>
                                    <i class="fas fa-chevron-down text-muted d-md-none" id="filterToggleIcon"></i>
                                </div>
                            </div>
                            
                            <!-- Filter Body - Collapsible on Mobile -->
                            <div class="card-body p-3" id="filterBody">
                                <div class="row g-2 mb-2">
                                    <!-- Search Field - Full Width on Mobile -->
                                    <div class="col-12 col-md-4">
                                        <label class="form-label small text-muted mb-1">
                                            <i class="fas fa-search me-1 text-primary"></i>Search
                                        </label>
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search title, speaker, venue">
                                    </div>
                                    
                                    <!-- Date Range - Side by Side on Mobile -->
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted mb-1">
                                            <i class="fas fa-calendar-alt me-1 text-info"></i>From Date
                                        </label>
                                        <input type="date" name="from" value="{{ request('from') }}" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <label class="form-label small text-muted mb-1">
                                            <i class="fas fa-calendar-check me-1 text-info"></i>To Date
                                        </label>
                                        <input type="date" name="to" value="{{ request('to') }}" class="form-control form-control-sm">
                                    </div>
                                    
                                    <!-- Apply Button - Full Width on Mobile -->
                                    <div class="col-12 col-md-2">
                                        <label class="form-label small text-muted mb-1 d-block">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary btn-sm w-100">
                                            <i class="fas fa-filter me-1"></i>
                                            <span class="d-none d-sm-inline">Apply</span>
                                            <span class="d-sm-none">Filter</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons - Compact, Full Width on Mobile -->
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ route('special.events.index') }}" class="btn btn-outline-secondary btn-sm flex-fill flex-md-grow-0">
                                        <i class="fas fa-redo me-1"></i>
                                        <span class="d-none d-sm-inline">Reset</span>
                                        <span class="d-sm-none">Clear</span>
                                    </a>
                                </div>
                            </div>
                        </form>

                        <!-- List View -->
                        <div class="card" id="listView">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="d-none d-md-table-cell">Title</th>
                                                <th class="d-table-cell d-md-none">Event</th>
                                                <th class="d-none d-lg-table-cell">Speaker</th>
                                                <th class="d-none d-md-table-cell">Category</th>
                                                <th>Date</th>
                                                <th class="d-none d-lg-table-cell">Time</th>
                                                <th class="d-none d-md-table-cell">Venue</th>
                                                <th class="d-none d-xl-table-cell">Budget</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($events as $event)
                                                @php
                                                    $fmtTime = function($t){
                                                        if (!$t) return '--:--';
                                                        try { 
                                                            if (preg_match('/^\d{2}:\d{2}/',$t)) return substr($t,0,5); 
                                                            return \Carbon\Carbon::parse($t)->format('H:i'); 
                                                        } catch (\Throwable $e) { 
                                                            return '--:--'; 
                                                        }
                                                    };
                                                @endphp
                                                <tr id="row-{{ $event->id }}">
                                                    <td>
                                                        <div class="fw-bold">{{ $event->title ?? '—' }}</div>
                                                        @if($event->description)
                                                            <small class="text-muted d-none d-md-inline">{{ Str::limit($event->description, 50) }}</small>
                                                        @endif
                                                        <div class="d-md-none">
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-user me-1"></i>{{ $event->speaker ?? '—' }}
                                                            </small>
                                                            @if($event->category)
                                                                <span class="badge bg-primary mt-1">{{ $event->category }}</span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="d-none d-lg-table-cell">{{ $event->speaker ?? '—' }}</td>
                                                    <td class="d-none d-md-table-cell">
                                                        @if($event->category)
                                                            <span class="event-type-badge">{{ $event->category }}</span>
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="event-date">
                                                            {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('M d, Y') : '—' }}
                                                        </span>
                                                    </td>
                                                    <td class="d-none d-lg-table-cell">
                                                        @if($event->start_time && $event->end_time)
                                                            {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($event->end_time)->format('g:i A') }}
                                                        @elseif($event->start_time)
                                                            {{ \Carbon\Carbon::parse($event->start_time)->format('g:i A') }}
                                                        @else
                                                            —
                                                        @endif
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{ $event->venue ?? '—' }}</td>
                                                    <td class="d-none d-xl-table-cell">{{ $event->budget_amount ? 'TZS ' . number_format($event->budget_amount) : '—' }}</td>
                                                    <td class="text-end">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-info btn-sm text-white" onclick="viewEvent({{ $event->id }})" title="View Details">
                                                                <i class="fas fa-eye"></i>
                                                                <span class="d-none d-sm-inline ms-1">View</span>
                                                            </button>
                                                            <button class="btn btn-primary btn-sm text-white" onclick="openEditEvent({{ $event->id }})" title="Edit Event">
                                                                <i class="fas fa-edit"></i>
                                                                <span class="d-none d-sm-inline ms-1">Edit</span>
                                                            </button>
                                                            <button class="btn btn-danger btn-sm text-white" onclick="confirmDeleteEvent({{ $event->id }})" title="Delete Event">
                                                                <i class="fas fa-trash"></i>
                                                                <span class="d-none d-sm-inline ms-1">Delete</span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center py-4">
                                                        <div class="text-muted">
                                                            <i class="fas fa-calendar-plus fa-3x mb-3"></i>
                                                            <p>No special events found</p>
                                                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal" onclick="openAddEvent()">
                                                                <i class="fas fa-plus me-2"></i>Add First Event
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="text-muted small">Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} entries</div>
                                <div>{{ $events->withQueryString()->links() }}</div>
                            </div>
                        </div>

                        <!-- Card View -->
                        <div class="card" id="cardView" style="display: none;">
                            <div class="card-body">
                                <div class="row g-4">
                                    @forelse($events as $event)
                                        @php
                                            $fmtTime = function($t){
                                                if (!$t) return '--:--';
                                                try { if (preg_match('/^\d{2}:\d{2}/',$t)) return substr($t,0,5); return \Carbon\Carbon::parse($t)->format('H:i'); } catch (\Throwable $e) { return '--:--'; }
                                            };
                                        @endphp
                                        <div class="col-lg-4 col-md-6" id="card-{{ $event->id }}">
                                            <div class="card h-100 border-0 shadow-lg event-card">
                                                <!-- Event Title Header -->
                                                <div class="card-header event-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 1rem 1rem 0.75rem;">
                                                    <div class="text-center">
                                                        <h5 class="mb-1 text-white fw-bold event-title">{{ $event->title ?? 'Untitled Event' }}</h5>
                                                        <div class="d-flex justify-content-center align-items-center">
                                                            <span class="badge bg-white text-dark px-2 py-1 fw-semibold event-date">
                                                                <i class="fas fa-calendar-alt me-1"></i>
                                                                {{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d M Y') : '—' }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Card Body -->
                                                <div class="card-body p-0">
                                                    <!-- Event Details -->
                                                    <div class="p-3">
                                                        <div class="event-details">
                                                            <div class="detail-item mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-wrapper me-3">
                                                                        <i class="fas fa-user"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="detail-label">Speaker</div>
                                                                        <div class="detail-value">{{ $event->speaker ?? '—' }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="detail-item mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-wrapper me-3">
                                                                        <i class="fas fa-clock"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="detail-label">Time</div>
                                                                        <div class="detail-value">{{ $fmtTime($event->start_time) }} - {{ $fmtTime($event->end_time) }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="detail-item mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-wrapper me-3">
                                                                        <i class="fas fa-map-marker-alt"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="detail-label">Venue</div>
                                                                        <div class="detail-value">{{ $event->venue ?? '—' }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            
                                                            @if($event->attendance_count)
                                                            <div class="detail-item mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-wrapper me-3">
                                                                        <i class="fas fa-users"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="detail-label">Attendance</div>
                                                                        <div class="detail-value">{{ $event->attendance_count }} people</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                            
                                                            @if($event->budget_amount)
                                                            <div class="detail-item mb-2">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="icon-wrapper me-3">
                                                                        <i class="fas fa-money-bill-wave"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <div class="detail-label">Budget</div>
                                                                        <div class="detail-value">TZS {{ number_format($event->budget_amount) }}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Card Footer with Actions -->
                                                <div class="card-footer event-footer">
                                                    <div class="btn-group w-100" role="group">
                                                        <button class="btn btn-outline-info btn-sm action-btn" onclick="viewEvent({{ $event->id }})" title="View Details">
                                                            <i class="fas fa-eye me-1"></i>View
                                                        </button>
                                                        <button class="btn btn-outline-primary btn-sm action-btn" onclick="openEditEvent({{ $event->id }})" title="Edit Event">
                                                            <i class="fas fa-edit me-1"></i>Edit
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm action-btn" onclick="confirmDeleteEvent({{ $event->id }})" title="Delete Event">
                                                            <i class="fas fa-trash me-1"></i>Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <div class="text-center py-5">
                                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                                <h5 class="text-muted">No special events found</h5>
                                                <p class="text-muted">Click "Add Event" to create your first special event.</p>
                                            </div>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center">
                                <div class="text-muted small">Showing {{ $events->firstItem() }} to {{ $events->lastItem() }} of {{ $events->total() }} entries</div>
                                <div>{{ $events->withQueryString()->links() }}</div>
                            </div>
                        </div>
                    </div>
@endsection

@section('modals')
        <!-- Add Event Modal -->
        <div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg service-modal-content" style="border-radius: 20px; overflow: hidden;">
                    <!-- Stylish Header -->
                    <div class="modal-header border-0 service-modal-header" style="background: linear-gradient(180deg, #17082d 0%, #17082ddd 100%); padding: 1.25rem 1.5rem;">
                        <div class="d-flex align-items-center">
                            <div class="service-icon-wrapper me-3">
                                <i class="fas fa-star"></i>
                            </div>
                            <h5 class="modal-title mb-0 fw-bold text-white">
                                Create Special Event
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Stylish Body -->
                    <div class="modal-body service-modal-body" style="padding: 1.75rem; background: #f8f9fa;">
                        <form id="addEventForm">
                            <input type="hidden" id="editing_event_id" value="">
                            <div class="row g-3">
                                <!-- Row 1: Title & Speaker -->
                                <div class="col-md-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-star me-1 text-warning"></i>Event Title
                                    </label>
                                    <input type="text" class="form-control service-input" id="ev_title" placeholder="Event title">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-tie me-1 text-primary"></i>Speaker
                                    </label>
                                    <input type="text" class="form-control service-input" id="ev_speaker" placeholder="Speaker name">
                                </div>
                                
                                <!-- Row 2: Date & Time -->
                                <div class="col-md-4">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-calendar-alt me-1 text-info"></i>Event Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control service-input" id="ev_date" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-clock me-1 text-success"></i>Start Time
                                    </label>
                                    <input type="time" class="form-control service-input" id="ev_start">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-clock me-1 text-danger"></i>End Time
                                    </label>
                                    <input type="time" class="form-control service-input" id="ev_end">
                                </div>
                                
                                <!-- Row 3: Category & Venue -->
                                <div class="col-md-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-tags me-1 text-primary"></i>Category
                                    </label>
                                    <input type="text" class="form-control service-input" id="ev_category" placeholder="Event category">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>Venue
                                    </label>
                                    <input type="text" class="form-control service-input" id="ev_venue" placeholder="Venue location">
                                </div>
                                
                                <!-- Row 4: Attendance & Budget -->
                                <div class="col-md-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-users me-1 text-info"></i>Expected Attendance <small class="text-muted">(Optional)</small>
                                    </label>
                                    <input type="number" min="0" class="form-control service-input" id="ev_attendance" placeholder="Count">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-money-bill-wave me-1 text-success"></i>Budget (TZS) <small class="text-muted">(Optional)</small>
                                    </label>
                                    <input type="number" min="0" step="0.01" class="form-control service-input" id="ev_budget" placeholder="Amount">
                                </div>
                                
                                <!-- Row 5: Description -->
                                <div class="col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-file-alt me-1 text-primary"></i>Description
                                    </label>
                                    <textarea class="form-control service-input" id="ev_description" rows="2" placeholder="Event description"></textarea>
                                </div>
                                
                                <!-- Row 6: Notes -->
                                <div class="col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-sticky-note me-1 text-secondary"></i>Notes
                                    </label>
                                    <textarea class="form-control service-input" id="ev_notes" rows="2" placeholder="Additional notes"></textarea>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-secondary service-btn-cancel" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn service-btn-save" id="submitButton">
                                    <i class="fas fa-save me-1"></i>Save Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <style>
            /* Service Modal Styling */
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
                background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
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
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
                box-shadow: 0 2px 8px rgba(0,0,0,0.05);
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
            
            /* Modal Backdrop */
            .modal-backdrop {
                background: rgba(23, 8, 45, 0.4) !important;
            }
            
            .modal-backdrop.show {
                opacity: 1 !important;
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
                box-shadow: 0 6px 20px rgba(0,0,0,0.15);
            }

            /* Card Hover Effects */
            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
            }

            /* Floating Label Animation */
            .form-floating > .form-control:focus ~ label,
            .form-floating > .form-control:not(:placeholder-shown) ~ label {
                color: #940000;
                font-weight: 600;
            }

            /* Modal Backdrop */
            .modal-backdrop {
                background: linear-gradient(135deg, rgba(148, 0, 0, 0.1) 0%, rgba(102, 126, 234, 0.1) 100%);
            }
            /* Compact & Interactive SweetAlert Styling */
            .swal-popup-compact {
                border-radius: 16px !important;
                box-shadow: 0 15px 35px rgba(0,0,0,0.15), 0 5px 15px rgba(0,0,0,0.08) !important;
                border: none !important;
                overflow: hidden !important;
            }
            
            .swal-title-compact {
                font-size: 1.2rem !important;
                font-weight: 600 !important;
                color: #333 !important;
                margin-bottom: 0.5rem !important;
                padding: 0 !important;
            }
            
            .swal-title-custom {
                display: flex !important;
                align-items: center !important;
                gap: 0.5rem !important;
                color: #667eea !important;
                font-weight: 600 !important;
            }
            
            .swal-title-custom i {
                font-size: 1.1rem !important;
                color: #667eea !important;
            }
            
            .swal-content-compact {
                padding: 0 !important;
                margin: 0 !important;
                max-height: 70vh !important;
                overflow-y: auto !important;
            }
            
            /* Event Details Container */
            .event-details-container {
                padding: 1rem;
                background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
                border-radius: 12px;
            }
            
            /* Header Section */
            .event-header-section {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 1.5rem;
                padding-bottom: 1rem;
                border-bottom: 2px solid #e9ecef;
            }
            
            .event-title-main {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                font-size: 1.1rem;
                font-weight: 700;
                color: #333;
            }
            
            .event-icon {
                color: #667eea;
                font-size: 1.2rem;
                animation: pulse 2s infinite;
            }
            
            .event-category-badge {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                padding: 0.4rem 0.8rem;
                border-radius: 20px;
                font-size: 0.75rem;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);
            }
            
            /* Info Grid */
            .event-info-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 0.75rem;
                margin-bottom: 1.5rem;
            }
            
            .info-item {
                display: flex;
                align-items: center;
                gap: 0.75rem;
                padding: 0.75rem;
                background: white;
                border-radius: 10px;
                border: 1px solid #e9ecef;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                cursor: pointer;
                position: relative;
                overflow: hidden;
            }
            
            .info-item::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(102, 126, 234, 0.1), transparent);
                transition: left 0.5s;
            }
            
            .info-item:hover::before {
                left: 100%;
            }
            
            .info-item:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
                border-color: #667eea;
            }
            
            .info-icon {
                width: 40px;
                height: 40px;
                border-radius: 10px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 0.9rem;
                flex-shrink: 0;
                transition: all 0.3s ease;
            }
            
            .info-item:hover .info-icon {
                transform: scale(1.1) rotate(5deg);
            }
            
            .info-content {
                flex: 1;
                min-width: 0;
            }
            
            .info-label {
                display: block;
                font-size: 0.75rem;
                font-weight: 600;
                color: #6c757d;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 0.25rem;
            }
            
            .info-value {
                display: block;
                font-size: 0.9rem;
                font-weight: 600;
                color: #333;
                word-break: break-word;
            }
            
            /* Description & Notes Sections */
            .event-description-section,
            .event-notes-section {
                margin-top: 1rem;
                background: white;
                border-radius: 10px;
                border: 1px solid #e9ecef;
                overflow: hidden;
                transition: all 0.3s ease;
            }
            
            .event-description-section:hover,
            .event-notes-section:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            }
            
            .section-header {
                display: flex;
                align-items: center;
                gap: 0.5rem;
                padding: 0.75rem 1rem;
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                font-weight: 600;
                font-size: 0.9rem;
            }
            
            .section-content {
                padding: 1rem;
            }
            
            .section-content p {
                margin: 0;
                line-height: 1.5;
                color: #555;
            }
            
            /* Animations */
            @keyframes pulse {
                0%, 100% { transform: scale(1); }
                50% { transform: scale(1.05); }
            }
            
            @keyframes slideInUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
            
            .info-item {
                animation: slideInUp 0.6s ease-out;
            }
            
            .info-item:nth-child(1) { animation-delay: 0.1s; }
            .info-item:nth-child(2) { animation-delay: 0.2s; }
            .info-item:nth-child(3) { animation-delay: 0.3s; }
            .info-item:nth-child(4) { animation-delay: 0.4s; }
            .info-item:nth-child(5) { animation-delay: 0.5s; }
            .info-item:nth-child(6) { animation-delay: 0.6s; }
            
            /* SweetAlert Button Styling */
            .swal2-confirm {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                border: none !important;
                border-radius: 20px !important;
                padding: 0.6rem 1.5rem !important;
                font-weight: 600 !important;
                font-size: 0.9rem !important;
                transition: all 0.3s ease !important;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
            }
            
            .swal2-confirm:hover {
                transform: translateY(-2px) !important;
                box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4) !important;
            }
            
            .swal2-close {
                color: #667eea !important;
                font-size: 1.5rem !important;
                transition: all 0.3s ease !important;
            }
            
            .swal2-close:hover {
                transform: scale(1.1) !important;
                color: #764ba2 !important;
            }
        </style>

        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
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

            // Define switchView function first so it's available for onclick handlers
            window.switchView = function(view) {
                console.log('switchView called with:', view);
                try {
                    const listView = document.getElementById('listView');
                    const cardView = document.getElementById('cardView');
                    const listBtn = document.getElementById('listViewBtn');
                    const cardBtn = document.getElementById('cardViewBtn');
                    
                    console.log('Elements found:', { listView: !!listView, cardView: !!cardView, listBtn: !!listBtn, cardBtn: !!cardBtn });
                    
                    if (!listView || !cardView || !listBtn || !cardBtn) {
                        console.error('View toggle elements not found:', { listView, cardView, listBtn, cardBtn });
                        return false;
                    }
                    
                    if (view === 'list') {
                        listView.style.display = 'block';
                        cardView.style.display = 'none';
                        listBtn.classList.add('active');
                        cardBtn.classList.remove('active');
                        console.log('Switched to list view');
                    } else if (view === 'card') {
                        listView.style.display = 'none';
                        cardView.style.display = 'block';
                        listBtn.classList.remove('active');
                        cardBtn.classList.add('active');
                        console.log('Switched to card view');
                    }
                    
                    // Save preference to localStorage
                    localStorage.setItem('specialEventsView', view);
                    return true;
                } catch (error) {
                    console.error('Error switching view:', error);
                    return false;
                }
            };
            
            // Test if function is available
            console.log('switchView function defined:', typeof window.switchView);
            
            // Auto-open add modal if coming from dashboard
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
                @if(request('search') || request('from') || request('to'))
                    if (window.innerWidth <= 768 && filterBody && filterIcon) {
                        toggleFilters(); // Expand if filters are active
                        const filterHeader = document.querySelector('.filter-header');
                        if (filterHeader) filterHeader.classList.add('active');
                    }
                @endif
                
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('action') === 'add') {
                    openAddEvent();
                }
                
                // Load saved view preference
                const savedView = localStorage.getItem('specialEventsView') || 'list';
                window.switchView(savedView);
                
                // Add event listeners to toggle buttons - replace onclick handlers
                const listBtn = document.getElementById('listViewBtn');
                const cardBtn = document.getElementById('cardViewBtn');
                console.log('Setting up toggle button listeners:', { listBtn: !!listBtn, cardBtn: !!cardBtn });
                
                if (listBtn) {
                    // Remove onclick attribute and use event listener
                    listBtn.onclick = null;
                    listBtn.removeAttribute('onclick');
                    listBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('List button clicked via event listener');
                        window.switchView('list');
                        return false;
                    }, true); // Use capture phase
                }
                
                if (cardBtn) {
                    // Remove onclick attribute and use event listener
                    cardBtn.onclick = null;
                    cardBtn.removeAttribute('onclick');
                    cardBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        console.log('Card button clicked via event listener');
                        window.switchView('card');
                        return false;
                    }, true); // Use capture phase
                }
                
                // Fix aria-hidden accessibility issue for modals
                const addEventModal = document.getElementById('addEventModal');
                if (addEventModal) {
                    // Ensure aria-hidden is properly managed
                    addEventModal.addEventListener('show.bs.modal', function() {
                        this.setAttribute('aria-hidden', 'false');
                    });
                    addEventModal.addEventListener('shown.bs.modal', function() {
                        this.setAttribute('aria-hidden', 'false');
                        // Focus on first input for accessibility
                        const firstInput = this.querySelector('input:not([type="hidden"]), textarea, select');
                        if (firstInput) {
                            setTimeout(() => firstInput.focus(), 100);
                        }
                    });
                    addEventModal.addEventListener('hide.bs.modal', function() {
                        this.setAttribute('aria-hidden', 'true');
                    });
                    addEventModal.addEventListener('hidden.bs.modal', function() {
                        this.setAttribute('aria-hidden', 'true');
                        // Reset form and button state when modal is closed
                        const form = document.getElementById('addEventForm');
                        if (form) {
                            form.reset();
                            document.getElementById('editing_event_id').value = '';
                        }
                        const submitBtn = document.getElementById('submitButton');
                        if (submitBtn) {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Save Event';
                        }
                        // Reset modal title and button text
                        const titleEl = document.querySelector('#addEventModal .modal-title');
                        if (titleEl) titleEl.textContent = 'Create Special Event';
                        
                        // Ensure modal backdrop is removed and page is interactive
                        const backdrop = document.querySelector('.modal-backdrop');
                        if (backdrop) {
                            backdrop.remove();
                        }
                        // Remove modal-open class from body if it exists
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';
                    });
                }
            });
            function viewEvent(id){
                fetch(`{{ url('/special-events') }}/${id}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => { if (!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
                    .then(s => {
                        const fmtTime = (t) => { 
                            try { 
                                if(!t) return '--:--'; 
                                // Handle ISO format
                                if (t.includes('T')) {
                                    const time = new Date(t);
                                    return time.toLocaleTimeString('en-US', { 
                                        hour: '2-digit', 
                                        minute: '2-digit',
                                        hour12: true 
                                    });
                                }
                                // Handle HH:MM:SS format
                                if(/^\d{2}:\d{2}/.test(t)) {
                                    const [hours, minutes] = t.split(':');
                                    const time = new Date();
                                    time.setHours(parseInt(hours), parseInt(minutes), 0);
                                    return time.toLocaleTimeString('en-US', { 
                                        hour: '2-digit', 
                                        minute: '2-digit',
                                        hour12: true 
                                    });
                                }
                                return t.substring(0,5); 
                            } catch { 
                                return '--:--'; 
                            } 
                        };
                        const fmtDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';
                        const fmtCurrency = (amount) => amount ? `TZS ${parseFloat(amount).toLocaleString()}` : '—';
                        
                        const html = `
                            <div class="event-details-container">
                                <!-- Header Section -->
                                <div class="event-header-section">
                                    <div class="event-title-main">
                                        <i class="fas fa-calendar-plus event-icon"></i>
                                        <span>${s.title || 'Special Event'}</span>
                                    </div>
                                    ${s.category ? `<span class="event-category-badge">${s.category}</span>` : ''}
                                </div>
                                
                                <!-- Main Info Grid -->
                                <div class="event-info-grid">
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-calendar-alt"></i>
                                        </div>
                                        <div class="info-content">
                                            <span class="info-label">Date</span>
                                            <span class="info-value">${fmtDate(s.event_date)}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="info-content">
                                            <span class="info-label">Time</span>
                                            <span class="info-value">${fmtTime(s.start_time)} - ${fmtTime(s.end_time)}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="info-content">
                                            <span class="info-label">Speaker</span>
                                            <span class="info-value">${s.speaker || '—'}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="info-content">
                                            <span class="info-label">Venue</span>
                                            <span class="info-value">${s.venue || '—'}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-users"></i>
                                        </div>
                                        <div class="info-content">
                                            <span class="info-label">Attendance</span>
                                            <span class="info-value">${s.attendance_count || '—'}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-icon">
                                            <i class="fas fa-money-bill-wave"></i>
                                        </div>
                                        <div class="info-content">
                                            <span class="info-label">Budget</span>
                                            <span class="info-value">${fmtCurrency(s.budget_amount)}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                ${s.description ? `
                                    <div class="event-description-section">
                                        <div class="section-header">
                                            <i class="fas fa-file-alt"></i>
                                            <span>Description</span>
                                        </div>
                                        <div class="section-content">
                                            <p>${s.description}</p>
                                        </div>
                                    </div>
                                ` : ''}
                                
                                ${s.notes ? `
                                    <div class="event-notes-section">
                                        <div class="section-header">
                                            <i class="fas fa-sticky-note"></i>
                                            <span>Notes</span>
                                        </div>
                                        <div class="section-content">
                                            <p>${s.notes}</p>
                                        </div>
                                    </div>
                                ` : ''}
                            </div>
                        `;
                        
                        Swal.fire({ 
                            title: '<div class="swal-title-custom"><i class="fas fa-calendar-plus"></i><span>Event Details</span></div>', 
                            html: html, 
                            width: 650, 
                            showConfirmButton: true,
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#667eea',
                            customClass: {
                                popup: 'swal-popup-compact',
                                title: 'swal-title-compact',
                                content: 'swal-content-compact'
                            },
                            showCloseButton: true,
                            focusConfirm: false,
                            allowOutsideClick: true
                        });
                    })
                    .catch(() => Swal.fire({ icon:'error', title:'Failed to load details' }));
            }

            function openAddEvent(){
                // Reset form and set add mode
                document.getElementById('editing_event_id').value = '';
                const titleEl = document.querySelector('#addEventModal .modal-title');
                if (titleEl) titleEl.textContent = 'Create Special Event';
                const submitBtnEl = document.getElementById('submitButton');
                if (submitBtnEl) submitBtnEl.textContent = 'Save';
                document.getElementById('addEventForm').reset();
                new bootstrap.Modal(document.getElementById('addEventModal')).show();
            }

            function openEditEvent(id){
                fetch(`{{ url('/special-events') }}/${id}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(s => {
                        // Set editing mode
                        document.getElementById('editing_event_id').value = id;
                        const titleEl = document.querySelector('#addEventModal .modal-title');
                        if (titleEl) titleEl.textContent = 'Edit Special Event';
                        const submitBtnEl = document.getElementById('submitButton');
                        if (submitBtnEl) submitBtnEl.textContent = 'Update';
                        
                        // Populate form fields
                        document.getElementById('ev_date').value = s.event_date || '';
                        document.getElementById('ev_start').value = s.start_time || '';
                        document.getElementById('ev_end').value = s.end_time || '';
                        document.getElementById('ev_title').value = s.title || '';
                        document.getElementById('ev_speaker').value = s.speaker || '';
                        document.getElementById('ev_venue').value = s.venue || '';
                        document.getElementById('ev_attendance').value = s.attendance_count || '';
                        document.getElementById('ev_budget').value = s.budget_amount || '';
                        document.getElementById('ev_category').value = s.category || '';
                        document.getElementById('ev_description').value = s.description || '';
                        document.getElementById('ev_notes').value = s.notes || '';
                        new bootstrap.Modal(document.getElementById('addEventModal')).show();
                    });
            }


            document.getElementById('addEventForm').addEventListener('submit', function(e){
                e.preventDefault();
                const submitBtn = document.getElementById('submitButton');
                submitBtn.disabled = true;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

                const fd = new FormData();
                fd.append('event_date', document.getElementById('ev_date').value);
                fd.append('start_time', document.getElementById('ev_start').value);
                fd.append('end_time', document.getElementById('ev_end').value);
                fd.append('title', document.getElementById('ev_title').value);
                fd.append('speaker', document.getElementById('ev_speaker').value);
                fd.append('venue', document.getElementById('ev_venue').value);
                fd.append('attendance_count', document.getElementById('ev_attendance').value);
                fd.append('budget_amount', document.getElementById('ev_budget').value);
                fd.append('category', document.getElementById('ev_category').value);
                fd.append('description', document.getElementById('ev_description').value);
                fd.append('notes', document.getElementById('ev_notes').value);
                
                const editingId = document.getElementById('editing_event_id').value;
                let url, method;
                
                if (editingId) {
                    // Update existing event
                    url = `{{ url('/special-events') }}/${editingId}`;
                    method = 'POST';
                    fd.append('_method', 'PUT');
                } else {
                    // Create new event
                    url = `{{ route('special.events.store') }}`;
                    method = 'POST';
                }
                
                fetch(url, { 
                    method: method, 
                    headers: { 
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }, 
                    body: fd
                })
                    .then(async (r) => {
                        const contentType = r.headers.get('content-type') || '';
                        if (!r.ok) {
                            let errorMsg = `HTTP ${r.status}`;
                            if (contentType.includes('application/json')) {
                                const err = await r.json().catch(() => null);
                                if (err && err.message) errorMsg = err.message;
                            } else {
                                const text = await r.text().catch(() => '');
                                if (text) errorMsg = text.substring(0, 200);
                            }
                            throw new Error(errorMsg);
                        }
                        if (contentType.includes('application/json')) {
                            return r.json();
                        }
                        // Fallback: treat non-JSON as success if reached here
                        return { success: true, message: 'Saved' };
                    })
                    .then(res => { 
                        if(res && res.success){ 
                            // Reset form after successful submission
                            document.getElementById('addEventForm').reset();
                            document.getElementById('editing_event_id').value = '';
                            const titleEl = document.querySelector('#addEventModal .modal-title');
                            if (titleEl) titleEl.textContent = 'Create Special Event';
                            const submitBtnEl = document.getElementById('submitButton');
                            if (submitBtnEl) submitBtnEl.textContent = 'Save';
                            
                            Swal.fire({ icon:'success', title: editingId ? 'Updated' : 'Saved', timer:1200, showConfirmButton:false }).then(()=>location.reload()); 
                        } else { 
                            Swal.fire({ icon:'error', title:'Failed', text: (res && res.message) ? res.message : 'Try again' }); 
                        } 
                    })
                    .catch((err) => {
                        Swal.fire({ icon:'error', title:'Request error', text: err?.message || 'Network error' });
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    });
            });

            function confirmDeleteEvent(id){
                Swal.fire({ title:'Delete event?', text:'This action cannot be undone.', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete', cancelButtonText:'Cancel', confirmButtonColor:'#dc3545' })
                .then((result)=>{ 
                    if(result.isConfirmed){ 
                        fetch(`{{ url('/special-events') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
                            .then(r => r.json())
                            .then(res => { 
                                if(res.success){ 
                                    // Remove from both list and card views
                                    document.getElementById(`row-${id}`)?.remove();
                                    document.getElementById(`card-${id}`)?.remove();
                                    Swal.fire({ icon:'success', title:'Deleted', timer:1200, showConfirmButton:false }); 
                                } else { 
                                    Swal.fire({ icon:'error', title:'Delete failed', text: res.message || 'Try again' }); 
                                } 
                            })
                            .catch(()=> Swal.fire({ icon:'error', title:'Error', text:'Request failed.' })); 
                    } 
                });
            }
        </script>
@endsection
