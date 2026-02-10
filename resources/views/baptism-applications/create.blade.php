@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-water me-2 text-primary"></i>Baptism Application Form
                                </h1>
                                <p class="text-muted mb-0">Submit a baptism application for review by the Pastor</p>
                            </div>
                            <a href="{{ route('evangelism-leader.baptism-applications.index') }}"
                                class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back to Applications
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

        <form action="{{ route('evangelism-leader.baptism-applications.store') }}" method="POST"
            enctype="multipart/form-data" id="baptismApplicationForm">
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
                            <label for="date_of_birth" class="form-label">Date of Birth <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror"
                                id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}"
                                max="{{ date('Y-m-d', strtotime('-1 day')) }}" required>
                            @error('date_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="age" class="form-label">Age</label>
                            <input type="number" class="form-control @error('age') is-invalid @enderror" id="age" name="age"
                                value="{{ old('age') }}" readonly>
                            @error('age')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Auto-calculated</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="place_of_birth" class="form-label">Place of Birth <span class="text-danger"
                                    id="birth_place_required_indicator" style="display: none;">*</span></label>
                            <input type="text" class="form-control @error('place_of_birth') is-invalid @enderror"
                                id="place_of_birth" name="place_of_birth" value="{{ old('place_of_birth') }}"
                                placeholder="Village / Town">
                            @error('place_of_birth')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3" id="phone_number_field">
                            <label for="phone_number" class="form-label">
                                Phone Number
                                <span class="text-danger" id="phone_required_indicator">*</span>
                            </label>
                            <input type="text" class="form-control @error('phone_number') is-invalid @enderror"
                                id="phone_number" name="phone_number" value="{{ old('phone_number') }}" required>
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3" id="email_field">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                name="email" value="{{ old('email') }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3" id="marital_status_field">
                            <label for="marital_status" class="form-label">Marital Status</label>
                            <select class="form-select @error('marital_status') is-invalid @enderror" id="marital_status"
                                name="marital_status">
                                <option value="">Select...</option>
                                <option value="single" {{ old('marital_status') == 'single' ? 'selected' : '' }}>Single
                                </option>
                                <option value="married" {{ old('marital_status') == 'married' ? 'selected' : '' }}>Married
                                </option>
                                <option value="divorced" {{ old('marital_status') == 'divorced' ? 'selected' : '' }}>Divorced
                                </option>
                                <option value="widowed" {{ old('marital_status') == 'widowed' ? 'selected' : '' }}>Widowed
                                </option>
                            </select>
                            @error('marital_status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="residential_address" class="form-label">Residential Address <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control @error('residential_address') is-invalid @enderror"
                                id="residential_address" name="residential_address" rows="2"
                                required>{{ old('residential_address') }}</textarea>
                            @error('residential_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Street / Village</small>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="church_branch_id" class="form-label">Church Branch / Parish</label>
                            <input type="hidden" id="church_branch_id" name="church_branch_id" value="{{ $campus->id }}">
                            <input type="text" class="form-control" value="{{ $campus->name }}" readonly>
                            <small class="text-muted">Automatically set to your branch</small>
                            @error('church_branch_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="community_id" class="form-label">Community <span
                                    class="text-danger">*</span></label>
                            <select class="form-select @error('community_id') is-invalid @enderror" id="community_id"
                                name="community_id" required>
                                <option value="">Select Community...</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}" {{ old('community_id') == $community->id ? 'selected' : '' }}>
                                        {{ $community->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('community_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Spiritual Information -->
            <div class="card border-0 shadow-sm mb-4" id="spiritual_info_section">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-praying-hands me-2"></i>2. Spiritual Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Have you ever been baptized before? <span
                                    class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="previously_baptized"
                                    id="previously_baptized_yes" value="1" {{ old('previously_baptized') == '1' || old('previously_baptized') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="previously_baptized_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="previously_baptized"
                                    id="previously_baptized_no" value="0" {{ old('previously_baptized') == '0' || old('previously_baptized') === null || old('previously_baptized') === '' ? 'checked' : '' }}>
                                <label class="form-check-label" for="previously_baptized_no">No</label>
                            </div>
                            <input type="hidden" name="previously_baptized_required" value="1">
                        </div>
                        <div class="col-md-6" id="previous_baptism_fields" style="display: none;">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="previous_church_name" class="form-label">Church Name</label>
                                    <input type="text" class="form-control" id="previous_church_name"
                                        name="previous_church_name" value="{{ old('previous_church_name') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="previous_baptism_date" class="form-label">Date of Baptism</label>
                                    <input type="date" class="form-control" id="previous_baptism_date"
                                        name="previous_baptism_date" value="{{ old('previous_baptism_date') }}"
                                        max="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Have you attended baptism classes? <span
                                    class="text-danger">*</span></label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="attended_baptism_classes"
                                    id="attended_classes_yes" value="1" {{ old('attended_baptism_classes') == '1' || old('attended_baptism_classes') === '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="attended_classes_yes">Yes</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="attended_baptism_classes"
                                    id="attended_classes_no" value="0" {{ old('attended_baptism_classes') == '0' || old('attended_baptism_classes') === null || old('attended_baptism_classes') === '' ? 'checked' : '' }}>
                                <label class="form-check-label" for="attended_classes_no">No</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="church_attendance_duration" class="form-label">Duration of Church Attendance</label>
                            <input type="text" class="form-control" id="church_attendance_duration"
                                name="church_attendance_duration" value="{{ old('church_attendance_duration') }}"
                                placeholder="e.g., 1 year, 2 years">
                            <small class="text-muted">How long have you been attending church?</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pastor_catechist_name" class="form-label">Name of Pastor / Catechist who knows
                                you</label>
                            <input type="text" class="form-control" id="pastor_catechist_name" name="pastor_catechist_name"
                                value="{{ old('pastor_catechist_name') }}">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Family Information -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>3. Family Information</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-3" id="childApplicantAlert" style="display: none;">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Child Applicant:</strong> Since the applicant is under 18 years old, parent/guardian
                        information is required.
                    </div>
                    <div class="row">
                        <!-- Child Parents Section -->
                        <div class="col-md-6 mb-3 child-only-fields" style="display: none;">
                            <label for="father_name" class="form-label">Father's Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('father_name') is-invalid @enderror"
                                id="father_name" name="father_name" value="{{ old('father_name') }}">
                            @error('father_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3 child-only-fields" style="display: none;">
                            <label for="mother_name" class="form-label">Mother's Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('mother_name') is-invalid @enderror"
                                id="mother_name" name="mother_name" value="{{ old('mother_name') }}">
                            @error('mother_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Wadhamini (Sponsors) Section -->
                        <div class="col-12 mt-2 child-only-fields sponsorship-section" style="display: none;">
                            <hr>
                            <h6 class="text-primary mb-3"><i class="fas fa-user-shield me-2"></i>Wadhamini (Godparents /
                                Sponsors)</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="godparent1_name" class="form-label">Wadhamini 1 <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('godparent1_name') is-invalid @enderror"
                                        id="godparent1_name" name="godparent1_name" value="{{ old('godparent1_name') }}">
                                    @error('godparent1_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="godparent2_name" class="form-label">Wadhamini 2 <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('godparent2_name') is-invalid @enderror"
                                        id="godparent2_name" name="godparent2_name" value="{{ old('godparent2_name') }}">
                                    @error('godparent2_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="family_religious_background" class="form-label">Family Religious Background</label>
                            <textarea class="form-control" id="family_religious_background"
                                name="family_religious_background" rows="2"
                                placeholder="Optional: Describe the family's religious background...">{{ old('family_religious_background') }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Statement -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>4. Application Statement</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="reason_for_baptism" class="form-label">Reason for requesting baptism <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control @error('reason_for_baptism') is-invalid @enderror"
                            id="reason_for_baptism" name="reason_for_baptism" rows="4"
                            placeholder="Please explain why you (or your child) want to be baptized..."
                            required>{{ old('reason_for_baptism') }}</textarea>
                        @error('reason_for_baptism')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Minimum 20 characters required. For child applicants, parent/guardian may
                            write on behalf of the child.</small>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input @error('declaration_agreed') is-invalid @enderror" type="checkbox"
                            id="declaration_agreed" name="declaration_agreed" value="1" required>
                        <label class="form-check-label" for="declaration_agreed">
                            <span id="declaration_text">
                                I declare that I am applying for baptism voluntarily and according to the teachings of the
                                Church.
                            </span>
                            <span class="text-danger">*</span>
                        </label>
                        @error('declaration_agreed')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>5. Attachments (Optional)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="photo" class="form-label">Passport-size Photo</label>
                            <input type="file" class="form-control @error('photo') is-invalid @enderror" id="photo"
                                name="photo" accept="image/jpeg,image/png,image/jpg">
                            @error('photo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max size: 2MB (JPEG, PNG, JPG)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="recommendation_letter" class="form-label">Recommendation Letter</label>
                            <input type="file" class="form-control @error('recommendation_letter') is-invalid @enderror"
                                id="recommendation_letter" name="recommendation_letter" accept=".pdf,.doc,.docx">
                            @error('recommendation_letter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Max size: 5MB (PDF, DOC, DOCX)</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mb-4">
                <a href="{{ route('evangelism-leader.baptism-applications.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary" id="submitBtn">
                    <i class="fas fa-paper-plane me-1"></i> Submit Application
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Show/hide previous baptism fields
            const previouslyBaptizedRadios = document.querySelectorAll('input[name="previously_baptized"]');
            const previousBaptismFields = document.getElementById('previous_baptism_fields');

            previouslyBaptizedRadios.forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.value === '1') {
                        previousBaptismFields.style.display = 'block';
                        document.getElementById('previous_church_name').required = true;
                        document.getElementById('previous_baptism_date').required = true;
                    } else {
                        previousBaptismFields.style.display = 'none';
                        document.getElementById('previous_church_name').required = false;
                        document.getElementById('previous_baptism_date').required = false;
                    }
                });
            });

            // Auto-calculate age from date of birth and handle child applicant requirements
            const dateOfBirthInput = document.getElementById('date_of_birth');
            const ageInput = document.getElementById('age');
            const childApplicantAlert = document.getElementById('childApplicantAlert');
            const declarationText = document.getElementById('declaration_text');

            function calculateAge() {
                if (dateOfBirthInput.value) {
                    const birthDate = new Date(dateOfBirthInput.value);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    ageInput.value = age > 0 ? age : 0;

                    // Handle child applicant (under 18)
                    const phoneNumberField = document.getElementById('phone_number_field');
                    const emailField = document.getElementById('email_field');
                    const maritalStatusField = document.getElementById('marital_status_field');
                    const phoneNumberInput = document.getElementById('phone_number');
                    const emailInput = document.getElementById('email');
                    const phoneRequiredIndicator = document.getElementById('phone_required_indicator');
                    const phoneHelpText = document.getElementById('phone_help_text');
                    const emailHelpText = document.getElementById('email_help_text');
                    const guardianContactFields = document.getElementById('guardian_contact_fields');

                    if (age < 18 && age > 0) {
                        // Show child applicant alert
                        childApplicantAlert.style.display = 'block';

                        // Show child specific fields
                        document.querySelectorAll('.child-only-fields').forEach(el => el.style.display = 'block');

                        // Hide spiritual information for children
                        const spiritualInfoSection = document.getElementById('spiritual_info_section');
                        spiritualInfoSection.style.display = 'none';

                        // Make child fields required
                        document.getElementById('father_name').required = true;
                        document.getElementById('mother_name').required = true;
                        document.getElementById('godparent1_name').required = true;
                        document.getElementById('place_of_birth').required = true;
                        document.getElementById('birth_place_required_indicator').style.display = 'inline';

                        // Disable spiritual info inputs so they aren't validated/submitted
                        spiritualInfoSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = true);

                        // Make phone and email optional for child
                        phoneNumberInput.required = false;
                        phoneRequiredIndicator.style.display = 'none';

                        // Hide marital status for children
                        maritalStatusField.style.display = 'none';

                        // Update declaration text for child
                        declarationText.textContent = 'I (as parent/guardian) declare that I am applying for baptism on behalf of the child voluntarily and according to the teachings of the Church.';
                    } else {
                        // Hide child applicant alert
                        childApplicantAlert.style.display = 'none';

                        // Hide child specific fields
                        document.querySelectorAll('.child-only-fields').forEach(el => el.style.display = 'none');

                        // Show spiritual information for adults
                        const spiritualInfoSection = document.getElementById('spiritual_info_section');
                        spiritualInfoSection.style.display = 'block';
                        spiritualInfoSection.querySelectorAll('input, select, textarea').forEach(el => el.disabled = false);

                        // Remove required attribute from child fields
                        document.getElementById('father_name').required = false;
                        document.getElementById('mother_name').required = false;
                        document.getElementById('godparent1_name').required = false;
                        document.getElementById('place_of_birth').required = false;
                        document.getElementById('birth_place_required_indicator').style.display = 'none';

                        // Make phone required for adults
                        phoneNumberInput.required = true;
                        phoneRequiredIndicator.style.display = 'inline';

                        // Show marital status for adults
                        maritalStatusField.style.display = 'block';

                        // Reset declaration text for adult
                        declarationText.textContent = 'I declare that I am applying for baptism voluntarily and according to the teachings of the Church.';
                    }
                } else {
                    ageInput.value = '';
                    childApplicantAlert.style.display = 'none';

                    // Reset phone/email requirements
                    const phoneNumberInput = document.getElementById('phone_number');
                    const phoneRequiredIndicator = document.getElementById('phone_required_indicator');
                    const maritalStatusField = document.getElementById('marital_status_field');

                    phoneNumberInput.required = true;
                    phoneRequiredIndicator.style.display = 'inline';
                    maritalStatusField.style.display = 'block';

                    declarationText.textContent = 'I declare that I am applying for baptism voluntarily and according to the teachings of the Church.';
                }
            }

            // Calculate age when date of birth changes
            dateOfBirthInput.addEventListener('change', calculateAge);
            dateOfBirthInput.addEventListener('input', calculateAge); // Also calculate on input for real-time updates

            // Calculate age on page load if date is pre-filled
            if (dateOfBirthInput.value) {
                calculateAge();
            }

            // Form submission handler - simplified to prevent double submission only
            const form = document.getElementById('baptismApplicationForm');
            const submitBtn = document.getElementById('submitBtn');

            form.addEventListener('submit', function (e) {
                console.log('Form submission started');

                // Only prevent double submission, let browser handle validation
                if (submitBtn.disabled) {
                    console.log('Form already submitting');
                    e.preventDefault();
                    return false;
                }

                console.log('Disabling submit button');
                // Disable submit button to prevent double submission
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Submitting...';

                // Allow form to submit normally
                console.log('Form submitting...');
                return true;
            });
        });
    </script>
@endsection