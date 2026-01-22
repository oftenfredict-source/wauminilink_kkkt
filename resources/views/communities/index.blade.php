@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-users me-2"></i>Communities - {{ $campus->name }}</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('campuses.communities.create', $campus) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Add New Community
            </a>
            <a href="{{ route('campuses.show', $campus) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Campus
            </a>
        </div>
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
            <i class="fas fa-list me-2"></i>All Communities
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="communitiesTable">
                    <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Members</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                    </thead>
                    <tbody>
                        @forelse($communities as $community)
                            <tr>
                                            <td>
                                                <strong>{{ $community->name }}</strong>
                                                @if($community->description)
                                                    <br><small class="text-muted">{{ Str::limit($community->description, 50) }}</small>
                                                @endif
                                            </td>
                                            <td>
                                    @if($community->ward || $community->district || $community->region)
                                        {{ implode(', ', array_filter([$community->ward, $community->district, $community->region])) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $community->members()->count() }}</span>
                                </td>
                                <td>
                                    @if($community->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('campuses.communities.show', [$campus, $community]) }}" class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('campuses.communities.edit', [$campus, $community]) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('campuses.communities.destroy', [$campus, $community]) }}" method="POST" class="d-inline delete-community-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" data-community-name="{{ $community->name }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                    No communities found. <a href="{{ route('campuses.communities.create', $campus) }}">Create the first community</a>
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
            $('#communitiesTable').DataTable({
                order: [[0, 'asc']],
                pageLength: 25
            });
        }
        
        // Handle community deletion with SweetAlert
        document.querySelectorAll('.delete-community-form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const form = this;
                const button = form.querySelector('button[type="submit"]');
                const communityName = button.getAttribute('data-community-name') || 'this community';
                
                showDeleteConfirm(
                    'Delete Community?',
                    `Are you sure you want to delete "${communityName}"? This will unassign all members from this community. This action cannot be undone!`,
                    function(result) {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    }
                );
            });
        });
        
        // Show success/error messages with SweetAlert
        @if(session('success'))
            showSuccess('Success!', '{{ session('success') }}');
        @endif
        
        @if(session('error'))
            showError('Error!', '{{ session('error') }}');
        @endif
    });
</script>
@endsection

