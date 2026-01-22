@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body py-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0"><i class="fas fa-church me-2 text-primary"></i>Create Service</h5>
                            <small class="text-muted">{{ $community->name }}</small>
                        </div>
                        <a href="{{ route('church-elder.services', $community->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <form id="serviceForm">
                        @csrf
                        <div class="row g-2 mb-2">
                            <div class="col-md-3">
                                <label for="service_date" class="form-label small">Service Date <span class="text-danger">*</span></label>
                                <input type="date" name="service_date" id="service_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="service_type" class="form-label small">Service Type <span class="text-danger">*</span></label>
                                <select name="service_type" id="service_type" class="form-select form-select-sm" required>
                                    <option value="sunday_service">Sunday Service</option>
                                    <option value="prayer_meeting">Prayer Meeting</option>
                                    <option value="bible_study">Bible Study</option>
                                    <option value="youth_service">Youth Service</option>
                                    <option value="children_service">Children Service</option>
                                    <option value="women_fellowship">Women Fellowship</option>
                                    <option value="men_fellowship">Men Fellowship</option>
                                    <option value="evangelism">Evangelism</option>
                                    <option value="special_event">Special Event</option>
                                    <option value="conference">Conference</option>
                                    <option value="retreat">Retreat</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="start_time" class="form-label small">Start Time</label>
                                <input type="time" name="start_time" id="start_time" class="form-control form-control-sm">
                            </div>
                            <div class="col-md-3">
                                <label for="end_time" class="form-label small">End Time</label>
                                <input type="time" name="end_time" id="end_time" class="form-control form-control-sm">
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label for="theme" class="form-label small">Theme</label>
                                <input type="text" name="theme" id="theme" class="form-control form-control-sm" maxlength="255">
                            </div>
                            <div class="col-md-4">
                                <label for="preacher" class="form-label small">Preacher</label>
                                <input type="text" name="preacher" id="preacher" class="form-control form-control-sm" maxlength="255">
                            </div>
                            <div class="col-md-4">
                                <label for="venue" class="form-label small">Venue</label>
                                <input type="text" name="venue" id="venue" class="form-control form-control-sm" maxlength="255">
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-md-4">
                                <label for="coordinator_id" class="form-label small">Coordinator</label>
                                <select name="coordinator_id" id="coordinator_id" class="form-select form-select-sm">
                                    <option value="">Select...</option>
                                    @foreach($members as $member)
                                        <option value="{{ $member->id }}">{{ $member->full_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="attendance_count" class="form-label small">Attendance</label>
                                <input type="number" name="attendance_count" id="attendance_count" class="form-control form-control-sm" min="0">
                            </div>
                            <div class="col-md-2">
                                <label for="guests_count" class="form-label small">Guests</label>
                                <input type="number" name="guests_count" id="guests_count" class="form-control form-control-sm" min="0">
                            </div>
                            <div class="col-md-4">
                                <label for="offerings_amount" class="form-label small">Offerings (TZS)</label>
                                <input type="number" name="offerings_amount" id="offerings_amount" class="form-control form-control-sm" step="0.01" min="0">
                            </div>
                        </div>

                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label for="scripture_readings" class="form-label small">Scripture Readings</label>
                                <textarea name="scripture_readings" id="scripture_readings" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="announcements" class="form-label small">Announcements</label>
                                <textarea name="announcements" id="announcements" class="form-control form-control-sm" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="notes" class="form-label small">Notes</label>
                            <textarea name="notes" id="notes" class="form-control form-control-sm" rows="2"></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <a href="{{ route('church-elder.services', $community->id) }}" class="btn btn-sm btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-sm btn-primary">
                                <i class="fas fa-save me-1"></i>Create Service
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('serviceForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating...';
        
        try {
            const response = await fetch('{{ route("church-elder.services.store", $community->id) }}', {
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
                    text: data.message,
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = '{{ route("church-elder.services", $community->id) }}';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to create service.'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while creating the service.'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>
@endsection

