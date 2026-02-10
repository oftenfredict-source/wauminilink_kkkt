@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-money-bill-wave me-2 text-success"></i>Branch Offering Details</h1>
                            <p class="text-muted mb-0">{{ $campus->name }} - {{ $offering->offering_date->format('F d, Y') }}</p>
                        </div>
                        <a href="{{ route('evangelism-leader.branch-offerings.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Offering Details -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Offering Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Amount:</strong><br>
                            <h3 class="text-success mb-0">TZS {{ number_format($offering->amount, 2) }}</h3>
                        </div>
                        <div class="col-md-6">
                            <strong>Offering Date:</strong><br>
                            <span class="text-muted">{{ $offering->offering_date->format('l, F d, Y') }}</span>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Collection Method:</strong><br>
                            <span class="badge bg-secondary fs-6">{{ ucfirst(str_replace('_', ' ', $offering->collection_method)) }}</span>
                        </div>
                        @if($offering->reference_number)
                        <div class="col-md-6">
                            <strong>Reference Number:</strong><br>
                            <span class="text-muted">{{ $offering->reference_number }}</span>
                        </div>
                        @endif
                    </div>

                    @if($offering->service)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Linked Service:</strong><br>
                            <a href="{{ route('evangelism-leader.branch-services.show', $offering->service->id) }}" class="text-decoration-none">
                                <span class="badge bg-info">{{ $offering->service->service_date->format('M d, Y') }}</span>
                                @if($offering->service->theme)
                                    - {{ $offering->service->theme }}
                                @endif
                            </a>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            @if($offering->status === 'pending_secretary')
                                <span class="badge bg-warning fs-6">Pending Secretary Approval</span>
                            @elseif($offering->status === 'completed')
                                <span class="badge bg-success fs-6">Completed</span>
                            @elseif($offering->status === 'rejected')
                                <span class="badge bg-danger fs-6">Rejected</span>
                            @endif
                        </div>
                        @if($offering->handover_to_secretary_at)
                        <div class="col-md-6">
                            <strong>Sent to Secretary:</strong><br>
                            <span class="text-muted">{{ $offering->handover_to_secretary_at->format('M d, Y h:i A') }}</span>
                        </div>
                        @endif
                    </div>

                    @if($offering->notes)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Notes:</strong><br>
                            <span class="text-muted">{{ $offering->notes }}</span>
                        </div>
                    </div>
                    @endif

                    @if($offering->leader_notes)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Leader Notes (Internal):</strong><br>
                            <span class="text-muted">{{ $offering->leader_notes }}</span>
                        </div>
                    </div>
                    @endif

                    @if($offering->secretary_notes)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Secretary Notes:</strong><br>
                            <span class="text-muted">{{ $offering->secretary_notes }}</span>
                        </div>
                    </div>
                    @endif

                    @if($offering->status === 'rejected' && $offering->rejection_reason)
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="alert alert-danger">
                                <strong><i class="fas fa-exclamation-triangle me-2"></i>Rejection Reason:</strong><br>
                                {{ $offering->rejection_reason }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Workflow Information -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Workflow</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Recorded By:</strong><br>
                        <span class="text-muted">{{ $offering->evangelismLeader->name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Recorded At:</strong><br>
                        <span class="text-muted">{{ $offering->created_at->format('M d, Y h:i A') }}</span>
                    </div>

                    @if($offering->secretary)
                    <div class="mb-3">
                        <strong>Approved By:</strong><br>
                        <span class="text-muted">{{ $offering->secretary->name }}</span>
                    </div>
                    @endif

                    @if($offering->rejectedBy)
                    <div class="mb-3">
                        <strong>Rejected By:</strong><br>
                        <span class="text-danger">{{ $offering->rejectedBy->name }}</span>
                    </div>
                    @if($offering->rejected_at)
                    <div class="mb-3">
                        <strong>Rejected At:</strong><br>
                        <span class="text-muted">{{ $offering->rejected_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @endif

                    <hr>

                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> Branch offerings go directly to the General Secretary for approval, bypassing the community elder workflow.
                    </div>
                </div>
            </div>

            <!-- Campus Info -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Campus</h5>
                </div>
                <div class="card-body">
                    <strong>{{ $campus->name }}</strong><br>
                    <small class="text-muted">{{ $campus->code }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection








