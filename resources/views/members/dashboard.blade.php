@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dashboard-profile-img">
                                @if($member->profile_picture)
                                    <img src="{{ asset('storage/' . $member->profile_picture) }}" alt="Profile Picture" class="rounded-circle border border-primary border-2" style="width:48px; height:48px; object-fit:cover;">
                                @else
                                    <div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2" style="width:48px; height:48px; background:rgba(0,123,255,.1);">
                                        <i class="fas fa-user text-primary"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold text-dark">Member Dashboard</h5>
                                <small class="text-muted">Welcome, {{ $memberInfo['full_name'] }}</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3 header-widgets">
                            <div class="widget d-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-primary text-white">
                                <i class="fas fa-id-card text-white"></i>
                                <span class="fw-bold text-white">{{ $memberInfo['member_id'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scrolling Announcement Text -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm payment-info-banner" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); overflow: hidden;">
                <div class="card-body py-3 px-0" style="position: relative;">
                    <div class="marquee-wrapper" style="overflow: hidden; white-space: nowrap;">
                        <div class="marquee-content" style="display: inline-block; animation: scroll-left 50s linear infinite; color: white; font-weight: 600; font-size: 1.05rem; padding: 0.75rem 0;">
                            <span class="marquee-item">
                                <i class="fas fa-church me-2"></i>
                                <strong>Karibu katika kanisa la AIC MOSHI KILIMANJARO</strong> | 
                                <i class="fas fa-mobile-alt me-1"></i> <strong>Lipa Namba Voda:</strong> <span class="highlight-number">68019088</span> - <strong>AIC Moshi</strong> | 
                                <i class="fas fa-university me-1"></i> <strong>CRDB Bank:</strong> Akaunti <span class="highlight-number">0152324275400</span> - <strong>Africa Inland Church Tanzania</strong> | 
                                <i class="fas fa-heart me-1"></i> Karibu tuabudu pamoja — <strong>Mungu akubariki!</strong>
                            </span>
                            <span class="marquee-item" style="padding-left: 80px;">
                                <i class="fas fa-church me-2"></i>
                                <strong>Karibu katika kanisa la AIC MOSHI KILIMANJARO</strong> | 
                                <i class="fas fa-mobile-alt me-1"></i> <strong>Lipa Namba Voda:</strong> <span class="highlight-number">68019088</span> - <strong>AIC Moshi</strong> | 
                                <i class="fas fa-university me-1"></i> <strong>CRDB Bank:</strong> Akaunti <span class="highlight-number">0152324275400</span> - <strong>Africa Inland Church Tanzania</strong> | 
                                <i class="fas fa-heart me-1"></i> Karibu tuabudu pamoja — <strong>Mungu akubariki!</strong>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <a href="{{ route('member.information') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-user-circle fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">My Information</h5>
                        <p class="card-text text-muted">View and manage your personal information</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('member.finance') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card">
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-wallet fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">My Finance</h5>
                        <p class="card-text text-muted">View your tithes, offerings, and donations</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-4 mb-3">
            <a href="{{ route('member.announcements') }}" class="text-decoration-none">
                <div class="card border-0 shadow-sm h-100 hover-card position-relative">
                    @if(isset($unreadCount) && $unreadCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.85rem; z-index: 10;">
                            {{ $unreadCount }}
                            <span class="visually-hidden">unread announcements</span>
                        </span>
                    @endif
                    <div class="card-body text-center">
                        <div class="mb-3">
                            <i class="fas fa-bullhorn fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title">Announcements</h5>
                        <p class="card-text text-muted">View upcoming events and celebrations</p>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <small class="text-danger fw-bold">{{ $unreadCount }} unread</small>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Financial Summary</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Tithes</h6>
                                    <h4 class="text-primary">TZS {{ number_format($financialSummary['total_tithes'], 2) }}</h4>
                                    <small class="text-muted">This Month: TZS {{ number_format($financialSummary['monthly_tithes'], 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Offerings</h6>
                                    <h4 class="text-success">TZS {{ number_format($financialSummary['total_offerings'], 2) }}</h4>
                                    <small class="text-muted">This Month: TZS {{ number_format($financialSummary['monthly_offerings'], 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Donations</h6>
                                    <h4 class="text-info">TZS {{ number_format($financialSummary['total_donations'], 2) }}</h4>
                                    <small class="text-muted">This Month: TZS {{ number_format($financialSummary['monthly_donations'], 2) }}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Pledges</h6>
                                    <h4 class="text-warning">TZS {{ number_format($financialSummary['total_pledges'], 2) }}</h4>
                                    <small class="text-muted">Remaining: TZS {{ number_format($financialSummary['remaining_pledges'], 2) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leadership Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users-cog me-2"></i>Church Leadership</h5>
                        @if(isset($leadershipData['has_leadership_position']) && $leadershipData['has_leadership_position'])
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-star me-1"></i>You are a Leader
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <!-- Leader Appointment Notifications -->
                    @if(isset($leadershipData['unread_notifications']) && $leadershipData['unread_notifications']->count() > 0)
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <h6 class="alert-heading"><i class="fas fa-bell me-2"></i>New Leadership Appointment!</h6>
                            @foreach($leadershipData['unread_notifications'] as $notification)
                                @php
                                    $data = $notification->data;
                                @endphp
                                <div class="mb-2">
                                    <strong>Congratulations!</strong> You have been appointed as <strong>{{ $data['position_display'] ?? 'Leader' }}</strong> 
                                    @if(isset($data['appointment_date']))
                                        on {{ \Carbon\Carbon::parse($data['appointment_date'])->format('d M Y') }}
                                    @endif
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="markNotificationAsRead('{{ $notification->id }}', event)"></button>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Current Member's Leadership Positions -->
                    @if(isset($leadershipData['member_positions']) && $leadershipData['member_positions']->count() > 0)
                        <div class="mb-4">
                            <h6 class="text-primary mb-3"><i class="fas fa-user-tie me-2"></i>Your Leadership Positions</h6>
                            <div class="row">
                                @foreach($leadershipData['member_positions'] as $position)
                                    <div class="col-md-6 mb-3">
                                        <div class="card border-primary h-100" style="border-width: 2px !important;">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                                        <i class="fas fa-star"></i>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1 text-primary">{{ $position->position_display }}</h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            Appointed: {{ $position->appointment_date->format('d M Y') }}
                                                            @if($position->end_date)
                                                                <br><i class="fas fa-calendar-times me-1"></i>
                                                                Until: {{ $position->end_date->format('d M Y') }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Church Announcements -->
    @if(isset($announcements['announcements']) && $announcements['announcements']->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Latest Announcements</h5>
                        @if(isset($unreadCount) && $unreadCount > 0)
                            <span class="badge bg-danger">{{ $unreadCount }} New</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    @foreach($announcements['announcements']->take(3) as $announcement)
                        @php
                            $isUnread = isset($announcement->is_unread) && $announcement->is_unread;
                        @endphp
                        <div class="card mb-2 border-0 shadow-sm {{ $announcement->is_pinned ? 'border-warning border-2' : '' }} {{ $isUnread ? 'border-primary border-2' : '' }}">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            @if($announcement->is_pinned)
                                                <i class="fas fa-thumbtack text-warning me-1" title="Pinned"></i>
                                            @endif
                                            @if($isUnread)
                                                <span class="badge bg-danger me-1">NEW</span>
                                            @endif
                                            {{ $announcement->title }}
                                        </h6>
                                        <p class="mb-1 text-muted small">{{ Str::limit($announcement->content, 100) }}</p>
                                        <small class="text-muted">
                                            <span class="badge bg-{{ $announcement->type === 'urgent' ? 'danger' : ($announcement->type === 'event' ? 'success' : ($announcement->type === 'reminder' ? 'warning' : 'info')) }} me-2">
                                                {{ ucfirst($announcement->type) }}
                                            </span>
                                            {{ $announcement->created_at->format('M d, Y') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center mt-2">
                        <a href="{{ route('member.announcements') }}" class="btn btn-sm btn-outline-info">
                            View All Announcements <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Announcements -->
    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Events</h5>
                </div>
                <div class="card-body">
                    @if($announcements['events']->count() > 0)
                        <div class="list-group">
                            @foreach($announcements['events']->take(5) as $event)
                                <div class="list-group-item border-0 mb-2 shadow-sm">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $event->event_name }}</h6>
                                            <p class="mb-1 text-muted">{{ $event->description }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>{{ $event->event_date->format('M d, Y') }}
                                                @if($event->start_time)
                                                    <i class="fas fa-clock ms-2 me-1"></i>{{ $event->start_time }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No upcoming events</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-birthday-cake me-2"></i>Upcoming Celebrations</h5>
                </div>
                <div class="card-body">
                    @if($announcements['celebrations']->count() > 0)
                        <div class="list-group">
                            @foreach($announcements['celebrations']->take(5) as $celebration)
                                <div class="list-group-item border-0 mb-2 shadow-sm">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $celebration->celebration_type }}</h6>
                                            <p class="mb-1 text-muted">{{ $celebration->celebrant_name }}</p>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>{{ $celebration->celebration_date->format('M d, Y') }}
                                                @if($celebration->start_time)
                                                    <i class="fas fa-clock ms-2 me-1"></i>{{ $celebration->start_time }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No upcoming celebrations</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    .dashboard-header .widget{
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        transition: transform .2s ease, background .2s ease;
    }
    .dashboard-header .widget:hover{
        transform: translateY(-2px);
        background: rgba(255,255,255,.14);
    }
    @keyframes scroll-left {
        0% {
            transform: translateX(0);
        }
        100% {
            transform: translateX(-50%);
        }
    }
    .marquee-wrapper {
        position: relative;
    }
    .marquee-content:hover {
        animation-play-state: paused;
    }
    .marquee-item {
        display: inline-block;
    }
    
    /* Leadership section styling */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    /* Payment info banner styling */
    .payment-info-banner {
        border-radius: 12px !important;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3) !important;
    }
    
    .payment-info-banner .highlight-number {
        background: rgba(255, 255, 255, 0.2);
        padding: 0.2rem 0.5rem;
        border-radius: 6px;
        font-weight: 700;
        letter-spacing: 0.5px;
        font-family: 'Courier New', monospace;
    }
    
    .payment-info-banner .marquee-content i {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.1rem;
    }
    
    .payment-info-banner .marquee-content strong {
        color: #fff;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
    }
    
    @media (max-width: 768px) {
        .payment-info-banner .marquee-content {
            font-size: 0.9rem !important;
        }
        
        .payment-info-banner .highlight-number {
            padding: 0.15rem 0.4rem;
            font-size: 0.95rem;
        }
    }
</style>

<script>
    function markNotificationAsRead(notificationId, event) {
        if (event) {
            event.preventDefault();
        }
        
        fetch(`/member/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // The Bootstrap alert will handle the dismissal
                console.log('Notification marked as read');
            }
        })
        .catch(error => {
            console.error('Error marking notification as read:', error);
        });
    }
</script>
@endsection

