@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm report-header-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-church me-2"></i>Usharika Dashboard</h1>
                                <p class="mb-0">Overview of All Branches</p>
                            </div>
                            <div>
                                <span class="badge bg-light text-dark fs-6">{{ $branches->count() }} Branches</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2">Total Members</h6>
                                <h2 class="mb-0">{{ number_format($totalMembers) }}</h2>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-users fa-2x"></i>
                            </div>
                        </div>
                        @if($newMembersThisMonth > 0)
                            <small class="text-success">
                                <i class="fas fa-arrow-up"></i> {{ $newMembersThisMonth }} new this month
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
                                <h6 class="text-muted mb-2">Total Branches</h6>
                                <h2 class="mb-0">{{ number_format($branches->count()) }}</h2>
                            </div>
                            <div class="text-dark">
                                <i class="fas fa-building fa-2x"></i>
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
                                <h6 class="text-muted mb-2">Usharika Members</h6>
                                <h2 class="mb-0">{{ number_format($mainCampusStats['total_members']) }}</h2>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-home fa-2x"></i>
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
                                <h6 class="text-muted mb-2">Total Leaders</h6>
                                <h2 class="mb-0">
                                    {{ number_format($mainCampusStats['total_leaders'] + collect($branchStats)->sum('stats.total_leaders')) }}
                                </h2>
                            </div>
                            <div class="text-danger">
                                <i class="fas fa-user-tie fa-2x"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branch Statistics Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header report-header-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-table me-2"></i>Branch Statistics</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Branch Name</th>
                                        <th>Code</th>
                                        <th>Total Members</th>
                                        <th>Leaders</th>
                                        <th>Communities</th>
                                        <th>New Members (This Month)</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($branchStats as $branchStat)
                                        <tr>
                                            <td>
                                                <strong>{{ $branchStat['branch']->name }}</strong>
                                                @if($branchStat['branch']->description)
                                                    <br><small
                                                        class="text-muted">{{ Str::limit($branchStat['branch']->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $branchStat['branch']->code }}</span></td>
                                            <td><span
                                                    class="badge bg-danger">{{ number_format($branchStat['stats']['total_members']) }}</span>
                                            </td>
                                            <td><span
                                                    class="badge bg-dark">{{ number_format($branchStat['stats']['total_leaders']) }}</span>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge bg-dark">{{ number_format($branchStat['communities_count'] ?? 0) }}</span>
                                            </td>
                                            <td>
                                                @if($branchStat['new_members_this_month'] > 0)
                                                    <span
                                                        class="badge bg-success">{{ $branchStat['new_members_this_month'] }}</span>
                                                @else
                                                    <span class="text-muted">0</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('campuses.show', $branchStat['branch']) }}"
                                                    class="btn btn-sm btn-outline-danger" title="View Branch">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('members.view', ['campus_id' => $branchStat['branch']->id]) }}"
                                                    class="btn btn-sm btn-outline-dark" title="View Members">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    @if(empty($branchStats))
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                                No branches created yet. <a href="{{ route('campuses.create') }}">Create the
                                                    first branch</a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header report-header-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Recent Member Registrations (All Branches)
                        </h5>
                    </div>
                    <div class="card-body">
                        @if($recentMembers->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Member ID</th>
                                            <th>Branch</th>
                                            <th>Registered Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentMembers as $member)
                                            <tr>
                                                <td>{{ $member->full_name }}</td>
                                                <td><span class="badge bg-secondary">{{ $member->member_id }}</span></td>
                                                <td>
                                                    @if($member->campus)
                                                        <span class="badge bg-dark">{{ $member->campus->name }}</span>
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td><small
                                                        class="text-muted">{{ $member->created_at->format('M d, Y H:i') }}</small>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('members.view') }}" class="btn btn-sm btn-outline-danger">View All Members</a>
                            </div>
                        @else
                            <p class="text-muted text-center py-4">No members registered yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection