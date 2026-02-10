@extends('layouts.index')

@section('title', 'Collect Offering')

@section('content')
    <div class="row">
        <div class="col-xl-8 mx-auto">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 text-primary"><i class="fas fa-hand-holding-usd me-2"></i>Collect Offering</h5>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('church-elder.offerings.store') }}" method="POST">
                        @csrf

                        <!-- Context Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Collection Location <span class="text-danger">*</span></label>
                            <div class="d-flex gap-4">
                                <div class="form-check custom-option custom-option-icon">
                                    <label class="form-check-label custom-option-content" for="contextCommunity">
                                        <span class="custom-option-body">
                                            <i class="fas fa-users fa-2x mb-2 text-primary"></i>
                                            <span class="custom-option-title">Community</span>
                                            <small>Jumuiya</small>
                                        </span>
                                        <input name="collection_context" class="form-check-input" type="radio"
                                            value="community" id="contextCommunity" checked onchange="toggleContext()">
                                    </label>
                                </div>
                                <div class="form-check custom-option custom-option-icon">
                                    <label class="form-check-label custom-option-content" for="contextCampus">
                                        <span class="custom-option-body">
                                            <i class="fas fa-church fa-2x mb-2 text-success"></i>
                                            <span class="custom-option-title">Campus</span>
                                            <small>Usharika/Mtaa</small>
                                        </span>
                                        <input name="collection_context" class="form-check-input" type="radio"
                                            value="campus" id="contextCampus" onchange="toggleContext()">
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Community Section -->
                        <div id="communitySection" class="mb-4 bg-light p-3 rounded border">
                            <h6 class="text-secondary mb-3"><i class="fas fa-users me-2"></i>Community Details</h6>
                            <div class="mb-3">
                                <label for="community_id" class="form-label">Select Community <span
                                        class="text-danger">*</span></label>
                                <select name="community_id" id="community_id" class="form-select select2">
                                    <option value="">-- Select Community --</option>
                                    @foreach($assignedCommunities as $community)
                                        <option value="{{ $community->id }}">{{ $community->name }}</option>
                                    @endforeach
                                </select>
                                @if($assignedCommunities->isEmpty())
                                    <div class="form-text text-danger">You are not assigned to any community. Please select
                                        'Campus' or contact admin.</div>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label for="offering_type" class="form-label">Offering Type <span
                                        class="text-danger">*</span></label>
                                <select name="offering_type" id="offering_type" class="form-select">
                                    <option value="sadaka_umoja">Sadaka ya Umoja</option>
                                    <option value="sadaka_jengo">Sadaka ya Jengo</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Campus Section -->
                        <div id="campusSection" class="mb-4 bg-light p-3 rounded border d-none">
                            <h6 class="text-secondary mb-3"><i class="fas fa-church me-2"></i>Campus Details</h6>
                            <div class="mb-3">
                                <label for="campus_id" class="form-label">Select Campus <span
                                        class="text-danger">*</span></label>
                                <select name="campus_id" id="campus_id" class="form-select select2">
                                    <option value="">-- Select Campus --</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <input type="hidden" name="offering_type" value="main_service" id="campusOfferingType" disabled>
                        </div>

                        <!-- Common Details -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="amount" class="form-label">Amount (TZS) <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">TZS</span>
                                    <input type="number" name="amount" id="amount" class="form-control" placeholder="0.00"
                                        min="0" step="100" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="offering_date" class="form-label">Date <span
                                        class="text-danger">*</span></label>
                                <input type="date" name="offering_date" id="offering_date" class="form-control"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="service_id" class="form-label">Related Service (Optional)</label>
                            <select name="service_id" id="service_id" class="form-select">
                                <option value="">-- No Specific Service --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->service_date->format('d M Y') }} -
                                        {{ $service->main_theme }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes / Remarks</label>
                            <textarea name="notes" id="notes" class="form-control" rows="3"
                                placeholder="Any additional details..."></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save me-2"></i>Submit for Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-option {
            border: 2px solid #e3e6f0;
            border-radius: 0.5rem;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.2s;
            width: 50%;
            text-align: center;
        }

        .custom-option:hover {
            border-color: #4e73df;
            background-color: #f8f9fc;
        }

        .custom-option-icon .form-check-input {
            display: none;
        }

        .custom-option-icon .form-check-input:checked+.custom-option-content {
            color: #4e73df;
        }

        .custom-option-icon:has(.form-check-input:checked) {
            border-color: #4e73df;
            background-color: #f0f4ff;
        }

        .custom-option-title {
            display: block;
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 0.2rem;
        }
    </style>

    <script>
        function toggleContext() {
            const isCommunity = document.getElementById('contextCommunity').checked;
            const communitySection = document.getElementById('communitySection');
            const campusSection = document.getElementById('campusSection');
            const communitySelect = document.getElementById('community_id');
            const campusSelect = document.getElementById('campus_id');
            const communityOfferingType = document.getElementById('offering_type');
            const campusOfferingType = document.getElementById('campusOfferingType');

            if (isCommunity) {
                communitySection.classList.remove('d-none');
                campusSection.classList.add('d-none');
                communitySelect.setAttribute('required', 'required');
                campusSelect.removeAttribute('required');
                campusSelect.value = '';

                communityOfferingType.removeAttribute('disabled');
                campusOfferingType.setAttribute('disabled', 'disabled');
            } else {
                communitySection.classList.add('d-none');
                campusSection.classList.remove('d-none');
                campusSelect.setAttribute('required', 'required');
                communitySelect.removeAttribute('required');
                communitySelect.value = '';

                communityOfferingType.setAttribute('disabled', 'disabled');
                campusOfferingType.removeAttribute('disabled');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            toggleContext();
        });
    </script>
@endsection