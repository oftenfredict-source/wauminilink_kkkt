<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Waumini Link - Special Events</title>
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="{{ asset('assets/js/fontawesome.min.js') }}" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .sb-sidenav { background-color: #17082d !important; }
            .sb-sidenav .nav-link { color: white !important; transition: all 0.3s ease; }
            .sb-sidenav .nav-link:hover { background-color: #293846 !important; color: white !important; }
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
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3 d-flex align-items-center logo-white-section" href="{{ route('dashboard.secretary') }}">
                <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo" style="height: 45px; max-width: 200px; object-fit: contain;">
            </a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Welcome Message -->
            <div class="navbar-text text-white me-auto ms-3" style="font-size: 1.1rem;">
                <strong>Welcome to Waumini Link</strong>
            </div>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Main</div>
                            <a class="nav-link" href="{{ route('dashboard.secretary') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Management</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMembers" aria-expanded="false" aria-controls="collapseMembers">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Members
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseMembers" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('members.add') }}">
                                        <i class="fas fa-user-plus me-2"></i>Add New Member
                                    </a>
                                    <a class="nav-link" href="{{ route('members.view') }}"><i class="fas fa-list me-2"></i>All Members</a>
                                </nav>
                            </div>
                            
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvents" aria-expanded="false" aria-controls="collapseEvents">
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Events & Services
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvents" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('services.sunday.index') }}"><i class="fas fa-church me-2"></i>Sunday Services</a>
                                    <a class="nav-link active" href="{{ route('special.events.index') }}"><i class="fas fa-calendar-plus me-2"></i>Special Events</a>
                                    <a class="nav-link" href="#"><i class="fas fa-birthday-cake me-2"></i>Celebrations</a>
                                </nav>
                            </div>
                            
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false" aria-controls="collapseFinance">
                                <div class="sb-nav-link-icon"><i class="fas fa-donate"></i></div>
                                Finance
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseFinance" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="#"><i class="fas fa-money-bill-wave me-2"></i>Donations</a>
                                    <a class="nav-link" href="#"><i class="fas fa-receipt me-2"></i>Expenses</a>
                                    <a class="nav-link" href="#"><i class="fas fa-chart-pie me-2"></i>Financial Reports</a>
                                </nav>
                            </div>
                            
                            <div class="sb-sidenav-menu-heading">Reports</div>
                            <a class="nav-link" href="#">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Analytics
                            </a>
                            <a class="nav-link" href="#">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                                Reports
                            </a>
                            
                            <div class="sb-sidenav-menu-heading">Settings</div>
                            <a class="nav-link" href="#">
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                System Settings
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {{ Auth::user()->name ?? 'User' }}
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3 gap-2">
                            <h2 class="mb-0">Special Events</h2>
                            <div class="d-flex gap-2">
                                <div class="btn-group" role="group" aria-label="View toggle">
                                    <button type="button" class="btn btn-outline-secondary active" id="listViewBtn" onclick="switchView('list')">
                                        <i class="fas fa-list me-1"></i>List View
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" id="cardViewBtn" onclick="switchView('card')">
                                        <i class="fas fa-th-large me-1"></i>Card View
                                    </button>
                                </div>
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEventModal" onclick="openAddEvent()"><i class="fas fa-plus me-2"></i>Add Event</button>
                            </div>
                        </div>

                        <form method="GET" action="{{ route('special.events.index') }}" class="card mb-3" id="filtersForm">
                            <div class="card-body">
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">Search</label>
                                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title, speaker, venue">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">From</label>
                                        <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">To</label>
                                        <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                                    </div>
                                    <div class="col-md-2 d-flex gap-2">
                                        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Apply</button>
                                    </div>
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
                                                <th class="text-nowrap">#</th>
                                                <th>Date</th>
                                                <th>Title</th>
                                                <th>Speaker</th>
                                                <th>Time</th>
                                                <th>Venue</th>
                                                <th class="text-end">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($events as $event)
                                                @php
                                                    $fmtTime = function($t){
                                                        if (!$t) return '--:--';
                                                        try { if (preg_match('/^\d{2}:\d{2}/',$t)) return substr($t,0,5); return \Carbon\Carbon::parse($t)->format('H:i'); } catch (\Throwable $e) { return '--:--'; }
                                                    };
                                                @endphp
                                                <tr id="row-{{ $event->id }}">
                                                    <td class="text-muted">{{ $events->firstItem() + $loop->index }}</td>
                                                    <td><span class="badge bg-secondary">{{ $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('d/m/Y') : '—' }}</span></td>
                                                    <td>{{ $event->title ?? '—' }}</td>
                                                    <td>{{ $event->speaker ?? '—' }}</td>
                                                    <td>{{ $fmtTime($event->start_time) }} - {{ $fmtTime($event->end_time) }}</td>
                                                    <td>{{ $event->venue ?? '—' }}</td>
                                                    <td class="text-end">
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-info" onclick="viewEvent({{ $event->id }})"><i class="fas fa-eye"></i></button>
                                                            <button class="btn btn-outline-primary" onclick="openEditEvent({{ $event->id }})"><i class="fas fa-edit"></i></button>
                                                            <button class="btn btn-outline-danger" onclick="confirmDeleteEvent({{ $event->id }})"><i class="fas fa-trash"></i></button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="7" class="text-center py-4">No special events found.</td></tr>
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
                </main>
            </div>
        </div>

        <!-- Add Event Modal -->
        <div class="modal fade" id="addEventModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden; animation: modalSlideIn 0.3s ease-out;">
                    <!-- Enhanced Header with Gradient and Icons -->
                    <div class="modal-header text-white position-relative" style="background: linear-gradient(135deg, #940000 0%, #667eea 50%, #764ba2 100%); border: none; padding: 2rem;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                <i class="fas fa-calendar-plus fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="modal-title mb-1 fw-bold">Create Special Event</h4>
                                <p class="mb-0 opacity-75">Plan and organize your special church events</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    
                    <!-- Enhanced Body with Better Layout -->
                    <div class="modal-body" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 2rem;">
                        <form id="addEventForm">
                            <input type="hidden" id="editing_event_id" value="">
                            
                            <!-- Event Basic Information Section -->
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                    <h6 class="mb-0 text-primary fw-bold">
                                        <i class="fas fa-info-circle me-2"></i>Event Information
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="ev_title" placeholder="Event Title" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_title" class="text-muted">
                                                    <i class="fas fa-star me-1"></i>Event Title
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="ev_speaker" placeholder="Speaker Name" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_speaker" class="text-muted">
                                                    <i class="fas fa-user-tie me-1"></i>Speaker
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="ev_category" placeholder="Event Category" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_category" class="text-muted">
                                                    <i class="fas fa-tags me-1"></i>Category
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" id="ev_venue" placeholder="Venue Location" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_venue" class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>Venue
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Date & Time Section -->
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                    <h6 class="mb-0 text-primary fw-bold">
                                        <i class="fas fa-clock me-2"></i>Date & Time
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="date" class="form-control" id="ev_date" required style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_date" class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>Event Date
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="time" class="form-control" id="ev_start" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_start" class="text-muted">
                                                    <i class="fas fa-play me-1"></i>Start Time
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="time" class="form-control" id="ev_end" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_end" class="text-muted">
                                                    <i class="fas fa-stop me-1"></i>End Time
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Budget & Attendance Section -->
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                    <h6 class="mb-0 text-primary fw-bold">
                                        <i class="fas fa-chart-line me-2"></i>Budget & Attendance
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="number" min="0" class="form-control" id="ev_attendance" placeholder="Expected Attendance" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_attendance" class="text-muted">
                                                    <i class="fas fa-users me-1"></i>Expected Attendance
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="number" min="0" step="0.01" class="form-control" id="ev_budget" placeholder="Budget Amount" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="ev_budget" class="text-muted">
                                                    <i class="fas fa-money-bill-wave me-1"></i>Budget (TZS)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description & Notes Section -->
                            <div class="card mb-4 border-0 shadow-sm">
                                <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                    <h6 class="mb-0 text-primary fw-bold">
                                        <i class="fas fa-align-left me-2"></i>Additional Details
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-4">
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" id="ev_description" placeholder="Event Description" style="height: 100px; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: none;"></textarea>
                                                <label for="ev_description" class="text-muted">
                                                    <i class="fas fa-file-alt me-1"></i>Description
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control" id="ev_notes" placeholder="Additional Notes" style="height: 100px; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: none;"></textarea>
                                                <label for="ev_notes" class="text-muted">
                                                    <i class="fas fa-sticky-note me-1"></i>Notes
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enhanced Action Buttons -->
                            <div class="d-flex justify-content-end gap-3 mt-4">
                                <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 25px; font-weight: 600; transition: all 0.3s ease;">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                                <button type="submit" class="btn btn-primary px-4 py-2" id="submitButton" style="border-radius: 25px; font-weight: 600; background: linear-gradient(135deg, #940000 0%, #667eea 100%); border: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(148, 0, 0, 0.3);">
                                    <i class="fas fa-save me-2"></i>Save Event
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <style>
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
        </style>

        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('assets/js/scripts.js') }}"></script>
        <script>
            // Auto-open add modal if coming from dashboard
            document.addEventListener('DOMContentLoaded', function() {
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.get('action') === 'add') {
                    openAddEvent();
                }
                
                // Load saved view preference
                const savedView = localStorage.getItem('specialEventsView') || 'list';
                switchView(savedView);
            });

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
                } else {
                    listView.style.display = 'none';
                    cardView.style.display = 'block';
                    listBtn.classList.remove('active');
                    cardBtn.classList.add('active');
                }
                
                // Save preference to localStorage
                localStorage.setItem('specialEventsView', view);
            }
            function viewEvent(id){
                fetch(`{{ url('/special-events') }}/${id}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => { if (!r.ok) throw new Error('HTTP '+r.status); return r.json(); })
                    .then(s => {
                        const row = (label, value) => `<tr><td class="text-muted text-nowrap">${label}</td><td class="fw-semibold">${value || '—'}</td></tr>`;
                        const fmtTime = (t) => { try { if(!t) return '--:--'; if(/^\d{2}:\d{2}/.test(t)) return t.substring(0,5); return new Date(`1970-01-01T${t}`).toISOString().substring(11,16);} catch { return '--:--'; } };
                        const html = `
                            <div class="small text-uppercase text-muted mt-2 mb-1">Overview</div>
                            <table class="table table-bordered table-striped align-middle interactive-table"><tbody>
                                ${row('Event Date', (s.event_date || '').replaceAll('-', '/'))}
                                ${row('Title', s.title)}
                                ${row('Speaker', s.speaker)}
                                ${row('Time', fmtTime(s.start_time)+' - '+fmtTime(s.end_time))}
                                ${row('Venue', s.venue)}
                                ${row('Attendance', s.attendance_count)}
                                ${row('Budget (TZS)', s.budget_amount)}
                                ${row('Category', s.category)}
                            </tbody></table>
                            <div class="small text-uppercase text-muted mt-3 mb-1">Details</div>
                            <table class="table table-bordered table-striped align-middle interactive-table"><tbody>
                                ${row('Description', s.description)}
                                ${row('Notes', s.notes)}
                            </tbody></table>
                        `;
                        Swal.fire({ title:'Event Details', html: html, width: 900, showConfirmButton: true });
                    })
                    .catch(() => Swal.fire({ icon:'error', title:'Failed to load details' }));
            }

            function openAddEvent(){
                // Reset form and set add mode
                document.getElementById('editing_event_id').value = '';
                document.querySelector('.modal-title span').textContent = 'Add Special Event';
                document.getElementById('submitButton').textContent = 'Save';
                document.getElementById('addEventForm').reset();
                new bootstrap.Modal(document.getElementById('addEventModal')).show();
            }

            function openEditEvent(id){
                fetch(`{{ url('/special-events') }}/${id}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => r.json())
                    .then(s => {
                        // Set editing mode
                        document.getElementById('editing_event_id').value = id;
                        document.querySelector('.modal-title span').textContent = 'Edit Special Event';
                        document.getElementById('submitButton').textContent = 'Update';
                        
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
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }, 
                    body: fd
                })
                    .then(r => r.json())
                    .then(res => { 
                        if(res.success){ 
                            // Reset form after successful submission
                            document.getElementById('addEventForm').reset();
                            document.getElementById('editing_event_id').value = '';
                            document.querySelector('.modal-title span').textContent = 'Add Special Event';
                            document.getElementById('submitButton').textContent = 'Save';
                            
                            Swal.fire({ icon:'success', title: editingId ? 'Updated' : 'Saved', timer:1200, showConfirmButton:false }).then(()=>location.reload()); 
                        } else { 
                            Swal.fire({ icon:'error', title:'Failed', text: res.message || 'Try again' }); 
                        } 
                    })
                    .catch(() => Swal.fire({ icon:'error', title:'Network error' }));
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
    </body>
</html>
