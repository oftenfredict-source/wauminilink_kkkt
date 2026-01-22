@extends('layouts.index')

@section('content')
<div class="container-fluid px-2 px-md-5 py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-gradient-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
            <span class="fs-5 fw-bold text-white d-flex align-items-center">
                <i class="fas fa-user-plus me-2"></i> <span id="stepHeaderTitle">Sample Member Registration Form</span>
            </span>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('members.add') }}" class="btn btn-light btn-sm me-2 shadow-sm"><i class="fas fa-user-plus me-1"></i> Actual Form</a>
                <a href="{{ route('members.view') }}" class="btn btn-outline-light btn-sm shadow-sm"><i class="fas fa-list me-1"></i> All Members</a>
            </div>
        </div>
        <div class="card-body bg-light px-4 py-4">
            <form id="sampleMemberForm">
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
                                </select>
                                <label for="campus_id">Branch/Campus <span class="text-danger">*</span></label>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-floating">
                                <select name="community_id" id="community_id" class="form-select">
                                    <option value="">Select Community...</option>
                                </select>
                                <label for="community_id">Community (Optional)</label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="full_name" id="full_name" required>
                                <label for="full_name">Full Name</label>
                            </div>
                        </div>
                        <div class="col-md-3">
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
                    <div class="border rounded-3 p-4 mb-4 bg-white shadow-sm" id="guardianSection">
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
                                <input type="number" class="form-control" name="children_count" id="children_count" min="0" max="20" value="0" placeholder="Enter number">
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
                    <div class="alert alert-info">
                        <h5 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Review Your Information</h5>
                        <p>Please review all the information you have entered. Click "Submit" to complete the registration, or "Back" to make changes.</p>
                    </div>
                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 shadow-sm prev-step" id="prevStep5"><i class="fas fa-arrow-left me-1"></i>Back</button>
                        <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm"><i class="fas fa-save me-2"></i>Submit</button>
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

<script>
// Step navigation functions
function setStepActive(step) {
    document.querySelectorAll('#wizardSteps .wizard-step').forEach((s, idx) => {
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

function showStep(stepToShow, stepToHide) {
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
}

// Step navigation event listeners
document.getElementById('nextStep1').addEventListener('click', function() {
    showStep(2, 1);
});
document.getElementById('nextStep2').addEventListener('click', function() {
    showStep(3, 2);
});
document.getElementById('nextStep3').addEventListener('click', function() {
    showStep(4, 3);
});
document.getElementById('nextStep4').addEventListener('click', function() {
    showStep(5, 4);
});
document.getElementById('prevStep2').addEventListener('click', function() {
    showStep(1, 2);
});
document.getElementById('prevStep3').addEventListener('click', function() {
    showStep(2, 3);
});
document.getElementById('prevStep4').addEventListener('click', function() {
    showStep(3, 4);
});
document.getElementById('prevStep5').addEventListener('click', function() {
    showStep(4, 5);
});

// Baptism status change handlers
document.getElementById('baptism_status').addEventListener('change', function() {
    const baptized = this.value === 'baptized';
    document.getElementById('baptismDateWrapper').style.display = baptized ? 'block' : 'none';
    document.getElementById('baptismLocationWrapper').style.display = baptized ? 'block' : 'none';
    document.getElementById('baptizedByWrapper').style.display = baptized ? 'block' : 'none';
    document.getElementById('baptismCertificateWrapper').style.display = baptized ? 'block' : 'none';
});

// Marital status change handler
document.getElementById('marital_status').addEventListener('change', function() {
    const spouseSection = document.getElementById('spouseInfoSection');
    const weddingSection = document.getElementById('weddingInfoSection');
    if (this.value === 'married') {
        spouseSection.style.display = 'block';
        weddingSection.style.display = 'block';
    } else {
        spouseSection.style.display = 'none';
        weddingSection.style.display = 'none';
    }
});

// Spouse baptism status change handler
document.getElementById('spouse_baptism_status').addEventListener('change', function() {
    const baptized = this.value === 'baptized';
    document.getElementById('spouseBaptismDateWrapper').style.display = baptized ? 'block' : 'none';
    document.getElementById('spouseBaptismLocationWrapper').style.display = baptized ? 'block' : 'none';
    document.getElementById('spouseBaptizedByWrapper').style.display = baptized ? 'block' : 'none';
    document.getElementById('spouseBaptismCertificateWrapper').style.display = baptized ? 'block' : 'none';
});

// Children count handler
document.getElementById('children_count').addEventListener('change', function() {
    const count = parseInt(this.value);
    const container = document.getElementById('childrenFields');
    
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
                        <div class="col-md-4 child-baptism-certificate-wrapper" id="child${i}_baptism_certificate_wrapper" style="display:none;">
                            <div class="form-floating">
                                <input type="text" class="form-control" name="children[${i}][baptism_certificate_number]" placeholder="Certificate number">
                                <label>Baptism Certificate Number (Optional)</label>
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
            document.getElementById('child' + childIndex + '_baptism_date_wrapper').style.display = baptized ? 'block' : 'none';
            document.getElementById('child' + childIndex + '_baptism_location_wrapper').style.display = baptized ? 'block' : 'none';
            document.getElementById('child' + childIndex + '_baptized_by_wrapper').style.display = baptized ? 'block' : 'none';
            document.getElementById('child' + childIndex + '_baptism_certificate_wrapper').style.display = baptized ? 'block' : 'none';
        });
    });
});

// Prevent form submission (this is just a sample/preview page)
document.getElementById('sampleMemberForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('This is a sample/preview page. The actual form submission will be implemented after review.');
});
</script>

@push('styles')
<style>
/* CRITICAL FIX: Prevent sidebar from hiding content on the left side */
/* Multiple approaches to ensure content is visible */

/* Approach 1: Ensure Bootstrap's padding-left is applied and not overridden */
@media (min-width: 992px) {
    /* Force padding-left when sidebar is visible (not toggled = sidebar is open) */
    body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav_content,
    body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav #layoutSidenav_content,
    #layoutSidenav:not(.sb-sidenav-toggled) #layoutSidenav_content {
        padding-left: 225px !important;
        margin-left: 0 !important;
    }
    
    /* Remove padding when sidebar is hidden (toggled = sidebar is closed) */
    body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav_content,
    body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav #layoutSidenav_content,
    #layoutSidenav.sb-sidenav-toggled #layoutSidenav_content {
        padding-left: 0 !important;
        margin-left: 0 !important;
    }
    
    /* Ensure sidebar is visible when not toggled */
    body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav #layoutSidenav_nav {
        transform: translateX(0) !important;
    }
    
    /* Ensure sidebar is hidden when toggled */
    body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav #layoutSidenav_nav {
        transform: translateX(-225px) !important;
    }
}

/* Approach 2: Also ensure main element respects the padding */
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

/* Approach 3: Ensure container doesn't extend into sidebar area */
.container-fluid {
    margin-left: 0 !important;
    margin-right: 0 !important;
    max-width: 100% !important;
    box-sizing: border-box !important;
    position: relative !important;
}

/* Approach 4: Add extra left margin/padding to button containers on desktop */
@media (min-width: 992px) {
    body.sb-nav-fixed:not(.sb-sidenav-toggled) .d-flex.justify-content-between,
    #layoutSidenav:not(.sb-sidenav-toggled) .d-flex.justify-content-between {
        margin-left: 0 !important;
        padding-left: 0 !important;
        min-width: 0 !important;
    }
}

/* Mobile: No adjustments needed */
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
</style>
@endpush
@endsection
