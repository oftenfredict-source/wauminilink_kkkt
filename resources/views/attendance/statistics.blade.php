@extends('layouts.index')

@section('title', 'Attendance Statistics')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-chart-bar"></i> Attendance Statistics
                    </h6>
                    <div class="btn-group" role="group">
                        <a href="{{ route('attendance.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Recording
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters - Mobile-friendly, like other pages -->
                    <form method="GET" action="{{ route('attendance.statistics') }}" class="card mb-4 border-0 shadow-sm" id="filtersForm">
                        <!-- Filter Header -->
                        <div class="card-header bg-white border-bottom p-2 px-3 filter-header" onclick="toggleFilters()">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-filter text-primary"></i>
                                    <span class="fw-semibold">Filters</span>
                                    @if(request('from') || request('to') || request('service_type') || request('service_id'))
                                        <span class="badge bg-primary rounded-pill" id="activeFiltersCount">
                                            {{ (request('from') ? 1 : 0) + (request('to') ? 1 : 0) + (request('service_type') ? 1 : 0) + (request('service_id') ? 1 : 0) }}
                                        </span>
                                    @endif
                                </div>
                                <i class="fas fa-chevron-down text-muted d-md-none" id="filterToggleIcon"></i>
                            </div>
                        </div>
                        
                        <!-- Filter Body - Collapsible on Mobile -->
                        <div class="card-body p-3" id="filterBody">
                            <div class="row g-2 mb-2">
                                <!-- From Date -->
                                <div class="col-6 col-md-3">
                                    <label for="from" class="form-label small text-muted mb-1">
                                        <i class="fas fa-calendar-alt me-1 text-info"></i>From Date
                                    </label>
                                    <input type="date" class="form-control form-control-sm" id="from" name="from" value="{{ request('from') }}">
                                </div>
                                
                                <!-- To Date -->
                                <div class="col-6 col-md-3">
                                    <label for="to" class="form-label small text-muted mb-1">
                                        <i class="fas fa-calendar-check me-1 text-info"></i>To Date
                                    </label>
                                    <input type="date" class="form-control form-control-sm" id="to" name="to" value="{{ request('to') }}">
                                </div>
                                
                                <!-- Service Type -->
                                <div class="col-12 col-md-3">
                                    <label for="service_type" class="form-label small text-muted mb-1">
                                        <i class="fas fa-church me-1 text-primary"></i>Service Type
                                    </label>
                                    <select class="form-select form-select-sm" id="service_type" name="service_type" onchange="updateServiceSelect()">
                                        <option value="">All Services</option>
                                        <option value="sunday_service" {{ request('service_type') == 'sunday_service' ? 'selected' : '' }}>Sunday Service</option>
                                        <option value="special_event" {{ request('service_type') == 'special_event' ? 'selected' : '' }}>Special Event</option>
                                    </select>
                                </div>
                                
                                <!-- Specific Service/Event -->
                                <div class="col-12 col-md-3">
                                    <label for="service_id" class="form-label small text-muted mb-1">
                                        <i class="fas fa-list me-1 text-secondary"></i>Specific Service/Event
                                    </label>
                                    <select class="form-select form-select-sm" id="service_id" name="service_id">
                                        <option value="">All</option>
                                        @if(request('service_type') == 'sunday_service')
                                            @foreach($sundayServices as $service)
                                                <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>
                                                    {{ $service->service_date->format('M d, Y') }} - {{ $service->theme ?? 'Service' }}
                                                </option>
                                            @endforeach
                                        @elseif(request('service_type') == 'special_event')
                                            @foreach($specialEvents as $event)
                                                <option value="{{ $event->id }}" {{ request('service_id') == $event->id ? 'selected' : '' }}>
                                                    {{ $event->event_date->format('M d, Y') }} - {{ $event->title ?? 'Event' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="row g-2">
                                <div class="col-12 col-md-6 d-grid">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-filter me-1"></i>
                                        <span class="d-none d-sm-inline">Apply Filters</span>
                                        <span class="d-sm-none">Apply</span>
                                    </button>
                                </div>
                                <div class="col-12 col-md-6 d-grid">
                                    <a href="{{ route('attendance.statistics') }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-times me-1"></i>
                                        <span class="d-none d-sm-inline">Clear Filters</span>
                                        <span class="d-sm-none">Clear</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Overall Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ number_format($totalAttendances) }}</h3>
                                    <p class="card-text mb-0">Total Attendances</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ number_format($sundayAttendances) }}</h3>
                                    <p class="card-text mb-0">Sunday Services</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ number_format($specialEventAttendances) }}</h3>
                                    <p class="card-text mb-0">Special Events</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h3 class="card-title">{{ number_format($adultMemberAttendances + $childrenAttendances + $totalGuests) }}</h3>
                                    <p class="card-text mb-0">Total Attendees</p>
                                    <small class="text-white-50">(Members + Children + Guests)</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Attendance by Category -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">
                                        <i class="fas fa-users"></i> Attendance by Category
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <!-- Adult Members -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-primary">
                                                <div class="card-header bg-primary text-white">
                                                    <h6 class="m-0">
                                                        <i class="fas fa-user"></i> Adult Members
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-12 mb-3">
                                                            <h2 class="text-primary mb-0">{{ number_format($adultMemberAttendances) }}</h2>
                                                            <small class="text-muted">Total Adult Member Attendances</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="p-3 bg-light rounded">
                                                                <h4 class="mb-1 text-primary">{{ number_format($maleMemberAttendances) }}</h4>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-mars"></i> Male
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="p-3 bg-light rounded">
                                                                <h4 class="mb-1 text-primary">{{ number_format($femaleMemberAttendances) }}</h4>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-venus"></i> Female
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Children -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-info">
                                                <div class="card-header bg-info text-white">
                                                    <h6 class="m-0">
                                                        <i class="fas fa-child"></i> Children's Ministry
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-12 mb-3">
                                                            <h2 class="text-info mb-0">{{ number_format($childrenAttendances) }}</h2>
                                                            <small class="text-muted">Total Children Attendances</small>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="p-3 bg-light rounded">
                                                                <h4 class="mb-1 text-info">{{ number_format($maleChildAttendances) }}</h4>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-mars"></i> Male
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="p-3 bg-light rounded">
                                                                <h4 class="mb-1 text-info">{{ number_format($femaleChildAttendances) }}</h4>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-venus"></i> Female
                                                                </small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Guests -->
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-warning">
                                                <div class="card-header bg-warning text-dark">
                                                    <h6 class="m-0">
                                                        <i class="fas fa-user-friends"></i> Guests (Non-Members)
                                                    </h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row text-center">
                                                        <div class="col-12 mb-3">
                                                            <h2 class="text-warning mb-0">{{ number_format($totalGuests) }}</h2>
                                                            <small class="text-muted">Total Guest Attendances</small>
                                                        </div>
                                                        <div class="col-12">
                                                            <div class="p-3 bg-light rounded">
                                                                <p class="text-muted mb-0">
                                                                    <i class="fas fa-info-circle"></i> Guests are visitors who are not registered church members
                                                                </p>
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

                    <!-- Most Regular Attendees -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">Most Regular Attendees</h6>
                                </div>
                                <div class="card-body">
                                    @if($mostRegularAttendees->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Rank</th>
                                                        <th>Member Name</th>
                                                        <th>Attendances</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($mostRegularAttendees as $index => $attendee)
                                                        <tr>
                                                            <td>
                                                                @if($index === 0)
                                                                    <i class="fas fa-trophy text-warning"></i>
                                                                @elseif($index === 1)
                                                                    <i class="fas fa-medal text-secondary"></i>
                                                                @elseif($index === 2)
                                                                    <i class="fas fa-award text-warning"></i>
                                                                @else
                                                                    <span class="badge bg-light text-dark">{{ $index + 1 }}</span>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <strong>{{ $attendee['name'] ?? 'Unknown' }}</strong>
                                                                @if(isset($attendee['type']) && $attendee['type'] === 'child')
                                                                    <span class="badge bg-info ms-2">Child</span>
                                                                @endif
                                                                @if(isset($attendee['member_id']) && $attendee['member_id'])
                                                                    <br>
                                                                    <small class="text-muted">{{ $attendee['member_id'] }}</small>
                                                                @endif
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary">{{ $attendee['attendance_count'] ?? 0 }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-users fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No attendance data available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Monthly Trends -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">Monthly Attendance Trends</h6>
                                </div>
                                <div class="card-body">
                                    @if($monthlyTrends->count() > 0)
                                        <div class="table-responsive">
                                            <table class="table table-sm">
                                                <thead>
                                                    <tr>
                                                        <th>Month</th>
                                                        <th>Year</th>
                                                        <th>Attendances</th>
                                                        <th>Chart</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($monthlyTrends as $trend)
                                                        <tr>
                                                            <td>{{ date('F', mktime(0, 0, 0, $trend->month, 1)) }}</td>
                                                            <td>{{ $trend->year }}</td>
                                                            <td>{{ $trend->attendance_count }}</td>
                                                            <td>
                                                                <div class="progress" style="height: 20px;">
                                                                    @php
                                                                        $maxAttendance = $monthlyTrends->max('attendance_count');
                                                                        $percentage = $maxAttendance > 0 ? ($trend->attendance_count / $maxAttendance) * 100 : 0;
                                                                    @endphp
                                                                    <div class="progress-bar bg-primary" style="width: {{ $percentage }}%">
                                                                        {{ $trend->attendance_count }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-3">
                                            <i class="fas fa-chart-line fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">No monthly data available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Missed Attendance Notifications -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold">Missed Attendance Notifications (SMS za Swahili)</h6>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-sm btn-outline-info" onclick="loadMissedMembers()">
                                            <i class="fas fa-refresh"></i> Refresh
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="triggerNotifications(true)">
                                            <i class="fas fa-eye"></i> Preview
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="triggerNotifications(false)">
                                            <i class="fas fa-paper-plane"></i> Send SMS
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle"></i> SMS Message Template (Swahili):</h6>
                                        <div class="bg-light p-3 rounded mt-2" style="font-family: monospace; font-size: 0.9em;">
                                            <strong>Shalom [Member Name],</strong><br><br>
                                            ni muda sasa hatujakuona kanisani. Tunaendelea kukuombea, tukitumaini utaungana nasi tena karibuni. Kumbuka, wewe ni sehemu muhimu ya familia ya Mungu. WAEBRANIA 10:25
                                        </div>
                                    </div>
                                    <div id="missedMembersContainer">
                                        <div class="text-center py-3">
                                            <i class="fas fa-spinner fa-spin"></i> Loading members with missed attendance...
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="m-0 font-weight-bold">Quick Actions</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <a href="{{ route('attendance.index') }}" class="btn btn-primary w-100 mb-2">
                                                <i class="fas fa-plus"></i> Record Attendance
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('members.index') }}" class="btn btn-info w-100 mb-2">
                                                <i class="fas fa-users"></i> View All Members
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('services.sunday.index') }}" class="btn btn-success w-100 mb-2">
                                                <i class="fas fa-calendar"></i> Sunday Services
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="{{ route('special.events.index') }}" class="btn btn-warning w-100 mb-2">
                                                <i class="fas fa-star"></i> Special Events
                                            </a>
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
@endsection

@section('scripts')
<script>
// Toggle Filters - mobile-friendly behavior (same pattern as other pages)
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

// Load members with missed attendance
function loadMissedMembers() {
    const container = document.getElementById('missedMembersContainer');
    if (!container) {
        console.error('Container missedMembersContainer not found');
        return;
    }
    container.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
    
    fetch('{{ route("attendance.missed.members") }}', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                displayMissedMembers(data.members);
            } else {
                container.innerHTML = '<div class="alert alert-danger">Failed to load members with missed attendance</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<div class="alert alert-danger">Error loading members with missed attendance: ' + error.message + '</div>';
        });
}

// Display members with missed attendance
function displayMissedMembers(members) {
    const container = document.getElementById('missedMembersContainer');
    
    if (members.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                <h5 class="text-success">Great News!</h5>
                <p class="text-muted">No members have missed 4+ consecutive weeks of attendance.</p>
            </div>
        `;
        return;
    }
    
    let html = `
        <div class="alert alert-info">
            <strong>${members.length} members</strong> have missed 4+ consecutive weeks of attendance
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Member Name</th>
                        <th>Member ID</th>
                        <th>Phone</th>
                        <th>Last Attendance</th>
                        <th>Weeks Missed</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
    `;
    
    members.forEach(member => {
        html += `
            <tr>
                <td><strong>${member.name}</strong></td>
                <td><code>${member.member_id}</code></td>
                <td>${member.phone}</td>
                <td>${member.last_attendance}</td>
                <td><span class="badge bg-danger">${member.weeks_missed} weeks</span></td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewMemberHistory(${member.id})">
                        <i class="fas fa-history"></i> History
                    </button>
                </td>
            </tr>
        `;
    });
    
    html += `
                </tbody>
            </table>
        </div>
    `;
    
    container.innerHTML = html;
}

// Trigger notifications
function triggerNotifications(dryRun) {
    const action = dryRun ? 'preview' : 'send';
    const confirmTitle = dryRun 
        ? 'Preview SMS Notifications'
        : 'Send SMS Notifications';
    const confirmText = dryRun 
        ? 'This will show you a preview of the SMS messages that would be sent to members who have missed 4+ consecutive weeks. No SMS will actually be sent.'
        : 'Are you sure you want to send SMS notifications to all members who have missed 4+ consecutive weeks? This action cannot be undone.';
    const confirmButtonText = dryRun ? 'Preview' : 'Yes, Send SMS';
    const confirmButtonColor = dryRun ? '#ffc107' : '#dc3545';
    
    Swal.fire({
        title: confirmTitle,
        html: confirmText,
        icon: dryRun ? 'info' : 'warning',
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: 'Cancel',
        confirmButtonColor: confirmButtonColor,
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: dryRun ? 'Generating Preview...' : 'Sending SMS...',
                html: 'Please wait while we process the notifications.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('{{ route("attendance.trigger.notifications") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    dry_run: dryRun
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (dryRun) {
                        // Show preview results in a nice format
                        let previewHtml = '<div class="text-start">';
                        previewHtml += '<p class="mb-3"><strong>Preview Results:</strong></p>';
                        
                        if (data.output) {
                            // Format the output - split by lines and display nicely
                            const lines = data.output.split('\n').filter(line => line.trim());
                            previewHtml += '<div class="bg-light p-3 rounded mb-3" style="max-height: 400px; overflow-y: auto;">';
                            previewHtml += '<pre style="white-space: pre-wrap; font-family: monospace; font-size: 0.85rem; margin: 0;">';
                            previewHtml += lines.map(line => {
                                // Highlight important lines
                                if (line.includes('Member:') || line.includes('Phone:') || line.includes('Message:')) {
                                    return '<strong style="color: #667eea;">' + escapeHtml(line) + '</strong>';
                                }
                                return escapeHtml(line);
                            }).join('\n');
                            previewHtml += '</pre>';
                            previewHtml += '</div>';
                        }
                        
                        previewHtml += '<p class="text-muted small mb-0"><i class="fas fa-info-circle"></i> No SMS messages were actually sent. This is a preview only.</p>';
                        previewHtml += '</div>';
                        
                        Swal.fire({
                            title: 'Preview Complete',
                            html: previewHtml,
                            icon: 'info',
                            confirmButtonText: 'Close',
                            confirmButtonColor: '#667eea',
                            width: '800px',
                            customClass: {
                                popup: 'text-start'
                            }
                        });
                    } else {
                        // Show success message for actual send
                        Swal.fire({
                            title: 'SMS Sent Successfully!',
                            html: '<div class="text-center"><i class="fas fa-check-circle fa-3x text-success mb-3"></i><p>' + (data.message || 'SMS notifications have been sent to all members who have missed 4+ consecutive weeks.') + '</p></div>',
                            icon: 'success',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#28a745'
                        }).then(() => {
                            // Refresh the missed members list
                            loadMissedMembers();
                        });
                    }
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: data.message || 'An error occurred while processing notifications.',
                        icon: 'error',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: 'Error',
                    html: '<p>An error occurred while processing notifications:</p><p class="text-danger">' + escapeHtml(error.message) + '</p>',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Helper function to escape HTML
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// View member attendance history
function viewMemberHistory(memberId) {
    window.open(`{{ url('/attendance/member') }}/${memberId}/history`, '_blank');
}

// Load missed members on page load and initialize filters behavior
document.addEventListener('DOMContentLoaded', function() {
    // Add a small delay to ensure the page is fully rendered
    setTimeout(function() {
        loadMissedMembers();
    }, 100);
    
    // Auto-expand filters on mobile if filters are active
    if (window.innerWidth <= 768) {
        const hasActiveFilters = {{ (request('from') || request('to') || request('service_type') || request('service_id')) ? 'true' : 'false' }};
        const filterBody = document.getElementById('filterBody');
        const filterIcon = document.getElementById('filterToggleIcon');
        
        if (hasActiveFilters && filterBody && filterIcon) {
            // Show filters and set icon state
            filterBody.style.display = 'block';
            filterIcon.classList.remove('fa-chevron-down');
            filterIcon.classList.add('fa-chevron-up');
            const filterHeader = document.querySelector('.filter-header');
            if (filterHeader) filterHeader.classList.add('active');
        }
    }
    
    // Handle window resize
    window.addEventListener('resize', function() {
        const filterBody = document.getElementById('filterBody');
        const filterIcon = document.getElementById('filterToggleIcon');
        
        if (!filterBody || !filterIcon) return;
        
        if (window.innerWidth > 768) {
            // Always show on desktop
            filterBody.style.display = 'block';
            filterIcon.style.display = 'none';
        } else {
            // On mobile, show chevron and hide body by default (unless active)
            filterIcon.style.display = 'block';
            const hasActiveFilters = {{ (request('from') || request('to') || request('service_type') || request('service_id')) ? 'true' : 'false' }};
            if (!hasActiveFilters) {
                filterBody.style.display = 'none';
                filterIcon.classList.remove('fa-chevron-up');
                filterIcon.classList.add('fa-chevron-down');
            }
        }
    });
});

// Service and event data for dynamic filtering
const sundayServicesData = [
    @foreach($sundayServices as $service)
    {
        id: {{ $service->id }},
        date: '{{ $service->service_date->format('M d, Y') }}',
        theme: '{{ addslashes($service->theme ?? 'Service') }}'
    }@if(!$loop->last),@endif
    @endforeach
];

const specialEventsData = [
    @foreach($specialEvents as $event)
    {
        id: {{ $event->id }},
        date: '{{ $event->event_date->format('M d, Y') }}',
        title: '{{ addslashes($event->title ?? 'Event') }}'
    }@if(!$loop->last),@endif
    @endforeach
];

// Update service/event select based on service type
function updateServiceSelect() {
    const serviceType = document.getElementById('service_type').value;
    const serviceIdSelect = document.getElementById('service_id');
    
    // Clear existing options except "All"
    serviceIdSelect.innerHTML = '<option value="">All</option>';
    
    if (serviceType === 'sunday_service') {
        sundayServicesData.forEach(function(service) {
            const option = document.createElement('option');
            option.value = service.id;
            option.textContent = service.date + ' - ' + service.theme;
            serviceIdSelect.appendChild(option);
        });
    } else if (serviceType === 'special_event') {
        specialEventsData.forEach(function(event) {
            const option = document.createElement('option');
            option.value = event.id;
            option.textContent = event.date + ' - ' + event.title;
            serviceIdSelect.appendChild(option);
        });
    }
}
</script>
@endsection
