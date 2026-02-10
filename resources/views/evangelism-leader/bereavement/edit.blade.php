@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex align-items-center justify-content-between mt-4 mb-3">
        <h2 class="mb-0">Edit Bereavement Event</h2>
        <a href="{{ route('evangelism-leader.bereavement.show', $bereavement->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Details
        </a>
    </div>

    @if(isset($campus))
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Editing bereavement event for <strong>{{ $campus->name }}</strong> branch.
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
            <form action="{{ route('evangelism-leader.bereavement.update', $bereavement->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Deceased Name / Affected Family <span class="text-danger">*</span></label>
                        <input type="text" name="deceased_name" class="form-control @error('deceased_name') is-invalid @enderror" value="{{ old('deceased_name', $bereavement->deceased_name) }}" required>
                        @error('deceased_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Incident Date <span class="text-danger">*</span></label>
                        <input type="date" name="incident_date" class="form-control @error('incident_date') is-invalid @enderror" value="{{ old('incident_date', $bereavement->incident_date->format('Y-m-d')) }}" required>
                        @error('incident_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Family Details</label>
                    <textarea name="family_details" class="form-control @error('family_details') is-invalid @enderror" rows="3" placeholder="Additional family information...">{{ old('family_details', $bereavement->family_details) }}</textarea>
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
                            <option value="{{ $community->id }}" {{ old('community_id', $bereavement->community_id) == $community->id ? 'selected' : '' }}>
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
                        <input type="text" name="related_departments" class="form-control @error('related_departments') is-invalid @enderror" value="{{ old('related_departments', $bereavement->related_departments) }}" placeholder="e.g., Youth, Women, Men, Choir">
                        @error('related_departments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contribution Start Date <span class="text-danger">*</span></label>
                        <input type="date" name="contribution_start_date" class="form-control @error('contribution_start_date') is-invalid @enderror" value="{{ old('contribution_start_date', $bereavement->contribution_start_date->format('Y-m-d')) }}" required>
                        @error('contribution_start_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Contribution End Date <span class="text-danger">*</span></label>
                        <input type="date" name="contribution_end_date" class="form-control @error('contribution_end_date') is-invalid @enderror" value="{{ old('contribution_end_date', $bereavement->contribution_end_date->format('Y-m-d')) }}" required>
                        @error('contribution_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3" placeholder="Additional notes...">{{ old('notes', $bereavement->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Update Event
                    </button>
                    <a href="{{ route('evangelism-leader.bereavement.show', $bereavement->id) }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection






