<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Waumini Link - Celebrations</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
    <script src="{{ asset('assets/js/fontawesome.min.js') }}" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .logo-white-section {
            background-color: white !important;
            border-radius: 8px;
            margin: 8px 0;
            padding: 8px 16px !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .logo-white-section:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
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
        
        /* Custom sidebar styling */
        .sb-sidenav {
            background-color: #17082d !important;
        }
        
        .sb-sidenav .nav-link {
            color: white !important;
            transition: all 0.3s ease;
        }
        
        .sb-sidenav .sb-sidenav-menu-heading {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        .sb-sidenav .sb-nav-link-icon {
            color: white !important;
        }
        
        .sb-sidenav .sb-sidenav-collapse-arrow {
            color: white !important;
        }
        
        .sb-sidenav .sb-sidenav-footer {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: white !important;
        }
        .table.interactive-table tbody tr:hover { background-color: #f8f9ff; }
        .table.interactive-table tbody tr td:first-child { border-left: 4px solid #5b2a86; }
        
        /* Celebration specific styles */
        .celebration-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .celebration-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
        #layoutSidenav { display: flex; }
        #layoutSidenav_nav { flex-shrink: 0; }
        #layoutSidenav_content { flex: 1; }
        .sb-nav-fixed #layoutSidenav #layoutSidenav_nav { position: fixed; top: 56px; left: 0; width: 225px; height: calc(100vh - 56px); z-index: 1039; }
        .sb-nav-fixed #layoutSidenav #layoutSidenav_content { padding-left: 225px; }
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
                <strong>Celebrations</strong>
            </div>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search" aria-describedby="basic-addon2" />
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button"><i class="fas fa-search"></i></button>
                    </div>
                </div>
            </form>
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li><a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </nav>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
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
                                <a class="nav-link" href="{{ route('special.events.index') }}"><i class="fas fa-calendar-plus me-2"></i>Special Events</a>
                                <a class="nav-link" href="{{ route('celebrations.index') }}"><i class="fas fa-birthday-cake me-2"></i>Celebrations</a>
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
                    Secretary
                </div>
            </nav>
        </div>
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3 gap-2">
                        <h2 class="mb-0">Celebrations</h2>
                        <div class="d-flex gap-2">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary view-toggle-btn active" id="listViewBtn" onclick="switchView('list')">
                                    <i class="fas fa-list me-1"></i>List View
                                </button>
                                <button class="btn btn-outline-primary view-toggle-btn" id="cardViewBtn" onclick="switchView('card')">
                                    <i class="fas fa-th-large me-1"></i>Card View
                                </button>
                            </div>
                            <a href="{{ route('celebrations.export.csv', request()->query()) }}" class="btn btn-outline-success"><i class="fas fa-file-excel me-2"></i>Export CSV</a>
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCelebrationModal" onclick="openAddCelebration()"><i class="fas fa-plus me-2"></i>Add Celebration</button>
                        </div>
                    </div>

                    <form method="GET" action="{{ route('celebrations.index') }}" class="card mb-3" id="filtersForm">
                        <div class="card-body">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label">Search</label>
                                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search title, celebrant, venue, type">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Type</label>
                                    <select name="type" class="form-select">
                                        <option value="">All Types</option>
                                        <option value="Birthday" {{ request('type') == 'Birthday' ? 'selected' : '' }}>Birthday</option>
                                        <option value="Anniversary" {{ request('type') == 'Anniversary' ? 'selected' : '' }}>Anniversary</option>
                                        <option value="Wedding" {{ request('type') == 'Wedding' ? 'selected' : '' }}>Wedding</option>
                                        <option value="Graduation" {{ request('type') == 'Graduation' ? 'selected' : '' }}>Graduation</option>
                                        <option value="Other" {{ request('type') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">From</label>
                                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">To</label>
                                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                                </div>
                                <div class="col-md-3 d-flex gap-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Apply</button>
                                    <a href="{{ route('celebrations.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                                </div>
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
                                                <th>Title</th>
                                                <th>Celebrant</th>
                                                <th>Type</th>
                                                <th>Date</th>
                                                <th>Time</th>
                                                <th>Venue</th>
                                                <th>Guests</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($celebrations as $celebration)
                                            <tr>
                                                <td>
                                                    <div class="fw-bold">{{ $celebration->title }}</div>
                                                    @if($celebration->description)
                                                        <small class="text-muted">{{ Str::limit($celebration->description, 50) }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $celebration->celebrant_name ?? '—' }}</td>
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
                                                <td>
                                                    @if($celebration->start_time && $celebration->end_time)
                                                        {{ \Carbon\Carbon::parse($celebration->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($celebration->end_time)->format('g:i A') }}
                                                    @elseif($celebration->start_time)
                                                        {{ \Carbon\Carbon::parse($celebration->start_time)->format('g:i A') }}
                                                    @else
                                                        —
                                                    @endif
                                                </td>
                                                <td>{{ $celebration->venue ?? '—' }}</td>
                                                <td>{{ $celebration->expected_guests ?? '—' }}</td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <button class="btn btn-outline-info btn-sm" onclick="viewDetails({{ $celebration->id }})" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button class="btn btn-outline-primary btn-sm" onclick="openEdit({{ $celebration->id }})" title="Edit Celebration">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $celebration->id }})" title="Delete Celebration">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="fas fa-birthday-cake fa-3x mb-3"></i>
                                                        <p>No celebrations found</p>
                                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCelebrationModal" onclick="openAddCelebration()">
                                                            <i class="fas fa-plus me-2"></i>Add First Celebration
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
                                                {{ \Carbon\Carbon::parse($celebration->start_time)->format('g:i A') }} - {{ \Carbon\Carbon::parse($celebration->end_time)->format('g:i A') }}
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
                                                {{ $celebration->expected_guests }} guests
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
                                            <button class="btn btn-outline-info btn-sm" onclick="viewDetails({{ $celebration->id }})" title="View Details">
                                                <i class="fas fa-eye me-1"></i>View
                                            </button>
                                            <button class="btn btn-outline-primary btn-sm" onclick="openEdit({{ $celebration->id }})" title="Edit Celebration">
                                                <i class="fas fa-edit me-1"></i>Edit
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="confirmDelete({{ $celebration->id }})" title="Delete Celebration">
                                                <i class="fas fa-trash me-1"></i>Delete
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
                                        <h4>No celebrations found</h4>
                                        <p>Start by adding your first celebration</p>
                                        <button class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#addCelebrationModal" onclick="openAddCelebration()">
                                            <i class="fas fa-plus me-2"></i>Add First Celebration
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
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 25px; overflow: hidden; animation: modalSlideIn 0.3s ease-out;">
                <!-- Enhanced Header with Gradient and Icons -->
                <div class="modal-header text-white position-relative" style="background: linear-gradient(135deg, #940000 0%, #667eea 50%, #764ba2 100%); border: none; padding: 2rem;">
                    <div class="d-flex align-items-center">
                        <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                            <i class="fas fa-birthday-cake fa-2x"></i>
                        </div>
                        <div>
                            <h4 class="modal-title mb-1 fw-bold">Create Celebration</h4>
                            <p class="mb-0 opacity-75">Plan and organize special celebrations and events</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <!-- Enhanced Body with Better Layout -->
                <div class="modal-body" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); padding: 2rem;">
                    <form id="addCelebrationForm">
                        <input type="hidden" id="editing_celebration_id" value="">
                        
                        <!-- Celebration Basic Information Section -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                <h6 class="mb-0 text-primary fw-bold">
                                    <i class="fas fa-info-circle me-2"></i>Celebration Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cel_title" placeholder="Celebration Title" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <label for="cel_title" class="text-muted">
                                                <i class="fas fa-star me-1"></i>Celebration Title
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cel_celebrant" placeholder="Celebrant Name" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <label for="cel_celebrant" class="text-muted">
                                                <i class="fas fa-user me-1"></i>Celebrant Name
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <select class="form-select" id="cel_type" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <option value="">Select Type</option>
                                                <option value="Birthday">Birthday</option>
                                                <option value="Anniversary">Anniversary</option>
                                                <option value="Wedding">Wedding</option>
                                                <option value="Graduation">Graduation</option>
                                                <option value="Other">Other</option>
                                            </select>
                                            <label for="cel_type" class="text-muted">
                                                <i class="fas fa-tags me-1"></i>Celebration Type
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" id="cel_venue" placeholder="Venue Location" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <label for="cel_venue" class="text-muted">
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
                                            <input type="date" class="form-control" id="cel_date" required style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <label for="cel_date" class="text-muted">
                                                <i class="fas fa-calendar-alt me-1"></i>Celebration Date
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="time" class="form-control" id="cel_start" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <label for="cel_start" class="text-muted">
                                                <i class="fas fa-play me-1"></i>Start Time
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-floating">
                                            <input type="time" class="form-control" id="cel_end" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <label for="cel_end" class="text-muted">
                                                <i class="fas fa-stop me-1"></i>End Time
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Guests & Budget Section -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                <h6 class="mb-0 text-primary fw-bold">
                                    <i class="fas fa-chart-line me-2"></i>Guests & Budget
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" min="0" class="form-control" id="cel_guests" placeholder="Expected Guests" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <label for="cel_guests" class="text-muted">
                                                <i class="fas fa-users me-1"></i>Expected Guests
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input type="number" min="0" step="0.01" class="form-control" id="cel_budget" placeholder="Budget Amount" style="border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                            <label for="cel_budget" class="text-muted">
                                                <i class="fas fa-money-bill-wave me-1"></i>Budget (TZS)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description & Details Section -->
                        <div class="card mb-4 border-0 shadow-sm">
                            <div class="card-header bg-white border-0" style="border-radius: 15px 15px 0 0;">
                                <h6 class="mb-0 text-primary fw-bold">
                                    <i class="fas fa-align-left me-2"></i>Description & Details
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="cel_description" placeholder="Celebration Description" style="height: 100px; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: none;"></textarea>
                                            <label for="cel_description" class="text-muted">
                                                <i class="fas fa-file-alt me-1"></i>Description
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="cel_requests" placeholder="Special Requests" style="height: 100px; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: none;"></textarea>
                                            <label for="cel_requests" class="text-muted">
                                                <i class="fas fa-gift me-1"></i>Special Requests
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-floating">
                                            <textarea class="form-control" id="cel_notes" placeholder="Additional Notes" style="height: 100px; border-radius: 10px; border: 2px solid #e9ecef; transition: all 0.3s ease; resize: none;"></textarea>
                                            <label for="cel_notes" class="text-muted">
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
                                <i class="fas fa-save me-2"></i>Save Celebration
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
                <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%); border: none;">
                    <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-birthday-cake"></i><span>Celebration Details</span></h5>
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body bg-light" id="celebrationDetailsBody">
                    <div class="text-center text-muted py-4">Loading...</div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <div class="small">
                        <span class="me-1">Powered by</span>
                        <a href="https://emca.tech/#" target="_blank" rel="noopener" class="emca-link fw-semibold">EmCa Technologies</a>
                    </div>
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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

        // Load saved view preference
        document.addEventListener('DOMContentLoaded', function() {
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
            document.querySelector('.modal-title span').textContent = 'Create Celebration';
            document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-2"></i>Save Celebration';
            document.getElementById('addCelebrationForm').reset();
        }

        function openEdit(id) {
            fetch(`/celebrations/${id}`)
                .then(res => res.json())
                .then(data => {
                    document.getElementById('editing_celebration_id').value = id;
                    document.querySelector('.modal-title span').textContent = 'Edit Celebration';
                    document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-2"></i>Update Celebration';
                    
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
                .catch(err => {
                    Swal.fire('Error', 'Failed to load celebration details', 'error');
                });
        }

        function viewDetails(id) {
            fetch(`/celebrations/${id}`)
                .then(res => res.json())
                .then(data => {
                    const body = document.getElementById('celebrationDetailsBody');
                    body.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-info-circle me-2"></i>Basic Information</h6>
                                <p><strong>Title:</strong> ${data.title || '—'}</p>
                                <p><strong>Celebrant:</strong> ${data.celebrant_name || '—'}</p>
                                <p><strong>Type:</strong> ${data.type ? `<span class="celebration-type-badge">${data.type}</span>` : '—'}</p>
                                <p><strong>Venue:</strong> ${data.venue || '—'}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-primary mb-3"><i class="fas fa-clock me-2"></i>Date & Time</h6>
                                <p><strong>Date:</strong> ${data.celebration_date ? new Date(data.celebration_date).toLocaleDateString() : '—'}</p>
                                <p><strong>Time:</strong> ${data.start_time && data.end_time ? `${data.start_time} - ${data.end_time}` : data.start_time || '—'}</p>
                                <p><strong>Expected Guests:</strong> ${data.expected_guests || '—'}</p>
                                <p><strong>Budget:</strong> ${data.budget ? `TZS ${parseFloat(data.budget).toLocaleString()}` : '—'}</p>
                            </div>
                        </div>
                        ${data.description ? `<div class="mt-4"><h6 class="text-primary mb-3"><i class="fas fa-file-alt me-2"></i>Description</h6><p>${data.description}</p></div>` : ''}
                        ${data.special_requests ? `<div class="mt-4"><h6 class="text-primary mb-3"><i class="fas fa-gift me-2"></i>Special Requests</h6><p>${data.special_requests}</p></div>` : ''}
                        ${data.notes ? `<div class="mt-4"><h6 class="text-primary mb-3"><i class="fas fa-sticky-note me-2"></i>Notes</h6><p>${data.notes}</p></div>` : ''}
                    `;
                    new bootstrap.Modal(document.getElementById('celebrationDetailsModal')).show();
                })
                .catch(err => {
                    Swal.fire('Error', 'Failed to load celebration details', 'error');
                });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
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
                            Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete celebration', 'error');
                        }
                    })
                    .catch(err => {
                        Swal.fire('Error', 'Failed to delete celebration', 'error');
                    });
                }
            });
        }

        // Form Submission
        document.getElementById('addCelebrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const editingId = document.getElementById('editing_celebration_id').value;
            
            formData.append('title', document.getElementById('cel_title').value);
            formData.append('celebrant_name', document.getElementById('cel_celebrant').value);
            formData.append('type', document.getElementById('cel_type').value);
            formData.append('venue', document.getElementById('cel_venue').value);
            formData.append('celebration_date', document.getElementById('cel_date').value);
            formData.append('start_time', document.getElementById('cel_start').value);
            formData.append('end_time', document.getElementById('cel_end').value);
            formData.append('expected_guests', document.getElementById('cel_guests').value);
            formData.append('budget', document.getElementById('cel_budget').value);
            formData.append('description', document.getElementById('cel_description').value);
            formData.append('special_requests', document.getElementById('cel_requests').value);
            formData.append('notes', document.getElementById('cel_notes').value);
            formData.append('is_public', '1');

            const url = editingId ? `/celebrations/${editingId}` : '/celebrations';
            const method = editingId ? 'PUT' : 'POST';
            
            if (editingId) {
                formData.append('_method', 'PUT');
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('addCelebrationForm').reset();
                    document.getElementById('editing_celebration_id').value = '';
                    document.querySelector('.modal-title span').textContent = 'Create Celebration';
                    document.getElementById('submitButton').innerHTML = '<i class="fas fa-save me-2"></i>Save Celebration';
                    
                    Swal.fire({
                        icon: 'success',
                        title: editingId ? 'Updated' : 'Saved',
                        text: data.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire('Error', data.message || 'Failed to save celebration', 'error');
                }
            })
            .catch(err => {
                Swal.fire('Error', 'Failed to save celebration', 'error');
            });
        });
    </script>

    <style>
        .celebration-card {
            transition: all 0.3s ease;
            border: none;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .celebration-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
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
    <script src="{{ asset('js/scripts.js') }}"></script>
</body>
</html>
