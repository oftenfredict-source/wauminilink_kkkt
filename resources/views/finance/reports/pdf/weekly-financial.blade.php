<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Financial Report - {{ $startDate->format('M d') }} to {{ $endDate->format('M d, Y') }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <style>
        /* Base Styles */
        * {
            box-sizing: border-box;
        }
        
        body { 
            background: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.5;
            color: #212529;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Report Header */
        .report-header { 
            border-bottom: 4px solid #0d6efd; 
            margin-bottom: 30px; 
            padding: 25px 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            page-break-inside: avoid;
        }
        
        .report-title {
            color: #0d6efd;
            font-weight: 700;
            font-size: 2.2rem;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .report-subtitle {
            color: #6c757d;
            font-size: 1.1rem;
            font-weight: 500;
            margin-top: 5px;
        }
        
        .report-meta {
            color: #6c757d;
            font-size: 0.95rem;
            margin-top: 10px;
        }
        
        /* Summary Cards */
        .summary-card {
            border: 2px solid;
            border-radius: 10px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            background: #fff;
        }
        
        .summary-value {
            font-size: 2rem;
            font-weight: 700;
            margin: 15px 0 10px 0;
            line-height: 1.2;
        }
        
        .summary-label {
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 15px;
        }
        
        .small-muted { 
            color: #6c757d; 
            font-size: 0.85rem; 
            font-weight: 500;
            margin-top: 5px;
        }
        
        /* Cards */
        .card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            background: #fff;
            overflow: hidden;
        }
        
        .card-header {
            background: #0d6efd;
            color: #fff;
            font-weight: 600;
            padding: 15px 20px;
            font-size: 1rem;
            border-bottom: 2px solid #0a58ca;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* Tables */
        .table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }
        
        .table-sm td, .table-sm th {
            padding: 10px 12px;
            border: 1px solid #dee2e6;
            font-size: 0.9rem;
        }
        
        .table-sm thead th {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
            border-bottom: 2px solid #0a58ca;
            text-align: left;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table-sm tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table-sm tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .table-light {
            background-color: #f8f9fa;
            font-weight: 600;
            font-size: 1rem;
        }
        
        .text-end {
            text-align: right;
        }
        
        .text-success {
            color: #198754;
        }
        
        .text-danger {
            color: #dc3545;
        }
        
        /* Footer */
        .footer-info {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #dee2e6;
            color: #6c757d;
            font-size: 0.85rem;
            text-align: center;
            page-break-inside: avoid;
        }
        
        /* Print Styles */
        @media print {
            @page {
                size: A4;
                margin: 1.5cm;
            }
            
            body { 
                margin: 0;
                padding: 0;
                background: white;
            }
            
            .container {
                max-width: 100%;
                padding: 0;
            }
            
            .no-print { 
                display: none !important; 
            }
            
            .card {
                page-break-inside: avoid;
                box-shadow: none;
                border: 2px solid #000;
            }
            
            .table {
                page-break-inside: auto;
            }
            
            .table thead {
                display: table-header-group;
            }
            
            .table tbody {
                display: table-row-group;
            }
            
            .table tr {
                page-break-inside: avoid;
            }
            
            .table-sm td, .table-sm th {
                border: 1px solid #000;
            }
            
            .text-success, .text-danger {
                color: #000 !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Report Header -->
    <div class="report-header">
        <h1 class="report-title">Weekly Financial Report</h1>
        <div class="report-subtitle">
            <i class="fas fa-calendar-week me-1"></i>
            Week: {{ $startDate->format('M d') }} - {{ $endDate->format('M d, Y') }}
        </div>
        <div class="report-meta">
            <i class="fas fa-church me-1"></i>WauminiLink Financial Management System
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-success summary-card">
                <div class="card-body text-center" style="padding: 25px 20px;">
                    <div class="summary-label text-success">Total Income</div>
                    <h2 class="summary-value text-success">TZS {{ number_format($totalIncome, 0) }}</h2>
                    <div class="small-muted">All revenue sources combined</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger summary-card">
                <div class="card-body text-center" style="padding: 25px 20px;">
                    <div class="summary-label text-danger">Total Expenses</div>
                    <h2 class="summary-value text-danger">TZS {{ number_format($totalExpenses, 0) }}</h2>
                    <div class="small-muted">All paid expenses</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-primary summary-card">
                <div class="card-body text-center" style="padding: 25px 20px;">
                    <div class="summary-label text-primary">Net Income</div>
                    <h2 class="summary-value {{ $netIncome >= 0 ? 'text-success' : 'text-danger' }}">
                        TZS {{ number_format($netIncome, 0) }}
                    </h2>
                    <div class="small-muted">Income minus expenses</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Income and Expenses Breakdown -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><strong><i class="fas fa-arrow-up me-2"></i>Income Sources</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Source</th>
                                <th class="text-end" style="width: 30%;">Amount (TZS)</th>
                                <th class="text-end" style="width: 20%;">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><i class="fas fa-coins me-2 text-success"></i>Tithes</td>
                                <td class="text-end"><strong>TZS {{ number_format($totalTithes, 0) }}</strong></td>
                                <td class="text-end">{{ $tithesCount }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-gift me-2 text-primary"></i>Offerings</td>
                                <td class="text-end"><strong>TZS {{ number_format($totalOfferings, 0) }}</strong></td>
                                <td class="text-end">{{ $offeringsCount }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-heart me-2 text-info"></i>Donations</td>
                                <td class="text-end"><strong>TZS {{ number_format($totalDonations, 0) }}</strong></td>
                                <td class="text-end">{{ $donationsCount }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-handshake me-2 text-warning"></i>Pledge Payments</td>
                                <td class="text-end"><strong>TZS {{ number_format($totalPledgePayments, 0) }}</strong></td>
                                <td class="text-end">{{ $pledgePaymentsCount }}</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>TOTAL INCOME</strong></td>
                                <td class="text-end"><strong style="font-size: 1.1rem;">TZS {{ number_format($totalIncome, 0) }}</strong></td>
                                <td class="text-end"><strong>{{ $tithesCount + $offeringsCount + $donationsCount + $pledgePaymentsCount }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><strong><i class="fas fa-arrow-down me-2"></i>Expenses by Category</strong></div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Category</th>
                                <th class="text-end" style="width: 30%;">Amount (TZS)</th>
                                <th class="text-end" style="width: 20%;">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expensesByCategory as $category => $data)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $category)) }}</td>
                                <td class="text-end"><strong>TZS {{ number_format($data['total'], 0) }}</strong></td>
                                <td class="text-end">{{ $data['count'] }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted" style="padding: 20px;">
                                    <i class="fas fa-inbox me-2"></i>No expenses in this week
                                </td>
                            </tr>
                            @endforelse
                            <tr class="table-light">
                                <td><strong>TOTAL EXPENSES</strong></td>
                                <td class="text-end"><strong style="font-size: 1.1rem;">TZS {{ number_format($totalExpenses, 0) }}</strong></td>
                                <td class="text-end"><strong>{{ $expensesCount }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Offerings by Type -->
    @if($offeringsByType->count() > 0)
    <div class="card mb-4">
        <div class="card-header"><strong><i class="fas fa-gift me-2"></i>Offerings by Type</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 40%;">Offering Type</th>
                        <th class="text-end" style="width: 30%;">Amount (TZS)</th>
                        <th class="text-end" style="width: 15%;">Count</th>
                        <th class="text-end" style="width: 15%;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offeringsByType as $type => $data)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($data['total'], 0) }}</strong></td>
                        <td class="text-end">{{ $data['count'] }}</td>
                        <td class="text-end">{{ $totalOfferings > 0 ? number_format(($data['total'] / $totalOfferings) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Donations by Type -->
    @if($donationsByType->count() > 0)
    <div class="card mb-4">
        <div class="card-header"><strong><i class="fas fa-heart me-2"></i>Donations by Type</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 40%;">Donation Type</th>
                        <th class="text-end" style="width: 30%;">Amount (TZS)</th>
                        <th class="text-end" style="width: 15%;">Count</th>
                        <th class="text-end" style="width: 15%;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donationsByType as $type => $data)
                    <tr>
                        <td>{{ ucfirst(str_replace('_', ' ', $type)) }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($data['total'], 0) }}</strong></td>
                        <td class="text-end">{{ $data['count'] }}</td>
                        <td class="text-end">{{ $totalDonations > 0 ? number_format(($data['total'] / $totalDonations) * 100, 1) : 0 }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Daily Breakdown -->
    @if(count($dailyData) > 0)
    <div class="card mb-4">
        <div class="card-header"><strong><i class="fas fa-calendar-day me-2"></i>Daily Breakdown</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 30%;">Date</th>
                        <th class="text-end" style="width: 25%;">Income (TZS)</th>
                        <th class="text-end" style="width: 25%;">Expenses (TZS)</th>
                        <th class="text-end" style="width: 20%;">Net (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($dailyData as $day)
                    <tr>
                        <td>{{ $day['date'] }} ({{ $day['day'] }})</td>
                        <td class="text-end"><strong>TZS {{ number_format($day['income'], 0) }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($day['expenses'], 0) }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($day['net'], 0) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Top Contributors -->
    @if($topContributors->count() > 0)
    <div class="card mb-4">
        <div class="card-header"><strong><i class="fas fa-users me-2"></i>Top Contributors (Top 20)</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 10%;">#</th>
                        <th style="width: 20%;">Member ID</th>
                        <th style="width: 40%;">Name</th>
                        <th class="text-end" style="width: 30%;">Total Giving (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topContributors as $index => $contributor)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $contributor->member_id }}</td>
                        <td>{{ $contributor->full_name }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($contributor->total_giving, 0) }}</strong></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Footer -->
    <div class="footer-info">
        <div style="margin-bottom: 10px;">
            <i class="fas fa-calendar-alt me-1"></i>
            <strong>Generated:</strong> {{ now()->format('F d, Y \a\t h:i A') }}
        </div>
        <div>
            <i class="fas fa-church me-1"></i>
            <strong>WauminiLink</strong> Financial Management System | 
            <i class="fas fa-file-alt me-1"></i>
            Weekly Financial Report
        </div>
        <div style="margin-top: 10px; font-size: 0.75rem; color: #adb5bd;">
            This is a computer-generated report. No signature required.
        </div>
    </div>
</div>

<script src="{{ asset('assets/js/fontawesome.min.js') }}" crossorigin="anonymous"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
<script>
    function triggerPrint() {
        window.print();
    }
    
    // Auto-print on load (optional - uncomment if needed)
    // window.onload = function() { setTimeout(triggerPrint, 500); }
</script>
</body>
</html>

