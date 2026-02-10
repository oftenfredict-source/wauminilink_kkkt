@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-door-open me-2 text-primary"></i>Return to Church
                                    Fellowship Request</h1>
                                <p class="text-muted mb-0">Kurudi Kundini - Submit a request to return to church fellowship
                                </p>
                            </div>
                            <a href="{{ route('evangelism-leader.return-to-fellowship-requests.index') }}"
                                class="btn btn-outline-primary">
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

        <form action="{{ route('evangelism-leader.return-to-fellowship-requests.store') }}" method="POST"
            id="fellowshipRequestForm">
            @csrf

            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>1. Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('full_name') is-invalid @enderror" id="full_name"
                                name="full_name" value="{{ old('full_name') }}" required>
                            @error('full_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                            <select class="form-select @error('gender') is-invalid @enderror" id="gender" name="gender"
                                required>
                                <option value="">Select...</option>
                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                            @error('gender')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                            @error('date_of_birth')
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
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}">
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

            <!-- Church Background -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>2. Church Background</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Were you previously a church member? <span
                                    class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="previously_member"
                                    id="previously_member_yes" value="1" {{ old('previously_member') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="previously_member_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="previously_member"
                                    id="previously_member_no" value="0" {{ old('previously_member') == '0' || old('previously_member') === null ? 'checked' : '' }}>
                                <label class="form-check-label" for="previously_member_no">No</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="previous_church_branch" class="form-label">Previous Church / Branch</label>
                            <input type="text" class="form-control" id="previous_church_branch"
                                name="previous_church_branch" value="{{ old('previous_church_branch') }}"
                                placeholder="If applicable">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="period_away" class="form-label">Period away from the church</label>
                            <input type="text" class="form-control" id="period_away" name="period_away"
                                value="{{ old('period_away') }}" placeholder="e.g., 2 years, 6 months">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="reason_for_leaving" class="form-label">Reason for leaving (optional)</label>
                            <textarea class="form-control" id="reason_for_leaving" name="reason_for_leaving" rows="3"
                                placeholder="Optional: Explain why you left the church...">{{ old('reason_for_leaving') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Return Declaration -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>3. Return Declaration</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="reason_for_returning" class="form-label">Reason for returning to church fellowship <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('reason_for_returning') is-invalid @enderror"
                            id="reason_for_returning" name="reason_for_returning" rows="4"
                            placeholder="Please explain why you want to return to church fellowship..."
                            required>{{ old('reason_for_returning') }}</textarea>
                        @error('reason_for_returning')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 20 characters required</small>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input @error('declaration_agreed') is-invalid @enderror" type="checkbox"
                            id="declaration_agreed" name="declaration_agreed" value="1" required>
                        <label class="form-check-label" for="declaration_agreed">
                            I willingly request to return to the church fellowship and commit to follow the teachings and
                            regulations of the Church. <span class="text-danger">*</span>
                        </label>
                        @error('declaration_agreed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('evangelism-leader.return-to-fellowship-requests.index') }}"
                    class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane me-1"></i> Submit Request
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('fellowshipRequestForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function (e) {
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