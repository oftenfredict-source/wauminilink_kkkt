@extends('layouts.index')

@section('content')
<div class="container-fluid px-4 pt-0">
    <!-- Enhanced Dashboard Header -->
    <div class="row mb-0 mt-0">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background: white; border-radius: 10px; overflow: hidden;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <!-- Left Section: User Info -->
                        <div class="d-flex align-items-center gap-2 flex-grow-1">
                            <div class="dashboard-profile-img position-relative">
                                @if(isset($secretary) && $secretary->member && $secretary->member->profile_picture)
                                    <img src="{{ asset('storage/' . $secretary->member->profile_picture) }}" 
                                         alt="Secretary Profile" 
                                         class="rounded-circle border border-primary border-2 shadow-sm" 
                                         style="width:50px; height:50px; object-fit:cover; background:white;">
                                @elseif(isset($user) && $user->profile_picture)
                                    <img src="{{ asset('storage/' . $user->profile_picture) }}" 
                                         alt="User Profile" 
                                         class="rounded-circle border border-primary border-2 shadow-sm" 
                                         style="width:50px; height:50px; object-fit:cover; background:white;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2 shadow-sm" 
                                         style="width:50px; height:50px; background:rgba(0,123,255,0.1);">
                                        <i class="fas fa-user-tie text-primary" style="font-size: 1.3rem;"></i>
                                    </div>
                                @endif
                                <span class="position-absolute bottom-0 end-0 bg-success border border-white border-2 rounded-circle" 
                                      style="width: 14px; height: 14px;"></span>
                            </div>
                            <div class="lh-sm">
                                @php
                                    $hour = (int)date('H');
                                    $greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
                                    $userName = 'Secretary';
                                    if(isset($secretary) && $secretary->member) {
                                        $userName = $secretary->member->full_name;
                                    } elseif(isset($user)) {
                                        $userName = $user->name;
                                    }
                                @endphp
                                <h5 class="mb-0 fw-semibold text-dark">
                                    {{ $greeting }}, {{ $userName }}
                                </h5>
                                <small class="text-muted">
                                    <i class="fas fa-briefcase me-1 text-primary"></i>
                                    @if(isset($secretary) && $secretary->position_display)
                                        {{ $secretary->position_display }}
                                    @else
                                        Secretary Dashboard
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Reduce gap between topbar and content */
        #layoutSidenav_content main {
            padding-top: 0 !important;
        }
        .container-fluid {
            padding-top: 0 !important;
        }
        
        /* Enhanced Header Styles */
        .dashboard-header {
            position: relative;
            overflow: hidden;
        }
        
        
        .dashboard-profile-img {
            position: relative;
        }
        
        .dashboard-profile-img img,
        .dashboard-profile-img div {
            transition: transform 0.3s ease;
        }
        
        .dashboard-profile-img:hover img,
        .dashboard-profile-img:hover div {
            transform: scale(1.05);
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .dashboard-profile-img .bg-success {
            animation: pulse 2s infinite;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 768px) {
            /* Reduce gap between topbar and content on mobile */
            #layoutSidenav_content main {
                padding-top: 0 !important;
                margin-top: -0.25rem !important;
            }
            /* Container padding adjustment */
            .container-fluid {
                padding-left: 10px !important;
                padding-right: 10px !important;
                padding-top: 0 !important;
                margin-top: -0.25rem !important;
            }
            .row.mb-0.mt-0 {
                margin-top: -0.15rem !important;
                margin-bottom: 0.1rem !important;
            }
            
            /* Header adjustments */
            .dashboard-header .card-body {
                padding: 12px 10px !important;
            }
            
            .dashboard-header > .card-body > .d-flex {
                flex-direction: row !important;
                align-items: center !important;
                gap: 12px !important;
            }
            
            /* Keep profile and text on same line */
            .dashboard-header .d-flex.align-items-center.gap-2 {
                flex-direction: row !important;
                flex-wrap: nowrap !important;
            }
            
            
            .dashboard-header h5 {
                font-size: 1.1rem !important;
            }
            
            .dashboard-header small {
                font-size: 0.8rem !important;
            }
            
            .dashboard-profile-img img,
            .dashboard-profile-img div {
                width: 45px !important;
                height: 45px !important;
            }
            
            .dashboard-profile-img i {
                font-size: 1.2rem !important;
            }
            
            /* Statistics cards - stack on mobile */
            .row > [class*="col-"] {
                padding-left: 8px !important;
                padding-right: 8px !important;
                margin-bottom: 10px !important;
            }
            
            .card-body {
                padding: 15px 12px !important;
            }
            
            .card-body .h4 {
                font-size: 1.5rem !important;
            }
            
            .card-body .small {
                font-size: 0.8rem !important;
            }
            
            .card-footer {
                padding: 10px 12px !important;
                font-size: 0.85rem !important;
            }
            
            /* Quick Actions - stack buttons */
            .card-body .row > [class*="col-"] {
                margin-bottom: 10px !important;
            }
            
            .btn {
                padding: 10px 15px !important;
                font-size: 0.9rem !important;
            }
            
            /* Demographics cards */
            .card-body .row > [class*="col-lg-3"] {
                margin-bottom: 15px !important;
            }
            
            .card-body .rounded-circle {
                width: 60px !important;
                height: 60px !important;
            }
            
            .card-body .rounded-circle i {
                font-size: 1.5rem !important;
            }
            
            .card-body h4 {
                font-size: 1.3rem !important;
            }
            
            /* Family breakdown */
            .card-body .row > [class*="col-md-4"] {
                margin-bottom: 15px !important;
            }
            
            /* Chart section */
            .card-body .row > [class*="col-md-6"] {
                margin-bottom: 15px !important;
            }
            
            /* Announcements and Events - stack on mobile */
            .row > [class*="col-lg-6"] {
                margin-bottom: 20px !important;
            }
            
            .list-group-item {
                padding: 12px 10px !important;
            }
            
            .list-group-item h6 {
                font-size: 0.95rem !important;
            }
            
            .list-group-item p {
                font-size: 0.85rem !important;
            }
            
            .list-group-item small {
                font-size: 0.75rem !important;
            }
            
            /* Card headers */
            .card-header {
                padding: 12px 15px !important;
                font-size: 0.95rem !important;
            }
            
            .card-header i {
                font-size: 0.9rem !important;
            }
        }
        
        @media (max-width: 576px) {
            /* Further reduce gap on extra small mobile */
            #layoutSidenav_content main {
                padding-top: 0 !important;
                margin-top: -0.35rem !important;
            }
            .container-fluid {
                padding-top: 0 !important;
                margin-top: -0.35rem !important;
            }
            .row.mb-0.mt-0 {
                margin-top: -0.25rem !important;
                margin-bottom: 0.05rem !important;
            }
            
            .dashboard-header .card-body {
                padding: 10px 8px !important;
            }
            
            .dashboard-header h5 {
                font-size: 1rem !important;
            }
            
            .dashboard-profile-img img,
            .dashboard-profile-img div {
                width: 40px !important;
                height: 40px !important;
            }
            
            .dashboard-profile-img i {
                font-size: 1rem !important;
            }
            
            /* Ensure profile and text stay on same line on mobile */
            .dashboard-header .d-flex.align-items-center.gap-2 {
                flex-direction: row !important;
                flex-wrap: nowrap !important;
            }
            
            .dashboard-header .lh-sm {
                min-width: 0;
                flex: 1;
            }
            
            .dashboard-header h5 {
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }
            
            .container-fluid {
                padding-left: 8px !important;
                padding-right: 8px !important;
            }
            
            .card-body {
                padding: 12px 10px !important;
            }
            
            .card-body .h4 {
                font-size: 1.3rem !important;
            }
            
            .btn {
                font-size: 0.85rem !important;
                padding: 8px 12px !important;
            }
            
            .card-body .rounded-circle {
                width: 50px !important;
                height: 50px !important;
            }
            
            .card-body .rounded-circle i {
                font-size: 1.2rem !important;
            }
        }
        /* Fix dashboard card header visibility */
        .card-header {
            background-color: #f8f9fa !important;
            color: #495057 !important;
            font-weight: 600 !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
        
        .card-header i {
            color: #007bff !important;
        }
        
        /* Ensure all text in cards is visible */
        .card-body {
            color: #212529 !important;
        }
        
        .card-body h5 {
            color: #495057 !important;
            font-weight: 600 !important;
        }
        
        .card-body p {
            color: #6c757d !important;
        }
        
        .card-body ul li {
            color: #495057 !important;
        }
        
        /* Ensure welcome section text is white */
        .card.bg-primary .card-body {
            color: white !important;
        }
        
        .card.bg-primary .card-title {
            color: white !important;
        }
        
        .card.bg-primary .card-text {
            color: white !important;
        }
        
        /* Ensure statistics cards text is white */
        .card.bg-primary .card-body,
        .card.bg-success .card-body,
        .card.bg-warning .card-body,
        .card.bg-info .card-body {
            color: white !important;
        }
        
        .card.bg-primary .h4,
        .card.bg-success .h4,
        .card.bg-warning .h4,
        .card.bg-info .h4 {
            color: white !important;
        }
        
        .card.bg-primary .small,
        .card.bg-success .small,
        .card.bg-warning .small,
        .card.bg-info .small {
            color: rgba(255, 255, 255, 0.8) !important;
        }
        
        .card.bg-primary .text-white-50,
        .card.bg-success .text-white-50,
        .card.bg-warning .text-white-50,
        .card.bg-info .text-white-50 {
            color: rgba(255, 255, 255, 0.8) !important;
        }
    </style>
    

    <!-- Dashboard Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-eye me-1"></i>
                    Overview
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small text-white-50">Total Family Members</div>
                            <div class="h4 mb-0">{{ number_format($totalMembers) }}</div>
                            <div class="small text-white-50 mt-1">
                                <i class="fas fa-users me-1"></i>{{ number_format($registeredMembers) }} registered
                            </div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-users fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('members.view') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small text-white-50">Active Events</div>
                            <div class="h4 mb-0">{{ number_format($activeEvents) }}</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-calendar-alt fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('special.events.index') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small text-white-50">Upcoming Celebrations</div>
                            <div class="h4 mb-0">{{ number_format($upcomingCelebrations) }}</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-birthday-cake fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('celebrations.index') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="small text-white-50">Sunday Services</div>
                            <div class="h4 mb-0">Active</div>
                        </div>
                        <div class="ms-3">
                            <i class="fas fa-church fa-2x text-white-50"></i>
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="{{ route('services.sunday.index') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt me-1"></i>
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 col-12 mb-3">
                            <a href="{{ route('members.add') }}" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Add New Member
                            </a>
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <a href="{{ route('special.events.index') }}?action=add" class="btn btn-success w-100">
                                <i class="fas fa-calendar-plus me-2"></i>Add Special Event
                            </a>
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <a href="{{ route('celebrations.index') }}?action=add" class="btn btn-warning w-100">
                                <i class="fas fa-gift me-2"></i>Add Celebration
                            </a>
                        </div>
                        <div class="col-md-3 col-12 mb-3">
                            <a href="{{ route('services.sunday.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-church me-2"></i>Sunday Services
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Member Demographics Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Member Demographics
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Male Members -->
                        <div class="col-lg-3 col-md-6 col-12 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-male fa-2x"></i>
                                    </div>
                                    <h4 class="text-primary mb-1">{{ number_format($maleMembers) }}</h4>
                                    <p class="text-muted mb-0">Male Members</p>
                                    <small class="text-muted">{{ $totalMembers > 0 ? round(($maleMembers / $totalMembers) * 100, 1) : 0 }}% of total</small>
                                </div>
                            </div>
                        </div>

                        <!-- Female Members -->
                        <div class="col-lg-3 col-md-6 col-12 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-pink text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; background-color: #e91e63 !important;">
                                        <i class="fas fa-female fa-2x"></i>
                                    </div>
                                    <h4 class="mb-1" style="color: #e91e63;">{{ number_format($femaleMembers) }}</h4>
                                    <p class="text-muted mb-0">Female Members</p>
                                    <small class="text-muted">{{ $totalMembers > 0 ? round(($femaleMembers / $totalMembers) * 100, 1) : 0 }}% of total</small>
                                </div>
                            </div>
                        </div>

                        <!-- Children -->
                        <div class="col-lg-3 col-md-6 col-12 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-child fa-2x"></i>
                                    </div>
                                    <h4 class="text-warning mb-1">{{ number_format($totalChildren) }}</h4>
                                    <p class="text-muted mb-0">Children</p>
                                    <small class="text-muted">Under 18 years</small>
                                </div>
                            </div>
                        </div>

                        <!-- Adults -->
                        <div class="col-lg-3 col-md-6 col-12 mb-4">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                        <i class="fas fa-user-tie fa-2x"></i>
                                    </div>
                                    <h4 class="text-success mb-1">{{ number_format($adultMembers) }}</h4>
                                    <p class="text-muted mb-0">Adult Members</p>
                                    <small class="text-muted">18+ years old</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Family Breakdown -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-family me-2"></i>Family Member Breakdown
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-4 col-12 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-primary rounded me-2" style="width: 20px; height: 20px;"></div>
                                                <div>
                                                    <div class="small fw-bold">Registered Members</div>
                                                    <div class="small text-muted">{{ $familyBreakdown['registered_males'] }} male, {{ $familyBreakdown['registered_females'] }} female</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-success rounded me-2" style="width: 20px; height: 20px;"></div>
                                                <div>
                                                    <div class="small fw-bold">Spouses</div>
                                                    <div class="small text-muted">{{ $familyBreakdown['spouse_males'] }} male, {{ $familyBreakdown['spouse_females'] }} female</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12 mb-3">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-warning rounded me-2" style="width: 20px; height: 20px;"></div>
                                                <div>
                                                    <div class="small fw-bold">Children</div>
                                                    <div class="small text-muted">{{ $familyBreakdown['child_males'] }} male, {{ $familyBreakdown['child_females'] }} female</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Demographics Chart -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-3">
                                        <i class="fas fa-chart-bar me-2"></i>Family Member Distribution
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-primary rounded me-2" style="width: 20px; height: 20px;"></div>
                                                <span class="small">Male: {{ $maleMembers }} ({{ $totalMembers > 0 ? round(($maleMembers / $totalMembers) * 100, 1) : 0 }}%)</span>
                                            </div>
                                            <div class="progress mb-3" style="height: 8px;">
                                                <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $totalMembers > 0 ? ($maleMembers / $totalMembers) * 100 : 0 }}%"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="rounded me-2" style="width: 20px; height: 20px; background-color: #e91e63;"></div>
                                                <span class="small">Female: {{ $femaleMembers }} ({{ $totalMembers > 0 ? round(($femaleMembers / $totalMembers) * 100, 1) : 0 }}%)</span>
                                            </div>
                                            <div class="progress mb-3" style="height: 8px;">
                                                <div class="progress-bar" role="progressbar" style="width: {{ $totalMembers > 0 ? ($femaleMembers / $totalMembers) * 100 : 0 }}%; background-color: #e91e63;"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-12 mb-2">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-warning rounded me-2" style="width: 20px; height: 20px;"></div>
                                                <span class="small">Children: {{ $totalChildren }}</span>
                                            </div>
                                        </div>
                                        <div class="col-md-6 col-12 mb-2">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="bg-success rounded me-2" style="width: 20px; height: 20px;"></div>
                                                <span class="small">Adults: {{ $adultMembers }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Announcements + Upcoming Events lists -->
    <div class="row">
        <div class="col-lg-6 col-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-bullhorn me-1"></i>
                    Latest Announcements
                </div>
                <div class="card-body">
                    @if(isset($latestAnnouncements) && $latestAnnouncements->count())
                        <ul class="list-group list-group-flush">
                            @foreach($latestAnnouncements as $announcement)
                                <li class="list-group-item">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            @if($announcement->is_pinned)
                                                <span class="badge bg-warning mb-1">
                                                    <i class="fas fa-thumbtack me-1"></i>Pinned
                                                </span>
                                            @endif
                                            <h6 class="mb-1">{{ $announcement->title }}</h6>
                                            <p class="mb-1 text-muted small">
                                                {{ Str::limit($announcement->content, 100) }}
                                            </p>
                                            @if($announcement->type)
                                                <small class="badge bg-info">{{ ucfirst($announcement->type) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            @if($announcement->start_date && $announcement->end_date)
                                                {{ $announcement->start_date->format('M d') }} - {{ $announcement->end_date->format('M d, Y') }}
                                            @elseif($announcement->start_date)
                                                Starts: {{ $announcement->start_date->format('M d, Y') }}
                                            @else
                                                {{ $announcement->created_at->format('M d, Y') }}
                                            @endif
                                        </small>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0 text-muted">No announcements available.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12">
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header">
                    <i class="fas fa-calendar-alt me-1"></i>
                    Upcoming Events
                </div>
                <div class="card-body">
                    @if(isset($upcomingEvents) && $upcomingEvents->count())
                        <ul class="list-group list-group-flush">
                            @foreach($upcomingEvents as $event)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        {{ $event->title }}
                                        <small class="text-muted">— {{ $event->venue }}</small>
                                    </span>
                                    <small class="text-muted">{{ optional($event->event_date)->format('M d, Y') }}</small>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="mb-0 text-muted">No upcoming events scheduled.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


