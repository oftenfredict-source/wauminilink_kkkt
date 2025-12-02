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
const biometricSyncUrl = "{{ route('attendance.biometric.sync') }}";

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
    
    // Submit form
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this),
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
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

    fetch(biometricSyncUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: new URLSearchParams({
            date: selectedDate
        })
    })
    .then(async (response) => {
        const data = await response.json().catch(() => ({}));
        if (!silent) {
            loadingModal.hide();
        }

        if (!response.ok || !data.success) {
            if (!silent) {
                Swal.fire({
                    icon: 'error',
                    title: 'Sync Failed',
                    text: data.message || 'Failed to sync attendance from device.',
                    confirmButtonText: 'OK'
                });
            } else {
                console.warn('Auto-sync failed:', data.message);
            }
            return;
        }

        // Automatically check checkboxes for synced members
        const syncedMemberIds = data.synced_member_ids || [];
        let checkedCount = 0;
        
        syncedMemberIds.forEach(memberId => {
            const checkbox = document.getElementById(`member_${memberId}`);
            if (checkbox && !checkbox.checked) {
                checkbox.checked = true;
                checkedCount++;
                
                // Add visual indicator (badge) to show member was synced from device
                const label = checkbox.closest('.form-check-label');
                if (label && !label.querySelector('.biometric-badge')) {
                    const badge = document.createElement('span');
                    badge.className = 'badge bg-success biometric-badge ms-2';
                    badge.innerHTML = '<i class="fas fa-fingerprint"></i> Device';
                    badge.title = 'Synced from biometric device';
                    label.appendChild(badge);
                }
            }
        });
        
        // Update selected count
        updateSelectedCount();
        
        // Show success message only if not in silent mode
        if (!silent) {
            const message = checkedCount > 0 
                ? `${data.message}\n\n${checkedCount} member(s) automatically checked from device.`
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
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: error.message || 'An unexpected error occurred during sync.',
                confirmButtonText: 'OK'
            });
        }
        console.error('Biometric sync error:', error);
    });
}
</script>
@endsection
