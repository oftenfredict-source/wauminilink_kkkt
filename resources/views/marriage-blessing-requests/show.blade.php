@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-heart me-2 text-primary"></i>Marriage Blessing Request Details</h1>
                            <p class="text-muted mb-0">{{ $marriageBlessingRequest->husband_full_name }} & {{ $marriageBlessingRequest->wife_full_name }}</p>
                        </div>
                        <div>
                            @if(auth()->user()->isEvangelismLeader())
                                <a href="{{ route('evangelism-leader.marriage-blessing-requests.index') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-1"></i> Back
                                </a>
                            @elseif(auth()->user()->isPastor() || auth()->user()->isAdmin())
                                <a href="{{ route('pastor.marriage-blessing-requests.pending') }}" class="btn btn-outline-primary">
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
            <!-- Couple Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Couple Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Husband's Full Name:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->husband_full_name }}</span>
                        </div>
                        <div class="col-md-6 mb-2">
                            <strong>Wife's Full Name:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->wife_full_name }}</span>
                        </div>
                    </div>
                    <div class="row">
                        @if($marriageBlessingRequest->phone_number)
                        <div class="col-md-6 mb-2">
                            <strong>Phone Number:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->phone_number }}</span>
                        </div>
                        @endif
                        @if($marriageBlessingRequest->email)
                        <div class="col-md-6 mb-2">
                            <strong>Email:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->email }}</span>
                        </div>
                        @endif
                    </div>
                    @if($marriageBlessingRequest->churchBranch)
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Church Branch:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->churchBranch->name }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Marriage Details -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-ring me-2"></i>Marriage Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Marriage Date:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->marriage_date->format('F d, Y') }}</span>
                        </div>
                        @if($marriageBlessingRequest->marriage_type)
                        <div class="col-md-6 mb-2">
                            <strong>Type of Marriage:</strong><br>
                            <span class="text-muted">{{ ucfirst(str_replace('_', ' ', $marriageBlessingRequest->marriage_type)) }}</span>
                        </div>
                        @endif
                    </div>
                    @if($marriageBlessingRequest->place_of_marriage)
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <strong>Place of Marriage:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->place_of_marriage }}</span>
                        </div>
                        @if($marriageBlessingRequest->marriage_certificate_number)
                        <div class="col-md-6 mb-2">
                            <strong>Certificate Number:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->marriage_certificate_number }}</span>
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Church Information -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-church me-2"></i>Church Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <strong>Both spouses members:</strong><br>
                            <span class="badge bg-{{ $marriageBlessingRequest->both_spouses_members ? 'success' : 'warning' }}">
                                {{ $marriageBlessingRequest->both_spouses_members ? 'Yes' : 'No' }}
                            </span>
                        </div>
                        <div class="col-md-6">
                            <strong>Attended marriage counseling:</strong><br>
                            <span class="badge bg-{{ $marriageBlessingRequest->attended_marriage_counseling ? 'success' : 'warning' }}">
                                {{ $marriageBlessingRequest->attended_marriage_counseling ? 'Yes' : 'No' }}
                            </span>
                        </div>
                    </div>
                    @if($marriageBlessingRequest->membership_duration)
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Membership duration:</strong><br>
                            <span class="text-muted">{{ $marriageBlessingRequest->membership_duration }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Declaration -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Declaration</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Reason for requesting blessing:</strong><br>
                        <span class="text-muted">{{ $marriageBlessingRequest->reason_for_blessing }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Declaration agreed:</strong><br>
                        <span class="badge bg-{{ $marriageBlessingRequest->declaration_agreed ? 'success' : 'danger' }}">
                            {{ $marriageBlessingRequest->declaration_agreed ? 'Yes' : 'No' }}
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
                        @if($marriageBlessingRequest->status === 'pending')
                            <span class="badge bg-warning fs-6">Pending</span>
                        @elseif($marriageBlessingRequest->status === 'approved')
                            <span class="badge bg-success fs-6">Approved</span>
                        @elseif($marriageBlessingRequest->status === 'scheduled')
                            <span class="badge bg-info fs-6">Scheduled</span>
                        @elseif($marriageBlessingRequest->status === 'counseling_required')
                            <span class="badge bg-info fs-6">Counseling Required</span>
                        @elseif($marriageBlessingRequest->status === 'rejected')
                            <span class="badge bg-danger fs-6">Rejected</span>
                        @elseif($marriageBlessingRequest->status === 'completed')
                            <span class="badge bg-dark fs-6">Completed</span>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Submitted:</strong><br>
                        <span class="text-muted">{{ $marriageBlessingRequest->submitted_at->format('F d, Y h:i A') }}</span>
                    </div>
                    @if($marriageBlessingRequest->reviewed_at)
                    <div class="mb-3">
                        <strong>Reviewed:</strong><br>
                        <span class="text-muted">{{ $marriageBlessingRequest->reviewed_at->format('F d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @if($marriageBlessingRequest->scheduled_blessing_date)
                    <div class="mb-3">
                        <strong>Scheduled Blessing Date:</strong><br>
                        <span class="text-muted">{{ $marriageBlessingRequest->scheduled_blessing_date->format('F d, Y') }}</span>
                    </div>
                    @endif
                    @if($marriageBlessingRequest->evangelismLeader)
                    <div class="mb-3">
                        <strong>Submitted by:</strong><br>
                        <span class="text-muted">{{ $marriageBlessingRequest->evangelismLeader->name }}</span>
                    </div>
                    @endif
                </div>
            </div>

            @if(auth()->user()->isPastor() || auth()->user()->isAdmin())
                @if($marriageBlessingRequest->status === 'pending')
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-gavel me-2"></i>Pastor Actions</h5>
                    </div>
                    <div class="card-body">
                        <!-- Approve Form -->
                        <form action="{{ route('pastor.marriage-blessing-requests.approve', $marriageBlessingRequest->id) }}" method="POST" class="mb-3">
                            @csrf
                            <div class="mb-2">
                                <label for="scheduled_blessing_date" class="form-label">Scheduled Blessing Date (optional)</label>
                                <input type="date" class="form-control mb-2" id="scheduled_blessing_date" name="scheduled_blessing_date" 
                                       min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                                <label for="pastor_comments_approve" class="form-label">Comments (optional)</label>
                                <textarea class="form-control" id="pastor_comments_approve" name="pastor_comments" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-1"></i> Approve Request
                            </button>
                        </form>

                        <!-- Require Counseling Form -->
                        <form action="{{ route('pastor.marriage-blessing-requests.require-counseling', $marriageBlessingRequest->id) }}" method="POST" class="mb-3">
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
                        <form action="{{ route('pastor.marriage-blessing-requests.reject', $marriageBlessingRequest->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to reject this request?');">
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
            @endif

            @if($marriageBlessingRequest->pastor_comments)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-comment me-2"></i>Pastor Comments</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $marriageBlessingRequest->pastor_comments }}</p>
                    @if($marriageBlessingRequest->pastor)
                        <small class="text-muted">By: {{ $marriageBlessingRequest->pastor->name }} on {{ $marriageBlessingRequest->reviewed_at->format('F d, Y h:i A') }}</small>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

