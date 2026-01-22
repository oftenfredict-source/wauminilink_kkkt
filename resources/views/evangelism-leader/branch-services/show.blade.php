@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-church me-2 text-primary"></i>Branch Sunday Service Details</h1>
                            <p class="text-muted mb-0">{{ $campus->name }} - {{ $service->service_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            @if(!$service->branchOfferings || $service->branchOfferings->count() == 0)
                                <a href="{{ route('evangelism-leader.branch-offerings.create', ['service_id' => $service->id]) }}" class="btn btn-success me-2">
                                    <i class="fas fa-money-bill-wave me-1"></i> Record Offering
                                </a>
                            @endif
                            <a href="{{ route('evangelism-leader.branch-services.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Service Details -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Service Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Service Date:</strong><br>
                            <span class="text-muted">{{ $service->service_date->format('l, F d, Y') }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong>Service Type:</strong><br>
                            <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $service->service_type)) }}</span>
                        </div>
                    </div>

                    @if($service->theme)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Theme:</strong><br>
                            <span class="text-muted">{{ $service->theme }}</span>
                        </div>
                    </div>
                    @endif

                    @if($service->preacher)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Preacher:</strong><br>
                            <span class="text-muted">{{ $service->preacher }}</span>
                        </div>
                        @if($service->venue)
                        <div class="col-md-6">
                            <strong>Venue:</strong><br>
                            <span class="text-muted">{{ $service->venue }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    @if($service->start_time || $service->end_time)
                    <div class="row mb-3">
                        @if($service->start_time)
                        <div class="col-md-6">
                            <strong>Start Time:</strong><br>
                            <span class="text-muted">{{ date('h:i A', strtotime($service->start_time)) }}</span>
                        </div>
                        @endif
                        @if($service->end_time)
                        <div class="col-md-6">
                            <strong>End Time:</strong><br>
                            <span class="text-muted">{{ date('h:i A', strtotime($service->end_time)) }}</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>Attendance:</strong><br>
                            <span class="badge bg-success fs-6">{{ $service->attendance_count ?? 0 }} members</span>
                            @if($service->guests_count > 0)
                                <span class="badge bg-secondary fs-6">{{ $service->guests_count }} guests</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <strong>Status:</strong><br>
                            <span class="badge bg-{{ $service->status === 'completed' ? 'success' : 'warning' }} fs-6">
                                {{ ucfirst($service->status ?? 'scheduled') }}
                            </span>
                        </div>
                    </div>

                    @if($service->scripture_readings)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Scripture Readings:</strong><br>
                            <span class="text-muted">{{ $service->scripture_readings }}</span>
                        </div>
                    </div>
                    @endif

                    @if($service->choir)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Choir:</strong><br>
                            <span class="text-muted">{{ $service->choir }}</span>
                        </div>
                    </div>
                    @endif

                    @if($service->announcements)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Announcements:</strong><br>
                            <span class="text-muted">{{ $service->announcements }}</span>
                        </div>
                    </div>
                    @endif

                    @if($service->notes)
                    <div class="row mb-3">
                        <div class="col-12">
                            <strong>Notes:</strong><br>
                            <span class="text-muted">{{ $service->notes }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Offerings Summary -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Offerings</h5>
                </div>
                <div class="card-body">
                    @if($service->branchOfferings && $service->branchOfferings->count() > 0)
                        <div class="mb-3">
                            <h3 class="text-success">TZS {{ number_format($service->branchOfferings->sum('amount'), 2) }}</h3>
                            <p class="text-muted mb-0">{{ $service->branchOfferings->count() }} offering(s) recorded</p>
                        </div>
                        <hr>
                        <h6 class="mb-3">Recent Offerings:</h6>
                        <div class="list-group list-group-flush">
                            @foreach($service->branchOfferings->take(5) as $offering)
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>TZS {{ number_format($offering->amount, 2) }}</strong><br>
                                        <small class="text-muted">{{ $offering->offering_date->format('M d, Y') }}</small><br>
                                        <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $offering->collection_method)) }}</span>
                                    </div>
                                    <div>
                                        @if($offering->status === 'pending_secretary')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($offering->status === 'completed')
                                            <span class="badge bg-success">Approved</span>
                                        @elseif($offering->status === 'rejected')
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @if($service->branchOfferings->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('evangelism-leader.branch-offerings.index') }}" class="btn btn-sm btn-outline-primary">View All Offerings</a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No offerings recorded yet</p>
                            <a href="{{ route('evangelism-leader.branch-offerings.create', ['service_id' => $service->id]) }}" class="btn btn-success">
                                <i class="fas fa-plus me-1"></i> Record Offering
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Service Info Card -->
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Service Info</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>Created By:</strong><br>
                        <span class="text-muted">{{ $service->evangelismLeader->name ?? 'N/A' }}</span>
                    </div>
                    <div class="mb-2">
                        <strong>Created At:</strong><br>
                        <span class="text-muted">{{ $service->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @if($service->updated_at != $service->created_at)
                    <div>
                        <strong>Last Updated:</strong><br>
                        <span class="text-muted">{{ $service->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



