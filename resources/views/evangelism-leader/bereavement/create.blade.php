@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h2 class="mb-0">Create Bereavement Event</h2>
        <a href="{{ route('evangelism-leader.bereavement.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    @if(isset($campus))
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Creating bereavement event for <strong>{{ $campus->name }}</strong> branch members only.
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('evangelism-leader.bereavement.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Deceased Name / Affected Family <span class="text-danger">*</span></label>
                        <input type="text" name="deceased_name" class="form-control @error('deceased_name') is-invalid @enderror" value="{{ old('deceased_name') }}" required>
                        @error('deceased_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Incident Date <span class="text-danger">*</span></label>
                        <input type="date" name="incident_date" class="form-control @error('incident_date') is-invalid @enderror" value="{{ old('incident_date') }}" required>
                        @error('incident_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Family Details</label>
                    <textarea name="family_details" class="form-control @error('family_details') is-invalid @enderror" rows="3" placeholder="Additional family information...">{{ old('family_details') }}</textarea>
                    @error('family_details')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Community</label>
                        <select name="community_id" class="form-select @error('community_id') is-invalid @enderror" id="communitySelect">
                            <option value="">All Communities (Branch-wide)</option>
                            @foreach($communities as $community)
                            <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                {{ $community->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('community_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Select a specific community or leave blank for branch-wide event</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Related Departments</label>
                        <input type="text" name="related_departments" class="form-control @error('related_departments') is-invalid @enderror" value="{{ old('related_departments') }}" placeholder="e.g., Youth, Women, Men, Choir">
                        @error('related_departments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contribution Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="contribution_start_date" class="form-control @error('contribution_start_date') is-invalid @enderror" value="{{ old('contribution_start_date') }}" required>
                        @error('contribution_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contribution End Date <span class="text-danger">*</span></label>
                        <input type="date" name="contribution_end_date" class="form-control @error('contribution_end_date') is-invalid @enderror" value="{{ old('contribution_end_date') }}" required>
                        @error('contribution_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Additional notes...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Members for Contribution Tracking</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllMembers" checked>
                        <label class="form-check-label" for="selectAllMembers">
                            Include all members from your branch (leave unchecked to select specific members)
                        </label>
                    </div>
                    <div id="memberSelection" class="mt-3" style="display: none;">
                        <select name="member_ids[]" class="form-select @error('member_ids') is-invalid @enderror" multiple size="10" id="memberSelect">
                            @foreach($members as $member)
                            <option value="{{ $member->id }}" data-community-id="{{ $member->community_id ?? '' }}">
                                {{ $member->full_name }} ({{ $member->member_id }})@if($member->community) - {{ $member->community->name }}@endif
                            </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Hold Ctrl/Cmd to select multiple members</small>
                        @error('member_ids')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="send_notifications" id="sendNotifications" value="1" {{ old('send_notifications', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="sendNotifications">
                            Send notifications to members about this bereavement event
                        </label>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Create Event
                    </button>
                    <a href="{{ route('evangelism-leader.bereavement.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('selectAllMembers').addEventListener('change', function() {
    document.getElementById('memberSelection').style.display = this.checked ? 'none' : 'block';
    if (this.checked) {
        document.getElementById('memberSelect').selectedIndex = -1;
    }
});

// Filter members by community when community is selected
document.getElementById('communitySelect').addEventListener('change', function() {
    const communityId = this.value;
    const memberSelect = document.getElementById('memberSelect');
    const options = memberSelect.options;
    
    for (let i = 0; i < options.length; i++) {
        const option = options[i];
        const memberCommunityId = option.getAttribute('data-community-id');
        
        if (!communityId || memberCommunityId === communityId || memberCommunityId === '') {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    }
});
</script>
@endsection

