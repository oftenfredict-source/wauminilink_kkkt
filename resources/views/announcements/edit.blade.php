@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2" style="width:48px; height:48px; background:rgba(0,123,255,.1);">
                                <i class="fas fa-edit text-primary"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold text-dark">Edit Announcement</h5>
                                <small class="text-muted">Update announcement details</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('announcements.update', $announcement) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $announcement->title) }}" 
                            class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Type <span class="text-danger">*</span></label>
                        <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="general" {{ old('type', $announcement->type) == 'general' ? 'selected' : '' }}>General</option>
                            <option value="urgent" {{ old('type', $announcement->type) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                            <option value="event" {{ old('type', $announcement->type) == 'event' ? 'selected' : '' }}>Event</option>
                            <option value="reminder" {{ old('type', $announcement->type) == 'reminder' ? 'selected' : '' }}>Reminder</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea name="content" rows="6" class="form-control @error('content') is-invalid @enderror" 
                            placeholder="Enter announcement content..." required>{{ old('content', $announcement->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" value="{{ old('start_date', $announcement->start_date?->format('Y-m-d')) }}" 
                            class="form-control @error('start_date') is-invalid @enderror">
                        <small class="text-muted">Leave empty for immediate publication</small>
                        @error('start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" value="{{ old('end_date', $announcement->end_date?->format('Y-m-d')) }}" 
                            class="form-control @error('end_date') is-invalid @enderror">
                        <small class="text-muted">Leave empty for no expiry</small>
                        @error('end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                {{ old('is_active', $announcement->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Active (Visible to members)
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_pinned" id="is_pinned" 
                                {{ old('is_pinned', $announcement->is_pinned) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_pinned">
                                Pin to top (Show at the top of announcements)
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-body">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="send_sms" id="send_sms" 
                                        {{ old('send_sms') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="send_sms">
                                        <i class="fas fa-sms text-info me-2"></i>Send SMS notification to members
                                    </label>
                                </div>
                                
                                <div id="sms_filters_section" style="{{ old('send_sms') ? '' : 'display:none;' }}">
                                    <hr>
                                    <h6 class="mb-3"><i class="fas fa-filter me-2 text-info"></i>SMS Recipient Filters (Optional)</h6>
                                    
                                    <div class="row g-3 mb-3">
                                        <div class="col-12">
                                            <label class="form-label small fw-bold text-primary">Specific Members</label>
                                            <select name="sms_member_ids[]" class="form-select select2-members" multiple="multiple" data-placeholder="Select specific members (optional)">
                                                @foreach($members as $member)
                                                    <option value="{{ $member->id }}" {{ in_array($member->id, old('sms_member_ids', [])) ? 'selected' : '' }}>
                                                        {{ $member->full_name }} ({{ $member->phone_number }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            <small class="text-muted d-block mt-1">If you select specific members, the general filters below will be ignored for those members.</small>
                                        </div>
                                    </div>
                                    
                                    <h6 class="mb-3 small text-muted border-bottom pb-2">Or Filter by Groups</h6>
                                    
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label small">Campus</label>
                                            <select name="sms_campus_id" class="form-select form-select-sm">
                                                <option value="">All Campuses</option>
                                                @foreach($campuses as $campus)
                                                    <option value="{{ $campus->id }}" {{ old('sms_campus_id') == $campus->id ? 'selected' : '' }}>
                                                        {{ $campus->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Community/Mtaa</label>
                                            <select name="sms_community_id" class="form-select form-select-sm">
                                                <option value="">All Communities</option>
                                                @foreach($communities as $community)
                                                    <option value="{{ $community->id }}" {{ old('sms_community_id') == $community->id ? 'selected' : '' }}>
                                                        {{ $community->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label small">Gender</label>
                                            <select name="sms_gender" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                <option value="male" {{ old('sms_gender') == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ old('sms_gender') == 'female' ? 'selected' : '' }}>Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Age Group</label>
                                            <select name="sms_age_group" class="form-select form-select-sm">
                                                <option value="">All Ages</option>
                                                <option value="adult" {{ old('sms_age_group') == 'adult' ? 'selected' : '' }}>Adults (18+)</option>
                                                <option value="child" {{ old('sms_age_group') == 'child' ? 'selected' : '' }}>Children (<18)</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small">Residence</label>
                                            <select name="sms_residence" class="form-select form-select-sm">
                                                <option value="">All</option>
                                                <option value="main_area" {{ old('sms_residence') == 'main_area' ? 'selected' : '' }}>Live in Main Area</option>
                                                <option value="outside" {{ old('sms_residence') == 'outside' ? 'selected' : '' }}>Live Outside Main Area</option>
                                            </select>
                                        </div>
                                    </div>
                                    <small class="text-info d-block mt-3">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Leave filters as "All" to send to all members.
                                    </small>
                                </div>

                                <small class="text-muted d-block mt-2" id="sms_info_default" style="{{ old('send_sms') ? 'display:none;' : '' }}">
                                    <i class="fas fa-info-circle me-1"></i>
                                    If checked, you can filter specific members to receive this announcement update via SMS.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('announcements.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sendSmsCheckbox = document.getElementById('send_sms');
        const smsFiltersSection = document.getElementById('sms_filters_section');
        const smsInfoDefault = document.getElementById('sms_info_default');

        if (sendSmsCheckbox) {
            sendSmsCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    smsFiltersSection.style.display = 'block';
                    smsInfoDefault.style.display = 'none';
                    // Initialize Select2 when section becomes visible
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 === 'function') {
                        $('.select2-members').select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: $(this).data('placeholder'),
                            allowClear: true
                        });
                    }
                } else {
                    smsFiltersSection.style.display = 'none';
                    smsInfoDefault.style.display = 'block';
                }
            });
            
            // Initial initialization if already checked
            if (sendSmsCheckbox.checked) {
                if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 === 'function') {
                    // Slight delay to ensure DOM is ready
                    setTimeout(function() {
                        $('.select2-members').select2({
                            theme: 'bootstrap-5',
                            width: '100%',
                            placeholder: $('.select2-members').data('placeholder'),
                            allowClear: true
                        });
                    }, 100);
                }
            }
        }
    });
</script>
@endsection

