@extends('layouts.index')

@section('content')
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        
        /* Actions Card */
        .actions-card {
            transition: all 0.3s ease;
        }
        .actions-card .card-header {
            user-select: none;
            transition: background-color 0.2s ease;
        }
        .actions-card .card-header:hover {
            background-color: #f8f9fa !important;
        }
        #actionsBody {
            transition: all 0.3s ease;
            display: none;
        }
        .actions-header {
            cursor: pointer !important;
        }
        #actionsToggleIcon {
            display: block !important;
        }
        
        /* Filter Section */
        #filtersForm .card-header {
            transition: all 0.2s ease;
        }
        .filter-header:hover {
            opacity: 0.9;
        }
        #filterBody {
            transition: all 0.3s ease;
            display: none;
            background: #fafbfc;
        }
        .filter-header {
            cursor: pointer !important;
        }
        #filterToggleIcon {
            display: block !important;
            transition: transform 0.3s ease;
        }
        .filter-header.active #filterToggleIcon {
            transform: rotate(180deg);
        }
        #filtersForm .card-body {
            padding: 0.75rem 0.5rem !important;
        }
        #filtersForm .form-label {
            font-size: 0.7rem !important;
            margin-bottom: 0.2rem !important;
            font-weight: 600 !important;
        }
        #filtersForm .form-control,
        #filtersForm .form-select {
            font-size: 0.8125rem !important;
            padding: 0.4rem 0.5rem !important;
            border-radius: 6px !important;
        }
        #filtersForm .btn-sm {
            padding: 0.4rem 0.75rem !important;
            font-size: 0.8125rem !important;
            border-radius: 6px !important;
            font-weight: 600 !important;
        }
        
        /* Cards - Stack on Mobile */
        .col-xl-4, .col-md-6, .col-xl-6 {
            margin-bottom: 1rem;
        }
        
        /* Summary Cards - Smaller on Mobile */
        .card-body .h4 {
            font-size: 1.25rem !important;
        }
        
        /* Table Responsive */
        .table {
            font-size: 0.75rem;
        }
        .table th,
        .table td {
            padding: 0.5rem 0.25rem;
        }
        
        /* Header adjustments */
        h1 {
            font-size: 1.25rem !important;
        }
        
        /* Chart Container */
        #offeringChart, #donationChart {
            max-height: 300px !important;
        }
    }
    
    /* Desktop: Always show actions and filters */
    @media (min-width: 769px) {
        .actions-header {
            cursor: default !important;
            pointer-events: none !important;
        }
        .actions-header .fa-chevron-down {
            display: none !important;
        }
        #actionsBody {
            display: block !important;
        }
        
        .filter-header {
            cursor: default !important;
            pointer-events: none !important;
        }
        .filter-header .fa-chevron-down {
            display: none !important;
        }
        #filterBody {
            display: block !important;
        }
    }
</style>
<div class="container-fluid px-4">
    <!-- Page Title and Quick Actions - Compact Collapsible -->
    <div class="card border-0 shadow-sm mb-3 actions-card">
        <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header" onclick="toggleActions()">
            <div class="d-flex align-items-center gap-2">
                <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-building me-2"></i>Department Giving Report</h1>
            </div>
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
            </div>
        </div>
        <div class="card-body p-3" id="actionsBody">
            <div class="d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-success btn-sm" onclick="exportReport('pdf')">
                    <i class="fas fa-file-pdf me-1"></i>
                    <span class="d-none d-sm-inline">Export PDF</span>
                    <span class="d-sm-none">PDF</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="exportReport('excel')">
                    <i class="fas fa-file-excel me-1"></i>
                    <span class="d-none d-sm-inline">Export Excel</span>
                    <span class="d-sm-none">Excel</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Filters & Search - Collapsible on Mobile -->
    <form method="GET" action="{{ route('reports.department-giving') }}" class="card mb-4 border-0 shadow-sm" id="filtersForm">
        <!-- Filter Header -->
        <div class="card-header report-header-neutral py-2 px-3 filter-header" onclick="toggleFilters()">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-filter me-1"></i>
                    <span class="fw-semibold text-white">Report Filters</span>
                    @if(request('start_date') || request('end_date'))
                        <span class="badge bg-white text-dark rounded-pill ms-2" id="activeFiltersCount">{{ (request('start_date') ? 1 : 0) + (request('end_date') ? 1 : 0) }}</span>
                    @endif
                </div>
                <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
            </div>
        </div>
        
        <!-- Filter Body - Collapsible on Mobile -->
        <div class="card-body p-3" id="filterBody">
            <div class="row g-2 mb-2">
                <!-- Start Date - Full Width on Mobile -->
                <div class="col-6 col-md-4">
                    <label for="start_date" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar me-1 text-primary"></i>Start Date
                    </label>
                    <input type="date" class="form-control form-control-sm" id="start_date" name="start_date" value="{{ request('start_date', date('Y-01-01')) }}">
                </div>
                
                <!-- End Date - Full Width on Mobile -->
                <div class="col-6 col-md-4">
                    <label for="end_date" class="form-label small text-muted mb-1">
                        <i class="fas fa-calendar me-1 text-info"></i>End Date
                    </label>
                    <input type="date" class="form-control form-control-sm" id="end_date" name="end_date" value="{{ request('end_date', date('Y-12-31')) }}">
                </div>
                
                <!-- Action Buttons - Full Width on Mobile -->
                <div class="col-12 col-md-4 d-flex gap-2 align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-search me-1"></i>
                        <span class="d-none d-sm-inline">Generate</span>
                        <span class="d-sm-none">Go</span>
                    </button>
                </div>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="row mb-4 g-3">
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Offerings</div>
                            <div class="h4">TZS {{ number_format($offeringTypes->sum('total_amount'), 0) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-gift fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Donations</div>
                            <div class="h4">TZS {{ number_format($donationTypes->sum('total_amount'), 0) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-heart fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-md-6 col-12">
            <div class="card bg-info text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small text-white-50">Total Pledged</div>
                            <div class="h4">TZS {{ number_format($pledgeTypes->sum('total_pledged'), 0) }}</div>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-handshake fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined by Purpose (Pledges + Offerings + Donations) -->
    @if(isset($combinedByPurpose) && !empty($combinedByPurpose))
    <div class="card mb-4">
        <div class="card-header report-header-warning py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-layer-group me-1"></i>Combined Giving by Purpose</h6>
            <small class="text-white" style="text-shadow: 0 1px 2px rgba(0,0,0,0.3); opacity: 0.95;">This section combines Pledges, Offerings, and Donations that share the same purpose</small>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="combinedTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Purpose</th>
                            <th>Pledges (Paid)</th>
                            <th>Offerings</th>
                            <th>Donations</th>
                            <th>Combined Total</th>
                            <th>Total Pledged</th>
                            <th>Outstanding</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grandTotal = 0;
                            $grandPledged = 0;
                        @endphp
                        @foreach($combinedByPurpose as $purpose => $data)
                        @php
                            $grandTotal += $data['combined_total'];
                            $grandPledged += $data['combined_pledged'];
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $data['display_name'] }}</strong>
                                <br>
                                <small class="text-muted">
                                    {{ $data['pledges']['count'] }} pledges, 
                                    {{ $data['offerings']['count'] }} offerings, 
                                    {{ $data['donations']['count'] }} donations
                                </small>
                            </td>
                            <td class="text-end">
                                TZS {{ number_format($data['pledges']['total_paid'], 0) }}
                                <br>
                                <small class="text-muted">of {{ number_format($data['pledges']['total_pledged'], 0) }} pledged</small>
                            </td>
                            <td class="text-end">TZS {{ number_format($data['offerings']['total'], 0) }}</td>
                            <td class="text-end">TZS {{ number_format($data['donations']['total'], 0) }}</td>
                            <td class="text-end">
                                <strong>TZS {{ number_format($data['combined_total'], 0) }}</strong>
                            </td>
                            <td class="text-end">TZS {{ number_format($data['combined_pledged'], 0) }}</td>
                            <td class="text-end">
                                <span class="badge bg-{{ $data['pledges']['outstanding'] > 0 ? 'warning' : 'success' }}">
                                    TZS {{ number_format($data['pledges']['outstanding'], 0) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <th>Grand Total</th>
                            <th class="text-end">TZS {{ number_format(collect($combinedByPurpose)->sum('pledges.total_paid'), 0) }}</th>
                            <th class="text-end">TZS {{ number_format(collect($combinedByPurpose)->sum('offerings.total'), 0) }}</th>
                            <th class="text-end">TZS {{ number_format(collect($combinedByPurpose)->sum('donations.total'), 0) }}</th>
                            <th class="text-end">TZS {{ number_format($grandTotal, 0) }}</th>
                            <th class="text-end">TZS {{ number_format($grandPledged, 0) }}</th>
                            <th class="text-end">TZS {{ number_format(collect($combinedByPurpose)->sum('pledges.outstanding'), 0) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Offering Types -->
    <div class="card mb-4">
        <div class="card-header report-header-primary py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-gift me-1"></i>Offering Types Breakdown</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="offeringTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Offering Type</th>
                            <th>Total Amount</th>
                            <th>Transaction Count</th>
                            <th>Average per Transaction</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalOfferings = $offeringTypes->sum('total_amount');
                        @endphp
                        @forelse($offeringTypes as $offering)
                        <tr>
                            <td>
                                <span class="badge bg-info">
                                    @if($offering->offering_type == 'general')
                                        General Offering
                                    @elseif(in_array($offering->offering_type, ['special', 'thanksgiving', 'building_fund']))
                                        {{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}
                                    @else
                                        {{ ucfirst($offering->offering_type) }}
                                    @endif
                                </span>
                            </td>
                            <td class="text-end">TZS {{ number_format($offering->total_amount, 0) }}</td>
                            <td class="text-center">{{ $offering->transaction_count }}</td>
                            <td class="text-end">TZS {{ number_format($offering->total_amount / max($offering->transaction_count, 1), 0) }}</td>
                            <td class="text-end">{{ $totalOfferings > 0 ? number_format(($offering->total_amount / $totalOfferings) * 100, 1) : 0 }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No offering data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Donation Types -->
    <div class="card mb-4">
        <div class="card-header report-header-success py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-heart me-1"></i>Donation Types Breakdown</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="donationTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Donation Type</th>
                            <th>Total Amount</th>
                            <th>Transaction Count</th>
                            <th>Average per Transaction</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalDonations = $donationTypes->sum('total_amount');
                        @endphp
                        @forelse($donationTypes as $donation)
                        <tr>
                            <td>
                                <span class="badge bg-success">{{ ucfirst($donation->donation_type) }}</span>
                            </td>
                            <td class="text-end">TZS {{ number_format($donation->total_amount, 0) }}</td>
                            <td class="text-center">{{ $donation->transaction_count }}</td>
                            <td class="text-end">TZS {{ number_format($donation->total_amount / max($donation->transaction_count, 1), 0) }}</td>
                            <td class="text-end">{{ $totalDonations > 0 ? number_format(($donation->total_amount / $totalDonations) * 100, 1) : 0 }}%</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No donation data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pledge Types -->
    <div class="card mb-4">
        <div class="card-header report-header-info py-2">
            <h6 class="mb-0 text-white"><i class="fas fa-handshake me-1"></i>Pledge Types Breakdown</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pledgeTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Pledge Type</th>
                            <th>Total Pledged</th>
                            <th>Total Paid</th>
                            <th>Remaining</th>
                            <th>Pledge Count</th>
                            <th>Completion Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pledgeTypes as $pledge)
                        <tr>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($pledge->pledge_type) }}</span>
                            </td>
                            <td class="text-end">TZS {{ number_format($pledge->total_pledged, 0) }}</td>
                            <td class="text-end">TZS {{ number_format($pledge->total_paid, 0) }}</td>
                            <td class="text-end">TZS {{ number_format($pledge->total_pledged - $pledge->total_paid, 0) }}</td>
                            <td class="text-center">{{ $pledge->pledge_count }}</td>
                            <td class="text-end">
                                @php
                                    $completionRate = $pledge->total_pledged > 0 ? ($pledge->total_paid / $pledge->total_pledged) * 100 : 0;
                                @endphp
                                <span class="badge {{ $completionRate >= 100 ? 'bg-success' : ($completionRate >= 75 ? 'bg-warning' : 'bg-danger') }}">
                                    {{ number_format($completionRate, 1) }}%
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No pledge data found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row mb-4 g-3">
        <div class="col-xl-6 col-12">
            <div class="card mb-4">
                <div class="card-header report-header-primary py-2">
                    <h6 class="mb-0 text-white"><i class="fas fa-chart-pie me-1"></i>Offering Types Distribution</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 400px; width: 100%;">
                        <canvas id="offeringChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-12">
            <div class="card mb-4">
                <div class="card-header report-header-success py-2">
                    <h6 class="mb-0 text-white"><i class="fas fa-chart-pie me-1"></i>Donation Types Distribution</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 400px; width: 100%;">
                        <canvas id="donationChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Offering Types Chart
    const offeringCtx = document.getElementById('offeringChart').getContext('2d');
    const offeringData = @json($offeringTypes);
    
    new Chart(offeringCtx, {
        type: 'doughnut',
        data: {
            labels: offeringData.map(item => {
                if (item.offering_type === 'general') {
                    return 'General Offering';
                } else if (['special', 'thanksgiving', 'building_fund'].includes(item.offering_type)) {
                    return item.offering_type.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
                } else {
                    return item.offering_type.charAt(0).toUpperCase() + item.offering_type.slice(1);
                }
            }),
            datasets: [{
                data: offeringData.map(item => item.total_amount),
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': TZS ' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });

    // Donation Types Chart
    const donationCtx = document.getElementById('donationChart').getContext('2d');
    const donationData = @json($donationTypes);
    
    new Chart(donationCtx, {
        type: 'doughnut',
        data: {
            labels: donationData.map(item => item.donation_type),
            datasets: [{
                data: donationData.map(item => item.total_amount),
                backgroundColor: [
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(54, 162, 235, 0.8)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': TZS ' + context.parsed.toLocaleString();
                        }
                    }
                }
            }
        }
    });
});
</script>

<script>
// Toggle Actions Function
function toggleActions() {
    // Only toggle on mobile devices
    if (window.innerWidth > 768) {
        return; // Don't toggle on desktop
    }
    
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    
    if (!actionsBody || !actionsIcon) return;
    
    // Check computed style to see if it's visible
    const computedStyle = window.getComputedStyle(actionsBody);
    const isVisible = computedStyle.display !== 'none';
    
    if (isVisible) {
        actionsBody.style.display = 'none';
        actionsIcon.classList.remove('fa-chevron-up');
        actionsIcon.classList.add('fa-chevron-down');
    } else {
        actionsBody.style.display = 'block';
        actionsIcon.classList.remove('fa-chevron-down');
        actionsIcon.classList.add('fa-chevron-up');
    }
}

// Toggle Filters Function
function toggleFilters() {
    // Only toggle on mobile devices
    if (window.innerWidth > 768) {
        return; // Don't toggle on desktop
    }
    
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    const filterHeader = document.querySelector('.filter-header');
    
    if (!filterBody || !filterIcon) return;
    
    // Check computed style to see if it's visible
    const computedStyle = window.getComputedStyle(filterBody);
    const isVisible = computedStyle.display !== 'none';
    
    if (isVisible) {
        filterBody.style.display = 'none';
        filterIcon.classList.remove('fa-chevron-up');
        filterIcon.classList.add('fa-chevron-down');
        if (filterHeader) filterHeader.classList.remove('active');
    } else {
        filterBody.style.display = 'block';
        filterIcon.classList.remove('fa-chevron-down');
        filterIcon.classList.add('fa-chevron-up');
        if (filterHeader) filterHeader.classList.add('active');
    }
}

// Handle window resize
window.addEventListener('resize', function() {
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (window.innerWidth > 768) {
        // Always show on desktop
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'block';
            actionsIcon.style.display = 'none';
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'block';
            filterIcon.style.display = 'none';
        }
    } else {
        // On mobile, show chevrons
        if (actionsIcon) actionsIcon.style.display = 'block';
        if (filterIcon) filterIcon.style.display = 'block';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize actions and filters
    const actionsBody = document.getElementById('actionsBody');
    const actionsIcon = document.getElementById('actionsToggleIcon');
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (window.innerWidth <= 768) {
        // Mobile: start collapsed
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'none';
            actionsIcon.classList.remove('fa-chevron-up');
            actionsIcon.classList.add('fa-chevron-down');
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'none';
            filterIcon.classList.remove('fa-chevron-up');
            filterIcon.classList.add('fa-chevron-down');
        }
    } else {
        // Desktop: always show
        if (actionsBody && actionsIcon) {
            actionsBody.style.display = 'block';
            actionsIcon.style.display = 'none';
        }
        if (filterBody && filterIcon) {
            filterBody.style.display = 'block';
            filterIcon.style.display = 'none';
        }
    }
    
    // Show filters if any are active
    @if(request('start_date') || request('end_date'))
        if (window.innerWidth <= 768 && filterBody && filterIcon) {
            toggleFilters(); // Expand if filters are active
            const filterHeader = document.querySelector('.filter-header');
            if (filterHeader) filterHeader.classList.add('active');
        }
    @endif
});

function exportReport(format) {
    const startDate = '{{ $startDate }}';
    const endDate = '{{ $endDate }}';
    // Get the current page URL and extract the base path
    const currentPath = window.location.pathname;
    const basePath = currentPath.substring(0, currentPath.indexOf('/reports/'));
    const baseUrl = window.location.origin + basePath;
    
    const url = `${baseUrl}/reports/export/${format}?report_type=department-giving&start_date=${startDate}&end_date=${endDate}`;
    
    // Force download - server will send Content-Disposition header
    window.location.href = url;
}
</script>
@endsection
<style>
.report-header-primary{ background: linear-gradient(135deg, #4e73df 0%, #6f42c1 100%) !important; color:#fff !important; }
.report-header-success{ background: linear-gradient(135deg, #1cc88a 0%, #16a36f 100%) !important; color:#fff !important; }
.report-header-info{ background: linear-gradient(135deg, #36b9cc 0%, #2aa2b3 100%) !important; color:#fff !important; }
.report-header-warning{ background: linear-gradient(135deg, #f6c23e 0%, #d6a62f 100%) !important; color:#fff !important; }
.report-header-neutral{ background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important; color:#fff !important; }
.report-header-primary h6, .report-header-success h6, .report-header-info h6, .report-header-warning h6, .report-header-neutral h6{ color:#fff !important; }
.report-header-warning small{ color:#fff !important; text-shadow: 0 1px 3px rgba(0,0,0,0.4) !important; }
</style>











