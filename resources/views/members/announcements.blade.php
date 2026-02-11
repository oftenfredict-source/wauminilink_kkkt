@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center border border-danger border-2"
                                    style="width:48px; height:48px; background:rgba(148,0,0,.1);">
                                    <i class="fas fa-bullhorn text-danger"></i>
                                </div>
                                <div class="lh-sm">
                                    <h5 class="mb-0 fw-semibold text-dark">Announcements</h5>
                                    <small class="text-muted">Upcoming events and celebrations</small>
                                </div>
                                @if(isset($unreadCount) && $unreadCount > 0)
                                    <div class="badge bg-danger rounded-pill" style="font-size: 0.9rem;">
                                        {{ $unreadCount }} {{ $unreadCount == 1 ? 'Unread' : 'Unread' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Church Announcements -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-bullhorn me-2"></i>Church Announcements</h5>
                            @if(isset($announcements['announcements']))
                                @php
                                    $unreadAnnouncements = $announcements['announcements']->filter(function ($ann) {
                                        return isset($ann->is_unread) && $ann->is_unread;
                                    });
                                @endphp
                                @if($unreadAnnouncements->count() > 0)
                                    <span class="badge bg-danger">{{ $unreadAnnouncements->count() }} New</span>
                                @endif
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($announcements['announcements']) && $announcements['announcements']->count() > 0)
                            @foreach($announcements['announcements'] as $announcement)
                                                @php
                                                    $isUnread = isset($announcement->is_unread) && $announcement->is_unread;
                                                @endphp
                                 <div
                                                    class="card mb-3 border-0 shadow-sm {{ $announcement->is_pinned ? 'border-warning border-2' : '' }} {{ $isUnread ? 'border-danger border-2' : '' }}">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <h6 class="card-title mb-0">
                                                                @if($announcement->is_pinned)
                                                                    <i class="fas fa-thumbtack text-warning me-2" title="Pinned"></i>
                                                                @endif
                                                                @if($isUnread)
                                                                    <span class="badge bg-danger me-2">NEW</span>
                                                                @endif
                                                                {{ $announcement->title }}
                                                            </h6>
                                                            <span
                                                                class="badge bg-{{ $announcement->type === 'urgent' ? 'danger' : ($announcement->type === 'event' ? 'success' : ($announcement->type === 'reminder' ? 'warning' : 'secondary')) }}">
                                                                {{ ucfirst($announcement->type) }}
                                                            </span>
                                                        </div>
                                                        <p class="card-text">{{ $announcement->content }}</p>
                                                        <div class="d-flex flex-wrap gap-3">
                                                            @if($announcement->start_date)
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calendar-check me-1"></i>Starts:
                                                                    {{ $announcement->start_date->format('M d, Y') }}
                                                                </small>
                                                            @endif
                                                            @if($announcement->end_date)
                                                                <small class="text-muted">
                                                                    <i class="fas fa-calendar-times me-1"></i>Expires:
                                                                    {{ $announcement->end_date->format('M d, Y') }}
                                                                </small>
                                                            @endif
                                                            <small class="text-muted">
                                                                <i class="fas fa-clock me-1"></i>{{ $announcement->created_at->format('M d, Y') }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No announcements at this time</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Special Events -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Upcoming Special Events</h5>
                    </div>
                    <div class="card-body">
                        @if($announcements['events']->count() > 0)
                            @foreach($announcements['events'] as $event)
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $event->event_name }}</h6>
                                        <p class="card-text text-muted">{{ $event->description }}</p>
                                        <div class="d-flex flex-wrap gap-3">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>{{ $event->event_date->format('M d, Y') }}
                                            </small>
                                            @if($event->start_time)
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ $event->start_time }}
                                                    @if($event->end_time)
                                                        - {{ $event->end_time }}
                                                    @endif
                                                </small>
                                            @endif
                                            @if($event->venue)
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $event->venue }}
                                                </small>
                                            @endif
                                        </div>
                                        @if($event->speaker)
                                            <small class="text-muted d-block mt-2">
                                                <i class="fas fa-user me-1"></i>Speaker: {{ $event->speaker }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No upcoming events</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Celebrations -->
            <div class="col-md-6 mb-4">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="fas fa-birthday-cake me-2"></i>Upcoming Celebrations</h5>
                    </div>
                    <div class="card-body">
                        @if($announcements['celebrations']->count() > 0)
                            @foreach($announcements['celebrations'] as $celebration)
                                <div class="card mb-3 border-0 shadow-sm">
                                    <div class="card-body">
                                        <h6 class="card-title">{{ $celebration->celebration_type }}</h6>
                                        <p class="card-text">
                                            <strong>Celebrant:</strong> {{ $celebration->celebrant_name }}
                                        </p>
                                        @if($celebration->description)
                                            <p class="card-text text-muted">{{ $celebration->description }}</p>
                                        @endif
                                        <div class="d-flex flex-wrap gap-3">
                                            <small class="text-muted">
                                                <i
                                                    class="fas fa-calendar me-1"></i>{{ $celebration->celebration_date->format('M d, Y') }}
                                            </small>
                                            @if($celebration->start_time)
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>{{ $celebration->start_time }}
                                                    @if($celebration->end_time)
                                                        - {{ $celebration->end_time }}
                                                    @endif
                                                </small>
                                            @endif
                                            @if($celebration->venue)
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>{{ $celebration->venue }}
                                                </small>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No upcoming celebrations</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sunday Services -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0"><i class="fas fa-church me-2"></i>Upcoming Sunday Services</h5>
                    </div>
                    <div class="card-body">
                        @if($announcements['sunday_services']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Service Type</th>
                                            <th>Theme</th>
                                            <th>Speaker</th>
                                            <th>Time</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($announcements['sunday_services'] as $service)
                                            <tr>
                                                <td>{{ $service->service_date->format('M d, Y') }}</td>
                                                <td>{{ $service->service_type ?? 'Sunday Service' }}</td>
                                                <td>{{ $service->theme ?? 'N/A' }}</td>
                                                <td>{{ $service->speaker ?? 'N/A' }}</td>
                                                <td>{{ $service->start_time ?? 'N/A' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted text-center">No upcoming Sunday services</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection