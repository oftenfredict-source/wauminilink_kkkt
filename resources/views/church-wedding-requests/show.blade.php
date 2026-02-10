@extends('layouts.index')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-rings-wedding me-2 text-primary"></i>Church Wedding
                                    Request Details</h1>
                                <p class="text-muted mb-0">{{ $churchWeddingRequest->groom_full_name }} &
                                    {{ $churchWeddingRequest->bride_full_name }}
                                </p>
                            </div>
                            <div>
                                @if(auth()->user()->isEvangelismLeader())
                                    <a href="{{ route('evangelism-leader.church-wedding-requests.index') }}"
                                        class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-1"></i> Back
                                    </a>
                                @elseif(auth()->user()->isPastor() || auth()->user()->isAdmin())
                                    <a href="{{ route('pastor.church-wedding-requests.pending') }}"
                                        class="btn btn-outline-primary">
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
            <div class="col-md-8 mb-4">
                <!-- Bride & Groom Information -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>Bride & Groom Information</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="mb-3">Groom's Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Full Name:</strong><br>
                                <span class="text-muted">{{ $churchWeddingRequest->groom_full_name }}</span>
                            </div>
                            @if($churchWeddingRequest->groom_date_of_birth)
                                <div class="col-md-3 mb-2">
                                    <strong>Date of Birth:</strong><br>
                                    <span
                                        class="text-muted">{{ $churchWeddingRequest->groom_date_of_birth->format('F d, Y') }}</span>
                                </div>
                            @endif
                            @if($churchWeddingRequest->groom_phone_number)
                                <div class="col-md-3 mb-2">
                                    <strong>Phone:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->groom_phone_number }}</span>
                                </div>
                            @endif
                        </div>
                        @if($churchWeddingRequest->groom_email)
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->groom_email }}</span>
                                </div>
                            </div>
                        @endif

                        <hr class="my-4">

                        <h6 class="mb-3">Bride's Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Full Name:</strong><br>
                                <span class="text-muted">{{ $churchWeddingRequest->bride_full_name }}</span>
                            </div>
                            @if($churchWeddingRequest->bride_date_of_birth)
                                <div class="col-md-3 mb-2">
                                    <strong>Date of Birth:</strong><br>
                                    <span
                                        class="text-muted">{{ $churchWeddingRequest->bride_date_of_birth->format('F d, Y') }}</span>
                                </div>
                            @endif
                            @if($churchWeddingRequest->bride_phone_number)
                                <div class="col-md-3 mb-2">
                                    <strong>Phone:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->bride_phone_number }}</span>
                                </div>
                            @endif
                        </div>
                        @if($churchWeddingRequest->bride_email)
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Email:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->bride_email }}</span>
                                </div>
                            </div>
                        @endif
                        @if($churchWeddingRequest->churchBranch)
                            <div class="row mt-2">
                                <div class="col-md-6 mb-2">
                                    <strong>Church Branch:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->churchBranch->name }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Membership & Spiritual Information -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-praying-hands me-2"></i>Membership & Spiritual Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>Both baptized:</strong><br>
                                <span class="badge bg-{{ $churchWeddingRequest->both_baptized ? 'success' : 'warning' }}">
                                    {{ $churchWeddingRequest->both_baptized ? 'Yes' : 'No' }}
                                </span>
                            </div>
                            <div class="col-md-6">
                                <strong>Both confirmed:</strong><br>
                                <span class="badge bg-{{ $churchWeddingRequest->both_confirmed ? 'success' : 'warning' }}">
                                    {{ $churchWeddingRequest->both_confirmed ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-6">
                                <strong>Attended premarital counseling:</strong><br>
                                <span
                                    class="badge bg-{{ $churchWeddingRequest->attended_premarital_counseling ? 'success' : 'warning' }}">
                                    {{ $churchWeddingRequest->attended_premarital_counseling ? 'Yes' : 'No' }}
                                </span>
                            </div>
                        </div>
                        @if($churchWeddingRequest->membership_duration)
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Membership duration:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->membership_duration }}</span>
                                </div>
                            </div>
                        @endif
                        @if($churchWeddingRequest->pastor_catechist_name)
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Pastor / Catechist:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->pastor_catechist_name }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Wedding Details -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Wedding Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($churchWeddingRequest->preferred_wedding_date)
                                <div class="col-md-6 mb-2">
                                    <strong>Preferred Wedding Date:</strong><br>
                                    <span
                                        class="text-muted">{{ $churchWeddingRequest->preferred_wedding_date->format('F d, Y') }}</span>
                                </div>
                            @endif
                            @if($churchWeddingRequest->preferred_church)
                                <div class="col-md-6 mb-2">
                                    <strong>Preferred Church:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->preferred_church }}</span>
                                </div>
                            @endif
                        </div>
                        @if($churchWeddingRequest->expected_guests)
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>Expected Guests:</strong><br>
                                    <span class="text-muted">{{ $churchWeddingRequest->expected_guests }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Documents -->
                @if(
                        $churchWeddingRequest->groom_baptism_certificate_path || $churchWeddingRequest->bride_baptism_certificate_path ||
                        $churchWeddingRequest->groom_confirmation_certificate_path || $churchWeddingRequest->bride_confirmation_certificate_path ||
                        $churchWeddingRequest->groom_birth_certificate_path || $churchWeddingRequest->bride_birth_certificate_path ||
                        $churchWeddingRequest->marriage_notice_path
                    )
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-file-upload me-2"></i>Uploaded Documents</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($churchWeddingRequest->groom_baptism_certificate_path)
                                    <div class="col-md-6 mb-2">
                                        <strong>Groom's Baptism Certificate:</strong><br>
                                        <a href="{{ Storage::url($churchWeddingRequest->groom_baptism_certificate_path) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> View
                                        </a>
                                    </div>
                                @endif
                                @if($churchWeddingRequest->bride_baptism_certificate_path)
                                    <div class="col-md-6 mb-2">
                                        <strong>Bride's Baptism Certificate:</strong><br>
                                        <a href="{{ Storage::url($churchWeddingRequest->bride_baptism_certificate_path) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> View
                                        </a>
                                    </div>
                                @endif
                                @if($churchWeddingRequest->groom_confirmation_certificate_path)
                                    <div class="col-md-6 mb-2">
                                        <strong>Groom's Confirmation Certificate:</strong><br>
                                        <a href="{{ Storage::url($churchWeddingRequest->groom_confirmation_certificate_path) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> View
                                        </a>
                                    </div>
                                @endif
                                @if($churchWeddingRequest->bride_confirmation_certificate_path)
                                    <div class="col-md-6 mb-2">
                                        <strong>Bride's Confirmation Certificate:</strong><br>
                                        <a href="{{ Storage::url($churchWeddingRequest->bride_confirmation_certificate_path) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> View
                                        </a>
                                    </div>
                                @endif
                                @if($churchWeddingRequest->groom_birth_certificate_path)
                                    <div class="col-md-6 mb-2">
                                        <strong>Groom's Birth Certificate:</strong><br>
                                        <a href="{{ Storage::url($churchWeddingRequest->groom_birth_certificate_path) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> View
                                        </a>
                                    </div>
                                @endif
                                @if($churchWeddingRequest->bride_birth_certificate_path)
                                    <div class="col-md-6 mb-2">
                                        <strong>Bride's Birth Certificate:</strong><br>
                                        <a href="{{ Storage::url($churchWeddingRequest->bride_birth_certificate_path) }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> View
                                        </a>
                                    </div>
                                @endif
                                @if($churchWeddingRequest->marriage_notice_path)
                                    <div class="col-md-6 mb-2">
                                        <strong>Marriage Notice:</strong><br>
                                        <a href="{{ Storage::url($churchWeddingRequest->marriage_notice_path) }}" target="_blank"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-download me-1"></i> View
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar - Status & Actions -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Request Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Status:</strong><br>
                            @if($churchWeddingRequest->status === 'pending')
                                <span class="badge bg-warning fs-6">Pending</span>
                            @elseif($churchWeddingRequest->status === 'approved')
                                <span class="badge bg-success fs-6">Approved</span>
                            @elseif($churchWeddingRequest->status === 'documents_required')
                                <span class="badge bg-warning fs-6">Documents Required</span>
                            @elseif($churchWeddingRequest->status === 'scheduled')
                                <span class="badge bg-info fs-6">Scheduled</span>
                            @elseif($churchWeddingRequest->status === 'rejected')
                                <span class="badge bg-danger fs-6">Rejected</span>
                            @elseif($churchWeddingRequest->status === 'completed')
                                <span class="badge bg-dark fs-6">Completed</span>
                            @endif
                        </div>
                        <div class="mb-3">
                            <strong>Submitted:</strong><br>
                            <span
                                class="text-muted">{{ $churchWeddingRequest->submitted_at->format('F d, Y h:i A') }}</span>
                        </div>
                        @if($churchWeddingRequest->reviewed_at)
                            <div class="mb-3">
                                <strong>Reviewed:</strong><br>
                                <span class="text-muted">{{ $churchWeddingRequest->reviewed_at->format('F d, Y h:i A') }}</span>
                            </div>
                        @endif
                        @if($churchWeddingRequest->scheduled_meeting_date)
                            <div class="mb-3">
                                <div class="p-2 bg-light rounded border border-primary border-start-4">
                                    <strong><i class="fas fa-calendar-alt text-primary me-1"></i>Scheduled Meeting:</strong><br>
                                    <span
                                        class="text-primary fw-bold">{{ $churchWeddingRequest->scheduled_meeting_date->format('F d, Y h:i A') }}</span>
                                </div>
                            </div>
                        @endif
                        @if($churchWeddingRequest->wedding_approval_date)
                            <div class="mb-3">
                                <strong>Wedding Approval Date:</strong><br>
                                <span
                                    class="text-muted">{{ $churchWeddingRequest->wedding_approval_date->format('F d, Y') }}</span>
                            </div>
                        @endif
                        @if($churchWeddingRequest->confirmed_wedding_date)
                            <div class="mb-3">
                                <strong>Confirmed Wedding Date:</strong><br>
                                <span
                                    class="text-muted">{{ $churchWeddingRequest->confirmed_wedding_date->format('F d, Y') }}</span>
                            </div>
                        @endif
                        @if($churchWeddingRequest->evangelismLeader)
                            <div class="mb-3">
                                <strong>Submitted by:</strong><br>
                                <span class="text-muted">{{ $churchWeddingRequest->evangelismLeader->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if(auth()->user()->isPastor() || auth()->user()->isAdmin())
                    @if($churchWeddingRequest->status === 'pending')
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>1. Schedule Meeting</h5>
                            </div>
                            <div class="card-body">
                                <p class="small text-muted mb-3">Schedule an initial meeting with the couple to verify details and
                                    documents.</p>
                                <form
                                    action="{{ route('pastor.church-wedding-requests.schedule-meeting', $churchWeddingRequest->id) }}"
                                    method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="scheduled_meeting_date" class="form-label">Meeting Date & Time <span
                                                class="text-danger">*</span></label>
                                        <input type="datetime-local" class="form-control" id="scheduled_meeting_date"
                                            name="scheduled_meeting_date" required min="{{ date('Y-m-d\TH:i') }}">
                                    </div>
                                    <div class="mb-3">
                                        <label for="pastor_comments_meeting" class="form-label">Internal Notes (optional)</label>
                                        <textarea class="form-control" id="pastor_comments_meeting" name="pastor_comments" rows="2"
                                            placeholder="Instructions for the couple..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="fas fa-calendar-check me-1"></i> Confirm Meeting
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    @if($churchWeddingRequest->status === 'scheduled' || $churchWeddingRequest->status === 'pending')
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>2. Complete Application</h5>
                            </div>
                            <div class="card-body text-center">
                                <p class="small text-muted mb-3">Fill in the full wedding details, spiritual info, and verify
                                    documents.</p>
                                <a href="{{ route('pastor.church-wedding-requests.edit', $churchWeddingRequest->id) }}"
                                    class="btn btn-outline-primary btn-lg w-100">
                                    <i class="fas fa-edit me-1"></i> Edit Full Form
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($churchWeddingRequest->status === 'scheduled' || $churchWeddingRequest->status === 'pending' || $churchWeddingRequest->status === 'documents_required')
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>3. Final Approval</h5>
                            </div>
                            <div class="card-body">
                                <!-- Approve Form -->
                                <form action="{{ route('pastor.church-wedding-requests.approve', $churchWeddingRequest->id) }}"
                                    method="POST" class="mb-3">
                                    @csrf
                                    <div class="mb-2">
                                        <label for="confirmed_wedding_date" class="form-label">Confirmed Wedding Date
                                            (optional)</label>
                                        <input type="date" class="form-control mb-2" id="confirmed_wedding_date"
                                            name="confirmed_wedding_date" min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                            value="{{ $churchWeddingRequest->confirmed_wedding_date ? $churchWeddingRequest->confirmed_wedding_date->format('Y-m-d') : '' }}">
                                        <label for="pastor_comments_approve" class="form-label">Final Pastor Comments
                                            (optional)</label>
                                        <textarea class="form-control" id="pastor_comments_approve" name="pastor_comments"
                                            rows="3">{{ $churchWeddingRequest->pastor_comments }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-check me-1"></i> Approve & Set Wedding Date
                                    </button>
                                </form>

                                @if($churchWeddingRequest->status !== 'documents_required')
                                    <!-- Require Documents Form -->
                                    <form
                                        action="{{ route('pastor.church-wedding-requests.require-documents', $churchWeddingRequest->id) }}"
                                        method="POST" class="mb-3">
                                        @csrf
                                        <div class="mb-2">
                                            <label for="pastor_comments_documents" class="form-label">Document Requirements <span
                                                    class="text-danger">*</span></label>
                                            <textarea class="form-control" id="pastor_comments_documents" name="pastor_comments"
                                                rows="2" required></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-warning w-100 text-dark">
                                            <i class="fas fa-file-alt me-1"></i> Request Documents
                                        </button>
                                    </form>
                                @endif

                                <!-- Reject Form -->
                                <form action="{{ route('pastor.church-wedding-requests.reject', $churchWeddingRequest->id) }}"
                                    method="POST" onsubmit="return confirm('Are you sure you want to reject this request?');">
                                    @csrf
                                    <div class="mb-2">
                                        <label for="pastor_comments_reject" class="form-label">Rejection Reason <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="pastor_comments_reject" name="pastor_comments" rows="2"
                                            required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-times me-1"></i> Reject Request
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                @endif

                @if($churchWeddingRequest->pastor_comments)
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Pastor Comments</h5>
                        </div>
                        <div class="card-body">
                            <p class="mb-0">{{ $churchWeddingRequest->pastor_comments }}</p>
                            @if($churchWeddingRequest->pastor && $churchWeddingRequest->reviewed_at)
                                <small class="text-muted">By: {{ $churchWeddingRequest->pastor->name }} on
                                    {{ $churchWeddingRequest->reviewed_at->format('F d, Y h:i A') }}</small>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection