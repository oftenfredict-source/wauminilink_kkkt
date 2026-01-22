@extends('layouts.index')

@section('content')
<style>
    /* Ensure badge text is always visible with proper colors - works with Bootstrap 4 and 5 */
    .badge.badge-danger,
    .badge[class*="badge-danger"] {
        background-color: #dc3545 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-success,
    .badge[class*="badge-success"] {
        background-color: #198754 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-info,
    .badge[class*="badge-info"] {
        background-color: #0dcaf0 !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-secondary,
    .badge[class*="badge-secondary"] {
        background-color: #6c757d !important;
        color: white !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    .badge.badge-warning,
    .badge[class*="badge-warning"] {
        background-color: #ffc107 !important;
        color: #212529 !important;
        font-weight: 600 !important;
        padding: 0.35em 0.65em !important;
        display: inline-block !important;
    }
    
    /* Fallback for any badge */
    .badge {
        display: inline-block !important;
        padding: 0.35em 0.65em !important;
        font-weight: 600 !important;
        border-radius: 0.25rem !important;
    }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .dashboard-header {
            margin-bottom: 12px !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        }

        .dashboard-header .card-body {
            padding: 12px 14px !important;
        }

        .dashboard-header .rounded-circle {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-shrink: 0 !important;
            background: rgba(255,255,255,0.2) !important;
            border: 2px solid rgba(255,255,255,0.3) !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.95rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 12px !important;
            flex: 1 !important;
            min-width: 0 !important;
        }

        .dashboard-header .lh-sm {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        .dashboard-header h5 {
            font-size: 1rem !important;
            line-height: 1.3 !important;
            margin-bottom: 2px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header small {
            font-size: 0.75rem !important;
            line-height: 1.2 !important;
            display: block !important;
            opacity: 0.9 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            transition: all 0.2s ease !important;
        }

        .dashboard-header .btn:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15) !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            align-items: center !important;
            flex-wrap: nowrap !important;
        }

        .dashboard-header .d-flex.justify-content-between > div:first-child {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        /* Statistics Cards - Stack on mobile */
        .row.mb-4 > div {
            margin-bottom: 15px !important;
        }

        /* Table responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .table {
            font-size: 0.85rem;
        }

        .table th,
        .table td {
            padding: 8px 4px !important;
            white-space: nowrap;
        }

        /* Card headers */
        .card-header {
            padding: 10px 15px !important;
        }

        .card-header h6 {
            font-size: 0.9rem !important;
        }

        /* Quick Links - Stack buttons */
        .card-body .row .col-md-3 {
            margin-bottom: 10px;
        }

        .btn-block {
            width: 100%;
        }

        /* Active Sessions - Adjust spacing */
        .card-body .mb-3 {
            margin-bottom: 15px !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }

        .dashboard-header {
            margin-bottom: 10px !important;
            border-radius: 10px !important;
        }

        .dashboard-header .card-body {
            padding: 10px 12px !important;
        }

        .dashboard-header .rounded-circle {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.9rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 10px !important;
        }

        .dashboard-header h5 {
            font-size: 0.95rem !important;
            line-height: 1.25 !important;
            margin-bottom: 1px !important;
        }

        .dashboard-header small {
            font-size: 0.72rem !important;
            line-height: 1.15 !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            width: auto !important;
            min-width: fit-content !important;
            padding: 7px 12px !important;
            font-size: 0.8rem !important;
            flex: 0 0 auto !important;
        }

        /* Stack on very small screens */
        @media (max-width: 400px) {
            .dashboard-header .d-flex.justify-content-between {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .dashboard-header .btn {
                width: 100% !important;
                margin-top: 8px !important;
            }
        }

        .table {
            font-size: 0.75rem;
        }

        .table th,
        .table td {
            padding: 6px 3px !important;
        }

        .card-body {
            padding: 10px !important;
        }
    }
</style>

<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:#17082d;">
                <div class="card-body text-white py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-2" style="width:48px; height:48px; background:rgba(255,255,255,.15);">
                                <i class="fas fa-shield-alt text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">{{ autoTranslate('Administrator Dashboard') }}</h5>
                                <small style="color: white !important;">{{ autoTranslate('System monitoring and management') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ autoTranslate('Total Users') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ autoTranslate('Active Sessions') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_sessions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ autoTranslate('Total Activities') }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_activities']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ autoTranslate("Today's Activities") }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['today_activities'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Activities -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ autoTranslate('Recent Activities') }}</h6>
                    <a href="{{ route('admin.activity-logs') }}" class="btn btn-sm btn-primary">{{ autoTranslate('View All') }}</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ autoTranslate('User') }}</th>
                                    <th>{{ autoTranslate('Action') }}</th>
                                    <th>{{ autoTranslate('Description') }}</th>
                                    <th>{{ autoTranslate('Time') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentActivities as $activity)
                                <tr>
                                    <td>
                                        @if($activity->user)
                                            <strong>{{ $activity->user->name }}</strong><br>
                                            <small class="text-muted">{{ $activity->user->email }}</small>
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $actionBadgeClass = match($activity->action) {
                                                'create' => 'badge-success',
                                                'delete' => 'badge-danger',
                                                'approve' => 'badge-warning',
                                                'update' => 'badge-info',
                                                'login' => 'badge-info',
                                                'logout' => 'badge-secondary',
                                                default => 'badge-info'
                                            };
                                        @endphp
                                        <span class="badge {{ $actionBadgeClass }}">
                                            {{ ucfirst($activity->action) }}
                                        </span>
                                    </td>
                                    <td>{{ Str::limit($activity->description, 50) }}</td>
                                    <td><small>{{ $activity->created_at->diffForHumans() }}</small></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">{{ autoTranslate('No activities found') }}</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">{{ autoTranslate('Active Sessions') }}</h6>
                    <a href="{{ route('admin.sessions') }}" class="btn btn-sm btn-primary">{{ autoTranslate('View All') }}</a>
                </div>
                <div class="card-body">
                    @forelse($activeSessions->take(5) as $session)
                    <div class="mb-3 pb-3 border-bottom">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $session->name }}</strong><br>
                                <small class="text-muted">{{ $session->email }}</small><br>
                                <span class="badge badge-secondary" style="font-size: 0.75em;">{{ ucfirst($session->role) }}</span>
                            </div>
                            @if($session->is_current)
                            <span class="badge badge-success">{{ autoTranslate('Current') }}</span>
                            @endif
                        </div>
                        <small class="text-muted">{{ $session->last_activity_formatted }}</small>
                    </div>
                    @empty
                    <p class="text-muted text-center">{{ autoTranslate('No active sessions') }}</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Activity by Action & Top Users -->
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ autoTranslate('Activity by Action (Last 7 Days)') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ autoTranslate('Action') }}</th>
                                    <th>{{ autoTranslate('Count') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activityByAction as $item)
                                <tr>
                                    <td>{{ ucfirst($item->action) }}</td>
                                    <td><strong>{{ $item->count }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ autoTranslate('Top Active Users (Last 7 Days)') }}</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>{{ autoTranslate('User') }}</th>
                                    <th>{{ autoTranslate('Activities') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topActiveUsers as $user)
                                <tr>
                                    <td>
                                        @if($user->user)
                                            <strong>{{ $user->user->name }}</strong><br>
                                            <small class="text-muted">{{ $user->user->email }}</small>
                                        @else
                                            <span class="text-muted">Unknown</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $user->activity_count }}</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="2" class="text-center text-muted">No data</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ autoTranslate('Quick Links') }}</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-list"></i> {{ autoTranslate('Activity Logs') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.sessions') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-user-check"></i> {{ autoTranslate('User Sessions') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-users"></i> {{ autoTranslate('Manage Users') }}
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.roles-permissions') }}" class="btn btn-outline-primary btn-block">
                                <i class="fas fa-shield-alt"></i> {{ autoTranslate('Roles & Permissions') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

