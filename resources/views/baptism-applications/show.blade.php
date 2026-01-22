@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-water me-2 text-primary"></i>Baptism Application Details</h1>
                            <p class="text-muted mb-0">{{ $baptismApplication->full_name }}</p>
                        </div>
                        <div>
                            @if(auth()->user()->isEvangelismLeader())
                                <a href="{{ route('evangelism-leader.baptism-applications.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            @elseif(auth()->user()->isPastor() || auth()->user()->isAdmin())
                                <a href="{{ route('pastor.baptism-applications.pending') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Application Details -->
        <div class="col-md-8 mb-4">
            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Full Name:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->full_name }}</span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>Gender:</strong><br>
                            <span class="text-muted">{{ ucfirst($baptismApplication->gender) }}</span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>Age:</strong><br>
                            <span class="text-muted">
                                {{ $baptismApplication->age }}
                                @if($baptismApplication->age < 18)
                                    <span class="badge bg-info ms-2">Child Applicant</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Date of Birth:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->date_of_birth->format('F d, Y') }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Phone Number:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->phone_number }}</span>
                        </div>
                    </div>
                    @if($baptismApplication->email)
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Email:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->email }}</span>
                        </div>
                        @if($baptismApplication->marital_status)
                        <div class="col-md-6 mb-2">
                            <strong>Marital Status:</strong><br>
                            <span class="text-muted">{{ ucfirst($baptismApplication->marital_status) }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-12 mb-2">
                            <strong>Residential Address:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->residential_address }}</span>
                        </div>
                    </div>
                    @if($baptismApplication->churchBranch || $baptismApplication->community)
                    <div class="row">
                        @if($baptismApplication->churchBranch)
                        <div class="col-md-6 mb-2">
                            <strong>Church Branch:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->churchBranch->name }}</span>
                        </div>
                        @endif
                        @if($baptismApplication->community)
                        <div class="col-md-6 mb-2">
                            <strong>Community:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->community->name }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Spiritual Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-praying-hands me-2"></i>Spiritual Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Previously Baptized:</strong><br>
                            <span class="badge bg-{{ $baptismApplication->previously_baptized ? 'warning' : 'success' }}">
                                {{ $baptismApplication->previously_baptized ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Attended Baptism Classes:</strong><br>
                            <span class="badge bg-{{ $baptismApplication->attended_baptism_classes ? 'success' : 'warning' }}">
                                {{ $baptismApplication->attended_baptism_classes ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    @if($baptismApplication->previously_baptized)
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Previous Church:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->previous_church_name ?? '-' }}</span>
                        </div>
                        @if($baptismApplication->previous_baptism_date)
                        <div class="col-md-6">
                            <strong>Previous Baptism Date:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->previous_baptism_date->format('F d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($baptismApplication->church_attendance_duration)
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Church Attendance Duration:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->church_attendance_duration }}</span>
                        </div>
                        @if($baptismApplication->pastor_catechist_name)
                        <div class="col-md-6">
                            <strong>Pastor / Catechist Name:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->pastor_catechist_name }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Family Information -->
            @if($baptismApplication->parent_guardian_name || $baptismApplication->family_religious_background || $baptismApplication->age < 18)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Family Information
                        @if($baptismApplication->age < 18)
                            <span class="badge bg-warning text-dark ms-2">Child Applicant - Parent/Guardian Required</span>
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    @if($baptismApplication->age < 18 && !$baptismApplication->parent_guardian_name)
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Parent/Guardian information is missing for this child applicant.
                    </div>
                    @endif
                    @if($baptismApplication->parent_guardian_name)
                    <div class="mb-2">
                        <strong>Parent / Guardian Name:</strong><br>
                        <span class="text-muted">{{ $baptismApplication->parent_guardian_name }}</span>
                    </div>
                    @endif
                    @if($baptismApplication->guardian_phone || $baptismApplication->guardian_email)
                    <div class="row mb-2">
                        @if($baptismApplication->guardian_phone)
                        <div class="col-md-6">
                            <strong>Guardian Phone:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->guardian_phone }}</span>
                        </div>
                        @endif
                        @if($baptismApplication->guardian_email)
                        <div class="col-md-6">
                            <strong>Guardian Email:</strong><br>
                            <span class="text-muted">{{ $baptismApplication->guardian_email }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($baptismApplication->family_religious_background)
                    <div class="mb-2">
                        <strong>Family Religious Background:</strong><br>
                        <span class="text-muted">{{ $baptismApplication->family_religious_background }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Application Statement -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Application Statement</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Reason for Baptism:</strong><br>
                        <p class="text-muted">{{ $baptismApplication->reason_for_baptism }}</p>
                    </div>
                    <div>
                        <strong>Declaration:</strong><br>
                        <span class="badge bg-success">
                            <i class="fas fa-check me-1"></i>Agreed
                        </span>
                    </div>
                </div>
            </div>

            <!-- Attachments -->
            @if($baptismApplication->photo_path || $baptismApplication->recommendation_letter_path)
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-paperclip me-2"></i>Attachments</h5>
                </div>
                <div class="card-body">
                    @if($baptismApplication->photo_path)
                    <div class="mb-2">
                        <strong>Photo:</strong><br>
                        <a href="{{ asset('storage/' . $baptismApplication->photo_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-image me-1"></i> View Photo
                        </a>
                    </div>
                    @endif
                    @if($baptismApplication->recommendation_letter_path)
                    <div>
                        <strong>Recommendation Letter:</strong><br>
                        <a href="{{ asset('storage/' . $baptismApplication->recommendation_letter_path) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-pdf me-1"></i> View Letter
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Status & Actions -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Status</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Application Status:</strong><br>
                        @if($baptismApplication->status === 'pending')
                            <span class="badge bg-warning fs-6">Pending Review</span>
                        @elseif($baptismApplication->status === 'approved')
                            <span class="badge bg-info fs-6">Approved</span>
                        @elseif($baptismApplication->status === 'scheduled')
                            <span class="badge bg-success fs-6">Scheduled</span>
                        @elseif($baptismApplication->status === 'rejected')
                            <span class="badge bg-danger fs-6">Rejected</span>
                        @elseif($baptismApplication->status === 'completed')
                            <span class="badge bg-dark fs-6">Completed</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Submitted:</strong><br>
                        <span class="text-muted">{{ $baptismApplication->submitted_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($baptismApplication->scheduled_baptism_date)
                    <div class="mb-3">
                        <strong>Scheduled Baptism Date:</strong><br>
                        <span class="text-success fw-bold">{{ $baptismApplication->scheduled_baptism_date->format('F d, Y') }}</span>
                    </div>
                    @endif
                    @if($baptismApplication->pastor)
                    <div class="mb-3">
                        <strong>Reviewed By:</strong><br>
                        <span class="text-muted">{{ $baptismApplication->pastor->name }}</span><br>
                        <small class="text-muted">{{ $baptismApplication->reviewed_at->format('M d, Y h:i A') }}</small>
                    </div>
                    @endif
                    @if($baptismApplication->pastor_comments)
                    <div>
                        <strong>Pastor Comments:</strong><br>
                        <p class="text-muted">{{ $baptismApplication->pastor_comments }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Pastor Actions -->
            @if((auth()->user()->isPastor() || auth()->user()->isAdmin()) && $baptismApplication->status === 'pending')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Pastor Actions</h5>
                </div>
                <div class="card-body">
                    <!-- Approve Form -->
                    <form action="{{ route('pastor.baptism-applications.approve', $baptismApplication->id) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-2">
                            <label for="scheduled_baptism_date" class="form-label small">Schedule Baptism Date (Optional)</label>
                            <input type="date" class="form-control form-control-sm" id="scheduled_baptism_date" 
                                   name="scheduled_baptism_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                        </div>
                        <div class="mb-2">
                            <label for="pastor_comments_approve" class="form-label small">Comments (Optional)</label>
                            <textarea class="form-control form-control-sm" id="pastor_comments_approve" 
                                      name="pastor_comments" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-check me-1"></i> Approve Application
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="{{ route('pastor.baptism-applications.reject', $baptismApplication->id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to reject this application?');">
                        @csrf
                        <div class="mb-2">
                            <label for="pastor_comments_reject" class="form-label small">Rejection Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control form-control-sm @error('pastor_comments') is-invalid @enderror" 
                                      id="pastor_comments_reject" name="pastor_comments" rows="2" required></textarea>
                            @error('pastor_comments')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-times me-1"></i> Reject Application
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Schedule/Complete Actions -->
            @if((auth()->user()->isPastor() || auth()->user()->isAdmin()) && $baptismApplication->status === 'approved')
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar me-2"></i>Schedule Baptism</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('pastor.baptism-applications.schedule', $baptismApplication->id) }}" method="POST">
                        @csrf
                        <div class="mb-2">
                            <label for="scheduled_date" class="form-label small">Baptism Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control form-control-sm" id="scheduled_date" 
                                   name="scheduled_baptism_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                        <button type="submit" class="btn btn-info btn-sm w-100">
                            <i class="fas fa-calendar-check me-1"></i> Schedule Baptism
                        </button>
                    </form>
                </div>
            </div>
            @endif

            @if((auth()->user()->isPastor() || auth()->user()->isAdmin()) && $baptismApplication->status === 'scheduled')
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <form action="{{ route('pastor.baptism-applications.complete', $baptismApplication->id) }}" method="POST" 
                          onsubmit="return confirm('Mark this baptism as completed?');">
                        @csrf
                        <button type="submit" class="btn btn-dark btn-sm w-100">
                            <i class="fas fa-check-double me-1"></i> Mark as Completed
                        </button>
                    </form>
                </div>
            </div>
            @endif

            <!-- Workflow Info -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Workflow</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Submitted By:</strong><br>
                        <span class="text-muted">{{ $baptismApplication->evangelismLeader->name ?? 'N/A' }}</span>
                    </div>
                    @if($baptismApplication->pastor)
                    <div>
                        <strong>Reviewed By:</strong><br>
                        <span class="text-muted">{{ $baptismApplication->pastor->name }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

