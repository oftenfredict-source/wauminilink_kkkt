@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                <div class="card-body py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2" style="width:48px; height:48px; background:rgba(0,123,255,.1);">
                                <i class="fas fa-users-cog text-primary"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold text-dark">Church Leadership</h5>
                                <small class="text-muted">View all church leaders and their contact information</small>
                            </div>
                        </div>
                        @if($memberPositions->count() > 0)
                            <div class="d-flex align-items-center gap-3 header-widgets">
                                <div class="widget d-flex align-items-center gap-2 px-3 py-2 rounded-3 bg-primary text-white">
                                    <i class="fas fa-star text-white"></i>
                                    <span class="fw-bold text-white">You are a Leader</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Your Leadership Positions -->
    @if($memberPositions->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm border-primary" style="border-width: 2px !important;">
                <div class="card-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0"><i class="fas fa-user-tie me-2"></i>Your Leadership Positions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($memberPositions as $position)
                            <div class="col-md-6 mb-3">
                                <div class="card border-primary h-100" style="border-width: 2px !important;">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                                <i class="fas fa-star fa-lg"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h5 class="mb-1 text-primary">{{ $position->position_display }}</h5>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    Appointed: {{ $position->appointment_date->format('d M Y') }}
                                                    @if($position->end_date)
                                                        <br><i class="fas fa-calendar-times me-1"></i>
                                                        Until: {{ $position->end_date->format('d M Y') }}
                                                    @else
                                                        <br><i class="fas fa-infinity me-1"></i>
                                                        Ongoing
                                                    @endif
                                                </small>
                                                @if($position->description)
                                                    <p class="mt-2 mb-0 small text-muted">{{ $position->description }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- All Leaders -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-users me-2"></i>All Church Leaders</h5>
                        <span class="badge bg-warning text-dark fw-bold" style="font-size: 0.875rem; padding: 0.5rem 0.75rem;">{{ $leaders->count() }} Active Leaders</span>
                    </div>
                </div>
                <div class="card-body">
                    @if($leaders->count() > 0)
                        <!-- Search and Filter -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <input type="text" id="searchLeaders" class="form-control" placeholder="Search by name, position, or phone number...">
                            </div>
                            <div class="col-md-6">
                                <select id="filterPosition" class="form-select">
                                    <option value="">All Positions</option>
                                    @foreach($leadersByPosition->keys() as $position)
                                        <option value="{{ $position }}">{{ $leaders->firstWhere('position', $position)->position_display ?? $position }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Leaders Table -->
                        <div class="table-responsive">
                            <table class="table table-hover" id="leadersTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Position</th>
                                        <th>Name</th>
                                        <th>Phone Number</th>
                                        <th>Email</th>
                                        <th>Appointment Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leaders as $leader)
                                        <tr class="{{ $memberPositions->contains('id', $leader->id) ? 'table-primary' : '' }}" 
                                            data-position="{{ $leader->position }}"
                                            data-name="{{ strtolower($leader->member->full_name ?? '') }}"
                                            data-phone="{{ $leader->member->phone_number ?? '' }}">
                                            <td>
                                                <strong>{{ $leader->position_display }}</strong>
                                                @if($memberPositions->contains('id', $leader->id))
                                                    <span class="badge bg-success ms-2">You</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-user-circle text-primary me-2"></i>
                                                    <strong>{{ $leader->member->full_name ?? 'N/A' }}</strong>
                                                </div>
                                            </td>
                                            <td>
                                                @if($leader->member->phone_number)
                                                    <a href="tel:{{ $leader->member->phone_number }}" class="text-decoration-none">
                                                        <i class="fas fa-phone text-success me-1"></i>
                                                        {{ $leader->member->phone_number }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($leader->member->email)
                                                    <a href="mailto:{{ $leader->member->email }}" class="text-decoration-none">
                                                        <i class="fas fa-envelope text-info me-1"></i>
                                                        {{ $leader->member->email }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <i class="fas fa-calendar text-muted me-1"></i>
                                                @if(is_object($leader->appointment_date) && method_exists($leader->appointment_date, 'format'))
                                                    {{ $leader->appointment_date->format('d M Y') }}
                                                @else
                                                    {{ \Carbon\Carbon::parse($leader->appointment_date)->format('d M Y') }}
                                                @endif
                                                @if($leader->end_date)
                                                    <br><small class="text-muted">Until: {{ is_object($leader->end_date) && method_exists($leader->end_date, 'format') ? $leader->end_date->format('d M Y') : \Carbon\Carbon::parse($leader->end_date)->format('d M Y') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                @if(method_exists($leader, 'isCurrentlyActive') ? $leader->isCurrentlyActive() : ($leader->is_active ?? false))
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check-circle me-1"></i>Active
                                                    </span>
                                                @else
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-times-circle me-1"></i>Inactive
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- No Results Message -->
                        <div id="noResults" class="alert alert-info d-none">
                            <i class="fas fa-info-circle me-2"></i>No leaders found matching your search criteria.
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>No active leaders found.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Leaders by Position (Grouped View) -->
    @if($leadersByPosition->count() > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-sitemap me-2"></i>Leaders by Position</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($leadersByPosition as $position => $positionLeaders)
                            <div class="col-md-6 col-lg-4 mb-4">
                                <div class="card h-100 border-0 shadow-sm">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">
                                            <i class="fas fa-user-tie text-primary me-2"></i>
                                            {{ $positionLeaders->first()->position_display }}
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        @foreach($positionLeaders as $leader)
                                            <div class="mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">
                                                            {{ $leader->member->full_name ?? 'N/A' }}
                                                            @if($memberPositions->contains('id', $leader->id))
                                                                <span class="badge bg-success ms-1">You</span>
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted d-block">
                                                            <i class="fas fa-id-card me-1"></i>
                                                            {{ $leader->member->member_id ?? 'N/A' }}
                                                        </small>
                                                        @if($leader->member->phone_number)
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-phone me-1"></i>
                                                                <a href="tel:{{ $leader->member->phone_number }}" class="text-decoration-none">
                                                                    {{ $leader->member->phone_number }}
                                                                </a>
                                                            </small>
                                                        @endif
                                                        @if($leader->member->email)
                                                            <small class="text-muted d-block">
                                                                <i class="fas fa-envelope me-1"></i>
                                                                <a href="mailto:{{ $leader->member->email }}" class="text-decoration-none">
                                                                    {{ Str::limit($leader->member->email, 25) }}
                                                                </a>
                                                            </small>
                                                        @endif
                                                        <small class="text-muted d-block mt-1">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            Since {{ $leader->appointment_date->format('M Y') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
    .dashboard-header .widget{
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        transition: transform .2s ease, background .2s ease;
    }
    .dashboard-header .widget:hover{
        transform: translateY(-2px);
        background: rgba(255,255,255,.14);
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
    }
    
    #leadersTable tbody tr:hover {
        background-color: rgba(0, 123, 255, 0.05);
        cursor: pointer;
    }
    
    .table-primary {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchLeaders');
        const filterSelect = document.getElementById('filterPosition');
        const table = document.getElementById('leadersTable');
        const noResults = document.getElementById('noResults');
        
        function filterLeaders() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedPosition = filterSelect.value;
            let visibleRows = 0;
            
            if (table) {
                const rows = table.querySelectorAll('tbody tr');
                
                rows.forEach(row => {
                    const name = row.getAttribute('data-name') || '';
                    const phone = row.getAttribute('data-phone') || '';
                    const position = row.getAttribute('data-position') || '';
                    const rowText = row.textContent.toLowerCase();
                    
                    const matchesSearch = !searchTerm || 
                        name.includes(searchTerm) || 
                        phone.includes(searchTerm) ||
                        rowText.includes(searchTerm);
                    
                    const matchesPosition = !selectedPosition || position === selectedPosition;
                    
                    if (matchesSearch && matchesPosition) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                
                // Show/hide no results message
                if (noResults) {
                    if (visibleRows === 0) {
                        noResults.classList.remove('d-none');
                        if (table) table.style.display = 'none';
                    } else {
                        noResults.classList.add('d-none');
                        if (table) table.style.display = '';
                    }
                }
            }
        }
        
        if (searchInput) {
            searchInput.addEventListener('input', filterLeaders);
        }
        
        if (filterSelect) {
            filterSelect.addEventListener('change', filterLeaders);
        }
    });
</script>
@endsection

