@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Members with Disabilities</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.welfare.index') }}">Social Welfare</a></li>
        <li class="breadcrumb-item active">Disabilities</li>
    </ol>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Report
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.welfare.disabilities') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name/Phone/ID">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Disability Type</label>
                        <input type="text" class="form-control" name="disability_type" value="{{ request('disability_type') }}" placeholder="e.g. Visual, Physical">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Campus/Branch</label>
                        <select class="form-select" name="campus_id">
                            <option value="">All Branches</option>
                            @foreach($campuses as $campus)
                                <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                    {{ $campus->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Gender</label>
                        <select class="form-select" name="gender">
                            <option value="">All</option>
                            <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="col-12 text-end">
                        <a href="{{ route('reports.welfare.disabilities') }}" class="btn btn-secondary me-2">Reset</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Filter</button>
                        <a href="{{ route('reports.welfare.disabilities.export', request()->all()) }}" class="btn btn-success ms-2"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Results ({{ $members->total() }})
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Member ID</th>
                            <th>Full Name</th>
                            <th>Gender</th>
                            <th>Disability Type</th>
                            <th>Branch</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($members as $member)
                            <tr>
                                <td>{{ $member->member_id }}</td>
                                <td>{{ $member->full_name }}</td>
                                <td>{{ ucfirst($member->gender) }}</td>
                                <td>{{ $member->disability_type ?: 'Unspecified' }}</td>
                                <td>{{ $member->campus->name ?? 'N/A' }}</td>
                                <td>{{ $member->phone_number ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('members.show', $member->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i> View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">No records found matching your criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $members->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
