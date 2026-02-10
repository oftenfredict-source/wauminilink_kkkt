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
            .col-xl-3,
            .col-md-6,
            .col-xl-6 {
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
            #monthlyChart {
                max-height: 300px !important;
            }

            /* Member Selection Cards */
            .col-md-4 {
                margin-bottom: 1rem;
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
            <div class="card-header bg-white border-bottom p-2 px-3 d-flex align-items-center justify-content-between actions-header"
                onclick="toggleActions()">
                <div class="d-flex align-items-center gap-2">
                    <h1 class="mb-0 mt-2" style="font-size: 1.5rem;"><i class="fas fa-user-chart me-2"></i>Member Giving
                        Report</h1>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-chevron-down text-muted d-md-none" id="actionsToggleIcon"></i>
                </div>
            </div>
            <div class="card-body p-3" id="actionsBody">
                <div class="d-flex flex-wrap gap-2">
                    @if($member)
                        <a href="{{ route('reports.member-receipt', $member->id) }}?start_date={{ request('start_date', date('Y-01-01')) }}&end_date={{ request('end_date', date('Y-12-31')) }}"
                            class="btn btn-info btn-sm" target="_blank">
                            <i class="fas fa-receipt me-1"></i>
                            <span class="d-none d-sm-inline">Generate Receipt</span>
                            <span class="d-sm-none">Receipt</span>
                        </a>
                        <a href="{{ route('reports.member-grid-report', $member->id) }}?type=Sadaka ya Umoja&year={{ date('Y') }}"
                            class="btn btn-primary btn-sm">
                            <i class="fas fa-th me-1"></i>
                            <span class="d-none d-sm-inline">View Grid Report</span>
                            <span class="d-sm-none">Grid</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Filters & Search - Collapsible on Mobile -->
        <form method="GET" action="{{ route('reports.member-giving') }}" class="card mb-4 border-0 shadow-sm"
            id="filtersForm">
            <!-- Filter Header -->
            <div class="card-header report-header-neutral py-2 px-3 filter-header" onclick="toggleFilters()">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-filter me-1"></i>
                        <span class="fw-semibold text-white">Report Filters</span>
                        @if(request('member_id') || request('start_date') || request('end_date'))
                            <span class="badge bg-white text-dark rounded-pill ms-2"
                                id="activeFiltersCount">{{ (request('member_id') ? 1 : 0) + (request('start_date') ? 1 : 0) + (request('end_date') ? 1 : 0) }}</span>
                        @endif
                    </div>
                    <i class="fas fa-chevron-down text-white d-md-none" id="filterToggleIcon"></i>
                </div>
            </div>

            <!-- Filter Body - Collapsible on Mobile -->
            <div class="card-body p-3" id="filterBody">
                <div class="row g-2 mb-2">
                    <!-- Member - Full Width on Mobile -->
                    <div class="col-12 col-md-4">
                        <label for="member_id" class="form-label small text-muted mb-1">
                            <i class="fas fa-user me-1 text-primary"></i>Select Member
                        </label>
                        <select class="form-select form-select-sm select2-member" id="member_id" name="member_id">
                            <option value="">All Members</option>
                            @foreach($members as $m)
                                <option value="{{ $m->id }}" {{ request('member_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->full_name }} ({{ $m->member_id }}) - Bag: {{ $m->envelope_number ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Start Date - Full Width on Mobile -->
                    <div class="col-6 col-md-3">
                        <label for="start_date" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-info"></i>Start Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="start_date" name="start_date"
                            value="{{ request('start_date', date('Y-01-01')) }}">
                    </div>

                    <!-- End Date - Full Width on Mobile -->
                    <div class="col-6 col-md-3">
                        <label for="end_date" class="form-label small text-muted mb-1">
                            <i class="fas fa-calendar me-1 text-success"></i>End Date
                        </label>
                        <input type="date" class="form-control form-control-sm" id="end_date" name="end_date"
                            value="{{ request('end_date', date('Y-12-31')) }}">
                    </div>

                    <!-- Action Buttons - Full Width on Mobile -->
                    <div class="col-12 col-md-2 d-flex gap-2 align-items-end">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="fas fa-search me-1"></i>
                            <span class="d-none d-sm-inline">Generate</span>
                            <span class="d-sm-none">Go</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>

        @if($member)
            <!-- Member Summary -->
            <div class="row mb-4 g-3">
                <div class="col-xl-6 col-md-6 col-12">
                    <div class="card bg-success text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Total Offerings</div>
                                    <div class="h4">TZS {{ number_format($totalOfferings, 0) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-gift fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-md-6 col-12">
                    <div class="card bg-warning text-white mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <div class="small text-white-50">Total Giving</div>
                                    <div class="h4">TZS {{ number_format($totalGiving, 0) }}</div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Member Information -->
            <div class="card mb-4">
                <div class="card-header report-header-primary py-2">
                    <h6 class="mb-0 text-white"><i class="fas fa-user me-1"></i>Member Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>{{ $member->full_name }}</h5>
                            <p class="text-muted">Member ID: {{ $member->member_id }}</p>
                            <p class="text-muted">Phone: {{ $member->phone_number }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Ahadi ya Bwana</h6>
                            <p class="text-muted">Ahadi: TZS {{ number_format($totalPledged, 0) }}</p>
                            <p class="text-muted">Imelipwa: TZS {{ number_format($totalPaid, 0) }}</p>
                            <p class="text-muted">Iliyobaki: TZS {{ number_format($totalPledged - $totalPaid, 0) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Breakdown Chart -->
            <div class="card mb-4">
                <div class="card-header report-header-info py-2">
                    <h6 class="mb-0 text-white"><i class="fas fa-chart-bar me-1"></i>Monthly Giving Breakdown</h6>
                </div>
                <div class="card-body">
                    <div style="position: relative; height: 400px; width: 100%;">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Transactions -->
            <div class="row mb-4 g-3">
                <div class="col-xl-6 col-12">
                    <div class="card mb-4">
                        <div class="card-header report-header-success py-2">
                            <h6 class="mb-0 text-white"><i class="fas fa-gift me-1"></i>Offerings</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Amount</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($offerings as $offering)
                                            <tr>
                                                <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                                                <td>TZS {{ number_format($offering->amount, 0) }}</td>
                                                <td>{{ ucfirst($offering->offering_type) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">No offerings found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-6 col-12">
                    <div class="card mb-4">
                        <div class="card-header report-header-warning py-2">
                            <h6 class="mb-0 text-white"><i class="fas fa-handshake me-1"></i>Ahadi ya Bwana</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Imelipwa</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($pledges as $pledge)
                                            <tr>
                                                <td>{{ $pledge->pledge_date ? \Carbon\Carbon::parse($pledge->pledge_date)->format('M d, Y') : '-' }}
                                                </td>
                                                <td>TZS {{ number_format($pledge->amount_paid, 0) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2" class="text-center">No Ahadi found</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Member Selection -->
            <div class="card mb-4">
                <div class="card-header report-header-primary py-2">
                    <h6 class="mb-0 text-white"><i class="fas fa-users me-1"></i>Select a Member to View Report</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($members as $m)
                            <div class="col-md-4 col-12">
                                <div class="card h-100">
                                    <div class="card-body text-center">
                                        <i class="fas fa-user fa-3x text-primary mb-3"></i>
                                        <h5 class="card-title">{{ $m->full_name }}</h5>
                                        <p class="card-text text-muted">Member ID: {{ $m->member_id }}</p>
                                        <a href="{{ route('reports.member-giving', ['member_id' => $m->id]) }}"
                                            class="btn btn-primary">View Report</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>

    @if($member && isset($monthlyData))
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <!-- html2pdf.js library for PDF generation -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize Select2 for member dropdown
                $('.select2-member').select2({
                    placeholder: 'Search for a member...',
                    allowClear: true,
                    width: '100%'
                });
                const ctx = document.getElementById('monthlyChart').getContext('2d');
                const monthlyData = @json($monthlyData);

                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: monthlyData.map(item => item.month),
                        datasets: [{
                            label: 'Ahadi ya Bwana',
                            data: monthlyData.map(item => item.ahadi),
                            backgroundColor: 'rgba(153, 102, 255, 0.8)', // Purple
                            borderColor: 'rgba(153, 102, 255, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Sadaka ya Umoja',
                            data: monthlyData.map(item => item.umoja),
                            backgroundColor: 'rgba(54, 162, 235, 0.8)', // Blue
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1
                        }, {
                            label: 'Sadaka ya Jengo',
                            data: monthlyData.map(item => item.jengo),
                            backgroundColor: 'rgba(75, 192, 192, 0.8)', // Teal
                            borderColor: 'rgba(75, 192, 192, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return 'TZS ' + value.toLocaleString();
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        return context.dataset.label + ': TZS ' + context.parsed.y.toLocaleString();
                                    }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endif

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
        window.addEventListener('resize', function () {
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

        // Initialize Select2 for member dropdown (when no member is selected)
        document.addEventListener('DOMContentLoaded', function () {
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
            @if(request('member_id') || request('start_date') || request('end_date'))
                if (window.innerWidth <= 768 && filterBody && filterIcon) {
                    toggleFilters(); // Expand if filters are active
                    const filterHeader = document.querySelector('.filter-header');
                    if (filterHeader) filterHeader.classList.add('active');
                }
            @endif

            $('.select2-member').select2({
                placeholder: 'Search for a member...',
                allowClear: true,
                width: '100%'
            });
        });

        function exportReport(format) {
            @if(!$member)
                Swal.fire({
                    icon: 'info',
                    title: 'Select Member',
                    text: 'Please select a member to export the report.'
                });
                return;
            @endif

                const memberId = '{{ $member->id ?? "" }}';
            const startDate = '{{ $startDate ? $startDate->format("Y-m-d") : date("Y-01-01") }}';
            const endDate = '{{ $endDate ? $endDate->format("Y-m-d") : date("Y-12-31") }}';
            const baseUrl = '{{ url("/") }}';

            const url = `${baseUrl}/reports/export/${format}?report_type=member-giving&member_id=${memberId}&start_date=${startDate}&end_date=${endDate}`;

            // Force download - server will send Content-Disposition header
            window.location.href = url;
        }

        // Download PDF function for member giving report
        function downloadPDF() {
            @if(!$member)
                Swal.fire({
                    icon: 'info',
                    title: 'Select Member',
                    text: 'Please select a member to generate the PDF report.'
                });
                return;
            @endif

                // Show loading message
                const loadingMsg = document.createElement('div');
            loadingMsg.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 20px 30px; border-radius: 8px; z-index: 10000; font-size: 16px;';
            loadingMsg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
            document.body.appendChild(loadingMsg);

            // Get the main content container
            const element = document.querySelector('.container-fluid');
            const memberName = '{{ $member->full_name ?? "Report" }}'.replace(/[^a-z0-9]/gi, '_');
            const fileName = `Member_Giving_Report_${memberName}_{{ date('Y-m-d') }}.pdf`;

            // Configure PDF options
            const opt = {
                margin: [0.5, 0.5, 0.5, 0.5],
                filename: fileName,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    letterRendering: true,
                    logging: false,
                    onclone: function (clonedDoc) {
                        // Hide action buttons and filters in PDF
                        const actionButtons = clonedDoc.querySelectorAll('.actions-card, .filter-header, .btn, .fa-chevron-down');
                        actionButtons.forEach(btn => {
                            if (btn) btn.style.display = 'none';
                        });

                        // Replace gradients with solid colors for better PDF rendering
                        const headers = clonedDoc.querySelectorAll('.report-header-primary, .report-header-success, .report-header-info, .report-header-warning, .report-header-neutral');
                        headers.forEach(header => {
                            if (header.classList.contains('report-header-primary')) {
                                header.style.background = '#940000';
                            } else if (header.classList.contains('report-header-success')) {
                                header.style.background = '#1cc88a';
                            } else if (header.classList.contains('report-header-info')) {
                                header.style.background = '#36b9cc';
                            } else if (header.classList.contains('report-header-warning')) {
                                header.style.background = '#f6c23e';
                            } else if (header.classList.contains('report-header-neutral')) {
                                header.style.background = '#6c757d';
                            }
                            header.style.backgroundImage = 'none';
                        });
                    }
                },
                jsPDF: {
                    unit: 'cm',
                    format: 'a4',
                    orientation: 'portrait',
                    compress: true
                },
                pagebreak: {
                    mode: ['avoid-all', 'css', 'legacy'],
                    avoid: ['.card', 'table']
                }
            };

            // Generate and download PDF
            html2pdf().set(opt).from(element).save().then(function () {
                // Remove loading message
                document.body.removeChild(loadingMsg);

                // Show success message
                Swal.fire({
                    icon: 'success',
                    title: 'PDF Downloaded!',
                    text: 'The report has been downloaded successfully.',
                    timer: 2000,
                    showConfirmButton: false,
                    toast: true,
                    position: 'top-end'
                });
            }).catch(function (error) {
                // Remove loading message
                if (document.body.contains(loadingMsg)) {
                    document.body.removeChild(loadingMsg);
                }

                // Show error message
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to generate PDF: ' + error.message
                });
                console.error('PDF generation error:', error);
            });
        }
    </script>
@endsection
<style>
    .report-header-primary {
        background: linear-gradient(135deg, #940000 0%, #7a0000 100%) !important;
        color: #fff !important;
    }

    .report-header-success {
        background: linear-gradient(135deg, #1cc88a 0%, #16a36f 100%) !important;
        color: #fff !important;
    }

    .report-header-info {
        background: linear-gradient(135deg, #36b9cc 0%, #2aa2b3 100%) !important;
        color: #fff !important;
    }

    .report-header-warning {
        background: linear-gradient(135deg, #f6c23e 0%, #d6a62f 100%) !important;
        color: #fff !important;
    }

    .report-header-neutral {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        color: #fff !important;
    }

    .report-header-primary h6,
    .report-header-success h6,
    .report-header-info h6,
    .report-header-warning h6,
    .report-header-neutral h6 {
        color: #fff !important;
    }
</style>