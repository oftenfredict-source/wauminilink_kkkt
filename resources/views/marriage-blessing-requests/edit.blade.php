@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-edit me-2 text-primary"></i>Complete Marriage Blessing
                                    Details</h1>
                                <p class="text-muted mb-0">Husband: {{ $marriageBlessingRequest->husband_full_name }} |
                                    Wife: {{ $marriageBlessingRequest->wife_full_name }}</p>
                            </div>
                            <a href="{{ route('pastor.marriage-blessing-requests.show', $marriageBlessingRequest->id) }}"
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

        <form action="{{ route('pastor.marriage-blessing-requests.update', $marriageBlessingRequest->id) }}" method="POST">
            @csrf
            @method('PATCH')

            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <h6 class="mb-3">Husband's Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="husband_full_name" class="form-label">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="husband_full_name" name="husband_full_name"
                                value="{{ old('husband_full_name', $marriageBlessingRequest->husband_full_name) }}" required
                                readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="husband_date_of_birth" class="form-label">Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('husband_date_of_birth') is-invalid @enderror"
                                id="husband_date_of_birth" name="husband_date_of_birth"
                                value="{{ old('husband_date_of_birth', $marriageBlessingRequest->husband_date_of_birth ? $marriageBlessingRequest->husband_date_of_birth->format('Y-m-d') : '') }}"
                                max="{{ date('Y-m-d', strtotime('-18 years')) }}" required>
                            @error('husband_date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr class="my-4">

                    <h6 class="mb-3">Wife's Information</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="wife_full_name" class="form-label">Full Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="wife_full_name" name="wife_full_name"
                                value="{{ old('wife_full_name', $marriageBlessingRequest->wife_full_name) }}" required
                                readonly>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="wife_date_of_birth" class="form-label">Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('wife_date_of_birth') is-invalid @enderror"
                                id="wife_date_of_birth" name="wife_date_of_birth"
                                value="{{ old('wife_date_of_birth', $marriageBlessingRequest->wife_date_of_birth ? $marriageBlessingRequest->wife_date_of_birth->format('Y-m-d') : '') }}"
                                max="{{ date('Y-m-d', strtotime('-18 years')) }}" required>
                            @error('wife_date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marriage Blessing Schedule -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Marriage Blessing Schedule</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="marriage_date" class="form-label">Scheduled Blessing Date <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('marriage_date') is-invalid @enderror"
                                id="marriage_date" name="marriage_date"
                                value="{{ old('marriage_date', $marriageBlessingRequest->marriage_date ? $marriageBlessingRequest->marriage_date->format('Y-m-d') : '') }}"
                                min="{{ date('Y-m-d') }}" required>
                            @error('marriage_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Date when the marriage blessing ceremony will take place</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="place_of_marriage" class="form-label">Blessing Venue <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('place_of_marriage') is-invalid @enderror"
                                id="place_of_marriage" name="place_of_marriage"
                                value="{{ old('place_of_marriage', $marriageBlessingRequest->place_of_marriage) }}"
                                placeholder="Church or venue for the blessing" required>
                            @error('place_of_marriage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="marriage_certificate_number" class="form-label">Civil Marriage Certificate Number
                                (optional)</label>
                            <input type="text" class="form-control" id="marriage_certificate_number"
                                name="marriage_certificate_number"
                                value="{{ old('marriage_certificate_number', $marriageBlessingRequest->marriage_certificate_number) }}"
                                placeholder="If already married civilly">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Church Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>Church Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Are both spouses church members? <span
                                    class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="both_spouses_members"
                                    id="both_members_yes" value="1" {{ old('both_spouses_members', $marriageBlessingRequest->both_spouses_members) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="both_members_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="both_spouses_members"
                                    id="both_members_no" value="0" {{ old('both_spouses_members', $marriageBlessingRequest->both_spouses_members) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="both_members_no">No</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Have they attended marriage counseling? <span
                                    class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="attended_marriage_counseling"
                                    id="counseling_yes" value="1" {{ old('attended_marriage_counseling', $marriageBlessingRequest->attended_marriage_counseling) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="counseling_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="attended_marriage_counseling"
                                    id="counseling_no" value="0" {{ old('attended_marriage_counseling', $marriageBlessingRequest->attended_marriage_counseling) == '0' ? 'checked' : '' }}>
                                <label class="form-check-label" for="counseling_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="membership_duration" class="form-label">Duration of church membership
                                (optional)</label>
                            <input type="text" class="form-control" id="membership_duration" name="membership_duration"
                                value="{{ old('membership_duration', $marriageBlessingRequest->membership_duration) }}"
                                placeholder="e.g., 2 years, 5 years">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Additional Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="reason_for_blessing" class="form-label">Reason for requesting marriage blessing <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('reason_for_blessing') is-invalid @enderror"
                            id="reason_for_blessing" name="reason_for_blessing" rows="4"
                            required>{{ old('reason_for_blessing', $marriageBlessingRequest->reason_for_blessing) }}</textarea>
                        @error('reason_for_blessing')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="pastor_comments" class="form-label">Pastor's Internal Comments / Notes</label>
                        <textarea class="form-control @error('pastor_comments') is-invalid @enderror" id="pastor_comments"
                            name="pastor_comments"
                            rows="3">{{ old('pastor_comments', $marriageBlessingRequest->pastor_comments) }}</textarea>
                        @error('pastor_comments')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('pastor.marriage-blessing-requests.show', $marriageBlessingRequest->id) }}"
                    class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-success px-4">
                    <i class="fas fa-save me-1"></i> Save All Details
                </button>
            </div>
        </form>
    </div>
@endsection