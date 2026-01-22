@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0"><i class="fas fa-building me-2 text-primary"></i>Branch Dashboard</h1>
                            <p class="text-muted mb-0">{{ $campus->name }}</p>
                        </div>
                        <div>
                            <span class="badge bg-primary fs-6">{{ $campus->code }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Members</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_members']) }}</h2>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                    @if($newMembersThisMonth > 0)
                    <small class="text-success">
                        <i class="fas fa-arrow-up"></i> {{ $newMembersThisMonth }} this month
                    </small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Leaders</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_leaders']) }}</h2>
                        </div>
                        <div class="text-info">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Tithes</h6>
                            <h2 class="mb-0">TZS {{ number_format($stats['total_tithes'], 0) }}</h2>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-coins fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Total Offerings</h6>
                            <h2 class="mb-0">TZS {{ number_format($stats['total_offerings'], 0) }}</h2>
                        </div>
                        <div class="text-warning">
                            <i class="fas fa-gift fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="{{ route('members.add') }}" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-user-plus fa-2x mb-2"></i>
                                <span>Register Member</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('leaders.create') }}" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-user-tie fa-2x mb-2"></i>
                                <span>Assign Leader</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('members.view') }}" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span>View Members</span>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('leaders.index') }}" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                <i class="fas fa-list fa-2x mb-2"></i>
                                <span>View Leaders</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Members -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Recent Members</h5>
                </div>
                <div class="card-body">
                    @if($recentMembers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Member ID</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentMembers as $member)
                                    <tr>
                                        <td>{{ $member->full_name }}</td>
                                        <td><span class="badge bg-secondary">{{ $member->member_id }}</span></td>
                                        <td><small class="text-muted">{{ $member->created_at->format('M d, Y') }}</small></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('members.view') }}" class="btn btn-sm btn-outline-success">View All Members</a>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No members registered yet.</p>
                        <div class="text-center">
                            <a href="{{ route('members.add') }}" class="btn btn-sm btn-primary">Register First Member</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Leaders -->
        <div class="col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Recent Leaders</h5>
                </div>
                <div class="card-body">
                    @if($recentLeaders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentLeaders as $leader)
                                    <tr>
                                        <td>{{ $leader->member->full_name ?? 'N/A' }}</td>
                                        <td><span class="badge bg-info">{{ $leader->position_display }}</span></td>
                                        <td><small class="text-muted">{{ $leader->created_at->format('M d, Y') }}</small></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('leaders.index') }}" class="btn btn-sm btn-outline-info">View All Leaders</a>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">No leaders assigned yet.</p>
                        <div class="text-center">
                            <a href="{{ route('leaders.create') }}" class="btn btn-sm btn-info">Assign First Leader</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Member Growth Chart -->
    @if(count($memberGrowth) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Member Growth (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <canvas id="memberGrowthChart" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@if(count($memberGrowth) > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('memberGrowthChart');
        if (ctx) {
            const growthData = @json($memberGrowth);
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: growthData.map(item => item.month),
                    datasets: [{
                        label: 'New Members',
                        data: growthData.map(item => item.count),
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endif
@endsection














