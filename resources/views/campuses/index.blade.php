@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-building me-2"></i>Campus Management</h1>
        <a href="{{ route('campuses.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add New Campus
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <i class="fas fa-list me-2"></i>All Campuses
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="campusesTable">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Type</th>
                            <th>Parent Campus</th>
                            <th>Members</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($campuses as $campus)
                            <tr>
                                <td>
                                    <strong>{{ $campus->name }}</strong>
                                    @if($campus->description)
                                        <br><small class="text-muted">{{ Str::limit($campus->description, 50) }}</small>
                                    @endif
                                </td>
                                <td><span class="badge bg-secondary">{{ $campus->code }}</span></td>
                                <td>
                                    @if($campus->is_main_campus)
                                        <span class="badge bg-primary">Main Campus</span>
                                    @else
                                        <span class="badge bg-info">Sub Campus</span>
                                    @endif
                                </td>
                                <td>
                                    @if($campus->parent)
                                        {{ $campus->parent->name }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $adultCount = $campus->members()->count();
                                        $childCount = $campus->memberChildren()->count();
                                        $totalCount = $adultCount + $childCount;
                                    @endphp
                                    @if($totalCount > 0)
                                        <a href="{{ route('campuses.show', $campus) }}" class="text-decoration-none" title="Click to view members">
                                            <span class="badge bg-success">{{ $totalCount }}</span>
                                        </a>
                                    @else
                                        <span class="badge bg-secondary">0</span>
                                    @endif
                                    @if($childCount > 0)
                                        <br><small class="text-muted">({{ $adultCount }} adults, {{ $childCount }} children)</small>
                                    @elseif($adultCount > 0)
                                        <br><small class="text-muted">({{ $adultCount }} adult{{ $adultCount > 1 ? 's' : '' }})</small>
                                    @endif
                                    @if($campus->is_main_campus && $campus->subCampuses->count() > 0)
                                        @php
                                            $subCampusAdultCount = $campus->subCampuses->sum(function($sub) {
                                                return $sub->members()->count();
                                            });
                                            $subCampusChildCount = $campus->subCampuses->sum(function($sub) {
                                                return $sub->memberChildren()->count();
                                            });
                                            $subCampusTotalCount = $subCampusAdultCount + $subCampusChildCount;
                                        @endphp
                                        @if($subCampusTotalCount > 0)
                                            <br><small class="text-muted">+ {{ $subCampusTotalCount }} from sub campuses</small>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if($campus->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('campuses.show', $campus) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('campuses.edit', $campus) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('campuses.destroy', $campus) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this campus?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-building fa-2x mb-2 d-block"></i>
                                    No campuses found. <a href="{{ route('campuses.create') }}">Create the first campus</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize DataTable if available
        if ($.fn.DataTable) {
            $('#campusesTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25
            });
        }
    });
</script>
@endsection

