@extends('layouts.index')

@section('content')
<div class="container-fluid px-2 px-md-5 py-4">
    <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
            <span class="fs-5 fw-bold text-white d-flex align-items-center">
                <i class="fas fa-edit me-2"></i> <span>Edit Member - {{ $member->full_name }}</span>
            </span>
            <div class="mt-3 mt-md-0">
                <a href="{{ route('members.index') }}" class="btn btn-outline-light btn-sm shadow-sm me-2">
                    <i class="fas fa-list me-1"></i> All Members
                </a>
                <a href="{{ route('members.show', $member->id) }}" class="btn btn-light btn-sm shadow-sm">
                    <i class="fas fa-eye me-1"></i> View
                </a>
            </div>
        </div>
        <div class="card-body bg-light px-4 py-4">
            <form id="editMemberForm" action="{{ route('members.update', $member->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Personal Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="full_name" id="full_name" value="{{ old('full_name', $member->full_name) }}" required>
                                    <label for="full_name">Full Name <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <select class="form-select" name="gender" id="gender" required>
                                        <option value="male" {{ old('gender', $member->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', $member->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                    <label for="gender">Gender <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-floating">
                                    <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth ? $member->date_of_birth->format('Y-m-d') : '') }}" required>
                                    <label for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select name="membership_type" id="membership_type" class="form-select" required>
                                        <option value="permanent" {{ old('membership_type', $member->membership_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                                        <option value="temporary" {{ old('membership_type', $member->membership_type) == 'temporary' ? 'selected' : '' }}>Temporary</option>
                                    </select>
                                    <label for="membership_type">Membership Type <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select name="campus_id" id="campus_id" class="form-select" required>
                                        @foreach($campuses as $campus)
                                            <option value="{{ $campus->id }}" {{ old('campus_id', $member->campus_id) == $campus->id ? 'selected' : '' }}>
                                                {{ $campus->name }}
                                                @if($campus->is_main_campus) (Usharika) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="campus_id">Branch/Campus <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <select name="community_id" id="community_id" class="form-select">
                                        <option value="">Select Community...</option>
                                        @foreach($communities as $community)
                                            <option value="{{ $community->id }}" {{ old('community_id', $member->community_id) == $community->id ? 'selected' : '' }}>
                                                {{ $community->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="community_id">Community (Optional)</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="phone_number" id="phone_number" value="{{ old('phone_number', $member->phone_number) }}" required>
                                    <label for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $member->email) }}">
                                    <label for="email">Email</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="nida_number" id="nida_number" value="{{ old('nida_number', $member->nida_number) }}">
                                    <label for="nida_number">NIDA Number</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="profession" id="profession" value="{{ old('profession', $member->profession) }}" required>
                                    <label for="profession">Profession <span class="text-danger">*</span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Location Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Location Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="region" id="region" value="{{ old('region', $member->region) }}" required>
                                    <label for="region">Region <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="district" id="district" value="{{ old('district', $member->district) }}" required>
                                    <label for="district">District <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="ward" id="ward" value="{{ old('ward', $member->ward) }}" required>
                                    <label for="ward">Ward <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="street" id="street" value="{{ old('street', $member->street) }}" required>
                                    <label for="street">Street <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="address" id="address" value="{{ old('address', $member->address) }}" required>
                                    <label for="address">P.O. Box / Address <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="tribe" id="tribe" required>
                                        <option value=""></option>
                                        <option value="Chaga" {{ old('tribe', $member->tribe) == 'Chaga' ? 'selected' : '' }}>Chaga</option>
                                        <option value="Sukuma" {{ old('tribe', $member->tribe) == 'Sukuma' ? 'selected' : '' }}>Sukuma</option>
                                        <option value="Haya" {{ old('tribe', $member->tribe) == 'Haya' ? 'selected' : '' }}>Haya</option>
                                        <option value="Nyamwezi" {{ old('tribe', $member->tribe) == 'Nyamwezi' ? 'selected' : '' }}>Nyamwezi</option>
                                        <option value="Other" {{ old('tribe', $member->tribe) == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    <label for="tribe">Tribe <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="col-md-6" id="otherTribeWrapper" style="{{ old('tribe', $member->tribe) == 'Other' ? '' : 'display:none;' }}">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="other_tribe" id="other_tribe" value="{{ old('other_tribe', $member->other_tribe) }}">
                                    <label for="other_tribe">Other Tribe</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Family Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Family Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select class="form-select" name="marital_status" id="marital_status">
                                        <option value="">Select...</option>
                                        <option value="single" {{ old('marital_status', $member->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                        <option value="married" {{ old('marital_status', $member->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                        <option value="divorced" {{ old('marital_status', $member->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                        <option value="widowed" {{ old('marital_status', $member->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        <option value="separated" {{ old('marital_status', $member->marital_status) == 'separated' ? 'selected' : '' }}>Separated</option>
                                    </select>
                                    <label for="marital_status">Marital Status</label>
                                </div>
                            </div>
                            <div class="col-md-6" id="weddingDateWrapper" style="{{ old('marital_status', $member->marital_status) == 'married' ? '' : 'display:none;' }}">
                                <div class="form-floating">
                                    <input type="date" class="form-control" name="wedding_date" id="wedding_date" value="{{ old('wedding_date', $member->wedding_date ? $member->wedding_date->format('Y-m-d') : '') }}">
                                    <label for="wedding_date">Wedding Date</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('members.show', $member->id) }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-1"></i> Cancel
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-1"></i> Update Member
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
    @media (min-width: 992px) {
        body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav_content {
            padding-left: 225px !important;
        }
        body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav_content {
            padding-left: 0 !important;
        }
    }
    
    @media (max-width: 991px) {
        #layoutSidenav_content {
            padding-left: 0 !important;
            margin-left: 0 !important;
        }
    }
    
    .card-header.bg-primary {
        background-color: #0d6efd !important;
    }
</style>
@endpush

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle tribe change
    const tribeSelect = document.getElementById('tribe');
    const otherTribeWrapper = document.getElementById('otherTribeWrapper');
    
    if (tribeSelect) {
        tribeSelect.addEventListener('change', function() {
            if (this.value === 'Other') {
                otherTribeWrapper.style.display = '';
                document.getElementById('other_tribe').required = true;
            } else {
                otherTribeWrapper.style.display = 'none';
                document.getElementById('other_tribe').required = false;
            }
        });
    }
    
    // Handle marital status change to show/hide wedding date
    const maritalStatusSelect = document.getElementById('marital_status');
    const weddingDateWrapper = document.getElementById('weddingDateWrapper');
    
    if (maritalStatusSelect && weddingDateWrapper) {
        maritalStatusSelect.addEventListener('change', function() {
            if (this.value === 'married') {
                weddingDateWrapper.style.display = '';
            } else {
                weddingDateWrapper.style.display = 'none';
            }
        });
    }
    
    // Handle form submission
    const form = document.getElementById('editMemberForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message || 'Member updated successfully',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href = '{{ route("members.show", $member->id) }}';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: data.message || 'Failed to update member'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while updating the member'
                });
            });
        });
    }
});
</script>
@endsection
@endsection


