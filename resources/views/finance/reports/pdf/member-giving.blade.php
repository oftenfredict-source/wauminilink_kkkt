<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Giving Report{{ $member ? ' - ' . $member->full_name : '' }} ({{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }})</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
    <style>
        /* Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body { 
            background: #fff; 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #212529;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        /* Report Header - Enhanced */
        .report-header { 
            border-bottom: 5px solid #0d6efd; 
            margin-bottom: 35px; 
            padding: 30px;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            border-radius: 10px;
            page-break-inside: avoid;
            color: #fff;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .report-title {
            color: #fff;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 15px;
            letter-spacing: -0.5px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .report-subtitle {
            color: #fff;
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 10px;
            margin-bottom: 10px;
            opacity: 0.95;
        }
        
        .report-meta {
            color: #fff;
            font-size: 1rem;
            margin-top: 15px;
            opacity: 0.9;
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .report-meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Member Info Card */
        .member-info-card {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 2px solid #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        
        .member-info-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .member-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .member-info-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .member-info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .member-info-value {
            color: #212529;
            font-weight: 500;
        }
        
        /* Summary Cards - Enhanced */
        .summary-card {
            border: 3px solid;
            border-radius: 12px;
            margin-bottom: 20px;
            page-break-inside: avoid;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .summary-value {
            font-size: 2.2rem;
            font-weight: 700;
            margin: 20px 0 12px 0;
            line-height: 1.2;
        }
        
        .summary-label {
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            margin-top: 18px;
        }
        
        .small-muted { 
            color: #6c757d; 
            font-size: 0.85rem; 
            font-weight: 500;
            margin-top: 8px;
        }
        
        /* Cards */
        .card {
            border: 2px solid #dee2e6;
            border-radius: 10px;
            margin-bottom: 25px;
            page-break-inside: avoid;
            background: #fff;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        
        .card-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            font-weight: 700;
            padding: 18px 25px;
            font-size: 1.1rem;
            border-bottom: 3px solid #0a58ca;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .card-body {
            padding: 25px;
        }
        
        /* Tables - Enhanced */
        .table {
            width: 100%;
            margin-bottom: 0;
            border-collapse: collapse;
        }
        
        .table-sm td, .table-sm th {
            padding: 12px 15px;
            border: 1px solid #dee2e6;
            font-size: 0.95rem;
        }
        
        .table-sm thead th {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: #fff;
            font-weight: 700;
            border-bottom: 3px solid #0a58ca;
            text-align: left;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            padding: 15px;
        }
        
        .table-sm tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .table-sm tbody tr:hover {
            background-color: #e9ecef;
        }
        
        .table-light {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            font-weight: 700;
            font-size: 1.05rem;
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
        
        .text-warning {
            color: #ffc107;
        }
        
        .text-primary {
            color: #0d6efd;
        }
        
        .text-info {
            color: #0dcaf0;
        }
        
        .badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .bg-success {
            background-color: #198754 !important;
            color: #fff;
        }
        
        /* Footer - Enhanced */
        .footer-info {
            margin-top: 50px;
            padding-top: 25px;
            border-top: 3px solid #dee2e6;
            color: #6c757d;
            font-size: 0.9rem;
            text-align: center;
            page-break-inside: avoid;
            background: #f8f9fa;
            padding: 25px;
            border-radius: 8px;
        }
        
        .footer-info div {
            margin-bottom: 8px;
        }
        
        /* Section Spacing */
        .section-divider {
            margin: 30px 0;
            border-top: 2px dashed #dee2e6;
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
            
            .text-success, .text-danger, .text-warning, .text-primary, .text-info {
                color: #000 !important;
            }
            
            .report-header {
                background: #0d6efd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .card-header {
                background: #0d6efd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .table-sm thead th {
                background: #0d6efd !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Report Header - Enhanced -->
    <div class="report-header">
        <h1 class="report-title">
            <i class="fas fa-user-chart me-2"></i>Member Giving Report
        </h1>
        @if($member)
        <div class="report-subtitle">
            <i class="fas fa-user me-2"></i>{{ $member->full_name }}
        </div>
        <div class="report-meta">
            <div class="report-meta-item">
                <i class="fas fa-id-card me-1"></i>
                <strong>Member ID:</strong> {{ $member->member_id }}
            </div>
            <div class="report-meta-item">
                <i class="fas fa-calendar-alt me-1"></i>
                <strong>Period:</strong> {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>
            <div class="report-meta-item">
                <i class="fas fa-church me-1"></i>
                <strong>WauminiLink</strong> Financial Management System
            </div>
        </div>
        @else
        <div class="report-subtitle">
            <i class="fas fa-user me-2"></i>Select a member to view report
        </div>
        <div class="report-meta">
            <div class="report-meta-item">
                <i class="fas fa-calendar-alt me-1"></i>
                <strong>Period:</strong> {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
            </div>
        </div>
        @endif
    </div>

    @if($member)
    <!-- Member Information Card -->
    <div class="member-info-card">
        <div class="member-info-title">
            <i class="fas fa-info-circle me-2"></i>Member Information
        </div>
        <div class="member-info-grid">
            <div class="member-info-item">
                <span class="member-info-label"><i class="fas fa-user me-1"></i>Full Name:</span>
                <span class="member-info-value">{{ $member->full_name }}</span>
            </div>
            <div class="member-info-item">
                <span class="member-info-label"><i class="fas fa-id-card me-1"></i>Member ID:</span>
                <span class="member-info-value">{{ $member->member_id }}</span>
            </div>
            @if($member->phone_number)
            <div class="member-info-item">
                <span class="member-info-label"><i class="fas fa-phone me-1"></i>Phone:</span>
                <span class="member-info-value">{{ $member->phone_number }}</span>
            </div>
            @endif
            @if($member->email)
            <div class="member-info-item">
                <span class="member-info-label"><i class="fas fa-envelope me-1"></i>Email:</span>
                <span class="member-info-value">{{ $member->email }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Summary Cards - Enhanced -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-success summary-card">
                <div class="card-body text-center" style="padding: 30px 20px;">
                    <div class="summary-label text-success">
                        <i class="fas fa-coins me-2"></i>Total Tithes
                    </div>
                    <h2 class="summary-value text-success">TZS {{ number_format($totalTithes, 0) }}</h2>
                    <div class="small-muted">{{ $tithes->count() }} {{ $tithes->count() == 1 ? 'transaction' : 'transactions' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary summary-card">
                <div class="card-body text-center" style="padding: 30px 20px;">
                    <div class="summary-label text-primary">
                        <i class="fas fa-gift me-2"></i>Total Offerings
                    </div>
                    <h2 class="summary-value text-primary">TZS {{ number_format($totalOfferings, 0) }}</h2>
                    <div class="small-muted">{{ $offerings->count() }} {{ $offerings->count() == 1 ? 'transaction' : 'transactions' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info summary-card">
                <div class="card-body text-center" style="padding: 30px 20px;">
                    <div class="summary-label text-info">
                        <i class="fas fa-heart me-2"></i>Total Donations
                    </div>
                    <h2 class="summary-value text-info">TZS {{ number_format($totalDonations, 0) }}</h2>
                    <div class="small-muted">{{ $donations->count() }} {{ $donations->count() == 1 ? 'transaction' : 'transactions' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning summary-card">
                <div class="card-body text-center" style="padding: 30px 20px;">
                    <div class="summary-label text-warning">
                        <i class="fas fa-chart-line me-2"></i>Total Giving
                    </div>
                    <h2 class="summary-value text-warning">TZS {{ number_format($totalGiving, 0) }}</h2>
                    <div class="small-muted">All contributions</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown -->
    @if(count($monthlyData) > 0)
    <div class="card mb-4">
        <div class="card-header">
            <strong><i class="fas fa-calendar-alt me-2"></i>Monthly Breakdown</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 20%;">Month</th>
                        <th class="text-end" style="width: 20%;">Tithes (TZS)</th>
                        <th class="text-end" style="width: 20%;">Offerings (TZS)</th>
                        <th class="text-end" style="width: 20%;">Donations (TZS)</th>
                        <th class="text-end" style="width: 20%;">Total (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($monthlyData as $month)
                    <tr>
                        <td><strong>{{ $month['month'] }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($month['tithes'], 0) }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($month['offerings'], 0) }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($month['donations'], 0) }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($month['total'], 0) }}</strong></td>
                    </tr>
                    @endforeach
                    <tr class="table-light">
                        <td><strong>TOTAL</strong></td>
                        <td class="text-end"><strong style="font-size: 1.15rem;">TZS {{ number_format($totalTithes, 0) }}</strong></td>
                        <td class="text-end"><strong style="font-size: 1.15rem;">TZS {{ number_format($totalOfferings, 0) }}</strong></td>
                        <td class="text-end"><strong style="font-size: 1.15rem;">TZS {{ number_format($totalDonations, 0) }}</strong></td>
                        <td class="text-end"><strong style="font-size: 1.15rem;">TZS {{ number_format($totalGiving, 0) }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Income Sources and Pledges Breakdown -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong><i class="fas fa-arrow-up me-2"></i>Income Sources</strong>
                </div>
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
                                <td class="text-end">{{ $tithes->count() }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-gift me-2 text-primary"></i>Offerings</td>
                                <td class="text-end"><strong>TZS {{ number_format($totalOfferings, 0) }}</strong></td>
                                <td class="text-end">{{ $offerings->count() }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-heart me-2 text-info"></i>Donations</td>
                                <td class="text-end"><strong>TZS {{ number_format($totalDonations, 0) }}</strong></td>
                                <td class="text-end">{{ $donations->count() }}</td>
                            </tr>
                            <tr class="table-light">
                                <td><strong>TOTAL INCOME</strong></td>
                                <td class="text-end"><strong style="font-size: 1.15rem;">TZS {{ number_format($totalGiving, 0) }}</strong></td>
                                <td class="text-end"><strong>{{ $tithes->count() + $offerings->count() + $donations->count() }}</strong></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <strong><i class="fas fa-handshake me-2"></i>Pledges Summary</strong>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50%;">Item</th>
                                <th class="text-end" style="width: 30%;">Amount (TZS)</th>
                                <th class="text-end" style="width: 20%;">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($pledges->count() > 0)
                            <tr>
                                <td><i class="fas fa-file-contract me-2 text-warning"></i>Total Pledged</td>
                                <td class="text-end"><strong>TZS {{ number_format($totalPledged, 0) }}</strong></td>
                                <td class="text-end">{{ $pledges->count() }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-check-circle me-2 text-success"></i>Total Paid</td>
                                <td class="text-end"><strong>TZS {{ number_format($totalPaid, 0) }}</strong></td>
                                <td class="text-end">{{ $pledges->count() }}</td>
                            </tr>
                            <tr>
                                <td><i class="fas fa-exclamation-circle me-2 {{ ($totalPledged - $totalPaid) > 0 ? 'text-warning' : 'text-success' }}"></i>Outstanding</td>
                                <td class="text-end"><strong class="{{ ($totalPledged - $totalPaid) > 0 ? 'text-warning' : 'text-success' }}">TZS {{ number_format($totalPledged - $totalPaid, 0) }}</strong></td>
                                <td class="text-end">-</td>
                            </tr>
                            @else
                            <tr>
                                <td colspan="3" class="text-center text-muted" style="padding: 25px;">
                                    <i class="fas fa-inbox me-2"></i>No pledges in this period
                                </td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tithes Details -->
    @if($tithes->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <strong><i class="fas fa-coins me-2"></i>Tithes Details</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 30%;">Date</th>
                        <th class="text-end" style="width: 30%;">Amount (TZS)</th>
                        <th style="width: 25%;">Payment Method</th>
                        <th style="width: 15%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tithes as $tithe)
                    <tr>
                        <td>{{ $tithe->tithe_date->format('M d, Y') }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($tithe->amount, 0) }}</strong></td>
                        <td>{{ ucfirst($tithe->payment_method ?? 'N/A') }}</td>
                        <td><span class="badge bg-success">Approved</span></td>
                    </tr>
                    @endforeach
                    <tr class="table-light">
                        <td><strong>TOTAL</strong></td>
                        <td class="text-end"><strong style="font-size: 1.15rem;">TZS {{ number_format($totalTithes, 0) }}</strong></td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Offerings Details -->
    @if($offerings->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <strong><i class="fas fa-gift me-2"></i>Offerings Details</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 25%;">Date</th>
                        <th style="width: 25%;">Type</th>
                        <th class="text-end" style="width: 25%;">Amount (TZS)</th>
                        <th style="width: 25%;">Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offerings as $offering)
                    <tr>
                        <td>{{ $offering->offering_date->format('M d, Y') }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($offering->amount, 0) }}</strong></td>
                        <td>{{ ucfirst($offering->payment_method ?? 'N/A') }}</td>
                    </tr>
                    @endforeach
                    <tr class="table-light">
                        <td><strong>TOTAL</strong></td>
                        <td></td>
                        <td class="text-end"><strong style="font-size: 1.15rem;">TZS {{ number_format($totalOfferings, 0) }}</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Donations Details -->
    @if($donations->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <strong><i class="fas fa-heart me-2"></i>Donations Details</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 25%;">Date</th>
                        <th style="width: 25%;">Type</th>
                        <th class="text-end" style="width: 25%;">Amount (TZS)</th>
                        <th style="width: 25%;">Payment Method</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donations as $donation)
                    <tr>
                        <td>{{ $donation->donation_date->format('M d, Y') }}</td>
                        <td>{{ ucfirst($donation->donation_type) }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($donation->amount, 0) }}</strong></td>
                        <td>{{ ucfirst($donation->payment_method ?? 'N/A') }}</td>
                    </tr>
                    @endforeach
                    <tr class="table-light">
                        <td><strong>TOTAL</strong></td>
                        <td></td>
                        <td class="text-end"><strong style="font-size: 1.15rem;">TZS {{ number_format($totalDonations, 0) }}</strong></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Pledges Details -->
    @if($pledges->count() > 0)
    <div class="card mb-4">
        <div class="card-header">
            <strong><i class="fas fa-handshake me-2"></i>Pledges Details</strong>
        </div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 20%;">Date</th>
                        <th style="width: 20%;">Type</th>
                        <th class="text-end" style="width: 20%;">Pledged (TZS)</th>
                        <th class="text-end" style="width: 20%;">Paid (TZS)</th>
                        <th class="text-end" style="width: 20%;">Remaining (TZS)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pledges as $pledge)
                    @php
                        $remaining = $pledge->pledge_amount - $pledge->amount_paid;
                    @endphp
                    <tr>
                        <td>{{ $pledge->pledge_date ? \Carbon\Carbon::parse($pledge->pledge_date)->format('M d, Y') : '-' }}</td>
                        <td>{{ ucfirst($pledge->pledge_type) }}</td>
                        <td class="text-end">TZS {{ number_format($pledge->pledge_amount, 0) }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($pledge->amount_paid, 0) }}</strong></td>
                        <td class="text-end">{{ $remaining > 0 ? 'TZS ' . number_format($remaining, 0) : '-' }}</td>
                    </tr>
                    @endforeach
                    <tr class="table-light">
                        <td><strong>TOTAL</strong></td>
                        <td></td>
                        <td class="text-end"><strong>TZS {{ number_format($totalPledged, 0) }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($totalPaid, 0) }}</strong></td>
                        <td class="text-end"><strong>{{ ($totalPledged - $totalPaid) > 0 ? 'TZS ' . number_format($totalPledged - $totalPaid, 0) : '-' }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Footer - Enhanced -->
    <div class="footer-info">
        <div style="margin-bottom: 12px;">
            <i class="fas fa-calendar-alt me-1"></i>
            <strong>Generated:</strong> {{ now()->format('F d, Y \a\t h:i A') }}
        </div>
        <div style="margin-bottom: 12px;">
            <i class="fas fa-church me-1"></i>
            <strong>WauminiLink</strong> Financial Management System | 
            <i class="fas fa-file-alt me-1"></i>
            Member Giving Report
        </div>
        <div style="margin-top: 15px; font-size: 0.8rem; color: #adb5bd;">
            <i class="fas fa-info-circle me-1"></i>
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
