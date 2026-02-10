@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Social Welfare Reports</h1>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Social Welfare</li>
        </ol>
        <a href="{{ route('reports.welfare.unified') }}" class="btn btn-primary">
            <i class="fas fa-hand-holding-heart me-1"></i> View Unified Social Welfare Report
        </a>
    </div>

    </div>

    <h2 class="mt-4 mb-3">Children Welfare</h2>
    <div class="row">
        <!-- Children Summary Cards -->
        <div class="col-xl-4 col-md-6">
            <div class="card bg-primary text-white mb-4 shadow rounded-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">Orphan Children</div>
                        <div class="fs-2 fw-bold">{{ number_format($totalChildOrphans) }}</div>
                    </div>
                    <i class="fas fa-child fa-3x opacity-25"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between border-0 bg-primary bg-gradient">
                    <a class="small text-white stretched-link text-decoration-none" href="{{ route('reports.welfare.children.orphans') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="card bg-warning text-dark mb-4 shadow rounded-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-dark-50 small text-uppercase fw-bold">Children with Disabilities</div>
                        <div class="fs-2 fw-bold">{{ number_format($totalChildDisabled) }}</div>
                    </div>
                    <i class="fas fa-wheelchair fa-3x opacity-25"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between border-0 bg-warning bg-gradient">
                    <a class="small text-dark stretched-link text-decoration-none" href="{{ route('reports.welfare.children.disabilities') }}">View Details</a>
                    <div class="small text-dark"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6">
            <div class="card bg-danger text-white mb-4 shadow rounded-3">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 small text-uppercase fw-bold">Vulnerable Children</div>
                        <div class="fs-2 fw-bold">{{ number_format($totalChildVulnerable) }}</div>
                    </div>
                    <i class="fas fa-hand-holding-heart fa-3x opacity-25"></i>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between border-0 bg-danger bg-gradient">
                    <a class="small text-white stretched-link text-decoration-none" href="{{ route('reports.welfare.children.vulnerable') }}">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mt-4 mb-3">Member Welfare Breakdown</h2>
    <div class="row">
        <!-- Orphan Breakdown -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Orphan Status Breakdown
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($orphanBreakdown as $status => $count)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                @php
                                    $label = match($status) {
                                        'father_deceased' => 'Father Deceased',
                                        'mother_deceased' => 'Mother Deceased',
                                        'both_deceased' => 'Both Deceased',
                                        default => ucwords(str_replace('_', ' ', $status))
                                    };
                                @endphp
                                {{ $label }}
                                <span class="badge bg-primary rounded-pill">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Disability Types Breakdown -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Top Disability Types
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($disabilityTypes as $type)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $type->disability_type }}
                                <span class="badge bg-warning text-dark rounded-pill">{{ $type->total }}</span>
                            </div>
                        @endforeach
                        @if($disabilityTypes->isEmpty())
                            <div class="text-center text-muted py-3">No data available</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Vulnerable Types Breakdown -->
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-bar me-1"></i>
                    Top Vulnerability Types
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        @foreach($vulnerableTypes as $type)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $type->vulnerable_type }}
                                <span class="badge bg-danger rounded-pill">{{ $type->total }}</span>
                            </div>
                        @endforeach
                        @if($vulnerableTypes->isEmpty())
                            <div class="text-center text-muted py-3">No data available</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
