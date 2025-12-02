@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2" style="width:48px; height:48px; background:rgba(0,123,255,.1);">
                                <i class="fas fa-cog text-primary"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold text-dark">Account Settings</h5>
                                <small class="text-muted">Manage your profile and account preferences</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-dismissible fade show" role="alert">
            <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Profile Picture Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-user-circle me-2 text-primary"></i>Profile Picture
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('member.profile.update') }}" enctype="multipart/form-data" id="profilePictureForm">
                        @csrf
                        <div class="row align-items-center">
                            <div class="col-md-4 text-center mb-3 mb-md-0">
                                <div class="position-relative d-inline-block">
                                    @if($member->profile_picture)
                                        <img src="{{ asset('storage/' . $member->profile_picture) }}" 
                                             alt="Profile Picture" 
                                             class="img-thumbnail rounded-circle" 
                                             style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #007bff;">
                                    @else
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 150px; height: 150px; border: 3px solid #007bff;">
                                            <i class="fas fa-user fa-4x text-muted"></i>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="profile_picture" class="form-label fw-bold">
                                        <i class="fas fa-camera me-2"></i>Upload New Photo
                                    </label>
                                    <input type="file" 
                                           class="form-control @error('profile_picture') is-invalid @enderror" 
                                           id="profile_picture" 
                                           name="profile_picture" 
                                           accept="image/jpeg,image/png,image/jpg"
                                           onchange="previewImage(this)">
                                    @error('profile_picture')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @else
                                        <small class="text-muted">JPG, PNG format. Maximum size: 2MB</small>
                                    @enderror
                                </div>
                                <div id="imagePreview" class="mb-3" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail rounded-circle" style="width: 100px; height: 100px; object-fit: cover;">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Update Photo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-phone me-2 text-primary"></i>Contact Information
                    </h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('member.profile.update') }}" id="contactForm">
                        @csrf
                        <div class="mb-3">
                            <label for="phone_number" class="form-label fw-bold">
                                <i class="fas fa-mobile-alt me-2"></i>Phone Number
                            </label>
                            <input type="text" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   id="phone_number" 
                                   name="phone_number" 
                                   value="{{ old('phone_number', $member->phone_number) }}"
                                   placeholder="Enter your phone number">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">
                                <i class="fas fa-envelope me-2"></i>Email Address
                                @if(!$member->email)
                                    <span class="badge bg-warning text-dark ms-2">Not Set</span>
                                @endif
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $member->email) }}"
                                   placeholder="Enter your email address">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @else
                                @if(!$member->email)
                                    <small class="text-muted">Add your email address to receive notifications and updates.</small>
                                @else
                                    <small class="text-muted">Update your email address if needed.</small>
                                @endif
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Update Contact Information
                        </button>
                    </form>
                </div>
            </div>

            <!-- Password Section -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-semibold">
                        <i class="fas fa-key me-2 text-primary"></i>Change Password
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">Keep your account secure by changing your password regularly.</p>
                    <a href="{{ route('member.change-password') }}" class="btn btn-outline-primary">
                        <i class="fas fa-lock me-2"></i>Change Password
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Account Information
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Member ID</small>
                        <strong>{{ $member->member_id }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Full Name</small>
                        <strong>{{ $member->full_name }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Member Type</small>
                        <strong>{{ ucfirst($member->member_type ?? 'N/A') }}</strong>
                    </div>
                    <hr>
                    <div class="alert alert-light mb-0">
                        <small>
                            <i class="fas fa-shield-alt me-2 text-primary"></i>
                            <strong>Security Tip:</strong> Keep your account information up to date and use a strong password.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        const previewDiv = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            previewDiv.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Validate file size and type before form submission
document.getElementById('profilePictureForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('profile_picture');
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        
        // Check file type
        const validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!validTypes.includes(file.type)) {
            e.preventDefault();
            alert('Please select a valid image file (JPG, PNG, or JPEG).');
            return false;
        }
        
        // Check file size (2MB = 2 * 1024 * 1024 bytes)
        if (file.size > 2 * 1024 * 1024) {
            e.preventDefault();
            alert('File size must be less than 2MB. Please choose a smaller image.');
            return false;
        }
    }
});
</script>
@endsection

