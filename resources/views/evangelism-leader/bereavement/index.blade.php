@extends('layouts.index')

@section('content')
<style>
    .bereavement-card {
        transition: all 0.3s ease;
        border-left: 4px solid #dc3545;
    }
    .bereavement-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    .status-open {
        background-color: #d4edda;
        color: #155724;
    }
    .status-closed {
        background-color: #f8d7da;
        color: #721c24;
    }
    .countdown-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
    }
    .progress-indicator {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 8px;
    }
    .progress-contributed {
        background-color: #28a745;
    }
    .progress-not-contributed {
        background-color: #6c757d;
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3 gap-2">
        <h2 class="mb-0">🕊️ Bereavement Management</h2>
        <div class="d-flex gap-2">
            <a href="{{ route('evangelism-leader.bereavement.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Create Bereavement Event
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(isset($campus))
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        Showing bereavement events for <strong>{{ $campus->name }}</strong> branch.
    </div>
    @endif

    <!-- Filters -->
    <form method="GET" action="{{ route('evangelism-leader.bereavement.index') }}" class="card mb-3">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search deceased name, family details...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Community</label>
                    <select name="community_id" class="form-select">
                        <option value="">All Communities</option>
                        @foreach($communities as $community)
                        <option value="{{ $community->id }}" {{ request('community_id') == $community->id ? 'selected' : '' }}>
                            {{ $community->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="from" value="{{ request('from') }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="to" value="{{ request('to') }}" class="form-control">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Filter</button>
                    <a href="{{ route('evangelism-leader.bereavement.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times"></i></a>
                </div>
            </div>
        </div>
    </form>

    <!-- Events List -->
    <div class="row">
        @forelse($events as $event)
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card bereavement-card h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">{{ $event->deceased_name }}</h6>
                    <span class="status-badge status-{{ $event->status }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </div>
                <div class="card-body">
                    @if($event->community)
                    <div class="mb-2">
                        <small class="text-muted">Community:</small>
                        <div class="fw-bold text-primary">{{ $event->community->name }}</div>
                    </div>
                    @endif
                    <div class="mb-2">
                        <small class="text-muted">Incident Date:</small>
                        <div class="fw-bold">{{ $event->incident_date->format('M d, Y') }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Contribution Period:</small>
                        <div>
                            {{ $event->contribution_start_date->format('M d') }} - 
                            {{ $event->contribution_end_date->format('M d, Y') }}
                        </div>
                    </div>
                    @if($event->isOpen())
                    <div class="mb-2">
                        <span class="countdown-badge">
                            <i class="fas fa-clock me-1"></i>
                            {{ $event->days_remaining }} days remaining
                        </span>
                    </div>
                    @endif
                    <div class="mb-2">
                        <small class="text-muted">Total Contributions:</small>
                        <div class="fw-bold text-success">TZS {{ number_format($event->contributions->where('has_contributed', true)->sum('contribution_amount'), 2) }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">Contributors:</small>
                        <div>
                            <span class="progress-indicator progress-contributed"></span>
                            {{ $event->contributions->where('has_contributed', true)->count() }} contributed
                        </div>
                    </div>
                    @if($event->related_departments)
                    <div class="mb-2">
                        <small class="text-muted">Related Departments:</small>
                        <div>{{ $event->related_departments }}</div>
                    </div>
                    @endif
                </div>
                <div class="card-footer bg-transparent">
                    <div class="btn-group w-100" role="group">
                        <a href="{{ route('evangelism-leader.bereavement.show', $event->id) }}" class="btn btn-outline-info btn-sm">
                            <i class="fas fa-eye me-1"></i>View
                        </a>
                        @if($event->isOpen())
                        <a href="{{ route('evangelism-leader.bereavement.edit', $event->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-edit me-1"></i>Edit
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-heart-broken fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No bereavement events found for your branch</p>
                    <a href="{{ route('evangelism-leader.bereavement.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Create First Event
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($events->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $events->links() }}
    </div>
    @endif
</div>
@endsection

