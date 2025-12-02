@extends('layouts.index')

@section('content')
        <style>
            /* Mobile Responsive Styles */
            @media (max-width: 767.98px) {
                .container-fluid {
                    padding-left: 0.75rem !important;
                    padding-right: 0.75rem !important;
                }
                
                /* Page Header - Stack on mobile */
                .page-header-mobile {
                    flex-direction: column !important;
                    align-items: flex-start !important;
                    gap: 1rem;
                }
                
                .page-header-mobile h2 {
                    font-size: 1.5rem !important;
                    margin-bottom: 0 !important;
                }
                
                .page-header-mobile .btn-group-mobile {
                    display: flex;
                    flex-direction: column;
                    width: 100%;
                    gap: 0.5rem;
                }
                
                .page-header-mobile .btn-group-mobile .btn {
                    width: 100%;
                    justify-content: center;
                    font-size: 0.875rem;
                }
                
                /* Filter Form - Stack on mobile */
                .filter-form .row > div {
                    margin-bottom: 0.75rem;
                }
                
                .filter-form .btn {
                    width: 100%;
                }
                
                /* Table - Horizontal scroll on mobile */
                .table-responsive {
                    overflow-x: auto;
                    -webkit-overflow-scrolling: touch;
                }
                
                .table {
                    font-size: 0.875rem !important;
                    min-width: 900px;
                }
                
                .table th,
                .table td {
                    padding: 0.5rem !important;
                    white-space: nowrap;
                }
                
                .table th:first-child,
                .table td:first-child {
                    position: sticky;
                    left: 0;
                    background-color: inherit;
                    z-index: 1;
                }
                
                /* Button groups - Stack on mobile */
                .btn-group-sm {
                    flex-direction: column;
                    width: 100%;
                }
                
                .btn-group-sm .btn {
                    width: 100%;
                    margin-bottom: 0.25rem;
                }
                
                /* Cards - Better spacing on mobile */
                .card {
                    margin-bottom: 1rem !important;
                }
                
                .card-body {
                    padding: 0.75rem !important;
                }
                
                .card-footer {
                    flex-direction: column;
                    gap: 0.5rem;
                    align-items: flex-start !important;
                }
                
                /* Modals - Full width on mobile */
                .modal-dialog {
                    margin: 0.5rem;
                    max-width: calc(100% - 1rem);
                }
                
                .modal-content {
                    border-radius: 0.5rem !important;
                }
                
                .modal-body {
                    padding: 1rem !important;
                }
                
                .modal-body .row > div {
                    margin-bottom: 0.75rem;
                }
                
                /* Badge adjustments */
                .badge {
                    font-size: 0.75rem;
                    padding: 0.25rem 0.5rem;
                }
            }
            
            @media (max-width: 575.98px) {
                .container-fluid {
                    padding-left: 0.5rem !important;
                    padding-right: 0.5rem !important;
                }
                
                .page-header-mobile h2 {
                    font-size: 1.25rem !important;
                }
                
                .table {
                    font-size: 0.75rem !important;
                }
                
                .btn {
                    font-size: 0.875rem !important;
                    padding: 0.375rem 0.75rem !important;
                }
                
                .btn i {
                    margin-right: 0.25rem !important;
                }
                
                .modal-dialog {
                    margin: 0.25rem;
                    max-width: calc(100% - 0.5rem);
                }
            }
            
            .table.interactive-table tbody tr:hover { background-color: #f8f9ff; }
            .table.interactive-table tbody tr td:first-child { border-left: 4px solid #5b2a86; }
            
            
            /* Custom Searchable Dropdown Styles */
            .searchable-select-container {
                position: relative;
            }
            
            .searchable-input {
                min-height: auto !important;
                padding: 0.375rem 0.75rem !important;
                line-height: 1.5 !important;
            }
            
            .searchable-input:focus ~ label,
            .searchable-input.has-value ~ label {
                display: none;
            }
            
            .searchable-input ~ label {
                display: none;
            }
            
            .searchable-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 2px solid #e9ecef;
                border-top: none;
                border-radius: 0 0 10px 10px;
                max-height: 200px;
                overflow-y: auto;
                z-index: 1000;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            
            .searchable-dropdown-item {
                padding: 10px 15px;
                cursor: pointer;
                border-bottom: 1px solid #f8f9fa;
                transition: background-color 0.2s;
            }
            
            .searchable-dropdown-item:hover {
                background-color: #f8f9fa;
            }
            
            .searchable-dropdown-item.selected {
                background-color: #940000;
                color: white;
            }
            
            .searchable-dropdown-item:last-child {
                border-bottom: none;
            }
            
            /* Compact Filter Section Styles */
            #filtersForm {
                transition: all 0.3s ease;
            }
            #filtersForm .card-header {
                transition: background-color 0.2s ease;
            }
            #filterBody {
                transition: all 0.3s ease;
            }
            
            /* Desktop: Always show filters, make header non-clickable */
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
                #filterToggleIcon {
                    font-size: 1.1rem !important;
                    width: 24px !important;
                    height: 24px !important;
                    display: inline-flex !important;
                    align-items: center !important;
                    justify-content: center !important;
                    cursor: pointer !important;
                    transition: transform 0.3s ease !important;
                    flex-shrink: 0 !important;
                }
            }
        </style>
                    <div class="container-fluid px-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3 gap-2 page-header-mobile">
                            <h2 class="mb-0">Church Services</h2>
                            <div class="d-flex flex-wrap gap-2 btn-group-mobile">
                                <a href="{{ route('attendance.index', ['service_type' => 'sunday_service']) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-users me-2"></i><span class="d-none d-sm-inline">Record </span>Attendance
                                </a>
                                <a href="{{ route('attendance.statistics') }}" class="btn btn-outline-info btn-sm">
                                    <i class="fas fa-chart-bar me-2"></i>Statistics
                                </a>
                                <a href="{{ route('services.sunday.export.csv', request()->query()) }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-excel me-2"></i><span class="d-none d-sm-inline">Export </span>CSV
                                </a>
                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addServiceModal">
                                    <i class="fas fa-plus me-2"></i>Add Service
                                </button>
                            </div>
                        </div>

                        <!-- Filters & Search - Collapsible on Mobile -->
                        <form method="GET" action="{{ route('services.sunday.index') }}" class="card mb-3 border-0 shadow-sm" id="filtersForm">
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
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control form-control-sm" placeholder="Search theme, preacher, venue">
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
                                    <a href="{{ route('services.sunday.index') }}" class="btn btn-outline-secondary btn-sm flex-fill flex-md-grow-0">
                                        <i class="fas fa-redo me-1"></i>
                                        <span class="d-none d-sm-inline">Reset</span>
                                        <span class="d-sm-none">Clear</span>
                                    </a>
                                </div>
                            </div>
                        </form>

                        <div class="card">
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="text-nowrap">#</th>
                                                <th>Date</th>
                                                <th>Service Type</th>
                                                <th>Theme</th>
                                                <th>Preacher</th>
                                                <th>Coordinator</th>
                                                <th>Time</th>
                                                <th>Venue</th>
                                                <th>Status</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($services as $service)
                                                <tr id="row-{{ $service->id }}">
                                                    <td class="text-muted">{{ $services->firstItem() + $loop->index }}</td>
                                                    <td><span class="badge bg-secondary">{{ optional($service->service_date)->format('d/m/Y') }}</span></td>
                                                    <td>
                                                        @php
                                                            $serviceTypeLabels = [
                                                                'sunday_service' => 'Sunday Service',
                                                                'prayer_meeting' => 'Prayer Meeting',
                                                                'bible_study' => 'Bible Study',
                                                                'youth_service' => 'Youth Service',
                                                                'children_service' => 'Children Service',
                                                                'women_fellowship' => 'Women Fellowship',
                                                                'men_fellowship' => 'Men Fellowship',
                                                                'evangelism' => 'Evangelism',
                                                                'other' => 'Other'
                                                            ];
                                                        @endphp
                                                        <span class="badge bg-primary">{{ $serviceTypeLabels[$service->service_type] ?? ucfirst(str_replace('_', ' ', $service->service_type)) }}</span>
                                                    </td>
                                                    <td>{{ $service->theme ?? '—' }}</td>
                                                    <td>{{ $service->preacher ?? '—' }}</td>
                                                    <td>{{ $service->coordinator ? $service->coordinator->full_name : '—' }}</td>
                                                    @php
                                                        $fmtTime = function($t){
                                                            if (!$t) return '--:--';
                                                            try {
                                                                if (preg_match('/^\d{2}:\d{2}/', $t)) return substr($t,0,5);
                                                                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/', $t)) return substr(substr($t,11),0,5);
                                                                return \Carbon\Carbon::parse($t)->format('H:i');
                                                            } catch (\Throwable $e) { return '--:--'; }
                                                        };
                                                    @endphp
                                                    <td>{{ $fmtTime($service->start_time) }} - {{ $fmtTime($service->end_time) }}</td>
                                                    <td>{{ $service->venue ?? '—' }}</td>
                                                    <td>
                                                        @if($service->status === 'completed')
                                                            <span class="badge bg-success">Completed</span>
                                                        @else
                                                            <span class="badge bg-warning">Scheduled</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-info" onclick="viewService({{ $service->id }})" title="View">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-primary" onclick="openEditService({{ $service->id }})" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger" onclick="confirmDeleteService({{ $service->id }})" title="Delete">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="10" class="text-center py-4">No services found.</td></tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-between align-items-center flex-wrap">
                                <div class="text-muted small mb-2 mb-md-0">Showing {{ $services->firstItem() }} to {{ $services->lastItem() }} of {{ $services->total() }} entries</div>
                                <div>{{ $services->withQueryString()->links() }}</div>
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

        <!-- Add Service Modal -->
        <div class="modal fade" id="addServiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg service-modal-content" style="border-radius: 20px; overflow: hidden;">
                    <!-- Stylish Header -->
                    <div class="modal-header border-0 service-modal-header" style="background: linear-gradient(180deg, #17082d 0%, #17082ddd 100%); padding: 1.25rem 1.5rem;">
                        <div class="d-flex align-items-center">
                            <div class="service-icon-wrapper me-3">
                                <i class="fas fa-church"></i>
                            </div>
                            <h5 class="modal-title mb-0 fw-bold text-white">
                                Create Church Service
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Stylish Body -->
                    <div class="modal-body service-modal-body" style="padding: 1.75rem; background: #f8f9fa;">
                        <form id="addServiceForm">
                            <div class="row g-3">
                                <!-- Row 1: Service Type & Theme -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-church me-1 text-primary"></i>Service Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select service-input" id="svc_service_type" required>
                                        <option value=""></option>
                                        <option value="sunday_service">Sunday Service</option>
                                        <option value="prayer_meeting">Prayer Meeting</option>
                                        <option value="bible_study">Bible Study</option>
                                        <option value="youth_service">Youth Service</option>
                                        <option value="children_service">Children Service</option>
                                        <option value="women_fellowship">Women Fellowship</option>
                                        <option value="men_fellowship">Men Fellowship</option>
                                        <option value="evangelism">Evangelism</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-12" id="svc_other_service_wrapper" style="display: none;">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-edit me-1 text-primary"></i>Specify Type
                                    </label>
                                    <input type="text" class="form-control service-input" id="svc_other_service" placeholder="Enter service type">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-star me-1 text-warning"></i>Theme
                                    </label>
                                    <input type="text" class="form-control service-input" id="svc_theme" placeholder="Service theme">
                                </div>
                                
                                <!-- Row 2: Date & Time -->
                                <div class="col-md-4 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-calendar-alt me-1 text-info"></i>Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control service-input" id="svc_date" required>
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-clock me-1 text-success"></i>Start Time
                                    </label>
                                    <input type="time" class="form-control service-input" id="svc_start">
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-clock me-1 text-danger"></i>End Time
                                    </label>
                                    <input type="time" class="form-control service-input" id="svc_end">
                                </div>
                                
                                <!-- Row 3: Preacher & Coordinator -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-tie me-1 text-primary"></i>Preacher
                                    </label>
                                    <select class="form-select service-input" id="svc_preacher_id">
                                        <option value="">-- Select Preacher or Type Custom --</option>
                                    </select>
                                    <input type="text" class="form-control service-input mt-2" id="svc_preacher_custom" placeholder="Or type custom preacher name..." style="display: none;">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-cog me-1 text-primary"></i>Coordinator
                                    </label>
                                    <select class="form-select service-input" id="svc_coordinator_id">
                                        <option value="">-- Select Coordinator --</option>
                                    </select>
                                </div>
                                
                                <!-- Row 4: Church Elder & Venue -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-shield me-1 text-primary"></i>Church Elder
                                    </label>
                                    <select class="form-select service-input" id="svc_church_elder_id">
                                        <option value="">-- Select Church Elder --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>Venue
                                    </label>
                                    <input type="text" class="form-control service-input" id="svc_venue" placeholder="Venue location">
                                </div>
                                
                                <!-- Row 5: Choir, Attendance & Offerings -->
                                <div class="col-md-3 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-music me-1 text-warning"></i>Choir
                                    </label>
                                    <input type="text" class="form-control service-input" id="svc_choir" placeholder="Choir name">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-users me-1 text-info"></i>Registered Members <small class="text-muted">(Optional)</small>
                                    </label>
                                    <input type="number" min="0" class="form-control service-input" id="svc_attendance" placeholder="Count">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-plus me-1 text-primary"></i>Guests <small class="text-muted">(Optional)</small>
                                    </label>
                                    <input type="number" min="0" class="form-control service-input" id="svc_guests" placeholder="Count">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-coins me-1 text-success"></i>Offerings <small class="text-muted">(Optional)</small>
                                    </label>
                                    <input type="number" min="0" step="0.01" class="form-control service-input" id="svc_offerings" placeholder="TZS">
                                </div>
                                
                                <!-- Row 6: Scripture Readings -->
                                <div class="col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-book-open me-1 text-primary"></i>Scripture Readings
                                    </label>
                                    <textarea class="form-control service-input" id="svc_readings" rows="2" placeholder="Enter scripture readings"></textarea>
                                </div>
                                
                                <!-- Row 7: Notes -->
                                <div class="col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-sticky-note me-1 text-secondary"></i>Notes
                                    </label>
                                    <textarea class="form-control service-input" id="svc_notes" rows="2" placeholder="Additional notes"></textarea>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-secondary service-btn-cancel" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn service-btn-save">
                                    <i class="fas fa-save me-1"></i>Save Service
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
            
            /* Searchable Dropdown Enhancement */
            .searchable-dropdown {
                border: 2px solid #17082d;
                border-top: none;
                border-radius: 0 0 10px 10px;
                box-shadow: 0 4px 12px rgba(23, 8, 45, 0.15);
            }
            
            .searchable-dropdown-item:hover {
                background: linear-gradient(180deg, rgba(23, 8, 45, 0.1) 0%, rgba(23, 8, 45, 0.15) 100%);
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
        </style>

        <!-- View Modal -->
        <div class="modal fade" id="serviceDetailsModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%); border: none;">
                        <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-info-circle"></i><span>Service Details</span></h5>
                        <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light" id="serviceDetailsBody">
                        <div class="text-center text-muted py-4">Loading...</div>
                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center">
                        <div class="small"><span class="me-1">Powered by</span><a href="https://emca.tech/#" target="_blank" rel="noopener" class="emca-link fw-semibold" style="color: #940000 !important;">EmCa Technologies</a></div>
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editServiceModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg service-modal-content" style="border-radius: 20px; overflow: hidden;">
                    <!-- Stylish Header -->
                    <div class="modal-header border-0 service-modal-header" style="background: linear-gradient(180deg, #17082d 0%, #17082ddd 100%); padding: 1.25rem 1.5rem;">
                        <div class="d-flex align-items-center">
                            <div class="service-icon-wrapper me-3">
                                <i class="fas fa-edit"></i>
                            </div>
                            <h5 class="modal-title mb-0 fw-bold text-white">
                                Edit Church Service
                            </h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Stylish Body -->
                    <div class="modal-body service-modal-body" style="padding: 1.75rem; background: #f8f9fa;">
                        <form id="editServiceForm">
                            <input type="hidden" id="edit_id">
                            <div class="row g-3">
                                <!-- Row 1: Service Type & Theme -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-church me-1 text-primary"></i>Service Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select service-input" id="edit_service_type" required>
                                        <option value=""></option>
                                        <option value="sunday_service">Sunday Service</option>
                                        <option value="prayer_meeting">Prayer Meeting</option>
                                        <option value="bible_study">Bible Study</option>
                                        <option value="youth_service">Youth Service</option>
                                        <option value="children_service">Children Service</option>
                                        <option value="women_fellowship">Women Fellowship</option>
                                        <option value="men_fellowship">Men Fellowship</option>
                                        <option value="evangelism">Evangelism</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                <div class="col-md-6" id="edit_other_service_wrapper" style="display: none;">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-edit me-1 text-primary"></i>Specify Type
                                    </label>
                                    <input type="text" class="form-control service-input" id="edit_other_service" placeholder="Enter service type">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-star me-1 text-warning"></i>Theme
                                    </label>
                                    <input type="text" class="form-control service-input" id="edit_theme" placeholder="Service theme">
                                </div>
                                
                                <!-- Row 2: Date & Time -->
                                <div class="col-md-4 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-calendar-alt me-1 text-info"></i>Date <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control service-input" id="edit_date" required>
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-clock me-1 text-success"></i>Start Time
                                    </label>
                                    <input type="time" class="form-control service-input" id="edit_start">
                                </div>
                                <div class="col-md-4 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-clock me-1 text-danger"></i>End Time
                                    </label>
                                    <input type="time" class="form-control service-input" id="edit_end">
                                </div>
                                
                                <!-- Row 3: Preacher & Coordinator -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-tie me-1 text-primary"></i>Preacher
                                    </label>
                                    <select class="form-select service-input" id="edit_preacher_id">
                                        <option value="">-- Select Preacher or Type Custom --</option>
                                    </select>
                                    <input type="text" class="form-control service-input mt-2" id="edit_preacher_custom" placeholder="Or type custom preacher name..." style="display: none;">
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-cog me-1 text-primary"></i>Coordinator
                                    </label>
                                    <select class="form-select service-input" id="edit_coordinator_id">
                                        <option value="">-- Select Coordinator --</option>
                                    </select>
                                </div>
                                
                                <!-- Row 4: Church Elder & Venue -->
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-shield me-1 text-primary"></i>Church Elder
                                    </label>
                                    <select class="form-select service-input" id="edit_church_elder_id">
                                        <option value="">-- Select Church Elder --</option>
                                    </select>
                                </div>
                                <div class="col-md-6 col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-map-marker-alt me-1 text-danger"></i>Venue
                                    </label>
                                    <input type="text" class="form-control service-input" id="edit_venue" placeholder="Venue location">
                                </div>
                                
                                <!-- Row 5: Choir, Attendance & Offerings -->
                                <div class="col-md-3 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-music me-1 text-warning"></i>Choir
                                    </label>
                                    <input type="text" class="form-control service-input" id="edit_choir" placeholder="Choir name">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-users me-1 text-info"></i>Registered Members <small class="text-muted">(Optional)</small>
                                    </label>
                                    <input type="number" min="0" class="form-control service-input" id="edit_attendance" placeholder="Count">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-user-plus me-1 text-primary"></i>Guests <small class="text-muted">(Optional)</small>
                                    </label>
                                    <input type="number" min="0" class="form-control service-input" id="edit_guests" placeholder="Count">
                                </div>
                                <div class="col-md-3 col-6">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-coins me-1 text-success"></i>Offerings <small class="text-muted">(Optional)</small>
                                    </label>
                                    <input type="number" min="0" step="0.01" class="form-control service-input" id="edit_offerings" placeholder="TZS">
                                </div>
                                
                                <!-- Row 6: Scripture Readings -->
                                <div class="col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-book-open me-1 text-primary"></i>Scripture Readings
                                    </label>
                                    <textarea class="form-control service-input" id="edit_readings" rows="2" placeholder="Enter scripture readings"></textarea>
                                </div>
                                
                                <!-- Row 7: Notes -->
                                <div class="col-12">
                                    <label class="form-label service-label mb-2">
                                        <i class="fas fa-sticky-note me-1 text-secondary"></i>Notes
                                    </label>
                                    <textarea class="form-control service-input" id="edit_notes" rows="2" placeholder="Additional notes"></textarea>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-secondary service-btn-cancel" data-bs-dismiss="modal">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                                <button type="submit" class="btn service-btn-save">
                                    <i class="fas fa-save me-1"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script>
            // Wait for DOM to be fully loaded
            (function() {
                // Set year in footer if element exists - try immediately and also on DOMContentLoaded
                function setYear() {
                    const yearElement = document.getElementById('year');
                    if (yearElement) {
                        yearElement.textContent = new Date().getFullYear();
                    }
                }
                
                // Try immediately (in case script is at end of body)
                setYear();
                
                // Also try on DOMContentLoaded (in case script is in head)
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', setYear);
                }
            })();
            
            // Handle dynamic "Other" service type input
            function toggleOtherServiceInput(selectId, wrapperId, inputId) {
                const select = document.getElementById(selectId);
                const wrapper = document.getElementById(wrapperId);
                const input = document.getElementById(inputId);
                
                if (select.value === 'other') {
                    wrapper.style.display = 'block';
                    input.required = true;
                } else {
                    wrapper.style.display = 'none';
                    input.required = false;
                    input.value = '';
                }
            }
            
            // Add event listeners for service type dropdowns
            document.getElementById('svc_service_type').addEventListener('change', function() {
                toggleOtherServiceInput('svc_service_type', 'svc_other_service_wrapper', 'svc_other_service');
            });
            
            document.getElementById('edit_service_type').addEventListener('change', function() {
                toggleOtherServiceInput('edit_service_type', 'edit_other_service_wrapper', 'edit_other_service');
            });
            
            // Custom Searchable Dropdown Functionality
            function initializeSearchableDropdowns() {
                // Initialize all searchable inputs
                document.querySelectorAll('.searchable-input').forEach(function(input) {
                    if (!input) return;
                    
                    const selectId = input.id.replace('_search', '_id');
                    const dropdownId = input.id.replace('_search', '_dropdown');
                    const select = document.getElementById(selectId);
                    const dropdown = document.getElementById(dropdownId);
                    
                    if (!select || !dropdown) {
                        console.warn('Select or dropdown not found for input:', input.id);
                        return;
                    }
                    
                    // Show dropdown on focus and load data if needed
                    const isCoordinatorField = input.id.includes('coordinator');
                    const isChurchElderField = input.id.includes('church_elder');
                    const isPreacherField = input.id.includes('preacher');
                    
                    input.addEventListener('focus', function() {
                        // Load data if select is empty and it's a dynamic field
                        if (select.options.length <= 1) {
                            if (isCoordinatorField) {
                                loadCoordinators(selectId, input.id, '').then(() => {
                                    showDropdown(input, select, dropdown);
                                });
                            } else if (isChurchElderField) {
                                loadChurchElders(selectId).then(() => {
                                    showDropdown(input, select, dropdown);
                                });
                            } else if (isPreacherField) {
                                loadPreachers(selectId).then(() => {
                                    showDropdown(input, select, dropdown);
                                });
                            } else {
                                showDropdown(input, select, dropdown);
                            }
                        } else {
                            showDropdown(input, select, dropdown);
                        }
                    });
                    
                    // Hide dropdown when clicking outside
                    document.addEventListener('click', function(e) {
                        if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                            dropdown.style.display = 'none';
                            // Remove has-value class if input is empty
                            if (input.value.length === 0) {
                                input.classList.remove('has-value');
                            }
                        }
                    });
                    
                    // Show dropdown when input is focused/clicked
                    input.addEventListener('focus', function() {
                        if (select.options.length > 1) {
                            showDropdown(input, select, dropdown);
                        } else {
                            // If no options loaded yet, load them
                            if (isCoordinatorField) {
                                loadCoordinators(selectId, input.id, '').then(() => {
                                    showDropdown(input, select, dropdown);
                                });
                            } else if (isChurchElderField) {
                                loadChurchElders(selectId).then(() => {
                                    showDropdown(input, select, dropdown);
                                });
                            } else {
                                showDropdown(input, select, dropdown);
                            }
                        }
                    });
                    
                    input.addEventListener('click', function() {
                        if (select.options.length > 1) {
                            showDropdown(input, select, dropdown);
                        }
                    });
                    
                    // Search functionality with debouncing for API calls
                    let searchTimeout;
                    
                    input.addEventListener('input', function() {
                        const searchTerm = input.value.trim();
                        
                        // Clear previous timeout
                        if (searchTimeout) {
                            clearTimeout(searchTimeout);
                        }
                        
                        // For coordinator and church elder fields, use API search if typing
                        if ((isCoordinatorField || isChurchElderField) && searchTerm.length >= 2) {
                            // Debounce API calls - wait 300ms after user stops typing
                            searchTimeout = setTimeout(function() {
                                if (isCoordinatorField) {
                                    loadCoordinators(selectId, input.id, searchTerm).then(function() {
                                        filterOptions(input, select, dropdown);
                                    });
                                } else if (isChurchElderField) {
                                    loadChurchElders(selectId, input.id, searchTerm).then(function() {
                                        filterOptions(input, select, dropdown);
                                    });
                                }
                            }, 300);
                        } else {
                            // For other fields or when search term is too short, use local filtering
                            filterOptions(input, select, dropdown);
                        }
                        
                        // Add class to trigger label animation when typing
                        if (input.value.length > 0) {
                            input.classList.add('has-value');
                        } else {
                            input.classList.remove('has-value');
                            // Clear the hidden select when input is cleared
                            select.value = '';
                            // Reload full list if coordinator or church elder
                            if (isCoordinatorField && searchTerm.length === 0) {
                                loadCoordinators(selectId, input.id, '');
                            } else if (isChurchElderField && searchTerm.length === 0) {
                                loadChurchElders(selectId);
                            }
                        }
                    });
                    
                    // Handle selection
                    dropdown.addEventListener('click', function(e) {
                        if (e.target && e.target.classList && e.target.classList.contains('searchable-dropdown-item')) {
                            const option = e.target;
                            const value = (option && option.dataset) ? option.dataset.value : '';
                            const text = (option && option.textContent) ? option.textContent : '';
                            
                            // For preacher fields, use just the name (value), not the full text with member ID
                            const isPreacherField = input.id.includes('preacher');
                            
                            // Handle "other" or custom option for preachers
                            if (isPreacherField && (value === '__other__' || value === '__custom__')) {
                                // Clear select to allow free text
                                select.value = '';
                                // Keep the current input value (user's custom text)
                                input.classList.add('has-value');
                                dropdown.style.display = 'none';
                                return;
                            }
                            
                            // Update hidden select
                            select.value = value;
                            
                            // Update input display - for preacher, use just the name; for others, use full text
                            input.value = isPreacherField ? value : text;
                            
                            // Add class to trigger label animation
                            input.classList.add('has-value');
                            
                            // Hide dropdown
                            dropdown.style.display = 'none';
                            
                            // Trigger change event
                            select.dispatchEvent(new Event('change'));
                        }
                    });
                });
            }
            
            function showDropdown(input, select, dropdown) {
                if (!select || !dropdown) {
                    console.error('Select or dropdown element is null');
                    return;
                }
                
                const options = Array.from(select.options).slice(1); // Skip empty option
                dropdown.innerHTML = '';
                
                if (options.length === 0) {
                    const noOptions = document.createElement('div');
                    if (noOptions && dropdown) {
                        noOptions.className = 'searchable-dropdown-item';
                        noOptions.textContent = 'No options available. Type to search...';
                        noOptions.style.color = '#6c757d';
                        noOptions.style.fontStyle = 'italic';
                        dropdown.appendChild(noOptions);
                    }
                } else {
                    options.forEach(function(option) {
                        if (!option) return;
                        
                        // Skip disabled options (like "no results" messages)
                        if (option.disabled) {
                            const item = document.createElement('div');
                            if (item && dropdown) {
                                item.className = 'searchable-dropdown-item';
                                item.textContent = (option && option.textContent) ? option.textContent : '';
                                item.style.color = '#6c757d';
                                item.style.fontStyle = 'italic';
                                item.style.pointerEvents = 'none';
                                dropdown.appendChild(item);
                            }
                        } else {
                            const item = document.createElement('div');
                            if (item && dropdown) {
                                item.className = 'searchable-dropdown-item';
                                item.dataset.value = (option && option.value) ? option.value : '';
                                item.textContent = (option && option.textContent) ? option.textContent : '';
                                dropdown.appendChild(item);
                            }
                        }
                    });
                }
                
                dropdown.style.display = 'block';
            }
            
            function filterOptions(input, select, dropdown) {
                if (!input || !select || !dropdown) {
                    console.error('filterOptions: Missing required parameters');
                    return;
                }
                
                const searchTerm = (input.value || '').toLowerCase();
                const options = Array.from(select.options || []).slice(1); // Skip empty option
                const filteredOptions = options.filter(function(option) {
                    return option && option.textContent && option.textContent.toLowerCase().includes(searchTerm);
                });
                
                dropdown.innerHTML = '';
                
                const isPreacherField = input.id.includes('preacher');
                
                if (filteredOptions.length === 0) {
                    const noResults = document.createElement('div');
                    if (noResults && dropdown) {
                        noResults.className = 'searchable-dropdown-item';
                        if (isPreacherField && searchTerm.length > 0) {
                            // For preacher field, allow custom input
                            noResults.textContent = 'No pastor found. You can type a custom name.';
                            noResults.style.color = '#6c757d';
                            noResults.style.fontStyle = 'italic';
                            // Add option to use custom name
                            const customOption = document.createElement('div');
                            if (customOption) {
                                customOption.className = 'searchable-dropdown-item';
                                customOption.dataset.value = '__custom__';
                                customOption.textContent = 'Use custom name: "' + (input ? input.value : '') + '"';
                                customOption.style.color = '#007bff';
                                customOption.style.fontWeight = '500';
                                customOption.addEventListener('click', function() {
                                    // Clear select value to indicate custom input
                                    if (select) select.value = '';
                                    if (input) input.classList.add('has-value');
                                    if (dropdown) dropdown.style.display = 'none';
                                });
                                dropdown.appendChild(customOption);
                            }
                        } else {
                            noResults.textContent = 'No members found';
                            noResults.style.color = '#6c757d';
                            noResults.style.fontStyle = 'italic';
                        }
                        dropdown.appendChild(noResults);
                    }
                } else {
                    filteredOptions.forEach(function(option) {
                        if (option && option.textContent && dropdown) {
                            const item = document.createElement('div');
                            if (item) {
                                item.className = 'searchable-dropdown-item';
                                item.dataset.value = (option.value !== undefined) ? option.value : '';
                                item.textContent = option.textContent;
                                dropdown.appendChild(item);
                            }
                        }
                    });
                    
                    // For preacher field, if user is typing and no exact match, show option to use custom text
                    if (isPreacherField && searchTerm.length > 0) {
                        const exactMatch = filteredOptions.some(opt => 
                            opt.value.toLowerCase() === searchTerm || 
                            opt.textContent.toLowerCase() === searchTerm
                        );
                        if (!exactMatch) {
                            const customOption = document.createElement('div');
                            if (customOption && dropdown && input) {
                                customOption.className = 'searchable-dropdown-item';
                                customOption.dataset.value = '__custom__';
                                customOption.textContent = 'Use custom name: "' + input.value + '"';
                                customOption.style.color = '#007bff';
                                customOption.style.fontWeight = '500';
                                customOption.style.borderTop = '1px solid #dee2e6';
                                customOption.style.marginTop = '5px';
                                customOption.style.paddingTop = '5px';
                                customOption.addEventListener('click', function() {
                                    // Clear select value to indicate custom input
                                    if (select) select.value = '';
                                    if (input) input.classList.add('has-value');
                                    if (dropdown) dropdown.style.display = 'none';
                                });
                                dropdown.appendChild(customOption);
                            }
                        }
                    }
                }
                
                dropdown.style.display = 'block';
            }
            
            // Function to load coordinators into dropdown
            function loadCoordinators(selectId) {
                const url = '{{ route("services.sunday.coordinators") }}?t=' + Date.now();
                const select = document.getElementById(selectId);
                
                if (!select) {
                    console.error('Select element not found:', selectId);
                    return Promise.resolve(null);
                }
                
                return fetch(url, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-cache',
                    credentials: 'same-origin'
                })
                .then(async r => {
                    console.log('Coordinators API response status:', r.status);
                    if (!r.ok) {
                        const errorText = await r.text();
                        console.error('Coordinators API error:', errorText);
                        throw new Error('Failed to load coordinators: ' + r.status);
                    }
                    return r.json();
                })
                .then(data => {
                    console.log('Coordinators API data:', data);
                    if (data && data.success && data.coordinators && Array.isArray(data.coordinators)) {
                        // Keep the first option (-- Select Coordinator --)
                        select.innerHTML = '<option value="">-- Select Coordinator --</option>';
                        
                        if (!select) {
                            console.error('Select element is null in coordinators callback');
                            return null;
                        }
                        
                        if (data.coordinators.length === 0) {
                            const noResultsOption = document.createElement('option');
                            if (noResultsOption) {
                                noResultsOption.value = '';
                                noResultsOption.disabled = true;
                                noResultsOption.textContent = 'No members found';
                                select.appendChild(noResultsOption);
                            }
                            console.warn('No coordinators found in response');
                        } else {
                            // Add all coordinators
                            data.coordinators.forEach(function(coordinator) {
                                if (coordinator && coordinator.id && coordinator.display_text) {
                                    const option = document.createElement('option');
                                    if (option) {
                                        option.value = coordinator.id;
                                        option.textContent = coordinator.display_text || 'Unknown';
                                        select.appendChild(option);
                                    }
                                }
                            });
                            
                            // Show message if there are more results
                            if (data.has_more && data.total) {
                                const moreOption = document.createElement('option');
                                if (moreOption) {
                                    moreOption.value = '';
                                    moreOption.disabled = true;
                                    moreOption.textContent = `... and ${data.total - data.coordinators.length} more members`;
                                    moreOption.style.fontStyle = 'italic';
                                    moreOption.style.color = '#6c757d';
                                    select.appendChild(moreOption);
                                }
                            }
                        }
                        
                        console.log('Loaded ' + data.coordinators.length + ' coordinators into dropdown');
                        return data;
                    } else {
                        console.error('Invalid coordinators data format:', data);
                        select.innerHTML = '<option value="">-- Select Coordinator --</option><option value="" disabled>Error loading members</option>';
                    }
                    return null;
                })
                .catch(err => {
                    console.error('Failed to load coordinators:', err);
                    select.innerHTML = '<option value="">-- Select Coordinator --</option><option value="" disabled>Error loading members. Please refresh.</option>';
                    return null;
                });
            }

            // Function to load preachers into dropdown
            function loadPreachers(selectId) {
                const url = '{{ route("services.sunday.preachers") }}?t=' + Date.now();
                const select = document.getElementById(selectId);
                const customInputId = selectId.replace('_id', '_custom');
                const customInput = document.getElementById(customInputId);
                
                if (!select) {
                    console.error('Select element not found:', selectId);
                    return Promise.resolve(null);
                }
                
                return fetch(url, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-cache',
                    credentials: 'same-origin'
                })
                .then(async r => {
                    console.log('Preachers API response status:', r.status);
                    if (!r.ok) {
                        const errorText = await r.text();
                        console.error('Preachers API error:', errorText);
                        throw new Error('Failed to load preachers: ' + r.status);
                    }
                    return r.json();
                })
                .then(data => {
                    console.log('Preachers data loaded:', data);
                    
                    if (!select) {
                        console.error('Select element is null in preachers callback');
                        return null;
                    }
                    
                    if (data && data.success && data.preachers && Array.isArray(data.preachers)) {
                        select.innerHTML = '<option value="">-- Select Preacher or Type Custom --</option>';
                        
                        // Add pastors
                        if (data.preachers.length > 0) {
                            data.preachers.forEach(function(preacher) {
                                if (preacher && preacher.name && preacher.display_text) {
                                    const option = document.createElement('option');
                                    if (option) {
                                        option.value = preacher.name;
                                        option.textContent = preacher.display_text || preacher.name || 'Unknown';
                                        select.appendChild(option);
                                    }
                                }
                            });
                        }
                        
                        // Add "Other" option at the end
                        const otherOption = document.createElement('option');
                        if (otherOption) {
                            otherOption.value = '__other__';
                            otherOption.textContent = 'Other (Type custom name)';
                            select.appendChild(otherOption);
                        }
                        
                        // Handle "Other" selection to show custom input
                        if (select && select.parentNode) {
                            // Remove existing listeners by cloning (avoids duplicate event listeners)
                            const existingValue = select.value;
                            const newSelect = select.cloneNode(true);
                            newSelect.value = existingValue;
                            
                            // Replace the select element
                            select.parentNode.replaceChild(newSelect, select);
                            const updatedSelect = document.getElementById(selectId);
                            
                            if (updatedSelect) {
                                updatedSelect.addEventListener('change', function() {
                                    if (this.value === '__other__' && customInput) {
                                        customInput.style.display = 'block';
                                        customInput.value = '';
                                    } else if (customInput) {
                                        customInput.style.display = 'none';
                                        customInput.value = '';
                                    }
                                });
                            }
                        } else if (select) {
                            // Fallback: just add the listener if we can't replace
                            select.addEventListener('change', function() {
                                if (this.value === '__other__' && customInput) {
                                    customInput.style.display = 'block';
                                    customInput.value = '';
                                } else if (customInput) {
                                    customInput.style.display = 'none';
                                    customInput.value = '';
                                }
                            });
                        }
                        
                        console.log('Added ' + data.preachers.length + ' preachers to dropdown');
                        return data;
                    } else {
                        console.warn('Invalid preachers data:', data);
                        if (select) {
                            select.innerHTML = '<option value="">-- Select Preacher or Type Custom --</option><option value="" disabled>Error loading preachers</option>';
                        }
                    }
                    return null;
                })
                .catch(err => {
                    console.error('Failed to load preachers:', err);
                    if (select) {
                        select.innerHTML = '<option value="">-- Select Preacher or Type Custom --</option><option value="" disabled>Error loading preachers. Please refresh.</option>';
                    }
                    return null;
                });
            }

            // Function to load church elders into dropdown
            function loadChurchElders(selectId) {
                const url = '{{ route("services.sunday.church.elders") }}?t=' + Date.now();
                const select = document.getElementById(selectId);
                
                if (!select) {
                    console.error('Select element not found:', selectId);
                    return Promise.resolve(null);
                }
                
                return fetch(url, {
                    headers: { 
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    cache: 'no-cache',
                    credentials: 'same-origin'
                })
                .then(async r => {
                    console.log('Church Elders API response status:', r.status);
                    if (!r.ok) {
                        const errorText = await r.text();
                        console.error('Church Elders API error:', errorText);
                        throw new Error('Failed to load church elders: ' + r.status);
                    }
                    return r.json();
                })
                .then(data => {
                    console.log('Church elders API data:', data);
                    
                    if (!select) {
                        console.error('Select element is null in church elders callback');
                        return null;
                    }
                    
                    if (data && data.success && data.church_elders && Array.isArray(data.church_elders)) {
                        select.innerHTML = '<option value="">-- Select Church Elder --</option>';
                        
                        if (data.church_elders.length === 0) {
                            const noResultsOption = document.createElement('option');
                            if (noResultsOption) {
                                noResultsOption.value = '';
                                noResultsOption.disabled = true;
                                noResultsOption.textContent = 'No active church elders found';
                                noResultsOption.style.fontStyle = 'italic';
                                noResultsOption.style.color = '#6c757d';
                                select.appendChild(noResultsOption);
                            }
                            console.warn('No church elders found in response');
                        } else {
                            // Add all church elders
                            data.church_elders.forEach(function(elder) {
                                if (elder && elder.id && elder.display_text) {
                                    const option = document.createElement('option');
                                    if (option) {
                                        option.value = elder.id;
                                        option.textContent = elder.display_text || 'Unknown';
                                        select.appendChild(option);
                                    }
                                }
                            });
                        }
                        
                        console.log('Loaded ' + data.church_elders.length + ' church elders into dropdown');
                        return data;
                    } else {
                        console.error('Invalid church elders data format:', data);
                        select.innerHTML = '<option value="">-- Select Church Elder --</option><option value="" disabled>Error loading church elders</option>';
                    }
                    return null;
                })
                .catch(err => {
                    console.error('Failed to load church elders:', err);
                    select.innerHTML = '<option value="">-- Select Church Elder --</option><option value="" disabled>Error loading church elders. Please refresh.</option>';
                    return null;
                });
            }

            // Load church elders when Add Service modal is shown
            const addServiceModal = document.getElementById('addServiceModal');
            if (addServiceModal) {
                // Fix aria-hidden accessibility issue
                addServiceModal.addEventListener('show.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'false');
                    console.log('Add Service modal opening, loading data...');
                    // Load all dropdowns immediately so users can see options
                    Promise.all([
                        loadPreachers('svc_preacher_id'),
                        loadCoordinators('svc_coordinator_id'),
                        loadChurchElders('svc_church_elder_id')
                    ]).then(() => {
                        console.log('All dropdowns loaded successfully');
                        // After loading, check if date is already set and auto-populate
                        const serviceDateInput = document.getElementById('svc_date');
                        if (serviceDateInput && serviceDateInput.value) {
                            setTimeout(() => {
                                checkWeeklyAssignmentForDate(serviceDateInput.value);
                            }, 300);
                        }
                    }).catch(err => {
                        console.error('Error loading dropdowns:', err);
                    });
                });
                
                // Also check when modal is fully shown
                addServiceModal.addEventListener('shown.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'false');
                    console.log('Add Service modal fully shown');
                    const serviceDateInput = document.getElementById('svc_date');
                    if (serviceDateInput && serviceDateInput.value) {
                        setTimeout(() => {
                            checkWeeklyAssignmentForDate(serviceDateInput.value);
                        }, 500);
                    }
                    // Focus on first input for accessibility
                    const firstInput = this.querySelector('input:not([type="hidden"]), textarea, select');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 100);
                    }
                });
                addServiceModal.addEventListener('hide.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'true');
                });
                addServiceModal.addEventListener('hidden.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'true');
                });
            }

            // Store the service data when opening edit modal (before modal shows)
            let currentEditServiceData = null;
            
            // Load church elders when Edit Service modal is shown
            const editServiceModal = document.getElementById('editServiceModal');
            if (editServiceModal) {
                // Fix aria-hidden accessibility issue
                editServiceModal.addEventListener('show.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'false');
                    console.log('Edit Service modal opening, loading data...');
                    // Load all dropdowns
                    Promise.all([
                        loadPreachers('edit_preacher_id'),
                        loadCoordinators('edit_coordinator_id'),
                        loadChurchElders('edit_church_elder_id')
                    ]).then(() => {
                        // If editing and values exist, restore them
                        if (currentEditServiceData) {
                            // Restore coordinator
                            if (currentEditServiceData.coordinator_id) {
                                const coordinatorSelect = document.getElementById('edit_coordinator_id');
                                if (coordinatorSelect) {
                                    coordinatorSelect.value = currentEditServiceData.coordinator_id;
                                }
                            }
                            
                            // Restore church elder
                            if (currentEditServiceData.church_elder_id) {
                                const elderSelect = document.getElementById('edit_church_elder_id');
                                if (elderSelect) {
                                    elderSelect.value = currentEditServiceData.church_elder_id;
                                }
                            }
                            
                            // Restore preacher
                            if (currentEditServiceData.preacher) {
                                const preacherSelect = document.getElementById('edit_preacher_id');
                                const preacherCustom = document.getElementById('edit_preacher_custom');
                                if (preacherSelect) {
                                    // Check if preacher exists in dropdown
                                    const preacherOption = Array.from(preacherSelect.options).find(opt => opt.value === currentEditServiceData.preacher);
                                    if (preacherOption) {
                                        preacherSelect.value = currentEditServiceData.preacher;
                                    } else if (preacherCustom) {
                                        // Use custom input
                                        preacherSelect.value = '__other__';
                                        preacherCustom.style.display = 'block';
                                        preacherCustom.value = currentEditServiceData.preacher;
                                    }
                                }
                            }
                        }
                    });
                    
                });
                editServiceModal.addEventListener('shown.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'false');
                    // Focus on first input for accessibility
                    const firstInput = this.querySelector('input:not([type="hidden"]), textarea, select');
                    if (firstInput) {
                        setTimeout(() => firstInput.focus(), 100);
                    }
                });
                editServiceModal.addEventListener('hide.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'true');
                });
                editServiceModal.addEventListener('hidden.bs.modal', function() {
                    this.setAttribute('aria-hidden', 'true');
                });
            }

            // Function to check weekly assignment and auto-populate church elder
            function checkWeeklyAssignmentForDate(serviceDate) {
                if (!serviceDate) {
                    console.log('No service date provided for weekly assignment check');
                    return;
                }

                const select = document.getElementById('svc_church_elder_id');
                const searchInput = document.getElementById('svc_church_elder_search');
                
                if (!select || !searchInput) {
                    console.warn('Church elder select or search input not found');
                    return;
                }

                // Check if we're in the add service modal (but don't block if modal is opening)
                const addModal = document.getElementById('addServiceModal');
                if (!addModal) {
                    console.warn('Add service modal not found');
                    return;
                }
                
                // Always proceed - don't block based on visibility since modal might be opening
                console.log('Checking weekly assignment - modal state:', {
                    hasShow: addModal.classList.contains('show'),
                    hasFade: addModal.classList.contains('fade'),
                    display: window.getComputedStyle(addModal).display
                });

                console.log('Checking weekly assignment for date:', serviceDate);
                
                // Ensure date is in YYYY-MM-DD format
                let formattedDate = serviceDate;
                if (serviceDate.includes('T')) {
                    formattedDate = serviceDate.split('T')[0];
                }

                fetch(`{{ url('/services/sunday/weekly-assignment') }}?date=${formattedDate}`, {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    },
                    cache: 'no-cache',
                    credentials: 'same-origin'
                })
                .then(r => {
                    if (!r.ok) {
                        console.error('Error checking weekly assignment:', r.status);
                        return r.text().then(text => {
                            console.error('Error response:', text);
                            return null;
                        });
                    }
                    return r.json();
                })
                .then(data => {
                    console.log('Weekly assignment API response:', data);
                    
                    if (data && data.success && data.has_assignment && data.assignment) {
                        const assignment = data.assignment;
                        console.log('Found weekly assignment:', assignment);

                        // First, ensure church elders are loaded
                        console.log('Loading church elders before auto-populating...');
                        loadChurchElders('svc_church_elder_id', 'svc_church_elder_search').then(() => {
                            // Wait a bit more to ensure DOM is updated
                            setTimeout(() => {
                                console.log('Church elders loaded, checking for member ID:', assignment.member_id);
                                console.log('Assignment data:', assignment);
                                console.log('Available options in dropdown:', Array.from(select.options).map(opt => ({
                                    value: opt.value, 
                                    text: opt.textContent,
                                    dataText: opt.getAttribute('data-text')
                                })));
                                
                                // Check if the assigned member exists in the dropdown
                                // The member_id from assignment should match the option value (which is the member's id)
                                let option = select.querySelector(`option[value="${assignment.member_id}"]`);
                                
                                // If not found by exact value, try to find by data attribute or text
                                if (!option) {
                                    console.log('Option not found by exact value, trying alternative methods...');
                                    // Try finding by data-text attribute
                                    option = Array.from(select.options).find(opt => {
                                        const dataText = opt.getAttribute('data-text');
                                        const textContent = opt.textContent.trim();
                                        const valueMatch = opt.value == assignment.member_id;
                                        const textMatch = dataText === assignment.display_text || textContent === assignment.display_text;
                                        
                                        console.log('Checking option:', {
                                            value: opt.value,
                                            dataText: dataText,
                                            textContent: textContent,
                                            valueMatch: valueMatch,
                                            textMatch: textMatch
                                        });
                                        
                                        return valueMatch || textMatch;
                                    });
                                }
                                
                                if (option) {
                                    console.log('Found matching option:', {
                                        value: option.value,
                                        text: option.textContent,
                                        dataText: option.getAttribute('data-text')
                                    });
                                }
                                
                                if (option) {
                                    // Member exists in dropdown, select it
                                    select.value = option.value;
                                    searchInput.value = assignment.display_text;
                                    searchInput.classList.add('has-value');
                                    
                                    // Trigger input event to ensure the searchable dropdown updates
                                    const inputEvent = new Event('input', { bubbles: true });
                                    searchInput.dispatchEvent(inputEvent);
                                    
                                    // Also trigger change event
                                    const changeEvent = new Event('change', { bubbles: true });
                                    select.dispatchEvent(changeEvent);
                                    
                                    console.log('✓ Auto-populated church elder:', assignment.display_text, 'with option value:', option.value);
                                } else {
                                    // Member not in dropdown - log for debugging
                                    console.warn('⚠ Assigned member not found in church elders dropdown:', {
                                        member_id: assignment.member_id,
                                        member_name: assignment.member_name,
                                        display_text: assignment.display_text,
                                        is_active_elder: data.is_active_elder,
                                        available_options: Array.from(select.options).map(opt => ({value: opt.value, text: opt.textContent}))
                                    });
                                    
                                    // Still try to set the value in the search input (user can see it even if not in dropdown)
                                    if (data.is_active_elder !== false) {
                                        searchInput.value = assignment.display_text;
                                        searchInput.classList.add('has-value');
                                        
                                        // Try to create a temporary option if possible
                                        const tempOption = document.createElement('option');
                                        if (tempOption && select && assignment) {
                                            tempOption.value = assignment.member_id || '';
                                            tempOption.textContent = assignment.display_text || 'Unknown';
                                            tempOption.setAttribute('data-text', assignment.display_text || '');
                                            select.appendChild(tempOption);
                                            select.value = assignment.member_id || '';
                                        }
                                        
                                        console.log('Created temporary option and set value:', assignment.display_text);
                                    }
                                }
                            }, 200); // Small delay to ensure DOM is updated
                        }).catch(err => {
                            console.error('Error loading church elders:', err);
                            // Even if loading fails, try to set the value
                            if (assignment && assignment.display_text) {
                                searchInput.value = assignment.display_text;
                                searchInput.classList.add('has-value');
                                console.log('Set value despite loading error');
                            }
                        });
                    } else {
                        console.log('No weekly assignment found for date:', serviceDate);
                    }
                })
                .catch(err => {
                    console.error('Failed to check weekly assignment:', err);
                });
            }

            // Initialize when page loads
            document.addEventListener('DOMContentLoaded', function() {
                initializeSearchableDropdowns();
                // Pre-load church elders for add modal on page load
                console.log('Page loaded, pre-loading church elders for add modal...');
                loadChurchElders('svc_church_elder_id', 'svc_church_elder_search');

                // Listen for date changes in the add service form
                const serviceDateInput = document.getElementById('svc_date');
                if (serviceDateInput) {
                    // Listen to both change and input events for better responsiveness
                    serviceDateInput.addEventListener('change', function() {
                        const selectedDate = this.value;
                        console.log('Date changed to:', selectedDate);
                        if (selectedDate) {
                            // Ensure church elders are loaded first, then check assignment
                            loadChurchElders('svc_church_elder_id', 'svc_church_elder_search').then(() => {
                                setTimeout(() => {
                                    checkWeeklyAssignmentForDate(selectedDate);
                                }, 300);
                            });
                        } else {
                            // Clear church elder if date is cleared
                            const select = document.getElementById('svc_church_elder_id');
                            const searchInput = document.getElementById('svc_church_elder_search');
                            if (select && searchInput) {
                                select.value = '';
                                searchInput.value = '';
                                searchInput.classList.remove('has-value');
                            }
                        }
                    });
                    
                    // Also listen to input event for immediate feedback
                    serviceDateInput.addEventListener('input', function() {
                        const selectedDate = this.value;
                        // Only check if date looks complete (YYYY-MM-DD format, 10 chars)
                        if (selectedDate && selectedDate.length === 10) {
                            console.log('Date input detected:', selectedDate);
                            loadChurchElders('svc_church_elder_id', 'svc_church_elder_search').then(() => {
                                setTimeout(() => {
                                    checkWeeklyAssignmentForDate(selectedDate);
                                }, 500);
                            });
                        }
                    });

                }
            });
            function viewService(id){
                fetch(`{{ url('/services/sunday') }}/${id}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => { if (!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
                    .then(s => {
                        const fmtTime = (t) => {
                            if (!t) return '—';
                            try {
                                // Handle ISO or "YYYY-MM-DD HH:MM:SS"
                                if (t.includes('T') || /\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}/.test(t)) {
                                    const d = new Date(t);
                                    return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                                }
                                // Handle HH:MM(:SS)
                                if (/^\d{2}:\d{2}/.test(t)) {
                                    const [hh, mm] = t.split(':');
                                    const d = new Date();
                                    d.setHours(parseInt(hh), parseInt(mm), 0);
                                    return d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
                                }
                                return t;
                            } catch { return '—'; }
                        };
                        const fmtDate = (d) => d ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' }) : '—';
                        const fmtCurrency = (amount) => (amount || amount === 0) ? `TZS ${parseFloat(amount).toLocaleString()}` : '—';

                        const serviceTypeLabels = {
                            'sunday_service': 'Sunday Service',
                            'prayer_meeting': 'Prayer Meeting',
                            'bible_study': 'Bible Study',
                            'youth_service': 'Youth Service',
                            'children_service': 'Children Service',
                            'women_fellowship': 'Women Fellowship',
                            'men_fellowship': 'Men Fellowship',
                            'evangelism': 'Evangelism',
                            'other': 'Other'
                        };

                        const basicInfo = `
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                <div class="mb-2"><strong>Service Type:</strong> ${serviceTypeLabels[s.service_type] || s.service_type || '—'}</div>
                                <div class="mb-2"><strong>Theme:</strong> ${s.theme ?? '—'}</div>
                                <div class="mb-2"><strong>Preacher:</strong> ${s.preacher ?? '—'}</div>
                                <div class="mb-2"><strong>Coordinator:</strong> ${s.coordinator ? s.coordinator.full_name : '—'}</div>
                                <div class="mb-2"><strong>Church Elder:</strong> ${s.church_elder ? s.church_elder.full_name : '—'}</div>
                                <div class="mb-2"><strong>Venue:</strong> ${s.venue ?? '—'}</div>
                                <div class="mb-2"><strong>Choir:</strong> ${s.choir ?? '—'}</div>
                            </div>`;

                        const dateTime = `
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-clock me-2"></i>Date & Time</h6>
                                <div class="mb-2"><strong>Date:</strong> ${fmtDate(s.service_date)}</div>
                                <div class="mb-2"><strong>Time:</strong> ${fmtTime(s.start_time)} ${s.end_time ? ' - ' + fmtTime(s.end_time) : ''}</div>
                                <div class="mb-2"><strong>Status:</strong> ${s.status === 'completed' ? '<span class="badge bg-success">Completed</span>' : '<span class="badge bg-warning">Scheduled</span>'}</div>
                                <div class="mb-2"><strong>Registered Members:</strong> ${s.attendance_count ?? '—'}</div>
                                <div class="mb-2"><strong>Guests:</strong> ${s.guests_count ?? '—'}</div>
                                <div class="mb-2"><strong>Total Attendance:</strong> ${(parseInt(s.attendance_count || 0) + parseInt(s.guests_count || 0)) || '—'}</div>
                                <div class="mb-2"><strong>Offerings:</strong> ${fmtCurrency(s.offerings_amount)}</div>
                            </div>`;

                        const scripture = s.scripture_readings ? `
                            <div class="mt-4">
                                <h6 class="text-primary mb-3"><i class="fas fa-bible me-2"></i>Scripture Readings</h6>
                                <div class="p-3 bg-white border rounded">${s.scripture_readings}</div>
                            </div>` : '';

                        const notes = s.notes ? `
                            <div class="mt-4">
                                <h6 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i>Notes</h6>
                                <div class="p-3 bg-white border rounded">${s.notes}</div>
                            </div>` : '';

                        const html = `
                            <div class="container-fluid">
                                <div class="row g-3">
                                    ${basicInfo}
                                    ${dateTime}
                                </div>
                                ${scripture}
                                ${notes}
                            </div>`;

                        document.getElementById('serviceDetailsBody').innerHTML = html;
                        new bootstrap.Modal(document.getElementById('serviceDetailsModal')).show();
                    })
                    .catch(() => {
                        document.getElementById('serviceDetailsBody').innerHTML = '<div class="text-danger">Failed to load details.</div>';
                        new bootstrap.Modal(document.getElementById('serviceDetailsModal')).show();
                    });
            }
            function openEditService(id){
                fetch(`{{ url('/services/sunday') }}/${id}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(s => {
                        // Store service data for use in modal show event
                        currentEditServiceData = s;
                        document.getElementById('edit_id').value = s.id;
                        document.getElementById('edit_date').value = (s.service_date || '');
                        
                        // Handle service type - check if it's a custom "other" type
                        const serviceTypeLabels = ['sunday_service', 'prayer_meeting', 'bible_study', 'youth_service', 'children_service', 'women_fellowship', 'men_fellowship', 'evangelism'];
                        if (serviceTypeLabels.includes(s.service_type)) {
                            document.getElementById('edit_service_type').value = s.service_type || '';
                            document.getElementById('edit_other_service_wrapper').style.display = 'none';
                            document.getElementById('edit_other_service').value = '';
                        } else {
                            document.getElementById('edit_service_type').value = 'other';
                            document.getElementById('edit_other_service_wrapper').style.display = 'block';
                            document.getElementById('edit_other_service').value = s.service_type || '';
                        }
                        
                        document.getElementById('edit_start').value = (s.start_time || '');
                        document.getElementById('edit_end').value = (s.end_time || '');
                        document.getElementById('edit_theme').value = s.theme || '';
                        // Preacher will be set after dropdown loads in modal show event
                        // Values will be set after dropdowns load in modal show event
                        document.getElementById('edit_venue').value = s.venue || '';
                        document.getElementById('edit_attendance').value = s.attendance_count || '';
                        document.getElementById('edit_guests').value = s.guests_count || '';
                        document.getElementById('edit_offerings').value = s.offerings_amount || '';
                        document.getElementById('edit_readings').value = s.scripture_readings || '';
                        document.getElementById('edit_choir').value = s.choir || '';
                        document.getElementById('edit_notes').value = s.notes || '';
                        
                        // Show modal first, then initialize Select2
                        new bootstrap.Modal(document.getElementById('editServiceModal')).show();
                        
                        // Initialize Select2 after modal is shown
                        setTimeout(function() {
                            initializeSelect2();
                        }, 500);
                    });
            }
            document.getElementById('addServiceForm').addEventListener('submit', function(e){
                e.preventDefault();
                
                // Validate required fields
                const serviceDate = document.getElementById('svc_date').value;
                const serviceType = document.getElementById('svc_service_type').value;
                
                if (!serviceDate || serviceDate.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Service date is required. Please select a date.',
                        showConfirmButton: true
                    });
                    return;
                }
                
                if (!serviceType || serviceType.trim() === '') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Validation Error',
                        text: 'Service type is required. Please select a service type.',
                        showConfirmButton: true
                    });
                    return;
                }
                
                if (serviceType === 'other') {
                    const otherService = document.getElementById('svc_other_service').value;
                    if (!otherService || otherService.trim() === '') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: 'Please specify the service type when selecting "Other".',
                            showConfirmButton: true
                        });
                        return;
                    }
                }
                
                const fd = new FormData();
                console.log('Service date value:', serviceDate);
                fd.append('service_date', serviceDate);
                
                const otherService = document.getElementById('svc_other_service').value;
                const finalServiceType = serviceType === 'other' && otherService ? otherService : serviceType;
                fd.append('service_type', finalServiceType);
                console.log('Service type:', finalServiceType);
                // Handle time fields - only append if not empty
                const startTime = document.getElementById('svc_start').value;
                const endTime = document.getElementById('svc_end').value;
                if (startTime && startTime.trim() !== '') {
                    fd.append('start_time', startTime);
                }
                if (endTime && endTime.trim() !== '') {
                    fd.append('end_time', endTime);
                }
                fd.append('theme', document.getElementById('svc_theme').value);
                
                // Handle preacher - get from select or custom input
                const preacherSelect = document.getElementById('svc_preacher_id');
                const preacherCustom = document.getElementById('svc_preacher_custom');
                let preacherValue = '';
                if (preacherSelect && preacherSelect.value) {
                    if (preacherSelect.value === '__other__' && preacherCustom && preacherCustom.value) {
                        preacherValue = preacherCustom.value;
                    } else if (preacherSelect.value !== '__other__') {
                        preacherValue = preacherSelect.value;
                    }
                }
                if (preacherValue) {
                    fd.append('preacher', preacherValue);
                }
                
                // Handle coordinator_id - only append if not empty
                const coordinatorId = document.getElementById('svc_coordinator_id').value;
                if (coordinatorId && coordinatorId.trim() !== '') {
                    fd.append('coordinator_id', coordinatorId);
                }
                
                // Handle church_elder_id - only append if not empty
                const churchElderId = document.getElementById('svc_church_elder_id').value;
                if (churchElderId && churchElderId.trim() !== '') {
                    fd.append('church_elder_id', churchElderId);
                }
                
                fd.append('venue', document.getElementById('svc_venue').value);
                // Handle empty values for optional fields
                const attendanceValue = document.getElementById('svc_attendance').value;
                const guestsValue = document.getElementById('svc_guests').value;
                const offeringsValue = document.getElementById('svc_offerings').value;
                
                if (attendanceValue && attendanceValue.trim() !== '') {
                    fd.append('attendance_count', attendanceValue);
                }
                if (guestsValue && guestsValue.trim() !== '') {
                    fd.append('guests_count', guestsValue);
                }
                if (offeringsValue && offeringsValue.trim() !== '') {
                    fd.append('offerings_amount', offeringsValue);
                }
                fd.append('scripture_readings', document.getElementById('svc_readings').value);
                fd.append('choir', document.getElementById('svc_choir').value);
                fd.append('notes', document.getElementById('svc_notes').value);
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log('CSRF Token for add:', csrfToken);
                console.log('Form data being sent:', Object.fromEntries(fd));
                
                // Show loading indicator
                const submitButton = document.querySelector('#addServiceForm button[type="submit"]');
                const originalButtonText = submitButton ? submitButton.innerHTML : '';
                if (submitButton) {
                    submitButton.disabled = true;
                    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Saving...';
                }
                
                // Log all form data before sending
                console.log('=== FORM SUBMISSION ===');
                console.log('Service Date:', serviceDate);
                console.log('Service Type:', finalServiceType);
                console.log('All form data:', Object.fromEntries(fd));
                
                fetch(`{{ route('services.sunday.store') }}`, { 
                    method: 'POST', 
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }, 
                    body: fd 
                })
                    .then(async r => {
                        console.log('Response status:', r.status);
                        console.log('Response headers:', r.headers);
                        
                        const contentType = r.headers.get('content-type');
                        console.log('Content-Type:', contentType);
                        
                        if (!r.ok) {
                            // Try to parse as JSON first
                            if (contentType && contentType.includes('application/json')) {
                                const errorData = await r.json();
                                console.log('Error response (JSON):', errorData);
                                
                                // Handle validation errors
                                if (errorData.errors) {
                                    let errorMessages = [];
                                    for (const [field, messages] of Object.entries(errorData.errors)) {
                                        errorMessages.push(`${field}: ${Array.isArray(messages) ? messages.join(', ') : messages}`);
                                    }
                                    throw new Error(errorMessages.join('\n'));
                                }
                                throw new Error(errorData.message || errorData.error || `HTTP ${r.status}: ${r.statusText}`);
                            } else {
                                // Try to get text response
                                const text = await r.text();
                                console.log('Error response (text):', text);
                                throw new Error(`HTTP ${r.status}: ${r.statusText}\n${text.substring(0, 200)}`);
                            }
                        }
                        
                        // Parse JSON response
                        if (contentType && contentType.includes('application/json')) {
                            return r.json();
                        } else {
                            const text = await r.text();
                            console.warn('Non-JSON response:', text);
                            throw new Error('Server returned non-JSON response');
                        }
                    })
                    .then(res => { 
                        console.log('Response data:', res);
                        if(res && res.success){ 
                            Swal.fire({ 
                                icon:'success', 
                                title:'Service Scheduled!', 
                                text: res.message || 'Sunday service has been scheduled successfully.',
                                timer:2000, 
                                showConfirmButton:false 
                            }).then(()=>{
                                // Close modal and reload
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addServiceModal'));
                                if (modal) {
                                    modal.hide();
                                }
                                location.reload();
                            }); 
                        } else { 
                            Swal.fire({ 
                                icon:'error', 
                                title:'Failed to Schedule', 
                                text: res.message || res.error || 'Please check the form and try again.',
                                showConfirmButton: true
                            }); 
                        } 
                    })
                    .catch(error => {
                        console.error('Error details:', error);
                        console.error('Error stack:', error.stack);
                        let errorMessage = 'An error occurred. Please try again.';
                        
                        if (error.message) {
                            errorMessage = error.message;
                        } else if (typeof error === 'string') {
                            errorMessage = error;
                        }
                        
                        Swal.fire({ 
                            icon:'error', 
                            title:'Error Scheduling Service', 
                            html: '<div style="text-align: left;">' + 
                                  '<strong>Error:</strong><br>' + 
                                  errorMessage.replace(/\n/g, '<br>') +
                                  '<br><br><small>Check the browser console (F12) for more details.</small>' +
                                  '</div>',
                            showConfirmButton: true,
                            width: '500px'
                        });
                    })
                    .finally(() => {
                        // Restore button state
                        if (submitButton) {
                            submitButton.disabled = false;
                            submitButton.innerHTML = originalButtonText;
                        }
                    });
            });
            document.getElementById('editServiceForm').addEventListener('submit', function(e){
                e.preventDefault();
                const id = document.getElementById('edit_id').value;
                const fd = new FormData();
                fd.append('service_date', document.getElementById('edit_date').value);
                const editServiceType = document.getElementById('edit_service_type').value;
                const editOtherService = document.getElementById('edit_other_service').value;
                fd.append('service_type', editServiceType === 'other' && editOtherService ? editOtherService : editServiceType);
                fd.append('start_time', document.getElementById('edit_start').value);
                fd.append('end_time', document.getElementById('edit_end').value);
                fd.append('theme', document.getElementById('edit_theme').value);
                // Handle preacher - get from select or custom input
                const editPreacherSelect = document.getElementById('edit_preacher_id');
                const editPreacherCustom = document.getElementById('edit_preacher_custom');
                let editPreacherValue = '';
                if (editPreacherSelect && editPreacherSelect.value) {
                    if (editPreacherSelect.value === '__other__' && editPreacherCustom && editPreacherCustom.value) {
                        editPreacherValue = editPreacherCustom.value;
                    } else if (editPreacherSelect.value !== '__other__') {
                        editPreacherValue = editPreacherSelect.value;
                    }
                }
                if (editPreacherValue) {
                    fd.append('preacher', editPreacherValue);
                }
                
                // Handle coordinator_id - only append if not empty
                const editCoordinatorId = document.getElementById('edit_coordinator_id').value;
                if (editCoordinatorId && editCoordinatorId.trim() !== '') {
                    fd.append('coordinator_id', editCoordinatorId);
                }
                
                // Handle church_elder_id - only append if not empty
                const editChurchElderId = document.getElementById('edit_church_elder_id').value;
                if (editChurchElderId && editChurchElderId.trim() !== '') {
                    fd.append('church_elder_id', editChurchElderId);
                }
                
                fd.append('venue', document.getElementById('edit_venue').value);
                // Handle empty values for optional fields
                const editAttendanceValue = document.getElementById('edit_attendance').value;
                const editGuestsValue = document.getElementById('edit_guests').value;
                const editOfferingsValue = document.getElementById('edit_offerings').value;
                
                if (editAttendanceValue && editAttendanceValue.trim() !== '') {
                    fd.append('attendance_count', editAttendanceValue);
                }
                if (editGuestsValue && editGuestsValue.trim() !== '') {
                    fd.append('guests_count', editGuestsValue);
                }
                if (editOfferingsValue && editOfferingsValue.trim() !== '') {
                    fd.append('offerings_amount', editOfferingsValue);
                }
                fd.append('scripture_readings', document.getElementById('edit_readings').value);
                fd.append('choir', document.getElementById('edit_choir').value);
                fd.append('notes', document.getElementById('edit_notes').value);
                fd.append('_method', 'PUT');
                fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                
                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                console.log('CSRF Token for edit:', csrfToken);
                
                fetch(`{{ url('/services/sunday') }}/${id}`, { 
                    method: 'POST', 
                    headers: { 
                        'X-Requested-With': 'XMLHttpRequest'
                    }, 
                    body: fd 
                })
                    .then(r => {
                        console.log('Edit response status:', r.status);
                        if (!r.ok) {
                            return r.text().then(text => {
                                console.log('Edit error response:', text);
                                try {
                                    const errorData = JSON.parse(text);
                                    console.log('Parsed edit error data:', errorData);
                                    throw new Error(`HTTP ${r.status}: ${errorData.message || r.statusText}`);
                                } catch (e) {
                                    throw new Error(`HTTP ${r.status}: ${r.statusText} - ${text.substring(0, 200)}`);
                                }
                            });
                        }
                        return r.json();
                    })
                    .then(res => { 
                        console.log('Edit response data:', res);
                        if(res.success){ 
                            Swal.fire({ icon:'success', title:'Saved', timer:1200, showConfirmButton:false }).then(()=>location.reload()); 
                        } else { 
                            Swal.fire({ icon:'error', title:'Failed', text: res.message || 'Try again' }); 
                        } 
                    })
                    .catch(error => {
                        console.error('Edit error details:', error);
                        Swal.fire({ 
                            icon:'error', 
                            title:'Error', 
                            text: error.message || 'Network error occurred',
                            showConfirmButton: true
                        });
                    });
            });
            function confirmDeleteService(id){
                Swal.fire({ title:'Delete service?', text:'This action cannot be undone.', icon:'warning', showCancelButton:true, confirmButtonText:'Yes, delete', cancelButtonText:'Cancel', confirmButtonColor:'#dc3545' })
                .then((result)=>{ if(result.isConfirmed){ fetch(`{{ url('/services/sunday') }}/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') } })
                    .then(r => r.json())
                    .then(res => { if(res.success){ document.getElementById(`row-${id}`)?.remove(); Swal.fire({ icon:'success', title:'Deleted', timer:1200, showConfirmButton:false }); } else { Swal.fire({ icon:'error', title:'Delete failed', text: res.message || 'Try again' }); } })
                    .catch(()=> Swal.fire({ icon:'error', title:'Error', text:'Request failed.' })); } });
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
            
            // Auto-expand filters on mobile if filters are active
            document.addEventListener('DOMContentLoaded', function() {
                if (window.innerWidth <= 768) {
                    const hasActiveFilters = {{ (request('search') || request('from') || request('to')) ? 'true' : 'false' }};
                    if (hasActiveFilters) {
                        toggleFilters(); // Expand if filters are active
                    }
                }
                
                // Handle window resize
                window.addEventListener('resize', function() {
                    const filterBody = document.getElementById('filterBody');
                    const filterIcon = document.getElementById('filterToggleIcon');
                    
                    if (window.innerWidth > 768) {
                        // Always show on desktop
                        if (filterBody && filterIcon) {
                            filterBody.style.display = 'block';
                            filterIcon.style.display = 'none';
                        }
                    } else {
                        // On mobile, show chevrons
                        if (filterIcon) filterIcon.style.display = 'block';
                    }
                });
            });

        </script>
@endsection


