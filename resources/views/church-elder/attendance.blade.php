@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="fas fa-user-check me-2 text-primary"></i>Record Attendance</h5>
                            <small class="text-muted">{{ $community->name }}</small>
                        </div>
                        <div>
                            <a href="{{ route('church-elder.attendance.view', $community->id) }}" class="btn btn-sm btn-outline-info me-2">
                                <i class="fas fa-list me-1"></i> View
                            </a>
                            <a href="{{ route('church-elder.dashboard') }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Record Attendance Form -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white py-2">
                    <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Record New Attendance</h6>
                </div>
                <div class="card-body">
                    <form id="attendanceForm">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label for="service_id" class="form-label small">Service <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="service_id" name="service_id" required>
                                    <option value="">Select Service...</option>
                                    @foreach($recentServices as $service)
                                    <option value="{{ $service->id }}" 
                                        {{ request('service_id') == $service->id ? 'selected' : '' }}
                                        data-service-type="{{ $service->service_type ?? 'sunday_service' }}">
                                        {{ $service->service_date->format('M d, Y') }} - {{ ucfirst(str_replace('_', ' ', $service->service_type ?? 'Sunday Service')) }}
                                        @if($service->theme)
                                            ({{ $service->theme }})
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">
                                    <a href="{{ route('church-elder.services.create', $community->id) }}" class="text-primary small">
                                        <i class="fas fa-plus-circle me-1"></i>Create Service
                                    </a>
                                </small>
                            </div>
                            <div class="col-md-3">
                                <label for="service_type" class="form-label small">Service Type <span class="text-danger">*</span></label>
                                <select class="form-select form-select-sm" id="service_type" name="service_type" required>
                                    <option value="">Select Type...</option>
                                    <optgroup label="Mid-Week Services">
                                        <option value="prayer_meeting" {{ isset($selectedService) && $selectedService->service_type == 'prayer_meeting' ? 'selected' : '' }}>Prayer Meeting</option>
                                        <option value="bible_study" {{ isset($selectedService) && $selectedService->service_type == 'bible_study' ? 'selected' : '' }}>Bible Study</option>
                                        <option value="youth_service" {{ isset($selectedService) && $selectedService->service_type == 'youth_service' ? 'selected' : '' }}>Youth Service</option>
                                        <option value="women_fellowship" {{ isset($selectedService) && $selectedService->service_type == 'women_fellowship' ? 'selected' : '' }}>Women Fellowship</option>
                                        <option value="men_fellowship" {{ isset($selectedService) && $selectedService->service_type == 'men_fellowship' ? 'selected' : '' }}>Men Fellowship</option>
                                        <option value="evangelism" {{ isset($selectedService) && $selectedService->service_type == 'evangelism' ? 'selected' : '' }}>Evangelism</option>
                                    </optgroup>
                                    <optgroup label="Other Services">
                                        <option value="sunday_service" {{ isset($selectedService) && $selectedService->service_type == 'sunday_service' ? 'selected' : '' }}>Sunday Service</option>
                                        <option value="children_service" {{ isset($selectedService) && $selectedService->service_type == 'children_service' ? 'selected' : '' }}>Children Service</option>
                                        <option value="special_event" {{ isset($selectedService) && $selectedService->service_type == 'special_event' ? 'selected' : '' }}>Special Event</option>
                                        <option value="conference" {{ isset($selectedService) && $selectedService->service_type == 'conference' ? 'selected' : '' }}>Conference</option>
                                        <option value="retreat" {{ isset($selectedService) && $selectedService->service_type == 'retreat' ? 'selected' : '' }}>Retreat</option>
                                        <option value="other" {{ isset($selectedService) && $selectedService->service_type == 'other' ? 'selected' : '' }}>Other</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="attendance_date" class="form-label small">Date</label>
                                <input type="date" class="form-control form-control-sm" id="attendance_date" name="attendance_date" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label for="member_ids" class="form-label small">Members <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="member_ids" name="member_ids[]" multiple size="8" required>
                                @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->full_name }} ({{ $member->member_id ?? 'N/A' }})</option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple</small>
                        </div>
                        <div class="mb-2">
                            <label for="notes" class="form-label small">Notes</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-sm btn-primary w-100">
                            <i class="fas fa-save me-1"></i>Record Attendance
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Recent Attendance Records -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white py-2">
                    <h6 class="mb-0"><i class="fas fa-history me-2"></i>Recent Records</h6>
                </div>
                <div class="card-body">
                    @if($recentAttendances->count() > 0)
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover mb-0">
                                <thead class="sticky-top bg-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Member</th>
                                        <th>Service</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAttendances as $attendance)
                                    <tr>
                                        <td><small>{{ $attendance->attended_at->format('M d, Y') }}</small></td>
                                        <td><small>{{ $attendance->member->full_name ?? 'N/A' }}</small></td>
                                        <td>
                                            @if($attendance->sundayService)
                                                <small>{{ $attendance->sundayService->service_date->format('M d') }}</small>
                                            @else
                                                <small class="text-muted">N/A</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-user-check fa-2x text-muted mb-2"></i>
                            <p class="text-muted small mb-0">No attendance records yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('attendanceForm');
    const serviceSelect = document.getElementById('service_id');
    const serviceTypeSelect = document.getElementById('service_type');
    
    // Auto-fill service type when service is selected
    if (serviceSelect && serviceTypeSelect) {
        serviceSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.value && selectedOption.dataset.serviceType) {
                // Auto-select the service type from the service
                serviceTypeSelect.value = selectedOption.dataset.serviceType;
            }
        });
        
        // Trigger change if service is pre-selected (from URL parameter)
        if (serviceSelect.value) {
            serviceSelect.dispatchEvent(new Event('change'));
        }
    }
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const selectedOptions = document.getElementById('member_ids').selectedOptions;
        const memberIds = Array.from(selectedOptions)
            .map(option => option.value)
            .filter(id => id && id.trim() !== '' && !isNaN(id) && parseInt(id) > 0)
            .map(id => parseInt(id));
        
        if (memberIds.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Members Selected',
                text: 'Please select at least one valid member.'
            });
            return;
        }
        
        // Add member_ids as array (ensure they're integers)
        memberIds.forEach(id => {
            formData.append('member_ids[]', id);
        });
        
        // Debug: Log member IDs being sent
        console.log('Sending member IDs:', memberIds);
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Recording...';
        
        try {
            const response = await fetch('{{ route("church-elder.attendance.record", $community->id) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message + ' Total attendance: ' + data.attendance_count,
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to record attendance.'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while recording attendance.'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>
@endsection

