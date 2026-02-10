@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Children Orphans Report</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.welfare.index') }}">Social Welfare</a></li>
        <li class="breadcrumb-item active">Children Orphans</li>
    </ol>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Report
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.welfare.children.orphans') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Child Name/Parent Name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Orphan Status</label>
                        <select class="form-select" name="orphan_status">
                            <option value="">All Types</option>
                            <option value="father_deceased" {{ request('orphan_status') == 'father_deceased' ? 'selected' : '' }}>Father Deceased</option>
                            <option value="mother_deceased" {{ request('orphan_status') == 'mother_deceased' ? 'selected' : '' }}>Mother Deceased</option>
                            <option value="both_deceased" {{ request('orphan_status') == 'both_deceased' ? 'selected' : '' }}>Both Deceased</option>
                        </select>
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
                        <a href="{{ route('reports.welfare.children.orphans') }}" class="btn btn-secondary me-2">Reset</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Filter</button>
                        <a href="{{ route('reports.welfare.children.orphans.export', request()->all()) }}" class="btn btn-success ms-2"><i class="fas fa-file-excel me-1"></i> Export Excel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Results ({{ $orphans->total() }})
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Child Name</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Orphan Status</th>
                            <th>Parent/Guardian</th>
                            <th>Parent Phone</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orphans as $child)
                            <tr>
                                <td>{{ $child->full_name }}</td>
                                <td>{{ $child->getAge() }} yrs</td>
                                <td>{{ ucfirst($child->gender) }}</td>
                                <td>
                                    @php
                                        $label = match($child->orphan_status) {
                                            'father_deceased' => 'Father Deceased',
                                            'mother_deceased' => 'Mother Deceased',
                                            'both_deceased' => 'Both Deceased',
                                            default => 'Unknown'
                                        };
                                        $class = match($child->orphan_status) {
                                            'both_deceased' => 'bg-danger',
                                            default => 'bg-warning text-dark'
                                        };
                                    @endphp
                                    <span class="badge {{ $class }}">{{ $label }}</span>
                                </td>
                                <td>{{ $child->getParentName() ?? 'N/A' }}</td>
                                <td>{{ $child->getParentPhone() ?? 'N/A' }}</td>
                                <td>{{ $child->campus->name ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('children.show', $child->id) }}" class="btn btn-sm btn-info text-white"><i class="fas fa-eye"></i> View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No records found matching your criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-end">
                {{ $orphans->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
