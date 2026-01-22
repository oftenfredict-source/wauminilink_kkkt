@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-chart-bar me-2 text-primary"></i>Consolidated Offerings</h1>
                            <p class="text-muted mb-0">Ready to forward to General Secretary</p>
                        </div>
                        <a href="{{ route('evangelism-leader.offerings.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left me-1"></i> Back to Offerings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Amount</h6>
                    <h2 class="mb-0">TZS {{ number_format($totalAmount, 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Services</h6>
                    <h2 class="mb-0">{{ $totalCount }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Communities</h6>
                    <h2 class="mb-0">{{ $groupedByCommunity->count() }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- By Community -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-home me-2"></i>Breakdown by Community</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Community</th>
                                    <th>Services</th>
                                    <th>Total Amount (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedByCommunity as $communityId => $communityOfferings)
                                <tr>
                                    <td><strong>{{ $communityOfferings->first()->community->name }}</strong></td>
                                    <td>{{ $communityOfferings->count() }}</td>
                                    <td><strong>{{ number_format($communityOfferings->sum('amount'), 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <th>Total</th>
                                    <th>{{ $totalCount }}</th>
                                    <th><strong>{{ number_format($totalAmount, 2) }}</strong></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- By Service Type -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>Breakdown by Service Type</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Service Type</th>
                                    <th>Count</th>
                                    <th>Total Amount (TZS)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($groupedByServiceType as $serviceType => $typeOfferings)
                                <tr>
                                    <td><strong>{{ ucfirst(str_replace('_', ' ', $serviceType ?: 'General')) }}</strong></td>
                                    <td>{{ $typeOfferings->count() }}</td>
                                    <td><strong>{{ number_format($typeOfferings->sum('amount'), 2) }}</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed List -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-list-alt me-2"></i>Detailed List</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Community</th>
                                    <th>Service Type</th>
                                    <th>Amount (TZS)</th>
                                    <th>Collection Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($offerings as $offering)
                                <tr>
                                    <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                    <td>{{ $offering->community->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $offering->service_type ?: 'General')) }}</span>
                                    </td>
                                    <td><strong>{{ number_format($offering->amount, 2) }}</strong></td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $offering->collection_method)) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



