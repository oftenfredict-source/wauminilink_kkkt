@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-rings-wedding me-2 text-primary"></i>Church Wedding Request</h1>
                            <p class="text-muted mb-0">Kufunga Ndoa Kanisani - Request to get married in church</p>
                        </div>
                        <a href="{{ route('evangelism-leader.church-wedding-requests.index') }}" class="btn btn-outline-primary">
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

    <form action="{{ route('evangelism-leader.church-wedding-requests.store') }}" method="POST" enctype="multipart/form-data" id="weddingRequestForm">
        @csrf
        
        <!-- Bride & Groom Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-users me-2"></i>1. Bride & Groom Information</h5>
            </div>
            <div class="card-body">
                <h6 class="mb-3">Groom's Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="groom_full_name" class="form-label">Groom's Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('groom_full_name') is-invalid @enderror" 
                               id="groom_full_name" name="groom_full_name" value="{{ old('groom_full_name') }}" required>
                        @error('groom_full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="groom_date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('groom_date_of_birth') is-invalid @enderror" 
                               id="groom_date_of_birth" name="groom_date_of_birth" value="{{ old('groom_date_of_birth') }}" 
                               max="{{ date('Y-m-d', strtotime('-1 day')) }}" required>
                        @error('groom_date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="groom_phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('groom_phone_number') is-invalid @enderror" 
                               id="groom_phone_number" name="groom_phone_number" value="{{ old('groom_phone_number') }}" required>
                        @error('groom_phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="groom_email" class="form-label">Email (optional)</label>
                        <input type="email" class="form-control @error('groom_email') is-invalid @enderror" 
                               id="groom_email" name="groom_email" value="{{ old('groom_email') }}">
                        @error('groom_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <hr class="my-4">

                <h6 class="mb-3">Bride's Information</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="bride_full_name" class="form-label">Bride's Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('bride_full_name') is-invalid @enderror" 
                               id="bride_full_name" name="bride_full_name" value="{{ old('bride_full_name') }}" required>
                        @error('bride_full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bride_date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('bride_date_of_birth') is-invalid @enderror" 
                               id="bride_date_of_birth" name="bride_date_of_birth" value="{{ old('bride_date_of_birth') }}" 
                               max="{{ date('Y-m-d', strtotime('-1 day')) }}" required>
                        @error('bride_date_of_birth')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="bride_phone_number" class="form-label">Phone Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('bride_phone_number') is-invalid @enderror" 
                               id="bride_phone_number" name="bride_phone_number" value="{{ old('bride_phone_number') }}" required>
                        @error('bride_phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="bride_email" class="form-label">Email (optional)</label>
                        <input type="email" class="form-control @error('bride_email') is-invalid @enderror" 
                               id="bride_email" name="bride_email" value="{{ old('bride_email') }}">
                        @error('bride_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="church_branch_id" class="form-label">Church Branch / Parish</label>
                        <input type="hidden" id="church_branch_id" name="church_branch_id" value="{{ $campus->id }}">
                        <input type="text" class="form-control" value="{{ $campus->name }}" readonly>
                        <small class="text-muted">Automatically set to your branch</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Membership & Spiritual Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-praying-hands me-2"></i>2. Membership & Spiritual Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Are both baptized? <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="both_baptized" id="both_baptized_yes" value="1" {{ old('both_baptized') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="both_baptized_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="both_baptized" id="both_baptized_no" value="0" {{ old('both_baptized') == '0' || old('both_baptized') === null ? 'checked' : '' }}>
                            <label class="form-check-label" for="both_baptized_no">No</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Are both confirmed? <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="both_confirmed" id="both_confirmed_yes" value="1" {{ old('both_confirmed') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="both_confirmed_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="both_confirmed" id="both_confirmed_no" value="0" {{ old('both_confirmed') == '0' || old('both_confirmed') === null ? 'checked' : '' }}>
                            <label class="form-check-label" for="both_confirmed_no">No</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="membership_duration" class="form-label">Church membership duration</label>
                        <input type="text" class="form-control" id="membership_duration" name="membership_duration" 
                               value="{{ old('membership_duration') }}" placeholder="e.g., 1 year, 3 years">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="pastor_catechist_name" class="form-label">Pastor / Catechist in charge</label>
                        <input type="text" class="form-control" id="pastor_catechist_name" name="pastor_catechist_name" 
                               value="{{ old('pastor_catechist_name') }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- Wedding Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>3. Wedding Details</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="preferred_wedding_date" class="form-label">Preferred Wedding Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control @error('preferred_wedding_date') is-invalid @enderror" 
                               id="preferred_wedding_date" name="preferred_wedding_date" value="{{ old('preferred_wedding_date') }}" 
                               min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        @error('preferred_wedding_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="preferred_church" class="form-label">Preferred Church</label>
                        <input type="text" class="form-control" id="preferred_church" name="preferred_church" 
                               value="{{ old('preferred_church') }}" placeholder="Church name or location">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="expected_guests" class="form-label">Number of Expected Guests</label>
                        <input type="number" class="form-control" id="expected_guests" name="expected_guests" 
                               value="{{ old('expected_guests') }}" min="1" placeholder="Approximate number">
                    </div>
                </div>
            </div>
        </div>

        <!-- Counseling & Documents -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>4. Counseling & Documents</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Have you attended premarital counseling? <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="attended_premarital_counseling" id="premarital_yes" value="1" {{ old('attended_premarital_counseling') == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="premarital_yes">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="attended_premarital_counseling" id="premarital_no" value="0" {{ old('attended_premarital_counseling') == '0' || old('attended_premarital_counseling') === null ? 'checked' : '' }}>
                            <label class="form-check-label" for="premarital_no">No</label>
                        </div>
                    </div>
                </div>
                
                <h6 class="mb-3">Required Documents (Upload)</h6>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="groom_baptism_certificate" class="form-label">Groom's Baptism Certificate</label>
                        <input type="file" class="form-control" id="groom_baptism_certificate" name="groom_baptism_certificate" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bride_baptism_certificate" class="form-label">Bride's Baptism Certificate</label>
                        <input type="file" class="form-control" id="bride_baptism_certificate" name="bride_baptism_certificate" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="groom_confirmation_certificate" class="form-label">Groom's Confirmation Certificate</label>
                        <input type="file" class="form-control" id="groom_confirmation_certificate" name="groom_confirmation_certificate" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bride_confirmation_certificate" class="form-label">Bride's Confirmation Certificate</label>
                        <input type="file" class="form-control" id="bride_confirmation_certificate" name="bride_confirmation_certificate" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="groom_birth_certificate" class="form-label">Groom's Birth Certificate</label>
                        <input type="file" class="form-control" id="groom_birth_certificate" name="groom_birth_certificate" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="bride_birth_certificate" class="form-label">Bride's Birth Certificate</label>
                        <input type="file" class="form-control" id="bride_birth_certificate" name="bride_birth_certificate" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="marriage_notice" class="form-label">Marriage Notice / Civil Certificate (if required)</label>
                        <input type="file" class="form-control" id="marriage_notice" name="marriage_notice" 
                               accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">PDF, JPG, PNG (Max 2MB)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Declaration -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>5. Declaration</h5>
            </div>
            <div class="card-body">
                <div class="form-check mb-3">
                    <input class="form-check-input @error('declaration_agreed') is-invalid @enderror" type="checkbox" 
                           id="declaration_agreed" name="declaration_agreed" value="1" required>
                    <label class="form-check-label" for="declaration_agreed">
                        We voluntarily request to be united in holy matrimony according to the doctrine and regulations of the Church. <span class="text-danger">*</span>
                    </label>
                    @error('declaration_agreed')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ route('evangelism-leader.church-wedding-requests.index') }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="fas fa-paper-plane me-1"></i> Submit Request
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('weddingRequestForm');
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



