@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-door-open me-2 text-primary"></i>Return to Fellowship Request Details</h1>
                            <p class="text-muted mb-0">{{ $returnToFellowshipRequest->full_name }}</p>
                        </div>
                        <div>
                            @if(auth()->user()->isEvangelismLeader())
                                <a href="{{ route('evangelism-leader.return-to-fellowship-requests.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            @elseif(auth()->user()->isPastor() || auth()->user()->isAdmin())
                                <a href="{{ route('pastor.return-to-fellowship-requests.pending') }}" class="btn btn-outline-primary">
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
            <!-- Personal Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Full Name:</strong><br>
                            <span class="text-muted">{{ $returnToFellowshipRequest->full_name }}</span>
                        </div>
                        <div class="col-md-3 mb-2">
                            <strong>Gender:</strong><br>
                            <span class="text-muted">{{ ucfirst($returnToFellowshipRequest->gender) }}</span>
                        </div>
                        @if($returnToFellowshipRequest->date_of_birth)
                        <div class="col-md-3 mb-2">
                            <strong>Date of Birth:</strong><br>
                            <span class="text-muted">{{ $returnToFellowshipRequest->date_of_birth->format('F d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="row">
                        @if($returnToFellowshipRequest->phone_number)
                        <div class="col-md-6 mb-2">
                            <strong>Phone Number:</strong><br>
                            <span class="text-muted">{{ $returnToFellowshipRequest->phone_number }}</span>
                        </div>
                        @endif
                        @if($returnToFellowshipRequest->email)
                        <div class="col-md-6 mb-2">
                            <strong>Email:</strong><br>
                            <span class="text-muted">{{ $returnToFellowshipRequest->email }}</span>
                        </div>
                        @endif
                    </div>
                    @if($returnToFellowshipRequest->churchBranch)
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Church Branch:</strong><br>
                            <span class="text-muted">{{ $returnToFellowshipRequest->churchBranch->name }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Church Background -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>Church Background</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Previously a member:</strong><br>
                            <span class="badge bg-{{ $returnToFellowshipRequest->previously_member ? 'info' : 'secondary' }}">
                                {{ $returnToFellowshipRequest->previously_member ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        @if($returnToFellowshipRequest->previous_church_branch)
                        <div class="col-md-6">
                            <strong>Previous Church / Branch:</strong><br>
                            <span class="text-muted">{{ $returnToFellowshipRequest->previous_church_branch }}</span>
                        </div>
                        @endif
                    </div>
                    @if($returnToFellowshipRequest->period_away)
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Period away:</strong><br>
                            <span class="text-muted">{{ $returnToFellowshipRequest->period_away }}</span>
                        </div>
                    </div>
                    @endif
                    @if($returnToFellowshipRequest->reason_for_leaving)
                    <div class="mb-2">
                        <strong>Reason for leaving:</strong><br>
                        <span class="text-muted">{{ $returnToFellowshipRequest->reason_for_leaving }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Return Declaration -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Return Declaration</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Reason for returning:</strong><br>
                        <span class="text-muted">{{ $returnToFellowshipRequest->reason_for_returning }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Declaration agreed:</strong><br>
                        <span class="badge bg-{{ $returnToFellowshipRequest->declaration_agreed ? 'success' : 'danger' }}">
                            {{ $returnToFellowshipRequest->declaration_agreed ? 'Yes' : 'No' }}
                        </span>
                    </div>
                </div>
            </div>
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
                        @if($returnToFellowshipRequest->status === 'pending')
                            <span class="badge bg-warning fs-6">Pending</span>
                        @elseif($returnToFellowshipRequest->status === 'approved')
                            <span class="badge bg-success fs-6">Approved</span>
                        @elseif($returnToFellowshipRequest->status === 'counseling_required')
                            <span class="badge bg-info fs-6">Counseling Required</span>
                        @elseif($returnToFellowshipRequest->status === 'rejected')
                            <span class="badge bg-danger fs-6">Rejected</span>
                        @elseif($returnToFellowshipRequest->status === 'completed')
                            <span class="badge bg-dark fs-6">Completed</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Submitted:</strong><br>
                        <span class="text-muted">{{ $returnToFellowshipRequest->submitted_at->format('F d, Y h:i A') }}</span>
                    </div>
                    @if($returnToFellowshipRequest->reviewed_at)
                    <div class="mb-3">
                        <strong>Reviewed:</strong><br>
                        <span class="text-muted">{{ $returnToFellowshipRequest->reviewed_at->format('F d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @if($returnToFellowshipRequest->evangelismLeader)
                    <div class="mb-3">
                        <strong>Submitted by:</strong><br>
                        <span class="text-muted">{{ $returnToFellowshipRequest->evangelismLeader->name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isPastor() || auth()->user()->isAdmin())
                @if($returnToFellowshipRequest->status === 'pending')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-gavel me-2"></i>Pastor Actions</h5>
                    </div>
                    <div class="card-body">
                        <!-- Approve Form -->
                        <form action="{{ route('pastor.return-to-fellowship-requests.approve', $returnToFellowshipRequest->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-2">
                                <label for="pastor_comments_approve" class="form-label">Comments (optional)</label>
                                <textarea class="form-control" id="pastor_comments_approve" name="pastor_comments" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-1"></i> Approve Request
                            </button>
                        </form>

                        <!-- Require Counseling Form -->
                        <form action="{{ route('pastor.return-to-fellowship-requests.require-counseling', $returnToFellowshipRequest->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-2">
                                <label for="pastor_comments_counseling" class="form-label">Comments <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="pastor_comments_counseling" name="pastor_comments" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-user-md me-1"></i> Require Counseling
                            </button>
                        </form>

                        <!-- Reject Form -->
                        <form action="{{ route('pastor.return-to-fellowship-requests.reject', $returnToFellowshipRequest->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this request?');">
                            @csrf
                            <div class="mb-2">
                                <label for="pastor_comments_reject" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="pastor_comments_reject" name="pastor_comments" rows="3" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-times me-1"></i> Reject Request
                            </button>
                        </form>
                    </div>
                </div>
                @endif

                @if($returnToFellowshipRequest->pastor_comments)
                <div class="card border-0 shadow-sm mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Pastor Comments</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $returnToFellowshipRequest->pastor_comments }}</p>
                    </div>
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection



