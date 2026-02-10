@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-heart me-2 text-primary"></i>Marriage Blessing Request
                                </h1>
                                <p class="text-muted mb-0">Baraka ya Ndoa - Request blessing for your marriage</p>
                            </div>
                            <a href="{{ route('evangelism-leader.marriage-blessing-requests.index') }}"
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

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="alert alert-info border-0 shadow-sm mb-4">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Evangelism Leader Referral:</strong> Please provide the basic contact information for the
                    couple. The Pastor will schedule a meeting to collect full marriage and church details.
                </div>

                <form action="{{ route('evangelism-leader.marriage-blessing-requests.store') }}" method="POST"
                    id="blessingRequestForm">
                    @csrf

                    <!-- Couple Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Couple Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="husband_full_name" class="form-label">Husband's Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('husband_full_name') is-invalid @enderror"
                                        id="husband_full_name" name="husband_full_name"
                                        value="{{ old('husband_full_name') }}" required>
                                    @error('husband_full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="wife_full_name" class="form-label">Wife's Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('wife_full_name') is-invalid @enderror"
                                        id="wife_full_name" name="wife_full_name" value="{{ old('wife_full_name') }}"
                                        required>
                                    @error('wife_full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone_number" class="form-label">Primary Contact Phone <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                        id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required
                                        placeholder="+255 ...">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email (optional)</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Referral Reason -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-comment-alt me-2"></i>Referral Details</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="reason_for_blessing" class="form-label">Why is this couple being referred? <span
                                        class="text-danger">*</span></label>
                                <textarea class="form-control @error('reason_for_blessing') is-invalid @enderror"
                                    id="reason_for_blessing" name="reason_for_blessing" rows="4"
                                    placeholder="Provide a brief reason for the blessing request..."
                                    required>{{ old('reason_for_blessing') }}</textarea>
                                @error('reason_for_blessing')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 10 characters required</small>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input @error('declaration_agreed') is-invalid @enderror"
                                    type="checkbox" id="declaration_agreed" name="declaration_agreed" value="1" required>
                                <label class="form-check-label" for="declaration_agreed">
                                    I verify that this couple has requested a marriage blessing and is ready to meet with
                                    the Pastor. <span class="text-danger">*</span>
                                </label>
                                @error('declaration_agreed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <a href="{{ route('evangelism-leader.marriage-blessing-requests.index') }}"
                            class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary px-4" id="submitBtn">
                            <i class="fas fa-paper-plane me-1"></i> Submit Referral to Pastor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('blessingRequestForm');
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