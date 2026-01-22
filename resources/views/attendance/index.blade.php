@extends('layouts.index')

@section('title', 'Attendance Recording')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-users"></i> Service Attendance Recording
                    </h6>
                    <div class="btn-group" role="group">
                        <a href="{{ route('attendance.statistics') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-chart-bar"></i> Statistics
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- TEMPORARY: Biometric Device Connection Test Section -->
                    <div class="card border-warning mb-4" id="biometricTestSection">
                        <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="fas fa-fingerprint"></i> <strong>Biometric Device Test (Temporary)</strong>
                            </h6>
                            <button type="button" class="btn btn-sm btn-outline-dark" onclick="toggleBiometricTest()">
                                <i class="fas fa-chevron-down" id="biometricTestToggleIcon"></i>
                            </button>
                        </div>
                        <div class="card-body" id="biometricTestContent" style="display: none;">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i> <strong>Quick Test:</strong> Enter your biometric device details below to test the connection.
                            </div>
                            
                            <form id="biometricTestForm">
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <label for="test_ip" class="form-label">Device IP Address <span class="text-danger">*</span></label>
                                        <input type="text" id="test_ip" name="ip" value="{{ config('zkteco.ip', '192.168.100.108') }}" 
                                               class="form-control form-control-sm" required placeholder="e.g., 192.168.100.108">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="test_port" class="form-label">Port <span class="text-danger">*</span></label>
                                        <input type="number" id="test_port" name="port" value="{{ config('zkteco.port', 4370) }}" 
                                               class="form-control form-control-sm" required min="1" max="65535">
                                    </div>
                                    <div class="col-md-3">
                                        <label for="test_password" class="form-label">Comm Key (Password)</label>
                                        <input type="number" id="test_password" name="password" value="{{ config('zkteco.password', 0) }}" 
                                               class="form-control form-control-sm" placeholder="0 (default)">
                                    </div>
                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-primary btn-sm w-100" onclick="testBiometricConnection()">
                                            <i class="fas fa-plug"></i> Test
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <div id="biometricTestLoading" class="text-center" style="display:none;">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <small class="d-block mt-2">Connecting to device...</small>
                            </div>

                            <div id="biometricTestResult" style="display:none;"></div>

                            <div class="mt-3">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-success" onclick="testBiometricDeviceInfo()">
                                        <i class="fas fa-info-circle"></i> Device Info
                                    </button>
                                    <button type="button" class="btn btn-outline-info" onclick="testBiometricAttendance()">
                                        <i class="fas fa-calendar-check"></i> Get Attendance
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="testBiometricUsers()">
                                        <i class="fas fa-users"></i> Get Users
                                    </button>
                                </div>
                            </div>

                            <div class="mt-3 border-top pt-3">
                                <h6 class="fw-bold mb-2"><i class="fas fa-user-plus"></i> Register Members to Device (Testing Mode)</h6>
                                <div class="alert alert-info small mb-2">
                                    <i class="fas fa-info-circle"></i> <strong>Auto-Generated Enroll IDs:</strong> 
                                    The system automatically generates a unique 2-3 digit enroll ID (10-999) for each member when they are registered.
                                    <br><strong>Testing Mode:</strong> Enter member name directly to register continuously.
                                </div>
                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="register_member_name" class="form-label small">Enter Member Name:</label>
                                        <input type="text" id="register_member_name" class="form-control form-control-sm" 
                                               placeholder="Type member full name (e.g., John Doe)" autocomplete="off"
                                               onkeypress="if(event.key === 'Enter') { event.preventDefault(); registerMemberToDevice(); }">
                                        <small class="text-muted">Type member name and press Enter or click Register</small>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" class="btn btn-success btn-sm w-100" onclick="registerMemberToDevice()">
                                            <i class="fas fa-fingerprint"></i> Register Member
                                        </button>
                                    </div>
                                </div>
                                <div id="registerMemberResult" class="mt-2" style="display:none;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Service Selection -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="service_type" class="form-label">Service Type</label>
                            <select id="service_type" name="service_type" class="form-select" onchange="loadServices()">
                                <option value="sunday_service" {{ $serviceType === 'sunday_service' ? 'selected' : '' }}>
                                    Main Service
                                </option>
                                <option value="children_service" {{ $serviceType === 'children_service' ? 'selected' : '' }}>
                                    Children Service (Sunday School)
                                </option>
                                <option value="special_event" {{ $serviceType === 'special_event' ? 'selected' : '' }}>
                                    Special Event
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="service_id" class="form-label">Select Service</label>
                            <select id="service_id" name="service_id" class="form-select" onchange="loadAttendanceForm()">
                                <option value="">Choose a service...</option>
                                @if($services->isEmpty())
                                    <option value="" disabled>No services found. Please create a service first.</option>
                                @endif
                                @foreach($services as $service)
                                    @php
                                        $serviceDate = $service->service_date ?? $service->event_date;
                                        
                                        // Get day name from date
                                        $dayName = '';
                                        if ($serviceDate) {
                                            try {
                                                $dateObj = is_string($serviceDate) ? \Carbon\Carbon::parse($serviceDate) : $serviceDate;
                                                $dayName = $dateObj->format('l'); // Full day name (Monday, Tuesday, etc.)
                                            } catch (\Exception $e) {
                                                $dayName = '';
                                            }
                                        }
                                        
                                        $formattedDate = $serviceDate ? (is_string($serviceDate) ? \Carbon\Carbon::parse($serviceDate)->format('d/m/Y') : $serviceDate->format('d/m/Y')) : '';
                                        $serviceTheme = $service->theme ?? $service->title ?? 'No theme';
                                        
                                        // Format time if available
                                        $fmtTime = function($t) {
                                            if (!$t) return null;
                                            try {
                                                if (preg_match('/^\d{2}:\d{2}/', $t)) {
                                                    return substr($t, 0, 5);
                                                }
                                                if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}/', $t)) {
                                                    return substr(substr($t, 11), 0, 5);
                                                }
                                                return \Carbon\Carbon::parse($t)->format('H:i');
                                            } catch (\Throwable $e) {
                                                return null;
                                            }
                                        };
                                        
                                        $startTime = isset($service->start_time) ? $fmtTime($service->start_time) : null;
                                        $endTime = isset($service->end_time) ? $fmtTime($service->end_time) : null;
                                        $timeDisplay = ($startTime && $endTime) ? " ({$startTime} - {$endTime})" : ($startTime ? " ({$startTime})" : '');
                                        
                                        // Build display text based on service type
                                        if ($serviceType === 'children_service') {
                                            // For Children Service: Date, Day Name, Time (no theme)
                                            $displayText = $formattedDate;
                                            if ($dayName) {
                                                $displayText .= ' (' . $dayName . ')';
                                            }
                                            $displayText .= $timeDisplay;
                                        } else {
                                            // For Main Service: Date, Time, Theme
                                            $displayText = $formattedDate . $timeDisplay . ' - ' . $serviceTheme;
                                        }
                                    @endphp
                                    <option value="{{ $service->id }}" 
                                            {{ $serviceId == $service->id ? 'selected' : '' }}
                                            data-date="{{ $serviceDate }}"
                                            data-theme="{{ $serviceTheme }}"
                                            data-service-type="{{ $service->service_type ?? 'sunday_service' }}">
                                        {{ $displayText }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    @if($selectedService)
                    @php
                        $selectedDate = $selectedService->service_date ?? $selectedService->event_date;
                        $formattedSelectedDate = $selectedDate ? (is_string($selectedDate) ? \Carbon\Carbon::parse($selectedDate)->format('d/m/Y') : $selectedDate->format('d/m/Y')) : '';
                        // Normalized date (Y-m-d) for biometric sync
                        $selectedDateForSync = $selectedDate
                            ? (is_string($selectedDate)
                                ? \Carbon\Carbon::parse($selectedDate)->format('Y-m-d')
                                : $selectedDate->format('Y-m-d'))
                            : '';
                        $startTime = $selectedService->start_time ?? null;
                        $endTime = $selectedService->end_time ?? null;
                        
                        // Check if attendance can be recorded (not before service start time)
                        $canRecordAttendance = true;
                        $timeRestrictionMessage = '';
                        
                        if ($startTime) {
                            try {
                                // Parse start_time (stored as TIME in database, so it's a string like "09:00:00" or "09:00")
                                $timeString = $startTime;
                                if ($startTime instanceof \Carbon\Carbon) {
                                    $timeString = $startTime->format('H:i:s');
                                } elseif (is_object($startTime) && method_exists($startTime, 'format')) {
                                    $timeString = $startTime->format('H:i:s');
                                } elseif (is_string($startTime)) {
                                    // Ensure it's in H:i:s format
                                    if (strlen($startTime) === 5) {
                                        $timeString = $startTime . ':00';
                                    }
                                }
                                
                                $serviceStartDateTime = \Carbon\Carbon::parse($selectedDate->format('Y-m-d') . ' ' . $timeString);
                                
                                $now = now();
                                if ($now->lt($serviceStartDateTime)) {
                                    $canRecordAttendance = false;
                                    $timeRestrictionMessage = 'Attendance cannot be recorded before the service start time. Service starts at ' . 
                                        $serviceStartDateTime->format('d/m/Y h:i A') . '.';
                                }
                            } catch (\Exception $e) {
                                // If time parsing fails, allow attendance (fallback)
                                $canRecordAttendance = true;
                            }
                        }
                    @endphp
                    
                    <!-- Service Information -->
                    <div class="alert alert-info">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0"><strong>Service Details:</strong></h6>
                            @if($serviceType === 'sunday_service')
                                <div class="btn-group" role="group">
                                    <button type="button"
                                            id="autoSyncToggle"
                                            class="btn btn-sm btn-outline-success"
                                            onclick="toggleAutoSync()"
                                            title="Enable/Disable automatic sync from device">
                                        <i class="fas fa-sync-alt"></i> <span id="autoSyncText">Enable Auto-Sync</span>
                                    </button>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            onclick="syncFromDevice()"
                                            title="Manually sync from device now">
                                        <i class="fas fa-fingerprint"></i> Sync Now
                                    </button>
                                </div>
                                <div id="autoSyncStatus" class="ms-2" style="display: none;">
                                    <span class="badge bg-success">
                                        <i class="fas fa-circle-notch fa-spin"></i> Auto-syncing...
                                    </span>
                                </div>
                            @endif
                        </div>
                        <input type="hidden" id="selected_service_date" value="{{ $selectedDateForSync }}">
                        <div class="row">
                            <div class="col-md-3">
                                <strong>Date:</strong> {{ $formattedSelectedDate }}
                            </div>
                            @if($serviceType !== 'children_service')
                            <div class="col-md-3">
                                <strong>Theme:</strong> {{ $selectedService->theme ?? $selectedService->title }}
                            </div>
                            @endif
                            <div class="{{ $serviceType === 'children_service' ? 'col-md-4' : 'col-md-3' }}">
                                <strong>Current Attendance:</strong> 
                                @if($serviceType === 'children_service')
                                    <span class="badge bg-primary">{{ $childAttendanceRecords->count() }}</span>
                                    <small class="text-muted d-block">
                                        ({{ $childAttendanceRecords->count() }} children)
                                    </small>
                                @else
                                    @php
                                        $guestsCount = $selectedService->guests_count ?? 0;
                                        $totalAttendance = $attendanceRecords->count() + $childAttendanceRecords->count() + $guestsCount;
                                    @endphp
                                    <span class="badge bg-primary">{{ $totalAttendance }}</span>
                                    <small class="text-muted d-block">
                                        ({{ $attendanceRecords->count() }} members, {{ $childAttendanceRecords->count() }} children{{ $guestsCount > 0 ? ', ' . $guestsCount . ' guests' : '' }})
                                    </small>
                                @endif
                            </div>
                            <div class="{{ $serviceType === 'children_service' ? 'col-md-5' : 'col-md-3' }}">
                                @if($serviceType === 'children_service')
                                    <strong>Total Children:</strong> 
                                    <span class="badge bg-info">{{ $children->count() }}</span>
                                @else
                                    <strong>Total Members:</strong> 
                                    <span class="badge bg-secondary">{{ $members->count() }}</span>
                                    <br>
                                    <strong>Total Children:</strong> 
                                    <span class="badge bg-info">{{ $children->count() }}</span>
                                @endif
                            </div>
                        </div>
                        @if($startTime)
                        <div class="row mt-2">
                            <div class="col-12">
                                <strong>Service Time:</strong> 
                                @php
                                    $formatTime = function($time) {
                                        if (!$time) return '';
                                        try {
                                            if ($time instanceof \Carbon\Carbon) {
                                                return $time->format('h:i A');
                                            } elseif (is_string($time)) {
                                                // Handle time string like "09:00:00" or "09:00"
                                                if (strlen($time) === 5) {
                                                    $time = $time . ':00';
                                                }
                                                return \Carbon\Carbon::parse($time)->format('h:i A');
                                            }
                                            return '';
                                        } catch (\Exception $e) {
                                            return $time;
                                        }
                                    };
                                @endphp
                                @if($startTime && $endTime)
                                    {{ $formatTime($startTime) }} - {{ $formatTime($endTime) }}
                                @elseif($startTime)
                                    {{ $formatTime($startTime) }}
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    @if(!$canRecordAttendance)
                    <!-- Time Restriction Warning -->
                    <div class="alert alert-warning d-flex align-items-center" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <strong>Attendance Recording Restricted</strong>
                            <p class="mb-0">{{ $timeRestrictionMessage }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Attendance Form -->
                    <form id="attendanceForm" method="POST" action="{{ route('attendance.store') }}" 
                          @if(!$canRecordAttendance) onsubmit="event.preventDefault(); showTimeRestrictionError();" @endif>
                        @csrf
                        <input type="hidden" name="service_type" value="{{ $serviceType }}">
                        <input type="hidden" name="service_id" value="{{ $serviceId }}">
                        <input type="hidden" id="canRecordAttendance" value="{{ $canRecordAttendance ? '1' : '0' }}">
                        <input type="hidden" id="timeRestrictionMessage" value="{{ $timeRestrictionMessage }}">
                        
                        <!-- Quick Actions -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-success btn-sm" onclick="selectAll()">
                                        <i class="fas fa-check-double"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-warning btn-sm" onclick="selectNone()">
                                        <i class="fas fa-times"></i> Select None
                                    </button>
                                    <button type="button" class="btn btn-info btn-sm" onclick="toggleSelection()">
                                        <i class="fas fa-exchange-alt"></i> Toggle Selection
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Tabs for Members and Children -->
                        @if($serviceType === 'children_service')
                            <!-- For Children Service, show only children (no tabs) -->
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i>
                                <strong>Children Service (Sunday School) Attendance</strong>
                                <p class="mb-0 mt-2">Recording attendance for children ages <strong>3-12</strong> only.</p>
                            </div>
                        @else
                            <!-- For Main Service and Special Events, show tabs -->
                            <ul class="nav nav-tabs mb-3" id="attendanceTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="members-tab" data-bs-toggle="tab" data-bs-target="#members" type="button" role="tab">
                                        <i class="fas fa-users"></i> Members ({{ $members->count() }})
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="children-tab" data-bs-toggle="tab" data-bs-target="#children" type="button" role="tab">
                                        <i class="fas fa-child"></i> Children's Ministry ({{ $children->count() }})
                                    </button>
                                </li>
                            </ul>
                        @endif

                        <div class="tab-content" id="attendanceTabsContent">
                            @if($serviceType === 'children_service')
                                <!-- For Children Service, show children directly (no Members tab) -->
                                <div class="tab-pane fade show active" id="children" role="tabpanel">
                            @else
                                <!-- Members Tab -->
                                <div class="tab-pane fade show active" id="members" role="tabpanel">
                                    <!-- Search Members -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <input type="text" id="memberSearch" class="form-control" placeholder="Search members by name..." onkeyup="filterMembers()">
                                        </div>
                                        <div class="col-md-6">
                                            <select id="genderFilter" class="form-select" onchange="filterMembers()">
                                                <option value="">All Genders</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Members List -->
                                    <div class="row" id="membersList">
                                        @foreach($members as $member)
                                            <div class="col-md-6 col-lg-4 mb-2 member-item" 
                                                 data-name="{{ strtolower($member->full_name) }}" 
                                                 data-gender="{{ $member->gender }}">
                                                <div class="form-check">
                                                    <input class="form-check-input member-checkbox" 
                                                           type="checkbox" 
                                                           name="member_ids[]" 
                                                           value="{{ $member->id }}" 
                                                           id="member_{{ $member->id }}"
                                                           {{ $attendanceRecords->has($member->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="member_{{ $member->id }}">
                                                        <strong>{{ $member->full_name }}</strong>
                                                        @php
                                                            // Check if this member's attendance was recorded from biometric device
                                                            $biometricAttendance = $attendanceRecords->get($member->id);
                                                            $isFromDevice = $biometricAttendance && $biometricAttendance->recorded_by === 'BiometricDevice';
                                                        @endphp
                                                        @if($isFromDevice)
                                                            <span class="badge bg-success ms-2" title="Synced from biometric device">
                                                                <i class="fas fa-fingerprint"></i> Device
                                                            </span>
                                                        @endif
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ $member->member_id }} | 
                                                            {{ ucfirst($member->gender) }} | 
                                                            {{ $member->phone_number }}
                                                        </small>
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Children Tab -->
                                <div class="tab-pane fade" id="children" role="tabpanel">
                            @endif
                                @if($serviceType === 'children_service')
                                    <h6 class="mb-3"><i class="fas fa-child"></i> Children (Ages 3-12)</h6>
                                @else
                                    <!-- Children Info Alert (only for Main Service) -->
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle"></i>
                                        <strong>Children's Ministry Attendance:</strong>
                                        <p class="mb-0 mt-2">
                                            <strong>Recording attendance for Main Service</strong><br>
                                            Only teenagers ages <strong>13-17</strong> (Children's Ministry) should be recorded here.
                                        </p>
                                    </div>
                                @endif

                                <!-- Search Children -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <input type="text" id="childSearch" class="form-control" placeholder="Search children by name..." onkeyup="filterChildren()">
                                    </div>
                                    <div class="col-md-6">
                                        <select id="childGenderFilter" class="form-select" onchange="filterChildren()">
                                            <option value="">All Genders</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Children List -->
                                <div class="row" id="childrenList">
                                    @foreach($children as $child)
                                        @php
                                            $age = (int) $child->getAge(); // Ensure age is a whole number
                                            $ageGroup = $child->getAgeGroup();
                                            $recommendedService = $child->getRecommendedServiceType();
                                            $isChecked = $childAttendanceRecords->has($child->id);
                                            $serviceMatch = false;
                                            
                                            // Check if the child's recommended service matches the attendance service type
                                            // (not the service's own type, but the attendance type selected)
                                            $serviceMatch = ($recommendedService === $serviceType);
                                        @endphp
                                        <div class="col-md-6 col-lg-4 mb-2 child-item" 
                                             data-name="{{ strtolower($child->full_name) }}" 
                                             data-gender="{{ $child->gender }}"
                                             data-age-group="{{ $ageGroup }}">
                                            <div class="form-check">
                                                <input class="form-check-input child-checkbox" 
                                                       type="checkbox" 
                                                       name="child_ids[]" 
                                                       value="{{ $child->id }}" 
                                                       id="child_{{ $child->id }}"
                                                       {{ $isChecked ? 'checked' : '' }}>
                                                <label class="form-check-label" for="child_{{ $child->id }}">
                                                    <strong>{{ $child->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">
                                                        Age: {{ $age }} | 
                                                        {{ ucfirst($child->gender) }}
                                                        @if($child->member)
                                                            | Parent: {{ $child->member->full_name }}
                                                        @endif
                                                    </small>
                                                    <br>
                                                    @if($recommendedService)
                                                        @if($serviceMatch)
                                                            <span class="badge bg-success mt-1">
                                                                <i class="fas fa-check-circle"></i> Correct Service
                                                            </span>
                                                        @else
                                                            <span class="badge bg-warning mt-1">
                                                                <i class="fas fa-exclamation-triangle"></i> 
                                                                Should attend: {{ $recommendedService === 'children_service' ? 'Children Service' : 'Main Service' }}
                                                            </span>
                                                        @endif
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        @if($serviceType === 'children_service')
                        <!-- Children Service Offering -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="children_offering_amount" class="form-label">
                                    <i class="fas fa-money-bill-wave"></i> Sunday School Offering Amount (Optional)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">TZS</span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="children_offering_amount" 
                                           name="children_offering_amount" 
                                           step="0.01" 
                                           min="0" 
                                           placeholder="0.00"
                                           value="{{ $existingOfferingAmount ?? ($selectedService && isset($selectedService->offerings_amount) ? $selectedService->offerings_amount : '') }}">
                                </div>
                                <small class="text-muted">Enter the total offering amount collected during Sunday School</small>
                            </div>
                        </div>
                        @endif

                        @if($serviceType === 'sunday_service')
                        <!-- Guests Count (Main Service Only) -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="guests_count" class="form-label">
                                    <i class="fas fa-user-friends"></i> Number of Guests (Non-Members) (Optional)
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-users"></i></span>
                                    <input type="number" 
                                           class="form-control" 
                                           id="guests_count" 
                                           name="guests_count" 
                                           min="0" 
                                           step="1"
                                           placeholder="0"
                                           value="{{ $selectedService && isset($selectedService->guests_count) ? $selectedService->guests_count : '' }}">
                                </div>
                                <small class="text-muted">Enter the number of guests/visitors who are not church members</small>
                            </div>
                        </div>
                        @endif

                        <!-- Notes -->
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="notes" class="form-label">Notes (Optional)</label>
                                <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="Any additional notes about the attendance..."></textarea>
                            </div>
                        </div>


                        <!-- Submit Button -->
                        <div class="row mt-4">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary btn-lg" 
                                        id="saveAttendanceBtn" 
                                        @if(!$canRecordAttendance) disabled @endif>
                                    <i class="fas fa-save"></i> Save Attendance
                                </button>
                            </div>
                        </div>
                    </form>
                    @else
                    <!-- No Service Selected -->
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Please select a service to record attendance</h5>
                        <p class="text-muted">Choose a service from the dropdown above to begin recording attendance.</p>
                        
                        @if($services->isEmpty())
                            <div class="alert alert-warning mt-4" style="max-width: 600px; margin: 20px auto;">
                                <h6><i class="fas fa-exclamation-triangle"></i> No Services Found</h6>
                                <p class="mb-2">You need to create a service first before recording attendance.</p>
                                <a href="{{ route('services.sunday.index') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Create Service
                                </a>
                                <p class="mt-2 mb-0 small text-muted">
                                    <strong>Tip:</strong> You can use the same service for both Main Service and Children Service attendance. The service type you select in attendance determines which children appear.
                                </p>
                            </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal fade" id="loadingModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="spinner-border text-primary mb-3" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0">Saving attendance...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Get the biometric sync URL - use test route that's outside attendance directory
// This works around the issue where php artisan serve serves the physical attendance directory
const biometricSyncUrl = "/test-biometric-sync";

function loadServices() {
    // Clear auto-sync when service type changes
    clearAutoSync();
    
    const serviceType = document.getElementById('service_type').value;
    const serviceSelect = document.getElementById('service_id');
    
    // Clear current options
    serviceSelect.innerHTML = '<option value="">Choose a service...</option>';
    
    // Show loading
    serviceSelect.disabled = true;
    
    // Reload page with new service type
    window.location.href = `{{ route('attendance.index') }}?service_type=${serviceType}`;
}

function loadAttendanceForm() {
    // Clear auto-sync when service changes
    clearAutoSync();
    
    const serviceType = document.getElementById('service_type').value;
    const serviceId = document.getElementById('service_id').value;
    
    if (serviceId) {
        window.location.href = `{{ route('attendance.index') }}?service_type=${serviceType}&service_id=${serviceId}`;
    }
}

function selectAll() {
    // Only select checkboxes that are currently visible (not hidden by filter)
    const activeTab = document.querySelector('.nav-link.active');
    const isChildrenService = document.querySelector('#service_type')?.value === 'children_service';
    const isChildrenTab = activeTab && activeTab.id === 'children-tab';
    
    // If Children Service is selected, only show children (no tabs)
    const selector = (isChildrenService || isChildrenTab) ? '.child-item' : '.member-item';
    const checkboxSelector = (isChildrenService || isChildrenTab) ? '.child-checkbox' : '.member-checkbox';
    
    document.querySelectorAll(selector).forEach(item => {
        const isVisible = item.offsetParent !== null && window.getComputedStyle(item).display !== 'none';
        if (isVisible) {
            const checkbox = item.querySelector(checkboxSelector);
            if (checkbox) {
                checkbox.checked = true;
            }
        }
    });
    updateSelectedCount();
}

function selectNone() {
    // Only deselect checkboxes that are currently visible (not hidden by filter)
    const activeTab = document.querySelector('.nav-link.active');
    const isChildrenService = document.querySelector('#service_type')?.value === 'children_service';
    const isChildrenTab = activeTab && activeTab.id === 'children-tab';
    
    // If Children Service is selected, only show children (no tabs)
    const selector = (isChildrenService || isChildrenTab) ? '.child-item' : '.member-item';
    const checkboxSelector = (isChildrenService || isChildrenTab) ? '.child-checkbox' : '.member-checkbox';
    
    document.querySelectorAll(selector).forEach(item => {
        const isVisible = item.offsetParent !== null && window.getComputedStyle(item).display !== 'none';
        if (isVisible) {
            const checkbox = item.querySelector(checkboxSelector);
            if (checkbox) {
                checkbox.checked = false;
            }
        }
    });
    updateSelectedCount();
}

function toggleSelection() {
    // Only toggle checkboxes that are currently visible (not hidden by filter)
    const activeTab = document.querySelector('.nav-link.active');
    const isChildrenService = document.querySelector('#service_type')?.value === 'children_service';
    const isChildrenTab = activeTab && activeTab.id === 'children-tab';
    
    // If Children Service is selected, only show children (no tabs)
    const selector = (isChildrenService || isChildrenTab) ? '.child-item' : '.member-item';
    const checkboxSelector = (isChildrenService || isChildrenTab) ? '.child-checkbox' : '.member-checkbox';
    
    document.querySelectorAll(selector).forEach(item => {
        // Check if the item is visible (not hidden by display: none)
        const isVisible = item.offsetParent !== null && window.getComputedStyle(item).display !== 'none';
        if (isVisible) {
            const checkbox = item.querySelector(checkboxSelector);
            if (checkbox) {
                checkbox.checked = !checkbox.checked;
            }
        }
    });
    updateSelectedCount();
}

function filterChildren() {
    const searchTerm = document.getElementById('childSearch').value.toLowerCase();
    const genderFilter = document.getElementById('childGenderFilter').value;
    
    document.querySelectorAll('.child-item').forEach(item => {
        const name = item.dataset.name;
        const gender = item.dataset.gender;
        
        const matchesSearch = name.includes(searchTerm);
        const matchesGender = !genderFilter || gender === genderFilter;
        
        if (matchesSearch && matchesGender) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function filterMembers() {
    const searchTerm = document.getElementById('memberSearch').value.toLowerCase();
    const genderFilter = document.getElementById('genderFilter').value;
    
    document.querySelectorAll('.member-item').forEach(item => {
        const name = item.dataset.name;
        const gender = item.dataset.gender;
        
        const matchesSearch = name.includes(searchTerm);
        const matchesGender = !genderFilter || gender === genderFilter;
        
        if (matchesSearch && matchesGender) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function updateSelectedCount() {
    const memberCount = document.querySelectorAll('.member-checkbox:checked').length;
    const childCount = document.querySelectorAll('.child-checkbox:checked').length;
    const totalCount = memberCount + childCount;
    // You can add a counter display here if needed
}

// Function to show time restriction error
function showTimeRestrictionError() {
    const message = document.getElementById('timeRestrictionMessage')?.value || 
                   'Attendance cannot be recorded before the service start time.';
    Swal.fire({
        icon: 'warning',
        title: 'Attendance Recording Restricted',
        text: message,
        confirmButtonText: 'OK'
    });
}

// Form submission
const attendanceForm = document.getElementById('attendanceForm');
if (attendanceForm) {
    attendanceForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Check if attendance can be recorded
    const canRecord = document.getElementById('canRecordAttendance')?.value === '1';
    if (!canRecord) {
        showTimeRestrictionError();
        return;
    }
    
    const memberCount = document.querySelectorAll('.member-checkbox:checked').length;
    const childCount = document.querySelectorAll('.child-checkbox:checked').length;
    const totalCount = memberCount + childCount;
    
    if (totalCount === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'No Attendees Selected',
            text: 'Please select at least one member or child to record attendance.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    // Show loading modal
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    loadingModal.show();
    
    // Get CSRF token
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (!csrfToken) {
        loadingModal.hide();
        Swal.fire({
            icon: 'error',
            title: 'Session Error',
            text: 'CSRF token not found. Please refresh the page and try again.',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.reload();
        });
        return;
    }
    
    // Create FormData and ensure CSRF token is included
    const formData = new FormData(this);
    if (!formData.has('_token')) {
        formData.append('_token', csrfToken);
    }
    
    // Submit form
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        },
        credentials: 'same-origin'
    })
    .then(async response => {
        // Handle 419 CSRF Token Mismatch / Session Expired
        if (response.status === 419) {
            loadingModal.hide();
            let errorMessage = 'Your session has expired. Please log in again.';
            try {
                const errorData = await response.json();
                if (errorData.message) {
                    errorMessage = errorData.message;
                }
                if (errorData.redirect) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Session Expired',
                        text: errorMessage,
                        confirmButtonText: 'Go to Login',
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then(() => {
                        window.location.href = errorData.redirect || '{{ route("login") }}';
                    });
                    return;
                }
            } catch (e) {
                // If JSON parsing fails, use default message
            }
            
            Swal.fire({
                icon: 'warning',
                title: 'Session Expired',
                text: errorMessage,
                confirmButtonText: 'Go to Login',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                window.location.href = '{{ route("login") }}';
            });
            return;
        }
        
        // Handle 401 Unauthorized
        if (response.status === 401) {
            loadingModal.hide();
            let errorMessage = 'You are not authorized to perform this action.';
            try {
                const errorData = await response.json();
                if (errorData.message) {
                    errorMessage = errorData.message;
                }
            } catch (e) {
                // Use default message
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Unauthorized',
                text: errorMessage,
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = '{{ route("login") }}';
            });
            return;
        }
        
        if (!response.ok) {
            return response.json().then(err => {
                // Check if it's a time restriction error (422 status)
                if (response.status === 422) {
                    loadingModal.hide();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Attendance Recording Restricted',
                        text: err.message || 'Attendance cannot be recorded before the service start time.',
                        confirmButtonText: 'OK'
                    });
                    return;
                }
                throw new Error(err.message || 'Server error');
            });
        }
        return response.json();
    })
    .then(data => {
        loadingModal.hide();
        
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: data.message,
                timer: 3000,
                showConfirmButton: true,
                confirmButtonText: 'OK'
            }).then(() => {
                // Reload the page to show updated attendance
                window.location.reload();
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to record attendance',
                confirmButtonText: 'OK'
            });
        }
    })
    .catch(error => {
        loadingModal.hide();
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: error.message || 'An error occurred while saving attendance.',
            confirmButtonText: 'OK'
        });
        console.error('Error:', error);
    });
});
}

// Auto-sync state
let autoSyncInterval = null;
let isAutoSyncEnabled = false;
const AUTO_SYNC_INTERVAL = 5000; // 5 seconds

// Toggle auto-sync on/off
function toggleAutoSync() {
    const serviceType = document.getElementById('service_type')?.value;
    const serviceId = document.getElementById('service_id')?.value;
    const selectedDate = document.getElementById('selected_service_date')?.value;
    
    if (!serviceId || !selectedDate) {
        Swal.fire({
            icon: 'warning',
            title: 'Service Not Selected',
            text: 'Please select a service before enabling auto-sync.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    if (serviceType !== 'sunday_service') {
        Swal.fire({
            icon: 'info',
            title: 'Auto-Sync Limited to Main Service',
            text: 'Auto-sync is currently available for Main Service attendance only.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    isAutoSyncEnabled = !isAutoSyncEnabled;
    const toggleBtn = document.getElementById('autoSyncToggle');
    const statusBadge = document.getElementById('autoSyncStatus');
    const syncText = document.getElementById('autoSyncText');
    
    if (isAutoSyncEnabled) {
        // Enable auto-sync
        toggleBtn.classList.remove('btn-outline-success');
        toggleBtn.classList.add('btn-success');
        syncText.textContent = 'Disable Auto-Sync';
        statusBadge.style.display = 'inline-block';
        
        // Start interval
        autoSyncInterval = setInterval(() => {
            syncFromDevice(true); // Silent mode - no confirmation dialog
        }, AUTO_SYNC_INTERVAL);
        
        // Do initial sync immediately
        syncFromDevice(true);
        
        console.log('Auto-sync enabled - syncing every 5 seconds');
    } else {
        // Disable auto-sync
        toggleBtn.classList.remove('btn-success');
        toggleBtn.classList.add('btn-outline-success');
        syncText.textContent = 'Enable Auto-Sync';
        statusBadge.style.display = 'none';
        
        // Clear interval
        if (autoSyncInterval) {
            clearInterval(autoSyncInterval);
            autoSyncInterval = null;
        }
        
        console.log('Auto-sync disabled');
    }
}

// Clear auto-sync when page unloads or service changes
function clearAutoSync() {
    if (autoSyncInterval) {
        clearInterval(autoSyncInterval);
        autoSyncInterval = null;
    }
    isAutoSyncEnabled = false;
    const toggleBtn = document.getElementById('autoSyncToggle');
    const statusBadge = document.getElementById('autoSyncStatus');
    const syncText = document.getElementById('autoSyncText');
    
    if (toggleBtn) {
        toggleBtn.classList.remove('btn-success');
        toggleBtn.classList.add('btn-outline-success');
    }
    if (syncText) {
        syncText.textContent = 'Enable Auto-Sync';
    }
    if (statusBadge) {
        statusBadge.style.display = 'none';
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    
    // Add event listeners to checkboxes
    document.querySelectorAll('.member-checkbox, .child-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });
    
    // Clear auto-sync on page unload
    window.addEventListener('beforeunload', clearAutoSync);
    
    // Make sure toggle functions are accessible globally
    window.selectAll = selectAll;
    window.selectNone = selectNone;
    window.toggleSelection = toggleSelection;
    window.filterMembers = filterMembers;
    window.filterChildren = filterChildren;
    window.syncFromDevice = syncFromDevice;
    window.toggleAutoSync = toggleAutoSync;
    window.clearAutoSync = clearAutoSync;
    
    console.log('Attendance page initialized. Toggle functions ready.');
});

function syncFromDevice(silent = false) {
    const serviceType = document.getElementById('service_type')?.value;
    const serviceId = document.getElementById('service_id')?.value;
    const selectedDate = document.getElementById('selected_service_date')?.value;

    if (!serviceId || !selectedDate) {
        if (!silent) {
            Swal.fire({
                icon: 'warning',
                title: 'Service Not Selected',
                text: 'Please select a service before syncing from the device.',
                confirmButtonText: 'OK'
            });
        }
        return;
    }

    if (serviceType !== 'sunday_service') {
        if (!silent) {
            Swal.fire({
                icon: 'info',
                title: 'Sync Limited to Main Service',
                text: 'Biometric sync is currently available for Main Service attendance only.',
                confirmButtonText: 'OK'
            });
        }
        return;
    }

    // Show confirmation dialog only if not in silent mode
    if (!silent) {
        Swal.fire({
            icon: 'question',
            title: 'Sync From Device',
            text: `Do you want to fetch attendance from the biometric device for ${selectedDate}?`,
            showCancelButton: true,
            confirmButtonText: 'Yes, Sync Now',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }
            performSync(selectedDate, silent);
        });
    } else {
        // Silent mode - perform sync directly
        performSync(selectedDate, silent);
    }
}

function performSync(selectedDate, silent = false) {
    const loadingModal = new bootstrap.Modal(document.getElementById('loadingModal'));
    const loadingText = document.querySelector('#loadingModal .modal-body p');
    if (loadingText) {
        loadingText.textContent = silent ? 'Auto-syncing from device...' : 'Syncing attendance from device...';
    }
    if (!silent) {
        loadingModal.show();
    }

    // Use the route URL - ensure it's a valid URL
    // NOTE: Using /test-biometric-sync because php artisan serve doesn't use .htaccess
    // and the physical public/attendance directory intercepts /attendance/* routes
    let syncUrl = biometricSyncUrl || '/test-biometric-sync';
    
    // Ensure URL is absolute (starts with /)
    if (!syncUrl.startsWith('/') && !syncUrl.startsWith('http')) {
        syncUrl = '/' + syncUrl;
    }
    
    console.log('Syncing from URL:', syncUrl, 'Date:', selectedDate);
    
    fetch(syncUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: new URLSearchParams({
            date: selectedDate
        })
    })
    .then(async (response) => {
        // Handle 404 specifically
        if (response.status === 404) {
            if (!silent) {
                loadingModal.hide();
                Swal.fire({
                    icon: 'error',
                    title: 'Route Not Found (404)',
                    html: 'The sync route was not found.<br><br>' +
                          '<strong>Route attempted:</strong> ' + syncUrl + '<br><br>' +
                          '<strong>Possible causes:</strong><br>' +
                          '1. Route cache needs clearing<br>' +
                          '2. Middleware blocking access<br>' +
                          '3. User permissions issue<br><br>' +
                          '<small>Please refresh the page (Ctrl+F5) or contact administrator.</small>',
                    confirmButtonText: 'OK',
                    footer: '<a href="javascript:location.reload(true)">Click here to refresh</a>'
                });
            }
            console.error('404 Error - Route not found:', syncUrl);
            
            // If using test route and it fails, try the original route
            if (syncUrl === '/test-biometric-sync') {
                console.log('Test route failed, trying original route...');
                syncUrl = '/attendance/biometric-sync';
                // Retry with original route
                return performSync(selectedDate, silent);
            }
            
            return;
        }
        
        // Handle other HTTP errors
        if (!response.ok) {
            const errorText = await response.text().catch(() => 'Unknown error');
            console.error('HTTP Error:', response.status, errorText);
        }
        
        const data = await response.json().catch(() => ({}));
        if (!silent) {
            loadingModal.hide();
        }

        if (!response.ok || !data.success) {
            let errorMessage = data.message || 'Failed to sync attendance from device.';
            
            // Add troubleshooting tips
            if (errorMessage.includes('connect') || errorMessage.includes('timeout')) {
                errorMessage += '\n\nTroubleshooting:\n1. Check if device is powered on\n2. Verify IP address in config\n3. Check network connectivity';
            } else if (errorMessage.includes('No attendance records')) {
                errorMessage += '\n\nNote: This is normal if no members have marked attendance on the device yet.';
            }
            
            if (!silent) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sync Failed',
                    text: errorMessage,
                    confirmButtonText: 'OK',
                    width: '600px'
                });
            } else {
                console.warn('Auto-sync failed:', data.message);
            }
            return;
        }

        // Automatically check checkboxes for synced members and children (teenagers)
        const syncedMemberIds = data.synced_member_ids || [];
        const syncedChildIds = data.synced_child_ids || [];
        let checkedCount = 0;
        
        console.log('=== SYNC RESPONSE DATA ===');
        console.log('Full response:', data);
        console.log('synced_member_ids:', syncedMemberIds, '(type:', typeof syncedMemberIds, ', length:', syncedMemberIds.length, ')');
        console.log('synced_child_ids:', syncedChildIds, '(type:', typeof syncedChildIds, ', length:', syncedChildIds.length, ')');
        console.log('Checking checkboxes for synced members:', syncedMemberIds);
        console.log('Checking checkboxes for synced children (teenagers):', syncedChildIds);
        
        // Check member checkboxes
        syncedMemberIds.forEach(memberId => {
            // Try multiple ways to find the checkbox
            let checkbox = document.getElementById(`member_${memberId}`);
            
            // If not found by ID, try by value attribute
            if (!checkbox) {
                checkbox = document.querySelector(`input.member-checkbox[value="${memberId}"]`);
            }
            
            console.log(`Looking for checkbox for member ${memberId}:`, checkbox);
            
            if (checkbox) {
                // Always check the checkbox if member has attendance from device
                const wasChecked = checkbox.checked;
                checkbox.checked = true;
                
                if (!wasChecked) {
                checkedCount++;
                    console.log(`Checked checkbox for member ${memberId}`);
                } else {
                    console.log(`Checkbox for member ${memberId} was already checked`);
                }
                
                // Add or update visual indicator (badge) to show member was synced from device
                // Label is a sibling, not a parent, so we need to find it differently
                const label = document.querySelector(`label[for="member_${memberId}"]`);
                
                if (label) {
                    // Remove existing badge if any
                    const existingBadge = label.querySelector('.biometric-badge');
                    if (existingBadge) {
                        existingBadge.remove();
                    }
                    
                    // Check if badge already exists in the label content
                    if (!label.querySelector('.biometric-badge')) {
                        // Add new badge
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-success biometric-badge ms-2';
                    badge.innerHTML = '<i class="fas fa-fingerprint"></i> Device';
                    badge.title = 'Synced from biometric device';
                    label.appendChild(badge);
                        console.log(`Added badge for member ${memberId}`);
                    }
                } else {
                    console.warn(`Could not find label for member ${memberId}`);
                }
            } else {
                console.error(`Checkbox not found for member ${memberId}. Available checkboxes:`, 
                    Array.from(document.querySelectorAll('.member-checkbox')).map(cb => ({
                        id: cb.id,
                        value: cb.value,
                        name: cb.closest('.member-item')?.querySelector('strong')?.textContent || 'Unknown'
                    }))
                );
                
                // Try to find by member name or other attributes
                console.warn(`Attempting alternative methods to find checkbox for member ${memberId}`);
            }
        });
        
        // Check child (teenager) checkboxes
        console.log('=== PROCESSING CHILD CHECKBOXES ===');
        console.log('Total child IDs to process:', syncedChildIds.length);
        console.log('Child IDs:', syncedChildIds);
        
        // Define function to process child checkboxes (must be defined before calling)
        const processChildCheckboxes = function() {
            // First, log all available child checkboxes on the page
            const allChildCheckboxes = Array.from(document.querySelectorAll('.child-checkbox'));
            console.log('Available child checkboxes on page:', allChildCheckboxes.length);
            console.log('Child checkbox details:', allChildCheckboxes.map(cb => ({
                id: cb.id,
                value: cb.value,
                name: cb.closest('.child-item')?.querySelector('strong')?.textContent || 'Unknown',
                visible: cb.offsetParent !== null,
                parentVisible: cb.closest('.child-item')?.offsetParent !== null
            })));
            
            syncedChildIds.forEach(childId => {
            console.log(`\n--- Processing child ID: ${childId} (type: ${typeof childId}) ---`);
            
            // Try multiple ways to find the checkbox
            let checkbox = document.getElementById(`child_${childId}`);
            console.log(`  Checkbox by ID "child_${childId}":`, checkbox);
            
            // If not found by ID, try by value attribute
            if (!checkbox) {
                checkbox = document.querySelector(`input.child-checkbox[value="${childId}"]`);
                console.log(`  Checkbox by value "${childId}":`, checkbox);
            }
            
            // Also try as string
            if (!checkbox) {
                checkbox = document.querySelector(`input.child-checkbox[value="${String(childId)}"]`);
                console.log(`  Checkbox by value (string) "${String(childId)}":`, checkbox);
            }
            
            // Also try as integer
            if (!checkbox) {
                checkbox = document.querySelector(`input.child-checkbox[value="${parseInt(childId)}"]`);
                console.log(`  Checkbox by value (int) "${parseInt(childId)}":`, checkbox);
            }
            
            console.log(`  Final checkbox found:`, checkbox);
            
            if (checkbox) {
                // Check if checkbox is visible
                const isVisible = checkbox.offsetParent !== null;
                console.log(`  Checkbox visible:`, isVisible);
                
                // Always check the checkbox if child has attendance from device
                const wasChecked = checkbox.checked;
                checkbox.checked = true;
                
                if (!wasChecked) {
                    checkedCount++;
                    console.log(`  ✅ Checked checkbox for child (teenager) ${childId}`);
                } else {
                    console.log(`  ℹ️ Checkbox for child ${childId} was already checked`);
                }
                
                // Add or update visual indicator (badge) to show child was synced from device
                const label = document.querySelector(`label[for="child_${childId}"]`);
                console.log(`  Label found:`, label);
                
                if (label) {
                    const existingBadge = label.querySelector('.biometric-badge');
                    if (existingBadge) {
                        existingBadge.remove();
                        console.log(`  Removed existing badge`);
                    }
                    
                    // Check if badge already exists in the label content
                    if (!label.querySelector('.biometric-badge')) {
                        // Add new badge
                        const badge = document.createElement('span');
                        badge.className = 'badge bg-success biometric-badge ms-2';
                        badge.innerHTML = '<i class="fas fa-fingerprint"></i> Device';
                        badge.title = 'Synced from biometric device';
                        label.appendChild(badge);
                        console.log(`  ✅ Added badge for child (teenager) ${childId}`);
                    } else {
                        console.log(`  Badge already exists`);
                    }
                } else {
                    console.warn(`  ⚠️ Could not find label for child ${childId}`);
                }
            } else {
                console.error(`  ❌ Checkbox not found for child ${childId}`);
                console.error(`  Available child checkboxes:`, 
                    Array.from(document.querySelectorAll('.child-checkbox')).map(cb => ({
                        id: cb.id,
                        value: cb.value,
                        valueType: typeof cb.value,
                        name: cb.closest('.child-item')?.querySelector('strong')?.textContent || 'Unknown'
                    }))
                );
            }
            });
            
            console.log('=== FINISHED PROCESSING CHILD CHECKBOXES ===');
        };
        
        // If we have child IDs to process, ensure children tab is visible/active
        if (syncedChildIds.length > 0) {
            // Check if we're in children_service (children are always visible)
            const isChildrenService = document.querySelector('#service_type')?.value === 'children_service';
            
            // If not children_service, we need to activate the children tab
            if (!isChildrenService) {
                const childrenTab = document.querySelector('#children-tab');
                const childrenTabPane = document.querySelector('#children');
                
                if (childrenTab && childrenTabPane) {
                    // Activate the children tab using Bootstrap
                    const tab = new bootstrap.Tab(childrenTab);
                    tab.show();
                    console.log('Activated children tab to show teenager checkboxes');
                    
                    // Wait a bit for the tab to be fully shown before processing checkboxes
                    setTimeout(() => {
                        processChildCheckboxes();
                    }, 100);
                } else {
                    console.warn('Children tab not found - children checkboxes might be hidden');
                    // Process anyway - checkboxes might still be in DOM even if hidden
                    processChildCheckboxes();
                }
            } else {
                // Children service - children are always visible, process immediately
                processChildCheckboxes();
            }
        } else {
            console.log('No child IDs to process - skipping child checkbox processing');
        }
        
        const totalSynced = syncedMemberIds.length + syncedChildIds.length;
        console.log(`Total checkboxes checked: ${checkedCount} out of ${totalSynced} synced (${syncedMemberIds.length} members + ${syncedChildIds.length} teenagers)`);
        
        // Update selected count
        updateSelectedCount();
        
        // Show success message only if not in silent mode
        if (!silent) {
            const message = checkedCount > 0 
                ? `${data.message}\n\n${checkedCount} member(s)/teenager(s) automatically checked from device.`
                : data.message || 'Attendance synced successfully from device.';
            
            Swal.fire({
                icon: 'success',
                title: 'Sync Complete',
                text: message,
                confirmButtonText: 'OK'
            });
        } else if (checkedCount > 0) {
            // Silent mode - just log to console
            console.log(`Auto-sync: ${checkedCount} member(s) checked from device`);
        }
    })
    .catch((error) => {
        if (!silent) {
            loadingModal.hide();
        }
        
        let errorMessage = error.message || 'An unexpected error occurred during sync.';
        
        // Check if it's a 404 error
        if (errorMessage.includes('404') || errorMessage.includes('Not Found')) {
            errorMessage = 'Route not found (404). Please refresh the page. If the issue persists, check that you are logged in and have the correct permissions.';
        }
        
        if (!silent) {
            Swal.fire({
                icon: 'error',
                title: 'Sync Error',
                text: errorMessage,
                confirmButtonText: 'OK',
                footer: '<small>URL: ' + syncUrl + '</small>'
            });
        }
        console.error('Biometric sync error:', error);
        console.error('Sync URL:', syncUrl);
        console.error('Full error:', error);
    });
}

// ============================================
// TEMPORARY: Biometric Device Test Functions
// ============================================

function toggleBiometricTest() {
    const content = document.getElementById('biometricTestContent');
    const icon = document.getElementById('biometricTestToggleIcon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    } else {
        content.style.display = 'none';
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    }
}

function getBiometricTestData() {
    return {
        ip: document.getElementById('test_ip').value,
        port: parseInt(document.getElementById('test_port').value),
        password: document.getElementById('test_password').value ? parseInt(document.getElementById('test_password').value) : 0
    };
}

function showBiometricTestLoading(show) {
    const el = document.getElementById('biometricTestLoading');
    if (el) {
        el.style.display = show ? 'block' : 'none';
    }
}

function showBiometricTestResult(success, message, data = null) {
    const resultDiv = document.getElementById('biometricTestResult');
    if (!resultDiv) return;
    
    resultDiv.style.display = 'block';
    
    let alertClass = success ? 'alert-success' : 'alert-danger';
    let icon = success ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
    
    let content = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">`;
    content += `${icon} <strong>${success ? 'Success' : 'Error'}</strong><br>`;
    content += message;
    content += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
    content += '</div>';
    
    if (data) {
        content += '<div class="mt-2"><small><strong>Response:</strong></small>';
        content += '<pre class="bg-light p-2 rounded small" style="max-height: 200px; overflow-y: auto; font-size: 0.75rem;">';
        content += JSON.stringify(data, null, 2);
        content += '</pre></div>';
    }
    
    resultDiv.innerHTML = content;
}

async function testBiometricConnection() {
    showBiometricTestLoading(true);
    const resultDiv = document.getElementById('biometricTestResult');
    if (resultDiv) {
        resultDiv.style.display = 'none';
    }
    
    try {
        const formData = getBiometricTestData();
        const response = await fetch('{{ route("biometric.test-connection") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        showBiometricTestResult(result.success, result.message || (result.success ? 'Connection successful!' : 'Connection failed.'), result.device_info || result);
    } catch (error) {
        showBiometricTestResult(false, 'Error: ' + error.message);
    } finally {
        showBiometricTestLoading(false);
    }
}

async function testBiometricDeviceInfo() {
    showBiometricTestLoading(true);
    const resultDiv = document.getElementById('biometricTestResult');
    if (resultDiv) {
        resultDiv.style.display = 'none';
    }
    
    try {
        const formData = getBiometricTestData();
        const response = await fetch('{{ route("biometric.device-info") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        const message = result.success 
            ? 'Device information retrieved successfully!' 
            : (result.message || 'Failed to get device information.');
        showBiometricTestResult(result.success, message, result);
    } catch (error) {
        showBiometricTestResult(false, 'Error: ' + error.message);
    } finally {
        showBiometricTestLoading(false);
    }
}

async function testBiometricAttendance() {
    showBiometricTestLoading(true);
    const resultDiv = document.getElementById('biometricTestResult');
    if (resultDiv) {
        resultDiv.style.display = 'none';
    }
    
    try {
        const formData = getBiometricTestData();
        const response = await fetch('{{ route("biometric.attendance") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        const message = result.success 
            ? `Retrieved ${result.count || 0} attendance record(s) from device` 
            : (result.message || 'Failed to get attendance records.');
        showBiometricTestResult(result.success, message, result);
    } catch (error) {
        showBiometricTestResult(false, 'Error: ' + error.message);
    } finally {
        showBiometricTestLoading(false);
    }
}

async function testBiometricUsers() {
    showBiometricTestLoading(true);
    const resultDiv = document.getElementById('biometricTestResult');
    if (resultDiv) {
        resultDiv.style.display = 'none';
    }
    
    try {
        const formData = getBiometricTestData();
        const response = await fetch('{{ route("biometric.users") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const result = await response.json();
        const message = result.success 
            ? `Retrieved ${result.count || 0} user(s) from device` 
            : (result.message || 'Failed to get users.');
        showBiometricTestResult(result.success, message, result);
    } catch (error) {
        showBiometricTestResult(false, 'Error: ' + error.message);
    } finally {
        showBiometricTestLoading(false);
    }
}

// Register a member to the biometric device (Testing Mode - by name)
async function registerMemberToDevice() {
    const memberName = document.getElementById('register_member_name').value.trim();
    const resultDiv = document.getElementById('registerMemberResult');
    
    if (!memberName) {
        if (resultDiv) {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<div class="alert alert-warning">Please enter a member name</div>';
        }
        return;
    }

    // First, search for the member by name (case-insensitive, flexible matching)
    let memberId = null;
    let foundMember = null;
    try {
        const searchResponse = await fetch(`{{ route("biometric.search-members") }}?q=${encodeURIComponent(memberName)}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const searchResult = await searchResponse.json();
        
        if (searchResult.success && searchResult.members && searchResult.members.length > 0) {
            // Try exact match first (case-insensitive, ignore extra spaces)
            const normalizedInput = memberName.toLowerCase().trim().replace(/\s+/g, ' ');
            const exactMatch = searchResult.members.find(m => {
                const normalizedName = m.name.toLowerCase().trim().replace(/\s+/g, ' ');
                return normalizedName === normalizedInput;
            });
            
            if (exactMatch) {
                memberId = exactMatch.id;
                foundMember = exactMatch;
            } else {
                // Use the first matching member if no exact match
                memberId = searchResult.members[0].id;
                foundMember = searchResult.members[0];
            }
        } else {
            // Show helpful error with suggestions
            let errorMsg = `<strong>Member "${memberName}" not found.</strong>`;
            errorMsg += '<br><small class="text-muted">';
            errorMsg += 'Please check the spelling. The search is case-insensitive and handles extra spaces.';
            errorMsg += '<br>Make sure the member exists in your members list.';
            errorMsg += '</small>';
            
            if (resultDiv) {
                resultDiv.style.display = 'block';
                resultDiv.innerHTML = `<div class="alert alert-warning">${errorMsg}</div>`;
            }
            return;
        }
    } catch (error) {
        if (resultDiv) {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `<div class="alert alert-danger">Error searching for member: ${error.message}</div>`;
        }
        return;
    }
    
    if (!memberId) {
        if (resultDiv) {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = '<div class="alert alert-warning">Could not find member. Please check the name.</div>';
        }
        return;
    }

    showBiometricTestLoading(true);
    if (resultDiv) {
        resultDiv.style.display = 'none';
    }

    try {
        const formData = getBiometricTestData();
        formData.member_id = parseInt(memberId);

        const response = await fetch('{{ route("biometric.register-member") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const result = await response.json();
        
        if (resultDiv) {
            resultDiv.style.display = 'block';
            let alertClass = result.success ? 'alert-success' : 'alert-danger';
            let icon = result.success ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-exclamation-circle"></i>';
            
            let message = result.message || 'Registration completed';
            if (foundMember && foundMember.name !== memberName) {
                message += ` (Registered as: ${foundMember.name})`;
            }
            
            // Show detailed registration list if available
            if (result.success && result.registered && result.registered.length > 0) {
                message += '<br><br><strong>Registered to Device:</strong><ul class="mb-0 mt-2">';
                result.registered.forEach(function(item) {
                    message += `<li>${item}</li>`;
                });
                message += '</ul>';
            }
            
            // Show errors/warnings if any
            if (result.errors && result.errors.length > 0) {
                message += '<br><br><strong class="text-warning">⚠️ Warnings:</strong><ul class="mb-0 mt-2">';
                result.errors.forEach(function(error) {
                    message += `<li class="text-warning">${error}</li>`;
                });
                message += '</ul>';
            }
            
            // Add device registration confirmation
            if (result.success) {
                message += '<br><small class="text-success"><i class="fas fa-check"></i> All eligible family members registered to biometric device!</small>';
            }
            
            resultDiv.innerHTML = `<div class="alert ${alertClass}">
                ${icon} <strong>${result.success ? 'Success' : 'Error'}</strong><br>
                ${message}
                ${result.enroll_id ? `<br><small>Main Member Enroll ID: <strong>${result.enroll_id}</strong> (Use this ID on the device to enroll fingerprint)</small>` : ''}
                ${result.registered_count ? `<br><small>Total registered: <strong>${result.registered_count}</strong> person(s)</small>` : ''}
            </div>`;
        }

        if (result.success) {
            // For testing: Keep the name in the field for continuous registration
            // Just focus the input so user can type next name
            const nameInput = document.getElementById('register_member_name');
            if (nameInput) {
                // Clear the field for next entry
                nameInput.value = '';
                nameInput.focus();
            }
        }
    } catch (error) {
        if (resultDiv) {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `<div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> <strong>Error</strong><br>
                ${error.message}
            </div>`;
        }
    } finally {
        showBiometricTestLoading(false);
    }
}
</script>
@endsection
