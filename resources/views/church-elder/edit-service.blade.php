<form id="editServiceForm">
    @csrf
    @method('PUT')
    <div class="row g-2 mb-2">
        <div class="col-md-3">
            <label for="edit_service_date" class="form-label small">Service Date <span class="text-danger">*</span></label>
            <input type="date" name="service_date" id="edit_service_date" class="form-control form-control-sm" value="{{ $service->service_date->format('Y-m-d') }}" required>
        </div>
        <div class="col-md-3">
            <label for="edit_service_type" class="form-label small">Service Type <span class="text-danger">*</span></label>
            <select name="service_type" id="edit_service_type" class="form-select form-select-sm" required>
                <option value="sunday_service" {{ $service->service_type === 'sunday_service' ? 'selected' : '' }}>Sunday Service</option>
                <option value="prayer_meeting" {{ $service->service_type === 'prayer_meeting' ? 'selected' : '' }}>Prayer Meeting</option>
                <option value="bible_study" {{ $service->service_type === 'bible_study' ? 'selected' : '' }}>Bible Study</option>
                <option value="youth_service" {{ $service->service_type === 'youth_service' ? 'selected' : '' }}>Youth Service</option>
                <option value="children_service" {{ $service->service_type === 'children_service' ? 'selected' : '' }}>Children Service</option>
                <option value="women_fellowship" {{ $service->service_type === 'women_fellowship' ? 'selected' : '' }}>Women Fellowship</option>
                <option value="men_fellowship" {{ $service->service_type === 'men_fellowship' ? 'selected' : '' }}>Men Fellowship</option>
                <option value="evangelism" {{ $service->service_type === 'evangelism' ? 'selected' : '' }}>Evangelism</option>
                <option value="special_event" {{ $service->service_type === 'special_event' ? 'selected' : '' }}>Special Event</option>
                <option value="conference" {{ $service->service_type === 'conference' ? 'selected' : '' }}>Conference</option>
                <option value="retreat" {{ $service->service_type === 'retreat' ? 'selected' : '' }}>Retreat</option>
                <option value="other" {{ $service->service_type === 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div>
        <div class="col-md-3">
            <label for="edit_start_time" class="form-label small">Start Time</label>
            <input type="time" name="start_time" id="edit_start_time" class="form-control form-control-sm" value="{{ $service->start_time }}">
        </div>
        <div class="col-md-3">
            <label for="edit_end_time" class="form-label small">End Time</label>
            <input type="time" name="end_time" id="edit_end_time" class="form-control form-control-sm" value="{{ $service->end_time }}">
        </div>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-md-4">
            <label for="edit_theme" class="form-label small">Theme</label>
            <input type="text" name="theme" id="edit_theme" class="form-control form-control-sm" maxlength="255" value="{{ $service->theme }}">
        </div>
        <div class="col-md-4">
            <label for="edit_preacher" class="form-label small">Preacher</label>
            <input type="text" name="preacher" id="edit_preacher" class="form-control form-control-sm" maxlength="255" value="{{ $service->preacher }}">
        </div>
        <div class="col-md-4">
            <label for="edit_venue" class="form-label small">Venue</label>
            <input type="text" name="venue" id="edit_venue" class="form-control form-control-sm" maxlength="255" value="{{ $service->venue }}">
        </div>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-md-4">
            <label for="edit_coordinator_id" class="form-label small">Coordinator</label>
            <select name="coordinator_id" id="edit_coordinator_id" class="form-select form-select-sm">
                <option value="">Select...</option>
                @foreach($members as $member)
                    <option value="{{ $member->id }}" {{ $service->coordinator_id == $member->id ? 'selected' : '' }}>{{ $member->full_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label for="edit_attendance_count" class="form-label small">Attendance</label>
            <input type="number" name="attendance_count" id="edit_attendance_count" class="form-control form-control-sm" min="0" value="{{ $service->attendance_count ?? 0 }}">
        </div>
        <div class="col-md-2">
            <label for="edit_guests_count" class="form-label small">Guests</label>
            <input type="number" name="guests_count" id="edit_guests_count" class="form-control form-control-sm" min="0" value="{{ $service->guests_count ?? 0 }}">
        </div>
        <div class="col-md-4">
            <label for="edit_offerings_amount" class="form-label small">Offerings (TZS)</label>
            <input type="number" name="offerings_amount" id="edit_offerings_amount" class="form-control form-control-sm" step="0.01" min="0" value="{{ $service->offerings_amount ?? 0 }}">
        </div>
    </div>

    <div class="row g-2 mb-2">
        <div class="col-md-6">
            <label for="edit_scripture_readings" class="form-label small">Scripture Readings</label>
            <textarea name="scripture_readings" id="edit_scripture_readings" class="form-control form-control-sm" rows="2">{{ $service->scripture_readings }}</textarea>
        </div>
        <div class="col-md-6">
            <label for="edit_announcements" class="form-label small">Announcements</label>
            <textarea name="announcements" id="edit_announcements" class="form-control form-control-sm" rows="2">{{ $service->announcements }}</textarea>
        </div>
    </div>

    <div class="mb-2">
        <label for="edit_notes" class="form-label small">Notes</label>
        <textarea name="notes" id="edit_notes" class="form-control form-control-sm" rows="2">{{ $service->notes }}</textarea>
    </div>

    <div class="d-flex justify-content-end gap-2 mt-3">
        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-sm btn-primary">
            <i class="fas fa-save me-1"></i>Update Service
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editServiceForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
        
        try {
            const response = await fetch(`{{ url("church-elder/community/{$community->id}/services") }}/{{ $service->id }}`, {
                method: 'PUT',
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
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editServiceModal'));
                    modal.hide();
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to update service.'
                });
            }
        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while updating the service.'
            });
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
});
</script>

