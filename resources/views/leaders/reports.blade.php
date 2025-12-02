@extends('layouts.index')

@section('content')
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 767.98px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }
        
        /* Page Header - Stack on mobile */
        .page-header-mobile {
            flex-direction: column !important;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .page-header-mobile h1 {
            font-size: 1.5rem !important;
            margin-bottom: 0 !important;
        }
        
        .page-header-mobile .btn-group-mobile {
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 0.5rem;
        }
        
        .page-header-mobile .btn-group-mobile .btn {
            width: 100%;
            justify-content: center;
        }
        
        /* Summary Cards - Full width on mobile */
        .summary-card .card-body {
            padding: 1rem !important;
        }
        
        .summary-card .h4 {
            font-size: 1.5rem !important;
        }
        
        .summary-card .fa-2x {
            font-size: 1.5rem !important;
        }
        
        /* Tables - Horizontal scroll on mobile */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            font-size: 0.875rem !important;
            min-width: 600px;
        }
        
        .table th,
        .table td {
            padding: 0.5rem !important;
            white-space: nowrap;
        }
        
        .table th:first-child,
        .table td:first-child {
            position: sticky;
            left: 0;
            background-color: inherit;
            z-index: 1;
        }
        
        /* Button groups - Stack on mobile */
        .btn-group-sm {
            flex-direction: column;
            width: 100%;
        }
        
        .btn-group-sm .btn {
            width: 100%;
            margin-bottom: 0.25rem;
        }
        
        /* Cards - Better spacing on mobile */
        .card {
            margin-bottom: 1rem !important;
        }
        
        .card-header {
            padding: 0.75rem !important;
        }
        
        .card-header h5 {
            font-size: 1rem !important;
        }
        
        .card-body {
            padding: 1rem !important;
        }
        
        /* Position cards - Full width on mobile */
        .position-card {
            margin-bottom: 1rem;
        }
        
        /* ID Card generation section */
        .id-card-section .row > div {
            margin-bottom: 1.5rem;
        }
        
        /* Action buttons in tables */
        .table .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }
        
        /* Badge adjustments */
        .badge {
            font-size: 0.7rem;
            padding: 0.25rem 0.5rem;
        }
        
        /* Complete Leadership Directory - Mobile Card View */
        #leadershipTable {
            display: none !important;
        }
        
        .leadership-mobile-view {
            display: block !important;
        }
        
        .leadership-card {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        
        .leadership-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            transform: translateY(-2px);
        }
        
        .leadership-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.75rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .leadership-card-name {
            flex: 1;
        }
        
        .leadership-card-name strong {
            display: block;
            font-size: 1rem;
            color: #212529;
            margin-bottom: 0.25rem;
        }
        
        .leadership-card-name small {
            display: block;
            color: #6c757d;
            font-size: 0.8rem;
        }
        
        .leadership-card-status {
            margin-left: 0.5rem;
        }
        
        .leadership-card-body {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .leadership-card-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        
        .leadership-card-item:last-child {
            border-bottom: none;
        }
        
        .leadership-card-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.85rem;
            min-width: 100px;
        }
        
        .leadership-card-value {
            flex: 1;
            text-align: right;
            color: #212529;
            font-size: 0.9rem;
        }
        
        .leadership-card-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #f0f0f0;
        }
        
        .leadership-card-actions .btn {
            flex: 1;
            font-size: 0.85rem;
        }
        
        .leadership-card-position {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        /* Hide cards that don't match filter */
        .leadership-card.hidden {
            display: none !important;
        }
        
        /* Search and filter styles */
        .leadership-mobile-view .input-group {
            margin-bottom: 0.5rem;
        }
        
        .leadership-mobile-view .form-select-sm {
            font-size: 0.875rem;
        }
    }
    
    @media (min-width: 768px) {
        .leadership-mobile-view {
            display: none !important;
        }
        
        #leadershipTable {
            display: table !important;
        }
    }
    
    @media (max-width: 575.98px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        .page-header-mobile h1 {
            font-size: 1.25rem !important;
        }
        
        .table {
            font-size: 0.75rem !important;
        }
        
        .btn {
            font-size: 0.875rem !important;
            padding: 0.375rem 0.75rem !important;
        }
        
        .btn i {
            margin-right: 0.25rem !important;
        }
    }
</style>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 page-header-mobile">
        <h1 class="mt-4 mb-0">Leadership Reports</h1>
        <div class="d-flex flex-wrap gap-2 btn-group-mobile">
            <a href="{{ route('leaders.export.csv') }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-csv me-2"></i><span class="d-none d-sm-inline">Export </span>CSV
            </a>
            <a href="{{ route('leaders.export.pdf') }}" class="btn btn-danger btn-sm" target="_blank">
                <i class="fas fa-file-pdf me-2"></i><span class="d-none d-sm-inline">Export </span>PDF
            </a>
            <a href="{{ route('leaders.identity-cards.bulk') }}" class="btn btn-info btn-sm" target="_blank">
                <i class="fas fa-id-card me-2"></i><span class="d-none d-sm-inline">All </span>ID Cards
            </a>
            <a href="{{ route('leaders.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i>Back
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-primary text-white mb-4 summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Leadership Positions</div>
                            <div class="h4">{{ $leaders->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-success text-white mb-4 summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Active Positions</div>
                            <div class="h4">{{ $activeLeaders->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-warning text-white mb-4 summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Inactive Positions</div>
                            <div class="h4">{{ $inactiveLeaders->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-pause-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 col-12">
            <div class="card bg-info text-white mb-4 summary-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Recent Appointments</div>
                            <div class="h4">{{ $recentAppointments->count() }}</div>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-calendar-plus fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leadership by Position -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0 text-white">
                <i class="fas fa-chart-pie me-2"></i>Leadership by Position
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($leadersByPosition as $position => $positionLeaders)
                    <div class="col-md-6 col-lg-4 col-12 mb-3 position-card">
                        <div class="card border-start border-4 border-primary">
                            <div class="card-body">
                                <h6 class="card-title">{{ $positionLeaders->first()->position_display }}</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h4 mb-0">{{ $positionLeaders->count() }}</span>
                                    <div class="text-muted small">
                                        {{ $positionLeaders->where('is_active', true)->count() }} active
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Appointments -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0 text-white">
                <i class="fas fa-calendar-plus me-2"></i>Recent Appointments (Last 6 Months)
            </h5>
        </div>
        <div class="card-body">
            @if($recentAppointments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Position</th>
                                <th>Appointment Date</th>
                                <th>Status</th>
                                <th>Appointed By</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAppointments->sortByDesc('appointment_date') as $leader)
                                <tr>
                                    <td>
                                        <strong>{{ $leader->member->full_name }}</strong><br>
                                        <small class="text-muted">{{ $leader->member->member_id }}</small>
                                    </td>
                                    <td>{{ $leader->position_display }}</td>
                                    <td>{{ $leader->appointment_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $leader->is_active ? 'success' : 'secondary' }}">
                                            {{ $leader->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $leader->appointed_by ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No Recent Appointments</h5>
                    <p class="text-muted">No leadership positions have been assigned in the last 6 months.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Expiring Terms -->
    @if($expiringTerms->count() > 0)
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0 text-dark">
                    <i class="fas fa-exclamation-triangle me-2"></i>Expiring Terms (Next 3 Months)
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Member</th>
                                <th>Position</th>
                                <th>End Date</th>
                                <th>Days Remaining</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expiringTerms->sortBy('end_date') as $leader)
                                <tr>
                                    <td>
                                        <strong>{{ $leader->member->full_name }}</strong><br>
                                        <small class="text-muted">{{ $leader->member->member_id }}</small>
                                    </td>
                                    <td>{{ $leader->position_display }}</td>
                                    <td>{{ $leader->end_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $leader->end_date->diffInDays(now()) <= 30 ? 'danger' : 'warning' }}">
                                            {{ $leader->end_date->diffInDays(now()) }} days
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('leaders.edit', $leader) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit me-1"></i><span class="d-none d-sm-inline">Extend </span>Term
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Leadership by Year -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0 text-white">
                <i class="fas fa-chart-bar me-2"></i>Leadership Appointments by Year
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                @foreach($leadersByYear->sortKeys() as $year => $yearLeaders)
                    <div class="col-md-6 col-lg-3 col-12 mb-3">
                        <div class="card border-start border-4 border-info">
                            <div class="card-body">
                                <h6 class="card-title">{{ $year }}</h6>
                                <div class="h4 mb-0">{{ $yearLeaders->count() }}</div>
                                <div class="text-muted small">
                                    {{ $yearLeaders->where('is_active', true)->count() }} still active
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Identity Card Generation -->
    <div class="card mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0 text-white">
                <i class="fas fa-id-card me-2"></i>Identity Card Generation
            </h5>
        </div>
        <div class="card-body id-card-section">
            <div class="row">
                <div class="col-md-6 col-12 mb-3 mb-md-0">
                    <h6>Generate ID Cards by Position</h6>
                    <div class="d-grid gap-2">
                        @foreach($leadersByPosition as $position => $positionLeaders)
                            <a href="{{ route('leaders.identity-cards.position', $position) }}" 
                               class="btn btn-outline-info btn-sm" target="_blank">
                                <i class="fas fa-id-card me-2"></i>{{ $positionLeaders->first()->position_display }} 
                                <span class="badge bg-info">({{ $positionLeaders->count() }})</span>
                            </a>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <h6>Quick Actions</h6>
                    <div class="d-grid gap-2">
                        <a href="{{ route('leaders.identity-cards.bulk') }}" 
                           class="btn btn-info" target="_blank">
                            <i class="fas fa-id-card me-2"></i>Generate All ID Cards
                        </a>
                        <small class="text-muted">
                            Total: {{ $leaders->count() }} leadership positions
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Complete Leadership List -->
    <div class="card mb-4">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0 text-white">
                <i class="fas fa-list me-2"></i>Complete Leadership Directory
            </h5>
        </div>
        <div class="card-body">
            <!-- Desktop Table View -->
            <div class="table-responsive">
                <table class="table table-striped" id="leadershipTable">
                    <thead>
                        <tr>
                            <th>Member</th>
                            <th>Position</th>
                            <th>Appointment Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Appointed By</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaders->sortBy('position') as $leader)
                            <tr>
                                <td>
                                    <strong>{{ $leader->member->full_name }}</strong><br>
                                    <small class="text-muted">{{ $leader->member->member_id }}</small>
                                </td>
                                <td>{{ $leader->position_display }}</td>
                                <td>{{ $leader->appointment_date->format('M d, Y') }}</td>
                                <td>{{ $leader->end_date ? $leader->end_date->format('M d, Y') : 'Indefinite' }}</td>
                                <td>
                                    <span class="badge bg-{{ $leader->is_active ? 'success' : 'secondary' }}">
                                        {{ $leader->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $leader->appointed_by ?? 'N/A' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('leaders.show', $leader) }}" class="btn btn-outline-primary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(auth()->user()->canManageLeadership())
                                            <a href="{{ route('leaders.edit', $leader) }}" class="btn btn-outline-warning" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile Card View -->
            <div class="leadership-mobile-view">
                <!-- Search and Filter for Mobile -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="mobileSearchInput" class="form-control" placeholder="Search by name, position, or member ID...">
                    </div>
                    <div class="mt-2 d-flex gap-2 flex-wrap">
                        <select id="mobileStatusFilter" class="form-select form-select-sm" style="flex: 1; min-width: 120px;">
                            <option value="all">All Status</option>
                            <option value="active">Active Only</option>
                            <option value="inactive">Inactive Only</option>
                        </select>
                        <select id="mobilePositionFilter" class="form-select form-select-sm" style="flex: 1; min-width: 120px;">
                            <option value="all">All Positions</option>
                            @foreach($leadersByPosition as $position => $positionLeaders)
                                <option value="{{ $position }}">{{ $positionLeaders->first()->position_display }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Results Count -->
                <div class="mb-2">
                    <small class="text-muted">
                        Showing <span id="mobileResultsCount">{{ $leaders->count() }}</span> of {{ $leaders->count() }} leaders
                    </small>
                </div>
                
                <!-- Cards Container -->
                <div id="mobileCardsContainer">
                    @foreach($leaders->sortBy('position') as $leader)
                        <div class="leadership-card" 
                             data-name="{{ strtolower($leader->member->full_name) }}"
                             data-member-id="{{ strtolower($leader->member->member_id) }}"
                             data-position="{{ strtolower($leader->position_display) }}"
                             data-status="{{ $leader->is_active ? 'active' : 'inactive' }}"
                             data-position-key="{{ $leader->position }}">
                            <div class="leadership-card-header">
                                <div class="leadership-card-name">
                                    <strong>{{ $leader->member->full_name }}</strong>
                                    <small>{{ $leader->member->member_id }}</small>
                                </div>
                                <div class="leadership-card-status">
                                    <span class="badge bg-{{ $leader->is_active ? 'success' : 'secondary' }}">
                                        {{ $leader->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="leadership-card-position">
                                <i class="fas fa-user-tie me-2"></i>{{ $leader->position_display }}
                            </div>
                            
                            <div class="leadership-card-body">
                                <div class="leadership-card-item">
                                    <span class="leadership-card-label">
                                        <i class="fas fa-calendar-check me-1"></i>Appointment
                                    </span>
                                    <span class="leadership-card-value">{{ $leader->appointment_date->format('M d, Y') }}</span>
                                </div>
                                
                                <div class="leadership-card-item">
                                    <span class="leadership-card-label">
                                        <i class="fas fa-calendar-times me-1"></i>End Date
                                    </span>
                                    <span class="leadership-card-value">{{ $leader->end_date ? $leader->end_date->format('M d, Y') : 'Indefinite' }}</span>
                                </div>
                                
                                <div class="leadership-card-item">
                                    <span class="leadership-card-label">
                                        <i class="fas fa-user-check me-1"></i>Appointed By
                                    </span>
                                    <span class="leadership-card-value">{{ $leader->appointed_by ?? 'N/A' }}</span>
                                </div>
                            </div>
                            
                            <div class="leadership-card-actions">
                                <a href="{{ route('leaders.show', $leader) }}" class="btn btn-outline-primary btn-sm">
                                    <i class="fas fa-eye me-1"></i>View
                                </a>
                                @if(auth()->user()->canManageLeadership())
                                    <a href="{{ route('leaders.edit', $leader) }}" class="btn btn-outline-warning btn-sm">
                                        <i class="fas fa-edit me-1"></i>Edit
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- No Results Message -->
                <div id="mobileNoResults" class="text-center py-5" style="display: none;">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No leaders found</h5>
                    <p class="text-muted">Try adjusting your search or filters</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize DataTable only on desktop (table is hidden on mobile)
    if (typeof $.fn.DataTable !== 'undefined' && window.innerWidth >= 768) {
        $('#leadershipTable').DataTable({
            "pageLength": 25,
            "order": [[ 2, "desc" ]],
            "columnDefs": [
                { "orderable": false, "targets": 6 }
            ],
            "responsive": true,
            "scrollX": true,
            "scrollCollapse": true,
            "language": {
                "lengthMenu": "Show _MENU_ entries",
                "search": "Search:",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries"
            }
        });
    }
    
    // Mobile Search and Filter Functionality
    function filterMobileCards() {
        const searchTerm = document.getElementById('mobileSearchInput')?.value.toLowerCase() || '';
        const statusFilter = document.getElementById('mobileStatusFilter')?.value || 'all';
        const positionFilter = document.getElementById('mobilePositionFilter')?.value || 'all';
        
        const cards = document.querySelectorAll('.leadership-card');
        let visibleCount = 0;
        
        cards.forEach(function(card) {
            const name = card.getAttribute('data-name') || '';
            const memberId = card.getAttribute('data-member-id') || '';
            const position = card.getAttribute('data-position') || '';
            const status = card.getAttribute('data-status') || '';
            const positionKey = card.getAttribute('data-position-key') || '';
            
            // Search filter
            const matchesSearch = !searchTerm || 
                name.includes(searchTerm) || 
                memberId.includes(searchTerm) || 
                position.includes(searchTerm);
            
            // Status filter
            const matchesStatus = statusFilter === 'all' || status === statusFilter;
            
            // Position filter
            const matchesPosition = positionFilter === 'all' || positionKey === positionFilter;
            
            // Show/hide card
            if (matchesSearch && matchesStatus && matchesPosition) {
                card.classList.remove('hidden');
                visibleCount++;
            } else {
                card.classList.add('hidden');
            }
        });
        
        // Update results count
        const resultsCountEl = document.getElementById('mobileResultsCount');
        if (resultsCountEl) {
            resultsCountEl.textContent = visibleCount;
        }
        
        // Show/hide no results message
        const noResultsEl = document.getElementById('mobileNoResults');
        if (noResultsEl) {
            if (visibleCount === 0) {
                noResultsEl.style.display = 'block';
            } else {
                noResultsEl.style.display = 'none';
            }
        }
    }
    
    // Attach event listeners for mobile filters
    const mobileSearchInput = document.getElementById('mobileSearchInput');
    const mobileStatusFilter = document.getElementById('mobileStatusFilter');
    const mobilePositionFilter = document.getElementById('mobilePositionFilter');
    
    if (mobileSearchInput) {
        mobileSearchInput.addEventListener('input', filterMobileCards);
    }
    
    if (mobileStatusFilter) {
        mobileStatusFilter.addEventListener('change', filterMobileCards);
    }
    
    if (mobilePositionFilter) {
        mobilePositionFilter.addEventListener('change', filterMobileCards);
    }
    
    // Reinitialize DataTable on window resize if needed
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth >= 768 && typeof $.fn.DataTable !== 'undefined') {
                if ($.fn.DataTable.isDataTable('#leadershipTable')) {
                    $('#leadershipTable').DataTable().destroy();
                }
                $('#leadershipTable').DataTable({
                    "pageLength": 25,
                    "order": [[ 2, "desc" ]],
                    "columnDefs": [
                        { "orderable": false, "targets": 6 }
                    ],
                    "responsive": true,
                    "scrollX": true,
                    "scrollCollapse": true,
                    "language": {
                        "lengthMenu": "Show _MENU_ entries",
                        "search": "Search:",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries"
                    }
                });
            } else if (window.innerWidth < 768 && $.fn.DataTable.isDataTable('#leadershipTable')) {
                $('#leadershipTable').DataTable().destroy();
            }
        }, 250);
    });
});
</script>
@endsection
