@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-rings-wedding me-2 text-primary"></i>Complete Church
                                    Wedding Details</h1>
                                <p class="text-muted mb-0">Groom: {{ $churchWeddingRequest->groom_full_name }} | Bride:
                                    {{ $churchWeddingRequest->bride_full_name }}</p>
                            </div>
                            <a href="{{ route('pastor.church-wedding-requests.show', $churchWeddingRequest->id) }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <form action="{{ route('pastor.church-wedding-requests.update', $churchWeddingRequest->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-friends me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="groom_date_of_birth" class="form-label">Groom's Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('groom_date_of_birth') is-invalid @enderror"
                                id="groom_date_of_birth" name="groom_date_of_birth"
                                value="{{ old('groom_date_of_birth', $churchWeddingRequest->groom_date_of_birth ? $churchWeddingRequest->groom_date_of_birth->format('Y-m-d') : '') }}"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="bride_date_of_birth" class="form-label">Bride's Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('bride_date_of_birth') is-invalid @enderror"
                                id="bride_date_of_birth" name="bride_date_of_birth"
                                value="{{ old('bride_date_of_birth', $churchWeddingRequest->bride_date_of_birth ? $churchWeddingRequest->bride_date_of_birth->format('Y-m-d') : '') }}"
                                required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Membership & Spiritual Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-heart me-2"></i>Membership & Spiritual Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Are both baptized? <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="both_baptized" id="both_baptized_yes"
                                    value="1" {{ old('both_baptized', $churchWeddingRequest->both_baptized) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="both_baptized_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="both_baptized" id="both_baptized_no"
                                    value="0" {{ old('both_baptized', $churchWeddingRequest->both_baptized) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="both_baptized_no">No</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Are both confirmed? <span class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="both_confirmed" id="both_confirmed_yes"
                                    value="1" {{ old('both_confirmed', $churchWeddingRequest->both_confirmed) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="both_confirmed_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="both_confirmed" id="both_confirmed_no"
                                    value="0" {{ old('both_confirmed', $churchWeddingRequest->both_confirmed) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="both_confirmed_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="membership_duration" class="form-label">Church membership duration</label>
                            <input type="text" class="form-control" id="membership_duration" name="membership_duration"
                                value="{{ old('membership_duration', $churchWeddingRequest->membership_duration) }}"
                                placeholder="e.g., 1 year, 3 years">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="pastor_catechist_name" class="form-label">Pastor / Catechist in charge</label>
                            <input type="text" class="form-control" id="pastor_catechist_name" name="pastor_catechist_name"
                                value="{{ old('pastor_catechist_name', $churchWeddingRequest->pastor_catechist_name) }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Wedding Details -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Wedding Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="preferred_wedding_date" class="form-label">Confirmed Wedding Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('preferred_wedding_date') is-invalid @enderror"
                                id="preferred_wedding_date" name="preferred_wedding_date"
                                value="{{ old('preferred_wedding_date', $churchWeddingRequest->preferred_wedding_date ? $churchWeddingRequest->preferred_wedding_date->format('Y-m-d') : '') }}"
                                required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="preferred_church" class="form-label">Wedding Location / Church</label>
                            <input type="text" class="form-control" id="preferred_church" name="preferred_church"
                                value="{{ old('preferred_church', $churchWeddingRequest->preferred_church) }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="expected_guests" class="form-label">Expected Guests</label>
                            <input type="number" class="form-control" id="expected_guests" name="expected_guests"
                                value="{{ old('expected_guests', $churchWeddingRequest->expected_guests) }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Attended premarital counseling? <span
                                    class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="attended_premarital_counseling"
                                    id="premarital_yes" value="1" {{ old('attended_premarital_counseling', $churchWeddingRequest->attended_premarital_counseling) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="premarital_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="attended_premarital_counseling"
                                    id="premarital_no" value="0" {{ old('attended_premarital_counseling', $churchWeddingRequest->attended_premarital_counseling) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="premarital_no">No</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pastor's Notes -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-sticky-note me-2"></i>Pastor's Notes</h5>
                </div>
                <div class="card-body">
                    <textarea class="form-control" name="pastor_comments" rows="3"
                        placeholder="Enter findings from the meeting or instructions...">{{ old('pastor_comments', $churchWeddingRequest->pastor_comments) }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('pastor.church-wedding-requests.show', $churchWeddingRequest->id) }}"
                    class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-save me-1"></i> Save Wedding Details
                </button>
            </div>
        </form>
    </div>
@endsection