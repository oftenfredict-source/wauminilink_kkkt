@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-heart me-2 text-primary"></i>Marriage Blessing Request</h1>
                            <p class="text-muted mb-0">Baraka ya Ndoa - Request blessing for your marriage</p>
                        </div>
                        <a href="{{ route('evangelism-leader.marriage-blessing-requests.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Requests
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Error:</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    <form action="{{ route('evangelism-leader.marriage-blessing-requests.store') }}" method="POST" id="blessingRequestForm">
        @csrf
        
        <!-- Couple Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>1. Couple Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="husband_full_name" class="form-label">Husband's Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('husband_full_name') is-invalid @enderror" 
                               id="husband_full_name" name="husband_full_name" value="{{ old('husband_full_name') }}" required>
                        @error('husband_full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="wife_full_name" class="form-label">Wife's Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('wife_full_name') is-invalid @enderror" 
                               id="wife_full_name" name="wife_full_name" value="{{ old('wife_full_name') }}" required>
                        @error('wife_full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control @error('phone_number') is-invalid @enderror" 
                               id="phone_number" name="phone_number" value="{{ old('phone_number') }}">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="email" class="form-label">Email (optional)</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                               id="email" name="email" value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="church_branch_id" class="form-label">Church Branch / Parish</label>
                        <input type="hidden" id="church_branch_id" name="church_branch_id" value="{{ $campus->id }}">
                        <input type="text" class="form-control" value="{{ $campus->name }}" readonly>
                        <small class="text-muted">Automatically set to your branch</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marriage Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-ring me-2"></i>2. Marriage Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="marriage_type" class="form-label">Type of Marriage</label>
                        <select class="form-select" id="marriage_type" name="marriage_type">
                            <option value="">Select...</option>
                            <option value="customary" {{ old('marriage_type') == 'customary' ? 'selected' : '' }}>Customary</option>
                            <option value="civil" {{ old('marriage_type') == 'civil' ? 'selected' : '' }}>Civil</option>
                            <option value="traditional" {{ old('marriage_type') == 'traditional' ? 'selected' : '' }}>Traditional</option>
                            <option value="other" {{ old('marriage_type') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="marriage_date" class="form-label">Date of Marriage <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('marriage_date') is-invalid @enderror" 
                               id="marriage_date" name="marriage_date" value="{{ old('marriage_date') }}" 
                               max="{{ date('Y-m-d') }}" required>
                        @error('marriage_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="place_of_marriage" class="form-label">Place of Marriage</label>
                        <input type="text" class="form-control" id="place_of_marriage" name="place_of_marriage" 
                               value="{{ old('place_of_marriage') }}" placeholder="Location where marriage took place">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="marriage_certificate_number" class="form-label">Marriage Certificate Number (optional)</label>
                        <input type="text" class="form-control" id="marriage_certificate_number" name="marriage_certificate_number" 
                               value="{{ old('marriage_certificate_number') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Church Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-church me-2"></i>3. Church Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Are both spouses church members? <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="both_spouses_members" id="both_members_yes" value="1" {{ old('both_spouses_members') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="both_members_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="both_spouses_members" id="both_members_no" value="0" {{ old('both_spouses_members') == '0' || old('both_spouses_members') === null ? 'checked' : '' }}>
                            <label class="form-check-label" for="both_members_no">No</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Have you attended marriage counseling? <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="attended_marriage_counseling" id="counseling_yes" value="1" {{ old('attended_marriage_counseling') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="counseling_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="attended_marriage_counseling" id="counseling_no" value="0" {{ old('attended_marriage_counseling') == '0' || old('attended_marriage_counseling') === null ? 'checked' : '' }}>
                            <label class="form-check-label" for="counseling_no">No</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="membership_duration" class="form-label">Duration of church membership</label>
                        <input type="text" class="form-control" id="membership_duration" name="membership_duration" 
                               value="{{ old('membership_duration') }}" placeholder="e.g., 2 years, 5 years">
                    </div>
                </div>
            </div>
        </div>

        <!-- Declaration -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>4. Declaration</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="reason_for_blessing" class="form-label">Reason for requesting marriage blessing <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('reason_for_blessing') is-invalid @enderror" 
                              id="reason_for_blessing" name="reason_for_blessing" rows="4" 
                              placeholder="Please explain why you want church blessing for your marriage..." required>{{ old('reason_for_blessing') }}</textarea>
                    @error('reason_for_blessing')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">Minimum 20 characters required</small>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input @error('declaration_agreed') is-invalid @enderror" type="checkbox" 
                           id="declaration_agreed" name="declaration_agreed" value="1" required>
                    <label class="form-check-label" for="declaration_agreed">
                        We request the blessing of our marriage according to the teachings and traditions of the Church. <span class="text-danger">*</span>
                    </label>
                    @error('declaration_agreed')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('evangelism-leader.marriage-blessing-requests.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-paper-plane me-1"></i> Submit Request
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('blessingRequestForm');
    const submitBtn = document.getElementById('submitBtn');
    
    form.addEventListener('submit', function(e) {
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';
        return true;
    });
});
</script>
@endsection



