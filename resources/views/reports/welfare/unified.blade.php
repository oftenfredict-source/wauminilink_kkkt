@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4">Unified Social Welfare Report</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('reports.welfare.index') }}">Social Welfare</a></li>
        <li class="breadcrumb-item active">Unified Report</li>
    </ol>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-filter me-1"></i>
            Filter Unified Report
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.welfare.unified') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Name or Member ID">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select class="form-select" name="type">
                            <option value="">All Types</option>
                            <option value="Adult" {{ request('type') == 'Adult' ? 'selected' : '' }}>Adult Members</option>
                            <option value="Child" {{ request('type') == 'Child' ? 'selected' : '' }}>Children</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category">
                            <option value="">All Categories</option>
                            <option value="orphan" {{ request('category') == 'orphan' ? 'selected' : '' }}>Orphans</option>
                            <option value="disabled" {{ request('category') == 'disabled' ? 'selected' : '' }}>Disabled</option>
                            <option value="vulnerable" {{ request('category') == 'vulnerable' ? 'selected' : '' }}>Vulnerable</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Branch</label>
                        <select class="form-select" name="campus_id">
                            <option value="">All Branches</option>
                            @foreach($campuses as $campus)
                                <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                    {{ $campus->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <div class="w-100 text-end">
                            <a href="{{ route('reports.welfare.unified') }}" class="btn btn-secondary me-2">Reset</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Filter</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-users me-1"></i>
                Total Registered ({{ $results->total() }})
            </div>
            <button class="btn btn-sm btn-success" disabled title="Export not yet implemented"><i class="fas fa-file-excel me-1"></i> Export Excel</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>ID/Identifier</th>
                            <th>Type</th>
                            <th>Age</th>
                            <th>Gender</th>
                            <th>Status/Details</th>
                            <th>Guardian/Parent</th>
                            <th>Contact</th>
                            <th>Branch</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $item)
                            <tr>
                                <td>{{ $item->full_name }}</td>
                                <td><code>{{ $item->identifier ?? 'N/A' }}</code></td>
                                <td>
                                    <span class="badge {{ $item->type == 'Adult' ? 'bg-primary' : 'bg-info text-dark' }}">
                                        {{ $item->type }}
                                    </span>
                                </td>
                                <td>
                                    @php
                                        $age = $item->date_of_birth ? \Carbon\Carbon::parse($item->date_of_birth)->age : 'N/A';
                                    @endphp
                                    {{ $age }} yrs
                                </td>
                                <td>{{ ucfirst($item->gender) }}</td>
                                <td>
                                    @if($item->orphan_status != 'not_orphan')
                                        <span class="badge bg-danger mb-1 d-block">Orphan ({{ str_replace('_', ' ', $item->orphan_status) }})</span>
                                    @endif
                                    @if($item->disability_status)
                                        <span class="badge bg-warning text-dark mb-1 d-block">Disability ({{ $item->disability_type ?? 'Yes' }})</span>
                                    @endif
                                    @if($item->vulnerable_status)
                                        <span class="badge bg-secondary mb-1 d-block text-white">Vulnerable ({{ $item->vulnerable_type ?? 'Yes' }})</span>
                                    @endif
                                </td>
                                <td>{{ $item->guardian ?? 'N/A' }}</td>
                                <td>{{ $item->contact_phone ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $campus = $campuses->find($item->campus_id);
                                    @endphp
                                    {{ $campus->name ?? 'Unknown' }}
                                </td>
                                <td>
                                    @if($item->type == 'Adult')
                                        <a href="{{ route('members.show', $item->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @else
                                        <a href="{{ route('children.show', $item->id) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $results->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
