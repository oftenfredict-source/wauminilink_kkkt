@extends('layouts.index')

@section('content')
<div class="container-fluid px-2 px-md-5 py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
            <span class="fs-5 fw-bold text-white d-flex align-items-center">
                <i class="fas fa-user-plus me-2"></i> <span id="stepHeaderTitle">Add Member</span>
            </span>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('members.index') }}" class="btn btn-outline-light btn-sm shadow-sm"><i class="fas fa-list me-1"></i> All Members</a>
            </div>
        </div>
        <div class="card-body bg-light px-4 py-4">
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <h5 class="alert-heading"><i class="fas fa-exclamation-triangle me-2"></i>Validation Errors</h5>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <form id="addMemberForm" action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Step Wizard -->
                <div class="mb-4">
                    <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap" id="wizardSteps">
                        <div class="wizard-step position-relative text-center active" data-step="1">
                            <div class="step-circle bg-primary text-white shadow">1</div>
                            <div class="step-label mt-2 small">Personal Info</div>
                        </div>
                        <div class="wizard-step position-relative text-center" data-step="2">
                            <div class="step-circle bg-secondary text-white shadow">2</div>
                            <div class="step-label mt-2 small">Other Info</div>
                        </div>
                        <div class="wizard-step position-relative text-center" data-step="3">
                            <div class="step-circle bg-secondary text-white shadow">3</div>
                            <div class="step-label mt-2 small">Residence</div>
                        </div>
                        <div class="wizard-step position-relative text-center" data-step="4">
                            <div class="step-circle bg-secondary text-white shadow">4</div>
                            <div class="step-label mt-2 small">Family Information</div>
                        </div>
                        <div class="wizard-step position-relative text-center" data-step="5">
                            <div class="step-circle bg-secondary text-white shadow">5</div>
                            <div class="step-label mt-2 small">Summary</div>
                        </div>
                    </div>
                </div>

                <!-- Step 1: Personal Information -->
                <div id="step1">
                    <div class="row g-4 mb-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="membership_type" id="membership_type" class="form-select" required>
                                    <option value=""></option>
                                    <option value="permanent">Permanent</option>
                                    <option value="temporary">Temporary</option>
                                </select>
                                <label for="membership_type">Membership Type</label>
                            </div>
                        </div>
                        <!-- Temporary Membership Duration Fields -->
                        <div class="col-md-4" id="temporaryMembershipDurationWrapper" style="display:none;">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" name="membership_duration_value" id="membership_duration_value" min="1" max="120" placeholder="3">
                                        <label for="membership_duration_value">Duration</label>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="membership_duration_unit" id="membership_duration_unit">
                                            <option value="months">Months</option>
                                            <option value="years">Years</option>
                                        </select>
                                        <label for="membership_duration_unit">Unit</label>
                                    </div>
                                </div>
                            </div>
                            <small class="text-muted">e.g., 3 months, 6 months, 2 years</small>
                        </div>
                        <div class="col-md-4" id="memberTypeWrapper">
                            <div class="form-floating">
                                <select name="member_type" id="member_type" class="form-select" required>
                                    <option value=""></option>
                                    <option value="father">Father</option>
                                    <option value="mother">Mother</option>
                                    <option value="independent">Independent Person</option>
                                </select>
                                <label for="member_type">Member Type</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="campus_id" id="campus_id" class="form-select" required>
                                    <option value="">Select Branch...</option>
                                    @if(isset($campuses) && $campuses->count() > 0)
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ (request('campus_id') == $campus->id || (auth()->check() && auth()->user()->getCampus() && auth()->user()->getCampus()->id == $campus->id)) ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                                @if($campus->is_main_campus) (Usharika) @endif
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No branches available</option>
                                    @endif
                                </select>
                                <label for="campus_id">Branch/Campus <span class="text-danger">*</span></label>
                            </div>
                            @if(!isset($campuses) || $campuses->count() == 0)
                                <small class="text-danger">No active branches found. Please contact administrator.</small>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="community_id" id="community_id" class="form-select">
                                    <option value="">Select Community...</option>
                                </select>
                                <label for="community_id">Community (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" name="envelope_number" id="envelope_number" class="form-control" placeholder="e.g. 024">
                                <label for="envelope_number">Envelope Number (Optional)</label>
                            </div>
                            <small class="text-muted ms-1">Unique number within the fellowship (Jumuiya)</small>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="full_name" id="full_name" required>
                                <label for="full_name">Full Name</label>
                            </div>
                        </div>
                        <div class="col-md-3" id="genderFieldWrapper">
                            <div class="form-floating">
                                <select class="form-select" name="gender" id="gender" required>
                                    <option value=""></option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                                <label for="gender">Gender</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" required>
                                <label for="date_of_birth">Date of Birth</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select class="form-select" name="education_level" id="education_level">
                                    <option value=""></option>
                                    <option value="primary">Primary</option>
                                    <option value="secondary">Secondary</option>
                                    <option value="high_level">High Level</option>
                                    <option value="certificate">Certificate</option>
                                    <option value="diploma">Diploma</option>
                                    <option value="bachelor_degree">Bachelor Degree</option>
                                    <option value="masters">Masters</option>
                                    <option value="phd">PhD</option>
                                    <option value="professor">Professor</option>
                                    <option value="not_studied">Not Studied</option>
                                </select>
                                <label for="education_level">Education Level</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="profession" id="profession" required>
                                <label for="profession">Profession</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="working_area" id="working_area" placeholder="e.g., Dar es Salaam">
                                <label for="working_area">Working Area</label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="nida_number" id="nida_number">
                                <label for="nida_number">NIDA Number (optional)</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Baptism Information -->
                    <div class="border rounded-3 p-4 mb-4 bg-white shadow-sm">
                        <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-tint me-2"></i>Baptism Information</h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="baptism_status" id="baptism_status" required>
                                        <option value="">Select...</option>
                                        <option value="baptized">Baptized</option>
                                        <option value="not_baptized">Not Baptized</option>
                                    </select>
                                    <label for="baptism_status">Baptism Status</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="baptismDateWrapper" style="display:none;">
                                <div class="form-floating">
                                    <input type="date" class="form-control" name="baptism_date" id="baptism_date">
                                    <label for="baptism_date">Baptism Date</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="baptismLocationWrapper" style="display:none;">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="baptism_location" id="baptism_location" placeholder="Church name">
                                    <label for="baptism_location">Baptism Location/Church</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="baptizedByWrapper" style="display:none;">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="baptized_by" id="baptized_by" placeholder="Pastor name">
                                    <label for="baptized_by">Baptized By</label>
                                </div>
                            </div>
                            <div class="col-md-4" id="baptismCertificateWrapper" style="display:none;">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="baptism_certificate_number" id="baptism_certificate_number" placeholder="Certificate number">
                                    <label for="baptism_certificate_number">Baptism Certificate Number (Optional)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Passport Picture Upload -->
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="profile_picture" class="form-label">
                                    <i class="fas fa-camera me-2"></i>Passport Picture (Optional)
                                </label>
                                <input type="file" class="form-control" name="profile_picture" id="profile_picture" accept="image/*">
                                <small class="text-muted">Upload a clear passport-sized photo (JPG, PNG, max 2MB)</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary btn-lg px-4 shadow-sm next-step" id="nextStep1">Next <i class="fas fa-arrow-right ms-1"></i></button>
                    </div>
                </div>

                <!-- Step 2: Other Information -->
                <div id="step2" style="display:none;">
                    <div class="row g-4 mb-4">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <div class="input-group">
                                    <span class="input-group-text">+255</span>
                                    <input type="text" class="form-control" name="phone_number" id="phone_number" placeholder="744000000" required>
                                </div>
                                <label for="phone_number"></label>
                            </div>
                            <small class="text-muted ms-1">Enter your phone number without +255 (e.g., 712345678)</small>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="email" class="form-control" name="email" id="email">
                                <label for="email">Email (optional)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="region" name="region" required>
                                    <option value="">Select Region...</option>
                                </select>
                                <label for="region">Region</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="district" name="district" required>
                                    <option value="">Select District...</option>
                                </select>
                                <label for="district">District</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="ward" id="ward" required>
                                <label for="ward">Ward</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="street" id="street" required>
                                <label for="street">Street</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="address" id="address" style="height: 48px;" required />
                                <label for="address">P O Box</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <select class="form-select" id="tribe" name="tribe" required>
                                    <option value="">Select Tribe...</option>
                                </select>
                                <label for="tribe">Tribe</label>
                            </div>
                        </div>
                        <div class="col-md-3" id="otherTribeWrapper" style="display:none;">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="other_tribe" id="other_tribe">
                                <label for="other_tribe">Other Tribe</label>
                            </div>
                        </div>
                    </div>

                    <!-- Social Welfare Section -->
                    <div class="border rounded-3 p-4 mb-4 bg-white shadow-sm">
                        <h6 class="mb-3 text-danger fw-bold"><i class="fas fa-hand-holding-heart me-2"></i>Hali ya Ustawi wa Jamii (Welfare Status)</h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select class="form-select" name="orphan_status" id="orphan_status">
                                        <option value="not_orphan">Si Yatima (Not Orphan)</option>
                                        <option value="father_deceased">Baba amefariki (Father Deceased)</option>
                                        <option value="mother_deceased">Mama amefariki (Mother Deceased)</option>
                                        <option value="both_deceased">Wote wamefariki (Both Deceased)</option>
                                    </select>
                                    <label for="orphan_status">Hali ya Uyathima (Orphan Status)</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" id="disability_status" name="disability_status" value="1">
                                    <label class="form-check-label fw-bold" for="disability_status">Ana Ulemavu? (Has Disability?)</label>
                                </div>
                                <div id="disabilityTypeWrapper" class="mt-2" style="display:none;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="disability_type" id="disability_type" placeholder="Nature of disability">
                                        <label for="disability_type">Aina ya Ulemavu (Type)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check form-switch pt-2">
                                    <input class="form-check-input" type="checkbox" id="vulnerable_status" name="vulnerable_status" value="1">
                                    <label class="form-check-label fw-bold" for="vulnerable_status">Hali Ngumu/Dhaifu? (Vulnerable?)</label>
                                </div>
                                <div id="vulnerableTypeWrapper" class="mt-2" style="display:none;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="vulnerable_type" id="vulnerable_type" placeholder="e.g. Poverty, Chronic Illness">
                                        <label for="vulnerable_type">Aina ya Changamoto (Type)</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 shadow-sm prev-step" id="prevStep2"><i class="fas fa-arrow-left me-1"></i>Back</button>
                        <button type="button" class="btn btn-primary btn-lg px-4 shadow-sm next-step" id="nextStep2">Next <i class="fas fa-arrow-right ms-1"></i></button>
                    </div>
                </div>

                <!-- Step 3: Current Residence -->
                <div id="step3" style="display:none;">
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="residence_region" id="residence_region" class="form-select" required>
                                    <option value="">Select Region...</option>
                                </select>
                                <label for="residence_region">Region</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <select name="residence_district" id="residence_district" class="form-select" required>
                                    <option value="">Select District...</option>
                                </select>
                                <label for="residence_district">District</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="residence_ward" id="residence_ward" required>
                                <label for="residence_ward">Ward</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="residence_street" id="residence_street" required>
                                <label for="residence_street">Street</label>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="residence_road" id="residence_road">
                                <label for="residence_road">Road Name (Optional)</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="residence_house_number" id="residence_house_number">
                                <label for="residence_house_number">House Number (Optional)</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Neighbors Information -->
                    <div class="border rounded-3 p-4 mb-4 bg-white shadow-sm">
                        <h6 class="mb-3 text-info fw-bold"><i class="fas fa-users me-2"></i>Neighbors Information</h6>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="neighbor_name" id="neighbor_name" placeholder="Full name of neighbor">
                                    <label for="neighbor_name">Neighbor's Name</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <div class="input-group">
                                        <span class="input-group-text">+255</span>
                                        <input type="text" class="form-control" name="neighbor_phone" id="neighbor_phone" placeholder="744000000">
                                    </div>
                                    <label for="neighbor_phone"></label>
                                </div>
                                <small class="text-muted">Enter neighbor's phone number for reference</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 shadow-sm prev-step" id="prevStep3"><i class="fas fa-arrow-left me-1"></i>Back</button>
                        <button type="button" class="btn btn-primary btn-lg px-4 shadow-sm next-step" id="nextStep3">Next <i class="fas fa-arrow-right ms-1"></i></button>
                    </div>
                </div>

                <!-- Step 4: Family Information -->
                <div id="step4" style="display:none;">
                    <!-- Marital Status Section -->
                    <div id="maritalStatusSection" class="border rounded-3 p-4 mb-4 bg-white shadow-sm">
                        <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-heart me-2"></i>Marital Status</h6>
                        <div class="mb-3">
                            <div class="form-floating">
                                <select class="form-select" name="marital_status" id="marital_status">
                                    <option value="">Select...</option>
                                    <option value="married">Married</option>
                                    <option value="divorced">Divorced</option>
                                    <option value="widowed">Widowed</option>
                                    <option value="separated">Separated</option>
                                </select>
                                <label for="marital_status">Marital Status</label>
                            </div>
                        </div>
                        
                        <!-- Wedding Information -->
                        <div id="weddingInfoSection" style="display:none;">
                            <hr class="my-4">
                            <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-ring me-2"></i>Wedding Information</h6>
                            <div class="row g-4 mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="wedding_type" id="wedding_type">
                                            <option value="">Select...</option>
                                            <option value="traditional">Traditional Wedding</option>
                                            <option value="church">Church Wedding</option>
                                            <option value="civil">Civil Wedding</option>
                                            <option value="both">Both Traditional & Church</option>
                                            <option value="customary">Customary Marriage</option>
                                            <option value="other">Other</option>
                                        </select>
                                        <label for="wedding_type">Type of Wedding</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="wedding_date" id="wedding_date">
                                        <label for="wedding_date">Date of Wedding</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Spouse Information -->
                        <div id="spouseInfoSection" style="display:none;">
                            <hr class="my-4">
                            <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-user me-2"></i>Spouse Information</h6>
                            <div class="row g-4 mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="spouse_full_name" id="spouse_full_name">
                                        <label for="spouse_full_name">Full Name</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="spouse_date_of_birth" id="spouse_date_of_birth">
                                        <label for="spouse_date_of_birth">Date of Birth</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" name="spouse_education_level" id="spouse_education_level">
                                            <option value="">Select...</option>
                                            <option value="primary">Primary</option>
                                            <option value="secondary">Secondary</option>
                                            <option value="high_level">High Level</option>
                                            <option value="certificate">Certificate</option>
                                            <option value="diploma">Diploma</option>
                                            <option value="bachelor_degree">Bachelor Degree</option>
                                            <option value="masters">Masters</option>
                                            <option value="phd">PhD</option>
                                            <option value="professor">Professor</option>
                                            <option value="not_studied">Not Studied</option>
                                        </select>
                                        <label for="spouse_education_level">Education Level</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="spouse_profession" id="spouse_profession">
                                        <label for="spouse_profession">Profession</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="spouse_nida_number" id="spouse_nida_number">
                                        <label for="spouse_nida_number">NIDA Number (optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="email" class="form-control" name="spouse_email" id="spouse_email">
                                        <label for="spouse_email">Email (optional)</label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="spouse_church_member" id="spouse_church_member">
                                            <option value="">Select...</option>
                                            <option value="yes">Yes, spouse is a church member</option>
                                            <option value="no">No, spouse is not a church member</option>
                                        </select>
                                        <label for="spouse_church_member">Is your spouse a member of this church?</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <div class="input-group">
                                            <span class="input-group-text">+255</span>
                                            <input type="text" class="form-control" name="spouse_phone_number" id="spouse_phone_number" placeholder="744000000">
                                        </div>
                                        <label for="spouse_phone_number"></label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Spouse Campus and Community (only if spouse is a church member) -->
                            <div class="row g-4 mb-3" id="spouseCampusCommunityFields" style="display:none;">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="spouse_campus_id" id="spouse_campus_id">
                                            <option value="">Select Campus...</option>
                                        </select>
                                        <label for="spouse_campus_id">Spouse Campus/Branch <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="spouse_community_id" id="spouse_community_id">
                                            <option value="">Select Fellowship...</option>
                                        </select>
                                        <label for="spouse_community_id">Spouse Fellowship (Jumuiya) <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" name="spouse_tribe" id="spouse_tribe">
                                            <option value="">Select Tribe...</option>
                                        </select>
                                        <label for="spouse_tribe">Spouse Tribe</label>
                                    </div>
                                </div>
                                <div class="col-md-4" id="spouseOtherTribeWrapper" style="display:none;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="spouse_other_tribe" id="spouse_other_tribe">
                                        <label for="spouse_other_tribe">Spouse Other Tribe</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Spouse Baptism Information -->
                            <hr class="my-3">
                            <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-tint me-2"></i>Spouse Baptism Information</h6>
                            <div class="row g-4 mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" name="spouse_baptism_status" id="spouse_baptism_status">
                                            <option value="">Select...</option>
                                            <option value="baptized">Baptized</option>
                                            <option value="not_baptized">Not Baptized</option>
                                        </select>
                                        <label for="spouse_baptism_status">Spouse Baptism Status</label>
                                    </div>
                                </div>
                                <div class="col-md-4" id="spouseBaptismDateWrapper" style="display:none;">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="spouse_baptism_date" id="spouse_baptism_date">
                                        <label for="spouse_baptism_date">Spouse Baptism Date</label>
                                    </div>
                                </div>
                                <div class="col-md-4" id="spouseBaptismLocationWrapper" style="display:none;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="spouse_baptism_location" id="spouse_baptism_location" placeholder="Church name">
                                        <label for="spouse_baptism_location">Spouse Baptism Location/Church</label>
                                    </div>
                                </div>
                                <div class="col-md-4" id="spouseBaptizedByWrapper" style="display:none;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="spouse_baptized_by" id="spouse_baptized_by" placeholder="Pastor name">
                                        <label for="spouse_baptized_by">Spouse Baptized By</label>
                                    </div>
                                </div>
                                <div class="col-md-4" id="spouseBaptismCertificateWrapper" style="display:none;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="spouse_baptism_certificate_number" id="spouse_baptism_certificate_number" placeholder="Certificate number">
                                        <label for="spouse_baptism_certificate_number">Spouse Baptism Certificate Number (Optional)</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Spouse Welfare Status -->
                            <hr class="my-3">
                            <h6 class="mb-3 text-danger fw-bold"><i class="fas fa-hand-holding-heart me-2"></i>Spouse Welfare Status</h6>
                            <div class="row g-4 mb-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" name="spouse_orphan_status" id="spouse_orphan_status">
                                            <option value="not_orphan">Si Yatima (Not Orphan)</option>
                                            <option value="father_deceased">Baba amefariki (Father Deceased)</option>
                                            <option value="mother_deceased">Mama amefariki (Mother Deceased)</option>
                                            <option value="both_deceased">Wote wamefariki (Both Deceased)</option>
                                        </select>
                                        <label for="spouse_orphan_status">Spouse Orphan Status</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input" type="checkbox" id="spouse_disability_status" name="spouse_disability_status" value="1">
                                        <label class="form-check-label fw-bold" for="spouse_disability_status">Spouse Ana Ulemavu? (Has Disability?)</label>
                                    </div>
                                    <div id="spouseDisabilityTypeWrapper" class="mt-2" style="display:none;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="spouse_disability_type" id="spouse_disability_type" placeholder="Nature of disability">
                                            <label for="spouse_disability_type">Spouse Disability Type</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Spouse Profile Picture -->
                            <div class="row g-4 mb-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="spouse_profile_picture" class="form-label">
                                            <i class="fas fa-camera me-2"></i>Spouse Passport Picture (Optional)
                                        </label>
                                        <input type="file" class="form-control" name="spouse_profile_picture" id="spouse_profile_picture" accept="image/*">
                                        <small class="text-muted">Upload a clear passport-sized photo (JPG, PNG, max 2MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Guardian Section -->
                    <div class="border rounded-3 p-4 mb-4 bg-white shadow-sm" id="guardianSection" style="display:none;">
                        <h6 class="mb-3 text-warning fw-bold"><i class="fas fa-user-shield me-2"></i>Guardian / Responsible Person Information</h6>
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="guardian_name" id="guardian_name">
                                    <label for="guardian_name">Guardian Name</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <div class="input-group">
                                        <span class="input-group-text">+255</span>
                                        <input type="text" class="form-control" name="guardian_phone" id="guardian_phone" placeholder="744000000">
                                    </div>
                                    <label for="guardian_phone"></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="guardian_relationship" id="guardian_relationship" placeholder="e.g., Father, Mother">
                                    <label for="guardian_relationship">Guardian Relationship</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Children Information -->
                    <div class="border rounded-3 p-4 mb-4 bg-white shadow-sm" id="childrenSection">
                        <h6 class="mb-3 text-success fw-bold"><i class="fas fa-child me-2"></i>Children Information</h6>
                        <div class="mb-3">
                            <div class="form-floating">
                                <input type="number" class="form-control" name="children_count" id="children_count" min="0" value="0" placeholder="Enter number">
                                <label for="children_count">Number of Children</label>
                            </div>
                            <small class="text-muted">Enter the number of children you want to register (0 if no children)</small>
                        </div>
                        <div id="childrenFields">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>Enter the number of children above to add their information.
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 shadow-sm prev-step" id="prevStep4"><i class="fas fa-arrow-left me-1"></i>Back</button>
                        <button type="button" class="btn btn-primary btn-lg px-4 shadow-sm next-step" id="nextStep4">Next <i class="fas fa-arrow-right ms-1"></i></button>
                    </div>
                </div>

                <!-- Step 5: Summary -->
                <div id="step5" style="display:none;">
                    <div class="alert alert-info mb-4">
                        <h5 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Review Your Information</h5>
                        <p>Please review all the information you have entered. Click "Submit" to complete the registration, or "Back" to make changes.</p>
                    </div>
                    
                    <div id="summaryContent" class="mb-4">
                        <!-- Summary will be populated by JavaScript -->
                    </div>
                    
                    <div class="card mb-4 border-warning">
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="previewMode" name="preview_mode">
                                <label class="form-check-label" for="previewMode">
                                    <i class="fas fa-eye me-1"></i><strong>Preview/Test Mode</strong> - Validate form without saving to database
                                </label>
                                <small class="text-muted d-block mt-1">Check this box to test the form submission and validation without actually saving data to the database.</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 shadow-sm prev-step" id="prevStep5"><i class="fas fa-arrow-left me-1"></i>Back</button>
                        <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm" id="submitBtn"><i class="fas fa-save me-2"></i>Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.wizard-step {
    min-width: 80px;
}
.step-circle {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin: 0 auto;
}
.wizard-step.active .step-circle {
    background-color: #0d6efd !important;
}
.wizard-step.completed .step-circle {
    background-color: #198754 !important;
}
.wizard-step.active .step-label {
    color: #0d6efd;
    font-weight: 700;
}
.wizard-step.completed .step-label {
    color: #198754;
    font-weight: 700;
}
.wizard-step:not(:last-child)::after {
    content: "";
    position: absolute;
    top: 22px;
    right: -32px;
    width: 64px;
    height: 4px;
    background: #dee2e6;
    border-radius: 2px;
    z-index: 0;
}
.wizard-step.completed:not(:last-child)::after {
    background: #198754;
}
.fade-in {
    animation: fadeInStep 0.5s;
}
.fade-out {
    animation: fadeOutStep 0.5s;
}
@keyframes fadeInStep {
    from { opacity: 0; transform: translateY(24px); }
    to { opacity: 1; transform: none; }
}
@keyframes fadeOutStep {
    from { opacity: 1; transform: none; }
    to { opacity: 0; transform: translateY(-24px); }
}
</style>

@section('scripts')
<script>
// Global variables for location and tribe data
let tzLocations = null;
let tribeList = ['Chaga','Sukuma','Haya','Nyakyusa','Makonde','Hehe','Other'];
const campusesUrl = '{{ route("campuses.ajax.get") }}';

// Load Tanzania locations data
function ensureLocationsLoaded() {
    if (tzLocations) return Promise.resolve(tzLocations);
    const locationsPath = `{{ asset('data/tanzania-locations.json') }}`;
    console.log('Loading locations from:', locationsPath);
    return fetch(locationsPath)
        .then(r => {
            if (!r.ok) {
                throw new Error(`HTTP error! status: ${r.status}`);
            }
            return r.json();
        })
        .then(json => { 
            // Transform the JSON structure from array format to object format
            // Input: { "regions": [{ "name": "Arusha", "districts": [{ "name": "District1", "wards": [...] }] }] }
            // Output: { "Arusha": { "District1": ["Ward1", "Ward2", ...] } }
            if (json.regions && Array.isArray(json.regions)) {
                tzLocations = {};
                json.regions.forEach(region => {
                    if (region.name && region.districts) {
                        tzLocations[region.name] = {};
                        region.districts.forEach(district => {
                            if (district.name && district.wards) {
                                tzLocations[region.name][district.name] = district.wards;
                            }
                        });
                    }
                });
            } else {
                // If already in the expected format, use as is
                tzLocations = json;
            }
            console.log('Locations loaded successfully:', Object.keys(tzLocations || {}).length, 'regions');
            return tzLocations; 
        })
        .catch(err => {
            console.error('Failed to load locations:', err);
            // Try alternative path
            return fetch('/data/tanzania-locations.json')
                .then(r => r.json())
                .then(json => { 
                    // Transform the JSON structure
                    if (json.regions && Array.isArray(json.regions)) {
                        tzLocations = {};
                        json.regions.forEach(region => {
                            if (region.name && region.districts) {
                                tzLocations[region.name] = {};
                                region.districts.forEach(district => {
                                    if (district.name && district.wards) {
                                        tzLocations[region.name][district.name] = district.wards;
                                    }
                                });
                            }
                        });
                    } else {
                        tzLocations = json;
                    }
                    return tzLocations; 
                })
                .catch(err2 => {
                    console.error('Failed to load locations from alternative path:', err2);
                    return {};
                });
        });
}

// Populate select dropdown
function populateSelect(selectEl, items, placeholder = 'Select') {
    if (!selectEl) return;
    selectEl.innerHTML = '';
    const opt = document.createElement('option');
    opt.value = '';
    opt.textContent = placeholder;
    selectEl.appendChild(opt);
    (items || []).forEach(v => {
        const o = document.createElement('option');
        o.value = v;
        o.textContent = v;
        selectEl.appendChild(o);
    });
}

// Step navigation functions
function setStepActive(step) {
    document.querySelectorAll('#wizardSteps .wizard-step').forEach((s) => {
        const stepNum = parseInt(s.getAttribute('data-step'));
        if (stepNum < step) {
            s.classList.add('completed');
            s.classList.remove('active');
        } else if (stepNum === step) {
            s.classList.add('active');
            s.classList.remove('completed');
        } else {
            s.classList.remove('active');
            s.classList.remove('completed');
        }
    });
}

// Check if element is visible
function isElementVisible(element) {
    if (!element) return false;
    const style = window.getComputedStyle(element);
    if (style.display === 'none' || style.visibility === 'hidden' || style.opacity === '0') {
        return false;
    }
    // Check if parent is hidden
    let parent = element.parentElement;
    while (parent && parent !== document.body) {
        const parentStyle = window.getComputedStyle(parent);
        if (parentStyle.display === 'none' || parentStyle.visibility === 'hidden') {
            return false;
        }
        parent = parent.parentElement;
    }
    return true;
}

// Validate step before proceeding
function validateStep(step) {
    const stepEl = document.getElementById('step' + step);
    if (!stepEl) return true;
    
    // Get all required fields in this step
    const requiredFields = stepEl.querySelectorAll('input[required], select[required], textarea[required]');
    const missingFields = [];
    
    requiredFields.forEach(field => {
        // Skip hidden fields (not visible to user)
        if (!isElementVisible(field)) {
            return;
        }
        
        // Skip file fields (they're optional)
        if (field.type === 'file') {
            return;
        }
        
        // Skip gender field if it's not required (hidden for father/mother)
        if (field.id === 'gender' && !field.hasAttribute('required')) {
            return;
        }
        
        let isValid = true;
        const value = field.value ? field.value.trim() : '';
        
        if (field.type === 'date') {
            isValid = value !== '';
        } else if (field.tagName === 'SELECT') {
            isValid = value !== '';
        } else {
            isValid = value !== '';
        }
        
        if (!isValid) {
            missingFields.push(field);
            field.classList.add('is-invalid');
            
            // Add invalid feedback to parent form-floating
            const formFloating = field.closest('.form-floating');
            if (formFloating) {
                const label = formFloating.querySelector('label');
                if (label && !label.querySelector('.text-danger')) {
                    const errorText = document.createElement('small');
                    errorText.className = 'text-danger d-block mt-1';
                    errorText.textContent = 'This field is required';
                    formFloating.appendChild(errorText);
                }
            }
        } else {
            field.classList.remove('is-invalid');
            
            // Remove error text
            const formFloating = field.closest('.form-floating');
            if (formFloating) {
                const errorText = formFloating.querySelector('.text-danger.d-block');
                if (errorText) {
                    errorText.remove();
                }
            }
        }
    });
    
    if (missingFields.length > 0) {
        // Scroll to first missing field
        missingFields[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => {
            missingFields[0].focus();
        }, 500);
        
        Swal.fire({
            icon: 'warning',
            title: 'Required Fields Missing',
            html: `Please fill in all required fields (marked with <span class="text-danger">*</span>) before proceeding.<br><br>Found ${missingFields.length} missing field(s).`,
            confirmButtonText: 'OK'
        });
        
        return false;
    }
    
    return true;
}

function showStep(stepToShow, stepToHide) {
    // Validate current step before proceeding
    if (stepToHide && !validateStep(stepToHide)) {
        return false;
    }
    
    // If moving to step 5, generate summary
    if (stepToShow === 5) {
        generateSummary();
    }
    
    const showEl = document.getElementById('step' + stepToShow);
    const hideEl = document.getElementById('step' + stepToHide);
    if (hideEl) {
        hideEl.classList.add('fade-out');
        setTimeout(() => {
            hideEl.style.display = 'none';
            hideEl.classList.remove('fade-out');
        }, 500);
    }
    if (showEl) {
        showEl.style.display = '';
        showEl.classList.add('fade-in');
        setTimeout(() => { showEl.classList.remove('fade-in'); }, 500);
    }
    setStepActive(stepToShow);
    return true;
}

// Generate summary of all entered information
function generateSummary() {
    const form = document.getElementById('addMemberForm');
    if (!form) return;
    
    const summaryContent = document.getElementById('summaryContent');
    if (!summaryContent) return;
    
    // Helper function to get field value
    const getValue = (id) => {
        const field = document.getElementById(id);
        if (!field) return 'N/A';
        if (field.type === 'file') {
            return field.files.length > 0 ? field.files[0].name : 'Not uploaded';
        }
        return field.value ? field.value.trim() : 'N/A';
    };
    
    // Helper function to get select text
    const getSelectText = (id) => {
        const field = document.getElementById(id);
        if (!field || !field.value) return 'N/A';
        return field.options[field.selectedIndex].text;
    };
    
    // Helper function to format date
    const formatDate = (dateStr) => {
        if (!dateStr || dateStr === 'N/A') return 'N/A';
        try {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        } catch (e) {
            return dateStr;
        }
    };
    
    // Helper function to format phone
    const formatPhone = (phone) => {
        if (!phone || phone === 'N/A') return 'N/A';
        return phone.startsWith('+255') ? phone : '+255' + phone;
    };
    
    let html = '<div class="row g-4">';
    
    // Step 1: Personal Information
    html += `
        <div class="col-12">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><strong>Membership Type:</strong> ${getSelectText('membership_type')}</div>
                        <div class="col-md-4"><strong>Member Type:</strong> ${getSelectText('member_type')}</div>
                        <div class="col-md-4"><strong>Branch/Campus:</strong> ${getSelectText('campus_id')}</div>
                        <div class="col-md-4"><strong>Community:</strong> ${getSelectText('community_id') || 'N/A'}</div>
                        <div class="col-md-4"><strong>Full Name:</strong> ${getValue('full_name')}</div>
                        <div class="col-md-4"><strong>Gender:</strong> ${getSelectText('gender')}</div>
                        <div class="col-md-4"><strong>Date of Birth:</strong> ${formatDate(getValue('date_of_birth'))}</div>
                        <div class="col-md-4"><strong>Education Level:</strong> ${getSelectText('education_level') || 'N/A'}</div>
                        <div class="col-md-4"><strong>Profession:</strong> ${getValue('profession')}</div>
                        <div class="col-md-4"><strong>Working Area:</strong> ${getValue('working_area') || 'N/A'}</div>
                        <div class="col-md-4"><strong>NIDA Number:</strong> ${getValue('nida_number') || 'N/A'}</div>
                        <div class="col-md-4"><strong>Baptism Status:</strong> ${getSelectText('baptism_status')}</div>
                        ${getValue('baptism_status') === 'baptized' ? `
                            <div class="col-md-4"><strong>Baptism Date:</strong> ${formatDate(getValue('baptism_date'))}</div>
                            <div class="col-md-4"><strong>Baptism Location:</strong> ${getValue('baptism_location') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Baptized By:</strong> ${getValue('baptized_by') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Baptism Certificate:</strong> ${getValue('baptism_certificate_number') || 'N/A'}</div>
                        ` : ''}
                        <div class="col-md-4"><strong>Profile Picture:</strong> ${getValue('profile_picture')}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Step 2: Other Information
    html += `
        <div class="col-12">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-address-card me-2"></i>Contact & Location Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><strong>Phone Number:</strong> ${formatPhone(getValue('phone_number'))}</div>
                        <div class="col-md-4"><strong>Email:</strong> ${getValue('email') || 'N/A'}</div>
                        <div class="col-md-4"><strong>Region:</strong> ${getSelectText('region')}</div>
                        <div class="col-md-4"><strong>District:</strong> ${getSelectText('district')}</div>
                        <div class="col-md-4"><strong>Ward:</strong> ${getValue('ward')}</div>
                        <div class="col-md-4"><strong>Street:</strong> ${getValue('street')}</div>
                        <div class="col-md-4"><strong>P O Box:</strong> ${getValue('address')}</div>
                        <div class="col-md-4"><strong>Tribe:</strong> ${getSelectText('tribe') === 'Other' ? getValue('other_tribe') : getSelectText('tribe')}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Social Welfare Information -->
        <div class="col-12">
            <div class="card border-danger mb-3">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0"><i class="fas fa-hand-holding-heart me-2"></i>Social Welfare Status</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><strong>Orphan Status:</strong> ${
                            ({
                                'not_orphan': 'Si Yatima',
                                'father_deceased': 'Baba amefariki',
                                'mother_deceased': 'Mama amefariki',
                                'both_deceased': 'Wote wamefariki'
                            })[getValue('orphan_status')] || getValue('orphan_status')
                        }</div>
                        <div class="col-md-4"><strong>Has Disability:</strong> ${document.getElementById('disability_status')?.checked ? 'Yes (' + (getValue('disability_type') || 'N/A') + ')' : 'No'}</div>
                        <div class="col-md-4"><strong>Vulnerable:</strong> ${document.getElementById('vulnerable_status')?.checked ? 'Yes (' + (getValue('vulnerable_type') || 'N/A') + ')' : 'No'}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Step 3: Current Residence
    html += `
        <div class="col-12">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-home me-2"></i>Current Residence</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><strong>Region:</strong> ${getSelectText('residence_region')}</div>
                        <div class="col-md-4"><strong>District:</strong> ${getSelectText('residence_district')}</div>
                        <div class="col-md-4"><strong>Ward:</strong> ${getValue('residence_ward')}</div>
                        <div class="col-md-4"><strong>Street:</strong> ${getValue('residence_street')}</div>
                        <div class="col-md-4"><strong>Road Name:</strong> ${getValue('residence_road') || 'N/A'}</div>
                        <div class="col-md-4"><strong>House Number:</strong> ${getValue('residence_house_number') || 'N/A'}</div>
                        <div class="col-md-6"><strong>Neighbor Name:</strong> ${getValue('neighbor_name') || 'N/A'}</div>
                        <div class="col-md-6"><strong>Neighbor Phone:</strong> ${getValue('neighbor_phone') ? formatPhone(getValue('neighbor_phone')) : 'N/A'}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Step 4: Family Information
    const maritalStatus = getSelectText('marital_status');
    html += `
        <div class="col-12">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>Family Information</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4"><strong>Marital Status:</strong> ${maritalStatus || 'N/A'}</div>
                        ${maritalStatus === 'Married' ? `
                            <div class="col-md-4"><strong>Wedding Type:</strong> ${getSelectText('wedding_type') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Wedding Date:</strong> ${formatDate(getValue('wedding_date')) || 'N/A'}</div>
                            <div class="col-12"><hr><h6 class="text-primary">Spouse Information</h6></div>
                            <div class="col-md-4"><strong>Spouse Name:</strong> ${getValue('spouse_full_name') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse Date of Birth:</strong> ${formatDate(getValue('spouse_date_of_birth')) || 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse Education:</strong> ${getSelectText('spouse_education_level') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse Profession:</strong> ${getValue('spouse_profession') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse NIDA:</strong> ${getValue('spouse_nida_number') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse Email:</strong> ${getValue('spouse_email') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse Church Member:</strong> ${getSelectText('spouse_church_member') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse Phone:</strong> ${getValue('spouse_phone_number') ? formatPhone(getValue('spouse_phone_number')) : 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse Tribe:</strong> ${getSelectText('spouse_tribe') === 'Other' ? getValue('spouse_other_tribe') : getSelectText('spouse_tribe') || 'N/A'}</div>
                            <div class="col-md-4"><strong>Spouse Baptism Status:</strong> ${getSelectText('spouse_baptism_status') || 'N/A'}</div>
                            ${getValue('spouse_baptism_status') === 'baptized' ? `
                                <div class="col-md-4"><strong>Spouse Baptism Date:</strong> ${formatDate(getValue('spouse_baptism_date')) || 'N/A'}</div>
                                <div class="col-md-4"><strong>Spouse Baptism Location:</strong> ${getValue('spouse_baptism_location') || 'N/A'}</div>
                                <div class="col-md-4"><strong>Spouse Baptized By:</strong> ${getValue('spouse_baptized_by') || 'N/A'}</div>
                            ` : ''}
                            <div class="col-md-4"><strong>Spouse Picture:</strong> ${getValue('spouse_profile_picture')}</div>
                        ` : ''}
                        <div class="col-12"><hr><h6 class="text-warning">Guardian Information</h6></div>
                        <div class="col-md-4"><strong>Guardian Name:</strong> ${getValue('guardian_name') || 'N/A'}</div>
                        <div class="col-md-4"><strong>Guardian Phone:</strong> ${getValue('guardian_phone') ? formatPhone(getValue('guardian_phone')) : 'N/A'}</div>
                        <div class="col-md-4"><strong>Guardian Relationship:</strong> ${getValue('guardian_relationship') || 'N/A'}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Children Information
    const childrenCount = parseInt(getValue('children_count')) || 0;
    if (childrenCount > 0) {
        html += `
            <div class="col-12">
                <div class="card border-primary mb-3">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="fas fa-child me-2"></i>Children Information (${childrenCount} ${childrenCount === 1 ? 'Child' : 'Children'})</h6>
                    </div>
                    <div class="card-body">
        `;
        
        for (let i = 0; i < childrenCount; i++) {
            const childNameEl = form.querySelector(`input[name="children[${i}][full_name]"]`);
            const childGenderEl = form.querySelector(`select[name="children[${i}][gender]"]`);
            const childDOBEl = form.querySelector(`input[name="children[${i}][date_of_birth]"]`);
            const childBaptismEl = form.querySelector(`select[name="children[${i}][baptism_status]"]`);
            
            const childName = childNameEl?.value || 'N/A';
            const childGender = childGenderEl?.options[childGenderEl.selectedIndex]?.text || 'N/A';
            const childDOB = childDOBEl?.value || 'N/A';
            const childBaptism = childBaptismEl?.options[childBaptismEl.selectedIndex]?.text || 'N/A';
            
            html += `
                <div class="border rounded p-3 mb-3">
                    <h6 class="text-primary">Child ${i + 1}</h6>
                    <div class="row g-3">
                        <div class="col-md-4"><strong>Name:</strong> ${childName}</div>
                        <div class="col-md-4"><strong>Gender:</strong> ${childGender}</div>
                        <div class="col-md-4"><strong>Date of Birth:</strong> ${formatDate(childDOB)}</div>
                            <div class="col-md-4"><strong>Baptism Status:</strong> ${childBaptism}</div>
                            ${childBaptism === 'Baptized' ? `
                                <div class="col-md-4"><strong>Baptism Date:</strong> ${formatDate(form.querySelector(`input[name="children[${i}][baptism_date]"]`)?.value || '')}</div>
                                <div class="col-md-4"><strong>Baptism Location:</strong> ${form.querySelector(`input[name="children[${i}][baptism_location]"]`)?.value || 'N/A'}</div>
                                <div class="col-md-4"><strong>Baptized By:</strong> ${form.querySelector(`input[name="children[${i}][baptized_by]"]`)?.value || 'N/A'}</div>
                            ` : ''}
                            <div class="col-12"><hr></div>
                            <div class="col-md-4"><strong>Orphan Status:</strong> ${form.querySelector(`select[name="children[${i}][orphan_status]"]`)?.options[form.querySelector(`select[name="children[${i}][orphan_status]"]`).selectedIndex]?.text || 'N/A'}</div>
                            <div class="col-md-4"><strong>Has Disability:</strong> ${form.querySelector(`input[name="children[${i}][disability_status]"]`)?.checked ? 'Yes (' + (form.querySelector(`input[name="children[${i}][disability_type]"]`)?.value || 'N/A') + ')' : 'No'}</div>
                            <div class="col-md-4"><strong>Vulnerable:</strong> ${form.querySelector(`input[name="children[${i}][vulnerable_status]"]`)?.checked ? 'Yes (' + (form.querySelector(`input[name="children[${i}][vulnerable_type]"]`)?.value || 'N/A') + ')' : 'No'}</div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        html += `
                    </div>
                </div>
            </div>
        `;
    }
    
    html += '</div>';
    summaryContent.innerHTML = html;
}

// Load communities when campus is selected
function loadCommunities(campusId) {
    const communitySelect = document.getElementById('community_id');
    if (!communitySelect || !campusId) {
        if (communitySelect) {
            communitySelect.innerHTML = '<option value="">Select Community...</option>';
        }
        return;
    }
    
    fetch(`/campuses/${campusId}/communities/json`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        const communities = Array.isArray(data) ? data : (data?.communities || []);
        communitySelect.innerHTML = '<option value="">Select Community...</option>';
        communities.forEach(community => {
            const option = document.createElement('option');
            option.value = community.id;
            option.textContent = community.name;
            communitySelect.appendChild(option);
        });
    })
    .catch(err => {
        console.error('Failed to load communities:', err);
        communitySelect.innerHTML = '<option value="">Select Community...</option>';
    });
}

// Setup cascading selects for regions, districts, wards
function setupLocationCascading() {
    ensureLocationsLoaded().then(() => {
        // Setup for permanent address (Step 2)
        const regionEl = document.getElementById('region');
        const districtEl = document.getElementById('district');
        const wardEl = document.getElementById('ward');
        
        // Populate regions regardless of whether other fields exist
        if (regionEl) {
            const regions = Object.keys(tzLocations || {});
            populateSelect(regionEl, regions, 'Select Region...');
            
            // Setup district and ward updates if they exist
            if (districtEl) {
                function updateDistricts() {
                    const region = regionEl.value;
                    const districts = region && tzLocations[region] ? Object.keys(tzLocations[region]) : [];
                    populateSelect(districtEl, districts, 'Select District...');
                    
                    // Update wards if ward is a select dropdown
                    if (wardEl && wardEl.tagName === 'SELECT') {
                        updateWards();
                    }
                }
                
                regionEl.addEventListener('change', updateDistricts);
                
                // Setup ward updates if ward is a select dropdown
                if (wardEl && wardEl.tagName === 'SELECT') {
                    function updateWards() {
                        const region = regionEl.value;
                        const district = districtEl.value;
                        const wards = region && district && tzLocations[region] && tzLocations[region][district] 
                            ? tzLocations[region][district] : [];
                        populateSelect(wardEl, wards, 'Select Ward...');
                    }
                    
                    districtEl.addEventListener('change', updateWards);
                }
            }
        }
        
        // Setup for residence address (Step 3)
        const residenceRegionEl = document.getElementById('residence_region');
        const residenceDistrictEl = document.getElementById('residence_district');
        
        if (residenceRegionEl) {
            const regions = Object.keys(tzLocations || {});
            populateSelect(residenceRegionEl, regions, 'Select Region...');
            
            if (residenceDistrictEl) {
                function updateResidenceDistricts() {
                    const region = residenceRegionEl.value;
                    const districts = region && tzLocations[region] ? Object.keys(tzLocations[region]) : [];
                    populateSelect(residenceDistrictEl, districts, 'Select District...');
                }
                
                residenceRegionEl.addEventListener('change', updateResidenceDistricts);
            }
        }
    }).catch(err => {
        console.error('Error setting up location cascading:', err);
        // Still try to populate regions even if there's an error
        const regionEl = document.getElementById('region');
        if (regionEl) {
            regionEl.innerHTML = '<option value="">Error loading regions</option>';
        }
    });
}

// Setup tribe selects
function setupTribeSelects() {
    const tribeEl = document.getElementById('tribe');
    const spouseTribeEl = document.getElementById('spouse_tribe');
    const otherTribeWrapper = document.getElementById('otherTribeWrapper');
    const spouseOtherTribeWrapper = document.getElementById('spouseOtherTribeWrapper');
    
    if (tribeEl) {
        populateSelect(tribeEl, tribeList, 'Select Tribe...');
        tribeEl.addEventListener('change', function() {
            if (otherTribeWrapper) {
                otherTribeWrapper.style.display = this.value === 'Other' ? 'block' : 'none';
                if (this.value !== 'Other') {
                    document.getElementById('other_tribe').value = '';
                }
            }
        });
    }
    
    if (spouseTribeEl) {
        populateSelect(spouseTribeEl, tribeList, 'Select Tribe...');
        spouseTribeEl.addEventListener('change', function() {
            if (spouseOtherTribeWrapper) {
                spouseOtherTribeWrapper.style.display = this.value === 'Other' ? 'block' : 'none';
                if (this.value !== 'Other') {
                    document.getElementById('spouse_other_tribe').value = '';
                }
            }
        });
    }
}

// Initialize all form handlers
document.addEventListener('DOMContentLoaded', function() {
    // Step navigation event listeners with validation
    const nextStep1 = document.getElementById('nextStep1');
    const nextStep2 = document.getElementById('nextStep2');
    const nextStep3 = document.getElementById('nextStep3');
    const nextStep4 = document.getElementById('nextStep4');
    const prevStep2 = document.getElementById('prevStep2');
    const prevStep3 = document.getElementById('prevStep3');
    const prevStep4 = document.getElementById('prevStep4');
    const prevStep5 = document.getElementById('prevStep5');
    
    // Next buttons - validate before proceeding
    if (nextStep1) nextStep1.addEventListener('click', function(e) {
        e.preventDefault();
        if (validateStep(1)) {
            showStep(2, 1);
        }
    });
    if (nextStep2) nextStep2.addEventListener('click', function(e) {
        e.preventDefault();
        if (validateStep(2)) {
            showStep(3, 2);
        }
    });
    if (nextStep3) nextStep3.addEventListener('click', function(e) {
        e.preventDefault();
        if (validateStep(3)) {
            showStep(4, 3);
        }
    });
    if (nextStep4) nextStep4.addEventListener('click', function(e) {
        e.preventDefault();
        if (validateStep(4)) {
            showStep(5, 4);
        }
    });
    
    // Previous buttons - no validation needed
    if (prevStep2) prevStep2.addEventListener('click', () => showStep(1, 2));
    if (prevStep3) prevStep3.addEventListener('click', () => showStep(2, 3));
    if (prevStep4) prevStep4.addEventListener('click', () => showStep(3, 4));
    if (prevStep5) prevStep5.addEventListener('click', () => showStep(4, 5));
    
    // Get references to elements
    const memberTypeSelect = document.getElementById('member_type');
    const genderFieldWrapper = document.getElementById('genderFieldWrapper');
    const genderSelect = document.getElementById('gender');
    const guardianSection = document.getElementById('guardianSection');
    const membershipTypeSelect = document.getElementById('membership_type');
    
    // Function to update guardian section visibility based on membership type and member type
    function updateGuardianSectionVisibility() {
        if (!guardianSection) return;
        
        const membershipType = membershipTypeSelect ? membershipTypeSelect.value : '';
        const memberType = memberTypeSelect ? memberTypeSelect.value : '';
        
        // Show guardian section if:
        // 1. Member type is independent (always show for independent)
        // 2. Membership type is temporary (required for all temporary members)
        const shouldShow = (memberType === 'independent') || (membershipType === 'temporary');
        
        guardianSection.style.display = shouldShow ? 'block' : 'none';
        
        // Update required attributes
        const guardianName = document.getElementById('guardian_name');
        const guardianPhone = document.getElementById('guardian_phone');
        const guardianRelationship = document.getElementById('guardian_relationship');
        
        if (shouldShow) {
            if (guardianName) guardianName.setAttribute('required', 'required');
            if (guardianPhone) guardianPhone.setAttribute('required', 'required');
            if (guardianRelationship) guardianRelationship.setAttribute('required', 'required');
        } else {
            if (guardianName) guardianName.removeAttribute('required');
            if (guardianPhone) guardianPhone.removeAttribute('required');
            if (guardianRelationship) guardianRelationship.removeAttribute('required');
        }
    }
    
    // Member type change handler - show/hide gender field and guardian section
    if (memberTypeSelect && genderFieldWrapper && genderSelect) {
        function handleMemberTypeChange() {
            const memberType = memberTypeSelect.value;
            
            if (memberType === 'father') {
                // Hide gender field and set to male
                genderFieldWrapper.style.display = 'none';
                genderSelect.value = 'male';
                genderSelect.removeAttribute('required');
            } else if (memberType === 'mother') {
                // Hide gender field and set to female
                genderFieldWrapper.style.display = 'none';
                genderSelect.value = 'female';
                genderSelect.removeAttribute('required');
            } else if (memberType === 'independent') {
                // Show gender field and make it required
                genderFieldWrapper.style.display = 'block';
                genderSelect.setAttribute('required', 'required');
                // Reset gender if it was auto-set
                if (genderSelect.value === 'male' || genderSelect.value === 'female') {
                    genderSelect.value = '';
                }
            } else {
                // Default: show gender field
                genderFieldWrapper.style.display = 'block';
                genderSelect.setAttribute('required', 'required');
            }
            
            // Update guardian section visibility after member type change
            updateGuardianSectionVisibility();
        }
        
        memberTypeSelect.addEventListener('change', handleMemberTypeChange);
        
        // Initialize on page load
        handleMemberTypeChange();
    }
    
    // Baptism status change handlers
    const baptismStatus = document.getElementById('baptism_status');
    if (baptismStatus) {
        baptismStatus.addEventListener('change', function() {
            const baptized = this.value === 'baptized';
            const wrappers = ['baptismDateWrapper', 'baptismLocationWrapper', 'baptizedByWrapper', 'baptismCertificateWrapper'];
            wrappers.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = baptized ? 'block' : 'none';
            });
        });
    }
    
    // Marital status change handler
    const maritalStatus = document.getElementById('marital_status');
    if (maritalStatus) {
        maritalStatus.addEventListener('change', function() {
            const spouseSection = document.getElementById('spouseInfoSection');
            const weddingSection = document.getElementById('weddingInfoSection');
            if (this.value === 'married') {
                if (spouseSection) spouseSection.style.display = 'block';
                if (weddingSection) weddingSection.style.display = 'block';
            } else {
                if (spouseSection) spouseSection.style.display = 'none';
                if (weddingSection) weddingSection.style.display = 'none';
            }
        });
    }
    
    // Spouse baptism status change handler
    const spouseBaptismStatus = document.getElementById('spouse_baptism_status');
    if (spouseBaptismStatus) {
        spouseBaptismStatus.addEventListener('change', function() {
            const baptized = this.value === 'baptized';
            const wrappers = ['spouseBaptismDateWrapper', 'spouseBaptismLocationWrapper', 'spouseBaptizedByWrapper', 'spouseBaptismCertificateWrapper'];
            wrappers.forEach(id => {
                const el = document.getElementById(id);
                if (el) el.style.display = baptized ? 'block' : 'none';
            });
        });
    }
    
    // Spouse disability status change handler
    const spouseDisabilityStatus = document.getElementById('spouse_disability_status');
    if (spouseDisabilityStatus) {
        spouseDisabilityStatus.addEventListener('change', function() {
            const wrapper = document.getElementById('spouseDisabilityTypeWrapper');
            if (wrapper) wrapper.style.display = this.checked ? 'block' : 'none';
        });
    }
    
    // Spouse church member change handler
    const spouseChurchMember = document.getElementById('spouse_church_member');
    const spouseCampusCommunityFields = document.getElementById('spouseCampusCommunityFields');
    if (spouseChurchMember && spouseCampusCommunityFields) {
        spouseChurchMember.addEventListener('change', function() {
            const isMember = this.value === 'yes';
            spouseCampusCommunityFields.style.display = isMember ? 'block' : 'none';
            
            const spouseCampusSelect = document.getElementById('spouse_campus_id');
            const spouseCommunitySelect = document.getElementById('spouse_community_id');
            
            if (isMember) {
                // Add required attribute
                if (spouseCampusSelect) spouseCampusSelect.setAttribute('required', 'required');
                if (spouseCommunitySelect) spouseCommunitySelect.setAttribute('required', 'required');
                // Load campuses for spouse
                loadCampusesForSpouse();
            } else {
                // Remove required attribute
                if (spouseCampusSelect) spouseCampusSelect.removeAttribute('required');
                if (spouseCommunitySelect) spouseCommunitySelect.removeAttribute('required');
                // Clear selections
                if (spouseCampusSelect) spouseCampusSelect.value = '';
                if (spouseCommunitySelect) {
                    spouseCommunitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
                }
            }
        });
    }
    
    // Spouse campus selection change handler
    const spouseCampusSelect = document.getElementById('spouse_campus_id');
    if (spouseCampusSelect) {
        spouseCampusSelect.addEventListener('change', function() {
            const campusId = this.value;
            if (campusId) {
                loadCommunitiesForSpouse(campusId);
            } else {
                const spouseCommunitySelect = document.getElementById('spouse_community_id');
                if (spouseCommunitySelect) {
                    spouseCommunitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
                }
            }
        });
    }
    
    // Membership type change handler - show/hide temporary duration fields and guardian section
    const temporaryDurationWrapper = document.getElementById('temporaryMembershipDurationWrapper');
    
    if (membershipTypeSelect && temporaryDurationWrapper) {
        membershipTypeSelect.addEventListener('change', function() {
            if (this.value === 'temporary') {
                temporaryDurationWrapper.style.display = 'block';
            } else {
                temporaryDurationWrapper.style.display = 'none';
            }
            // Update guardian section visibility when membership type changes
            updateGuardianSectionVisibility();
        });
        // Initialize on page load
        if (membershipTypeSelect.value === 'temporary') {
            temporaryDurationWrapper.style.display = 'block';
        }
        // Also initialize guardian section visibility on page load
        updateGuardianSectionVisibility();
    }
    
    // Campus change handler - load communities
    const campusSelect = document.getElementById('campus_id');
    if (campusSelect) {
        campusSelect.addEventListener('change', function() {
            loadCommunities(this.value);
        });
        // Load communities for initially selected campus
        if (campusSelect.value) {
            loadCommunities(campusSelect.value);
        }
    }
    
    // Welfare status change handlers for main member
    const disabilityStatus = document.getElementById('disability_status');
    if (disabilityStatus) {
        disabilityStatus.addEventListener('change', function() {
            const wrapper = document.getElementById('disabilityTypeWrapper');
            if (wrapper) wrapper.style.display = this.checked ? 'block' : 'none';
        });
    }
    
    const vulnerableStatus = document.getElementById('vulnerable_status');
    if (vulnerableStatus) {
        vulnerableStatus.addEventListener('change', function() {
            const wrapper = document.getElementById('vulnerableTypeWrapper');
            if (wrapper) wrapper.style.display = this.checked ? 'block' : 'none';
        });
    }
    
    // Children count handler
    const childrenCount = document.getElementById('children_count');
    if (childrenCount) {
        childrenCount.addEventListener('change', function() {
            const count = parseInt(this.value) || 0;
            const container = document.getElementById('childrenFields');
            if (!container) return;
            
            if (count === 0) {
                container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle me-2"></i>Enter the number of children above to add their information.</div>';
                return;
            }
            
            container.innerHTML = '';
            
            for (let i = 0; i < count; i++) {
                const childHtml = `
                    <div class="card mb-3 border-light">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">Child ${i + 1}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-4 mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="children[${i}][full_name]" required>
                                        <label>Full Name <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select" name="children[${i}][gender]" required>
                                            <option value="">Select...</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                        <label>Gender <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="children[${i}][date_of_birth]" required>
                                        <label>Date of Birth <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-floating">
                                        <select class="form-select child-membership-status" name="children[${i}][is_church_member]" data-child-index="${i}">
                                            <option value="no">No</option>
                                            <option value="yes">Yes</option>
                                        </select>
                                        <label>Church Member?</label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Campus and Fellowship (only for member children) -->
                            <div class="row g-4 mb-3 child-member-fields" id="child${i}_member_fields" style="display:none;">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select child-campus-select" name="children[${i}][campus_id]" id="child${i}_campus_id" data-child-index="${i}">
                                            <option value="">Select Campus...</option>
                                        </select>
                                        <label>Campus/Branch <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select child-community-select" name="children[${i}][community_id]" id="child${i}_community_id" data-child-index="${i}">
                                            <option value="">Select Fellowship...</option>
                                        </select>
                                        <label>Fellowship (Jumuiya) <span class="text-danger">*</span></label>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location for children living outside main area -->
                            <div class="row g-4 mb-3">
                                <div class="col-md-12">
                                    <div class="form-check">
                                        <input class="form-check-input child-outside-area" type="checkbox" name="children[${i}][lives_outside_main_area]" value="yes" id="child${i}_outside_area" data-child-index="${i}">
                                        <label class="form-check-label" for="child${i}_outside_area">
                                            This child lives outside the main church area
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 mb-3 child-location-fields" id="child${i}_location_fields" style="display:none;">
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select child-region-select" name="children[${i}][region]" id="child${i}_region" data-child-index="${i}">
                                            <option value="">Select Region...</option>
                                        </select>
                                        <label>Region</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <select class="form-select child-district-select" name="children[${i}][district]" id="child${i}_district" data-child-index="${i}">
                                            <option value="">Select District...</option>
                                        </select>
                                        <label>District</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="children[${i}][city_town]" placeholder="City/Town">
                                        <label>City/Town</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="children[${i}][current_church_attended]" placeholder="Church name">
                                        <label>Current Church Attended (Optional)</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-floating">
                                        <input type="text" class="form-control child-phone-input" name="children[${i}][phone_number]" id="child${i}_phone_number" placeholder="+255XXXXXXXXX" pattern="^\+255[0-9]{9,15}$">
                                        <label>Phone Number (Optional)</label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Format: +255XXXXXXXXX</small>
                                </div>
                            </div>
                            
                            <hr class="my-3">
                            <h6 class="text-primary mb-3"><i class="fas fa-tint me-2"></i>Child ${i + 1} Baptism Information</h6>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select child-baptism-status" name="children[${i}][baptism_status]" data-child-index="${i}">
                                            <option value="">Select...</option>
                                            <option value="baptized">Baptized</option>
                                            <option value="not_baptized">Not Baptized</option>
                                        </select>
                                        <label>Baptism Status</label>
                                    </div>
                                </div>
                                <div class="col-md-4 child-baptism-date-wrapper" id="child${i}_baptism_date_wrapper" style="display:none;">
                                    <div class="form-floating">
                                        <input type="date" class="form-control" name="children[${i}][baptism_date]">
                                        <label>Baptism Date</label>
                                    </div>
                                </div>
                                <div class="col-md-4 child-baptism-location-wrapper" id="child${i}_baptism_location_wrapper" style="display:none;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="children[${i}][baptism_location]" placeholder="Church name">
                                        <label>Baptism Location/Church</label>
                                    </div>
                                </div>
                                <div class="col-md-4 child-baptized-by-wrapper" id="child${i}_baptized_by_wrapper" style="display:none;">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" name="children[${i}][baptized_by]" placeholder="Pastor name">
                                        <label>Baptized By</label>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3">
                            <h6 class="text-danger mb-3"><i class="fas fa-hand-holding-heart me-2"></i>Child ${i + 1} Welfare Status</h6>
                            <div class="row g-4 mb-3">
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" name="children[${i}][orphan_status]">
                                            <option value="not_orphan">Si Yatima (Not Orphan)</option>
                                            <option value="father_deceased">Baba amefariki (Father Deceased)</option>
                                            <option value="mother_deceased">Mama amefariki (Mother Deceased)</option>
                                            <option value="both_deceased">Wote wamefariki (Both Deceased)</option>
                                        </select>
                                        <label>Hali ya Uyathima (Orphan Status)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input child-disability-status" type="checkbox" name="children[${i}][disability_status]" value="1" data-child-index="${i}" id="child${i}_disability_status">
                                        <label class="form-check-label fw-bold" for="child${i}_disability_status">Ana Ulemavu? (Has Disability?)</label>
                                    </div>
                                    <div id="child${i}_disabilityTypeWrapper" class="mt-2" style="display:none;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="children[${i}][disability_type]" placeholder="Nature of disability">
                                            <label>Aina ya Ulemavu (Type)</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check form-switch pt-2">
                                        <input class="form-check-input child-vulnerable-status" type="checkbox" name="children[${i}][vulnerable_status]" value="1" data-child-index="${i}" id="child${i}_vulnerable_status">
                                        <label class="form-check-label fw-bold" for="child${i}_vulnerable_status">Hali Ngumu/Dhaifu? (Vulnerable?)</label>
                                    </div>
                                    <div id="child${i}_vulnerableTypeWrapper" class="mt-2" style="display:none;">
                                        <div class="form-floating">
                                            <input type="text" class="form-control" name="children[${i}][vulnerable_type]" placeholder="e.g. Poverty">
                                            <label>Aina ya Changamoto (Type)</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                container.innerHTML += childHtml;
            }
            
            // Add event listeners for child baptism status changes
            document.querySelectorAll('.child-baptism-status').forEach(function(select) {
                select.addEventListener('change', function() {
                    const childIndex = this.getAttribute('data-child-index');
                    const baptized = this.value === 'baptized';
                    const wrappers = [
                        'child' + childIndex + '_baptism_date_wrapper',
                        'child' + childIndex + '_baptism_location_wrapper',
                        'child' + childIndex + '_baptized_by_wrapper',
                        'child' + childIndex + '_baptism_certificate_wrapper'
                    ];
                    wrappers.forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.style.display = baptized ? 'block' : 'none';
                    });
                });
            });
            
            // Add event listeners for child "lives outside main area" checkbox
            document.querySelectorAll('.child-outside-area').forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const childIndex = this.getAttribute('data-child-index');
                    const locationFields = document.getElementById('child' + childIndex + '_location_fields');
                    if (locationFields) {
                        locationFields.style.display = this.checked ? 'block' : 'none';
                    }
                });
            });
        });
        
        // Use event delegation for dynamically added child fields (set up once on page load)
        const childrenContainer = document.getElementById('childrenFields');
        if (childrenContainer) {
            // Handle child membership status changes
            childrenContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('child-membership-status')) {
                    const childIndex = e.target.getAttribute('data-child-index');
                    const isMember = e.target.value === 'yes';
                    const memberFields = document.getElementById('child' + childIndex + '_member_fields');
                    if (memberFields) {
                        memberFields.style.display = isMember ? 'block' : 'none';
                        // Load campuses for child campus select
                        if (isMember) {
                            loadCampusesForChild(childIndex);
                        }
                    }
                }
            });
            
            // Handle child campus selection changes
            childrenContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('child-campus-select')) {
                    const childIndex = e.target.getAttribute('data-child-index');
                    const campusId = e.target.value;
                    if (campusId) {
                        loadCommunitiesForChild(campusId, childIndex);
                    } else {
                        const communitySelect = document.getElementById('child' + childIndex + '_community_id');
                        if (communitySelect) {
                            communitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
                        }
                    }
                }
            });
            
            // Handle child "lives outside main area" checkbox
            childrenContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('child-outside-area')) {
                    const childIndex = e.target.getAttribute('data-child-index');
                    const locationFields = document.getElementById('child' + childIndex + '_location_fields');
                    if (locationFields) {
                        locationFields.style.display = e.target.checked ? 'block' : 'none';
                        // Load regions when checkbox is checked
                        if (e.target.checked) {
                            setupChildLocationCascading(childIndex);
                        }
                    }
                }
            });
            
            // Handle child region selection changes (for cascading districts)
            childrenContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('child-region-select')) {
                    const childIndex = e.target.getAttribute('data-child-index');
                    const region = e.target.value;
                    const districtSelect = document.getElementById('child' + childIndex + '_district');
                    
                    if (districtSelect && tzLocations) {
                        const districts = region && tzLocations[region] ? Object.keys(tzLocations[region]) : [];
                        populateSelect(districtSelect, districts, 'Select District...');
                    }
                }
            });
            
            // Handle child welfare toggles
            childrenContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('child-disability-status')) {
                    const childIndex = e.target.getAttribute('data-child-index');
                    const wrapper = document.getElementById('child' + childIndex + '_disabilityTypeWrapper');
                    if (wrapper) wrapper.style.display = e.target.checked ? 'block' : 'none';
                }
                
                if (e.target.classList.contains('child-vulnerable-status')) {
                    const childIndex = e.target.getAttribute('data-child-index');
                    const wrapper = document.getElementById('child' + childIndex + '_vulnerableTypeWrapper');
                    if (wrapper) wrapper.style.display = e.target.checked ? 'block' : 'none';
                }
            });
        }
        
        // Function to load campuses for child campus select
        function loadCampusesForChild(childIndex) {
            const campusSelect = document.getElementById('child' + childIndex + '_campus_id');
            if (!campusSelect) return;
            
            fetch(campusesUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(campuses => {
                campusSelect.innerHTML = '<option value="">Select Campus...</option>';
                campuses.forEach(campus => {
                    const option = document.createElement('option');
                    option.value = campus.id;
                    option.textContent = campus.name + (campus.is_main_campus ? ' (Usharika)' : '');
                    campusSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Failed to load campuses for child:', err);
                campusSelect.innerHTML = '<option value="">Select Campus...</option>';
            });
        }
        
        // Function to load communities for child fellowship select
        function loadCommunitiesForChild(campusId, childIndex) {
            const communitySelect = document.getElementById('child' + childIndex + '_community_id');
            if (!communitySelect || !campusId) {
                if (communitySelect) {
                    communitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
                }
                return;
            }
            
            fetch(`/campuses/${campusId}/communities/json`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const communities = Array.isArray(data) ? data : (data?.communities || []);
                communitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
                communities.forEach(community => {
                    const option = document.createElement('option');
                    option.value = community.id;
                    option.textContent = community.name;
                    communitySelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Failed to load communities for child:', err);
                communitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
            });
        }
        
        // Function to set up location cascading for child fields
        function setupChildLocationCascading(childIndex) {
            ensureLocationsLoaded().then(() => {
                const regionSelect = document.getElementById('child' + childIndex + '_region');
                
                if (regionSelect && tzLocations) {
                    // Populate regions
                    const regions = Object.keys(tzLocations || {});
                    populateSelect(regionSelect, regions, 'Select Region...');
                }
            }).catch(err => {
                console.error('Error loading locations for child:', err);
            });
        }
        
        // Function to load campuses for spouse
        function loadCampusesForSpouse() {
            const campusSelect = document.getElementById('spouse_campus_id');
            if (!campusSelect) return;
            
            fetch(campusesUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(campuses => {
                campusSelect.innerHTML = '<option value="">Select Campus...</option>';
                campuses.forEach(campus => {
                    const option = document.createElement('option');
                    option.value = campus.id;
                    option.textContent = campus.name + (campus.is_main_campus ? ' (Usharika)' : '');
                    campusSelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Failed to load campuses for spouse:', err);
                campusSelect.innerHTML = '<option value="">Select Campus...</option>';
            });
        }
        
        // Function to load communities for spouse
        function loadCommunitiesForSpouse(campusId) {
            const communitySelect = document.getElementById('spouse_community_id');
            if (!communitySelect || !campusId) {
                if (communitySelect) {
                    communitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
                }
                return;
            }
            
            fetch(`/campuses/${campusId}/communities/json`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                const communities = Array.isArray(data) ? data : (data?.communities || []);
                communitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
                communities.forEach(community => {
                    const option = document.createElement('option');
                    option.value = community.id;
                    option.textContent = community.name;
                    communitySelect.appendChild(option);
                });
            })
            .catch(err => {
                console.error('Failed to load communities for spouse:', err);
                communitySelect.innerHTML = '<option value="">Select Fellowship...</option>';
            });
        }
    }
    
    // Envelope Number Real-time Validation
    const envelopeInput = document.getElementById('envelope_number');
    const communitySelectForEnvelope = document.getElementById('community_id');
    
    if (envelopeInput && communitySelectForEnvelope) {
        const checkEnvelope = function() {
            const envelope = envelopeInput.value.trim();
            const communityId = communitySelectForEnvelope.value;
            
            // Remove existing feedback
            const existingFeedback = envelopeInput.parentNode.querySelector('.envelope-feedback');
            if (existingFeedback) existingFeedback.remove();
            envelopeInput.classList.remove('is-invalid', 'is-valid');
            
            if (!envelope || !communityId) return;

            fetch(`/members/check-envelope?envelope=${encodeURIComponent(envelope)}&community_id=${communityId}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                const feedback = document.createElement('small');
                feedback.className = `envelope-feedback d-block mt-1 fw-bold ${data.available ? 'text-success' : 'text-danger'}`;
                feedback.innerHTML = `<i class="fas fa-${data.available ? 'check' : 'times'}-circle me-1"></i>${data.message}`;
                envelopeInput.parentNode.appendChild(feedback);
                
                if (data.available) {
                    envelopeInput.classList.add('is-valid');
                } else {
                    envelopeInput.classList.add('is-invalid');
                }
            })
            .catch(err => console.error('Error checking envelope:', err));
        };

        envelopeInput.addEventListener('blur', checkEnvelope);
        communitySelectForEnvelope.addEventListener('change', checkEnvelope);
    }

    // Setup location cascading and tribe selects
    // Ensure locations are loaded first, then setup cascading
    ensureLocationsLoaded().then(() => {
        setupLocationCascading();
    }).catch(err => {
        console.error('Error loading locations:', err);
        // Still try to setup even if there's an error
        setupLocationCascading();
    });
    setupTribeSelects();
    
    // Add real-time validation feedback
    const allRequiredFields = document.querySelectorAll('input[required], select[required], textarea[required]');
    allRequiredFields.forEach(field => {
        // Remove invalid class and error message on input/change
        const clearError = function() {
            if (this.value && this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                const formFloating = this.closest('.form-floating');
                if (formFloating) {
                    const errorText = formFloating.querySelector('.text-danger.d-block');
                    if (errorText) {
                        errorText.remove();
                    }
                }
            }
        };
        
        field.addEventListener('input', clearError);
        field.addEventListener('change', clearError);
        
        // Validate on blur
        field.addEventListener('blur', function() {
            if (this.hasAttribute('required') && this.type !== 'file') {
                const value = this.value ? this.value.trim() : '';
                if (value === '' && isElementVisible(this)) {
                    this.classList.add('is-invalid');
                    const formFloating = this.closest('.form-floating');
                    if (formFloating && !formFloating.querySelector('.text-danger.d-block')) {
                        const errorText = document.createElement('small');
                        errorText.className = 'text-danger d-block mt-1';
                        errorText.textContent = 'This field is required';
                        formFloating.appendChild(errorText);
                    }
                } else {
                    this.classList.remove('is-invalid');
                    const formFloating = this.closest('.form-floating');
                    if (formFloating) {
                        const errorText = formFloating.querySelector('.text-danger.d-block');
                        if (errorText) {
                            errorText.remove();
                        }
                    }
                }
            }
        });
    });
    
    // Form submission handler
    const form = document.getElementById('addMemberForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Check if we're on step 5
            const step5 = document.getElementById('step5');
            if (!step5 || step5.style.display === 'none') {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Please Complete All Steps',
                    text: 'Please complete all steps before submitting. Click "Next" on Step 4 to proceed to the summary.',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            
            // Validate all steps before submission
            let allValid = true;
            for (let step = 1; step <= 4; step++) {
                if (!validateStep(step)) {
                    allValid = false;
                    // Show the step that has errors
                    showStep(step, 5);
                    break;
                }
            }
            
            if (!allValid) {
                e.preventDefault();
                return false;
            }
            
            // Format phone numbers with +255 prefix
            const phoneInput = document.getElementById('phone_number');
            if (phoneInput && phoneInput.value) {
                let phone = phoneInput.value.trim().replace(/\s+/g, '');
                phone = phone.replace(/^\+255/, '');
                phoneInput.value = '+255' + phone;
            }
            
            // Format guardian phone
            const guardianPhoneInput = document.getElementById('guardian_phone');
            if (guardianPhoneInput && guardianPhoneInput.value) {
                let phone = guardianPhoneInput.value.trim().replace(/\s+/g, '');
                phone = phone.replace(/^\+255/, '');
                guardianPhoneInput.value = '+255' + phone;
            }
            
            // Format spouse phone
            const spousePhoneInput = document.getElementById('spouse_phone_number');
            if (spousePhoneInput && spousePhoneInput.value) {
                let phone = spousePhoneInput.value.trim().replace(/\s+/g, '');
                phone = phone.replace(/^\+255/, '');
                spousePhoneInput.value = '+255' + phone;
            }
            
            // Format neighbor phone
            const neighborPhoneInput = document.getElementById('neighbor_phone');
            if (neighborPhoneInput && neighborPhoneInput.value) {
                let phone = neighborPhoneInput.value.trim().replace(/\s+/g, '');
                phone = phone.replace(/^\+255/, '');
                neighborPhoneInput.value = '+255' + phone;
            }
            
            // Check if preview mode is enabled
            const previewModeCheckbox = document.getElementById('previewMode');
            const isPreviewMode = previewModeCheckbox && previewModeCheckbox.checked;
            
            if (isPreviewMode) {
                // Prevent form submission
                e.preventDefault();
                
                // Show preview success message
                Swal.fire({
                    icon: 'info',
                    title: 'Preview Mode',
                    html: `
                        <div class="text-start">
                            <p><strong>Form validation successful!</strong></p>
                            <p>This is a preview/test submission. No data was saved to the database.</p>
                            <hr>
                            <p class="small text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                Uncheck "Preview/Test Mode" and submit again to actually save the member data.
                            </p>
                        </div>
                    `,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#5b2a86'
                });
                
                return false;
            }
            
            // Show loading indicator for actual submission
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we register the member.',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            // Form will submit normally - let it proceed
            // If there are validation errors, they will be shown via the error alert at the top
        });
        
        // Handle form submission response if it's an AJAX request
        // But since this is a regular form submission, errors will be shown via redirect back
    }
});
</script>
@endsection

@push('styles')
<style>
/* CRITICAL FIX: Prevent sidebar from hiding content on the left side */
@media (min-width: 992px) {
    body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav_content,
    body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav #layoutSidenav_content,
    #layoutSidenav:not(.sb-sidenav-toggled) #layoutSidenav_content {
        padding-left: 225px !important;
        margin-left: 0 !important;
    }
    
    body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav_content,
    body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav #layoutSidenav_content,
    #layoutSidenav.sb-sidenav-toggled #layoutSidenav_content {
        padding-left: 0 !important;
        margin-left: 0 !important;
    }
}

#layoutSidenav_content {
    overflow-x: visible !important;
    position: relative !important;
    box-sizing: border-box !important;
}

#layoutSidenav_content > main {
    width: 100% !important;
    position: relative !important;
    min-height: calc(100vh - 56px) !important;
    margin-left: 0 !important;
    padding-left: 0 !important;
    box-sizing: border-box !important;
}

.container-fluid {
    margin-left: 0 !important;
    margin-right: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    position: relative !important;
}

@media (max-width: 991px) {
    #layoutSidenav_content {
        padding-left: 0 !important;
        margin-left: 0 !important;
    }
    
    .container-fluid {
        padding-left: 1rem !important;
        padding-right: 1rem !important;
    }
}

/* Validation styles */
.is-invalid {
    border-color: #dc3545 !important;
}

.is-invalid:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.form-floating > .is-invalid ~ label {
    color: #dc3545 !important;
}
</style>
@endpush
@endsection

