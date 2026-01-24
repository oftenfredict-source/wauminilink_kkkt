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
                            <small class="text-muted">{{ $campus->name }} - {{ $service->service_date->format('F d, Y') }}</small>
                        </div>
                        <div>
                            <a href="{{ route('evangelism-leader.branch-services.show', $service->id) }}" class="btn btn-sm btn-outline-info me-2">
                                <i class="fas fa-eye me-1"></i> View Service
                            </a>
                            <a href="{{ route('evangelism-leader.branch-services.index') }}" class="btn btn-sm btn-outline-primary">
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
                    <h6 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Record Attendance</h6>
                </div>
                <div class="card-body">
                    @if(!$canRecordAttendance)
                    <!-- Time Restriction Warning -->
                    <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                        <i class="fas fa-exclamation-triangle fa-2x me-3"></i>
                        <div>
                            <strong>Attendance & Offering Recording Restricted</strong>
                            <p class="mb-0">{{ $timeRestrictionMessage }}</p>
                        </div>
                    </div>
                    @endif
                    
                    <form id="attendanceForm">
                        @csrf
                        <input type="hidden" name="service_type" value="sunday_service">
                        <input type="hidden" name="service_id" value="{{ $service->id }}">
                        <input type="hidden" id="canRecordAttendance" value="{{ $canRecordAttendance ? '1' : '0' }}">
                        <input type="hidden" id="timeRestrictionMessage" value="{{ $timeRestrictionMessage }}">
                        
                        <div class="mb-3">
                            <label class="form-label small">Service Information</label>
                            <div class="alert alert-info mb-0">
                                <strong>{{ $service->service_date->format('l, F d, Y') }}</strong><br>
                                @if($service->start_time)
                                    <small>Start Time: {{ \Carbon\Carbon::parse($service->start_time)->format('h:i A') }}</small><br>
                                @endif
                                @if($service->theme)
                                    <small>Theme: {{ $service->theme }}</small><br>
                                @endif
                                @if($service->preacher)
                                    <small>Preacher: {{ $service->preacher }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="attendance_date" class="form-label small">Attendance Date</label>
                            <input type="date" class="form-control form-control-sm" id="attendance_date" name="attendance_date" value="{{ $service->service_date->format('Y-m-d') }}">
                        </div>

                        <div class="mb-3">
                            <label for="member_ids" class="form-label small">Members <span class="text-danger">*</span></label>
                            <select class="form-select form-select-sm" id="member_ids" name="member_ids[]" multiple size="10" required>
                                @foreach($members as $member)
                                <option value="{{ $member->id }}" {{ $existingAttendance->contains('member_id', $member->id) ? 'selected' : '' }}>
                                    {{ $member->full_name }} ({{ $member->member_id ?? 'N/A' }})
                                    @if($member->community)
                                        - {{ $member->community->name }}
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Hold Ctrl/Cmd to select multiple members from all communities</small>
                        </div>

                        <div class="mb-3">
                            <label for="guests_count" class="form-label small">Guests Count</label>
                            <input type="number" class="form-control form-control-sm" id="guests_count" name="guests_count" value="{{ $service->guests_count ?? 0 }}" min="0">
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label small">Notes</label>
                            <textarea class="form-control form-control-sm" id="notes" name="notes" rows="2"></textarea>
                        </div>

                        <button type="submit" class="btn btn-sm btn-primary w-100" id="saveAttendanceBtn" @if(!$canRecordAttendance) disabled @endif>
                            <i class="fas fa-save me-1"></i> Record Attendance
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Existing Attendance -->
        <div class="col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white py-2">
                    <h6 class="mb-0"><i class="fas fa-list me-2"></i>Current Attendance</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><strong>Total Members:</strong></span>
                            <span class="badge bg-success fs-6">{{ $existingAttendance->count() }}</span>
                        </div>
                        @if($service->guests_count > 0)
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span><strong>Guests:</strong></span>
                            <span class="badge bg-secondary fs-6">{{ $service->guests_count }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span><strong>Total Attendance:</strong></span>
                            <span class="badge bg-primary fs-6">{{ $existingAttendance->count() + ($service->guests_count ?? 0) }}</span>
                        </div>
                    </div>

                    @if($existingAttendance->count() > 0)
                        <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                            @foreach($existingAttendance as $attendance)
                            <div class="list-group-item px-0 py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>{{ $attendance->member->full_name ?? 'Unknown' }}</strong>
                                        @if($attendance->member && $attendance->member->community)
                                            <br><small class="text-muted">{{ $attendance->member->community->name }}</small>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ $attendance->attended_at->format('M d, Y') }}</small>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-user-times fa-2x mb-2"></i>
                            <p class="mb-0">No attendance recorded yet</p>
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
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Check if attendance can be recorded
        const canRecord = document.getElementById('canRecordAttendance')?.value === '1';
        if (!canRecord) {
            const message = document.getElementById('timeRestrictionMessage')?.value || 'Attendance cannot be recorded before the service start time.';
            Swal.fire({
                icon: 'warning',
                title: 'Time Restriction',
                text: message,
                confirmButtonText: 'OK'
            });
            return;
        }
        
        const formData = new FormData(form);
        
        // Filter out empty member IDs
        const memberIds = Array.from(form.querySelectorAll('#member_ids option:checked'))
            .map(option => option.value)
            .filter(id => id && id !== '');
        
        formData.delete('member_ids[]');
        memberIds.forEach(id => formData.append('member_ids[]', id));
        
        // Show loading
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Recording...';
        
        fetch('{{ route("evangelism-leader.branch-services.attendance.record", $service->id) }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || formData.get('_token')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to record attendance'
                });
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while recording attendance'
            });
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });
});
</script>
@endsection



