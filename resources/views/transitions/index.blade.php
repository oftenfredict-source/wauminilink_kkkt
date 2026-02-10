@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mt-4"><i class="fas fa-exchange-alt me-2"></i>Child to Member Transitions</h1>
        @php
            $dashboardRoute = auth()->user()->isAdmin() ? 'admin.dashboard' : 'dashboard.pastor';
        @endphp
        <a href="{{ route($dashboardRoute) }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
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

    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>Pending Transitions
                <span class="badge bg-light text-dark ms-2">{{ $transitions->count() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($transitions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Child Name</th>
                                <th>Age</th>
                                <th>Gender</th>
                                <th>Date of Birth</th>
                                <th>Parent/Guardian</th>
                                <th>Current Campus</th>
                                <th>Current Community</th>
                                <th>Request Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transitions as $transition)
                                @php
                                    $child = $transition->child;
                                    $age = $child->getAge();
                                @endphp
                                <tr>
                                    <td><strong>{{ $child->full_name }}</strong></td>
                                    <td><span class="badge bg-info">{{ $age }} years</span></td>
                                    <td>
                                        <span class="badge bg-{{ $child->gender === 'male' ? 'primary' : 'danger' }}">
                                            {{ ucfirst($child->gender) }}
                                        </span>
                                    </td>
                                    <td>{{ $child->date_of_birth ? $child->date_of_birth->format('M d, Y') : '—' }}</td>
                                    <td>
                                        @if($child->member)
                                            <a href="{{ route('members.view') }}?search={{ $child->member->member_id }}" class="text-decoration-none">
                                                {{ $child->member->full_name }}
                                            </a>
                                        @else
                                            {{ $child->parent_name ?? '—' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($child->campus)
                                            <span class="badge bg-primary">{{ $child->campus->name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($child->community)
                                            <span class="badge bg-info">{{ $child->community->name }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $transition->created_at->format('M d, Y') }}</td>
                                    <td>
                                        @php
                                            $transitionRoute = auth()->user()->isAdmin() ? 'admin.transitions.show' : 'pastor.transitions.show';
                                        @endphp
                                        <a href="{{ route($transitionRoute, $transition) }}" class="btn btn-sm btn-info" title="Review Transition">
                                            <i class="fas fa-eye me-1"></i>Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="fas fa-check-circle fa-3x mb-3 d-block text-success"></i>
                    <p class="mb-0">No pending transitions at this time.</p>
                    <small>Children who turn 18 and are church members will appear here for review.</small>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

