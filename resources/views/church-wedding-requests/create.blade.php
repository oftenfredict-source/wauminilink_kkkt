@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-rings-wedding me-2 text-primary"></i>Church Wedding
                                    Request</h1>
                                <p class="text-muted mb-0">Kufunga Ndoa Kanisani - Request to get married in church</p>
                            </div>
                            <a href="{{ route('evangelism-leader.church-wedding-requests.index') }}"
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
                    <strong>Evangelism Leader Referral:</strong> Please provide the basic contact information for the bride
                    and groom. The Pastor will schedule a meeting to collect digital documents, spiritual info, and confirm
                    wedding details.
                </div>

                <form action="{{ route('evangelism-leader.church-wedding-requests.store') }}" method="POST"
                    id="weddingRequestForm">
                    @csrf

                    <!-- Couple Contact Information -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-users me-2"></i>Couple Contact Information</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="text-primary fw-bold mb-3"><i class="fas fa-male me-2"></i>Groom's Details</h6>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label for="groom_full_name" class="form-label">Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('groom_full_name') is-invalid @enderror"
                                        id="groom_full_name" name="groom_full_name" value="{{ old('groom_full_name') }}"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="groom_phone_number" class="form-label">Phone Number <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('groom_phone_number') is-invalid @enderror"
                                        id="groom_phone_number" name="groom_phone_number"
                                        value="{{ old('groom_phone_number') }}" required placeholder="+255 ...">
                                </div>
                            </div>

                            <h6 class="text-danger fw-bold mb-3"><i class="fas fa-female me-2"></i>Bride's Details</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="bride_full_name" class="form-label">Full Name <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('bride_full_name') is-invalid @enderror"
                                        id="bride_full_name" name="bride_full_name" value="{{ old('bride_full_name') }}"
                                        required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="bride_phone_number" class="form-label">Phone Number <span
                                            class="text-danger">*</span></label>
                                    <input type="text"
                                        class="form-control @error('bride_phone_number') is-invalid @enderror"
                                        id="bride_phone_number" name="bride_phone_number"
                                        value="{{ old('bride_phone_number') }}" required placeholder="+255 ...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Declaration -->
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-danger text-white">
                            <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Referral Declaration</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input @error('declaration_agreed') is-invalid @enderror"
                                    type="checkbox" id="declaration_agreed" name="declaration_agreed" value="1" required>
                                <label class="form-check-label" for="declaration_agreed">
                                    I verify that this couple has requested to be united in holy matrimony and is ready to
                                    meet with the Pastor for further processing. <span class="text-danger">*</span>
                                </label>
                                @error('declaration_agreed')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mb-4">
                        <a href="{{ route('evangelism-leader.church-wedding-requests.index') }}"
                            class="btn btn-secondary px-4">Cancel</a>
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
            const form = document.getElementById('weddingRequestForm');
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