<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Department Giving Report - {{ $startDate->format('M d, Y') }} to {{ $endDate->format('M d, Y') }}</title>
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
        <h1 class="report-title">Department Giving Report</h1>
        <div class="report-subtitle">
            <i class="fas fa-calendar-alt me-1"></i>
            Period: {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
        </div>
        <div class="report-meta">
            <i class="fas fa-church me-1"></i>WauminiLink Financial Management System
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-primary summary-card">
                <div class="card-body text-center" style="padding: 25px 20px;">
                    <div class="summary-label text-primary">Total Offerings</div>
                    <h2 class="summary-value text-primary">TZS {{ number_format($offeringTypes->sum('total_amount'), 0) }}</h2>
                    <div class="small-muted">{{ $offeringTypes->sum('transaction_count') }} transactions</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-success summary-card">
                <div class="card-body text-center" style="padding: 25px 20px;">
                    <div class="summary-label text-success">Total Donations</div>
                    <h2 class="summary-value text-success">TZS {{ number_format($donationTypes->sum('total_amount'), 0) }}</h2>
                    <div class="small-muted">{{ $donationTypes->sum('transaction_count') }} transactions</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info summary-card">
                <div class="card-body text-center" style="padding: 25px 20px;">
                    <div class="summary-label text-info">Total Pledged</div>
                    <h2 class="summary-value text-info">TZS {{ number_format($pledgeTypes->sum('total_pledged'), 0) }}</h2>
                    <div class="small-muted">{{ $pledgeTypes->sum('pledge_count') }} pledges</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Combined by Purpose -->
    @if(isset($combinedByPurpose) && !empty($combinedByPurpose))
    <div class="card mb-4">
        <div class="card-header"><strong><i class="fas fa-layer-group me-2"></i>Combined Giving by Purpose</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 25%;">Purpose</th>
                        <th class="text-end" style="width: 15%;">Pledges (Paid)</th>
                        <th class="text-end" style="width: 15%;">Offerings</th>
                        <th class="text-end" style="width: 15%;">Donations</th>
                        <th class="text-end" style="width: 15%;">Combined Total</th>
                        <th class="text-end" style="width: 15%;">Outstanding</th>
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
                            <strong>TZS {{ number_format($data['pledges']['total_paid'], 0) }}</strong>
                            <br>
                            <small class="text-muted">of {{ number_format($data['pledges']['total_pledged'], 0) }}</small>
                        </td>
                        <td class="text-end"><strong>TZS {{ number_format($data['offerings']['total'], 0) }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($data['donations']['total'], 0) }}</strong></td>
                        <td class="text-end"><strong style="font-size: 1.1rem;">TZS {{ number_format($data['combined_total'], 0) }}</strong></td>
                        <td class="text-end">
                            <span class="badge bg-{{ $data['pledges']['outstanding'] > 0 ? 'warning' : 'success' }}">
                                TZS {{ number_format($data['pledges']['outstanding'], 0) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-light">
                        <th>Grand Total</th>
                        <th class="text-end">TZS {{ number_format(collect($combinedByPurpose)->sum('pledges.total_paid'), 0) }}</th>
                        <th class="text-end">TZS {{ number_format(collect($combinedByPurpose)->sum('offerings.total'), 0) }}</th>
                        <th class="text-end">TZS {{ number_format(collect($combinedByPurpose)->sum('donations.total'), 0) }}</th>
                        <th class="text-end">TZS {{ number_format($grandTotal, 0) }}</th>
                        <th class="text-end">TZS {{ number_format(collect($combinedByPurpose)->sum('pledges.outstanding'), 0) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    @endif

    <!-- Offering Types -->
    <div class="card mb-4">
        <div class="card-header"><strong><i class="fas fa-gift me-2"></i>Offering Types Breakdown</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 30%;">Offering Type</th>
                        <th class="text-end" style="width: 25%;">Total Amount</th>
                        <th class="text-end" style="width: 15%;">Count</th>
                        <th class="text-end" style="width: 15%;">Average</th>
                        <th class="text-end" style="width: 15%;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalOfferings = $offeringTypes->sum('total_amount');
                    @endphp
                    @forelse($offeringTypes as $offering)
                    <tr>
                        <td>
                            @if($offering->offering_type == 'general')
                                General Offering
                            @elseif(in_array($offering->offering_type, ['special', 'thanksgiving', 'building_fund']))
                                {{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}
                            @else
                                {{ ucfirst($offering->offering_type) }}
                            @endif
                        </td>
                        <td class="text-end"><strong>TZS {{ number_format($offering->total_amount, 0) }}</strong></td>
                        <td class="text-end">{{ $offering->transaction_count }}</td>
                        <td class="text-end">TZS {{ number_format($offering->total_amount / max($offering->transaction_count, 1), 0) }}</td>
                        <td class="text-end">{{ $totalOfferings > 0 ? number_format(($offering->total_amount / $totalOfferings) * 100, 1) : 0 }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 20px;">
                            <i class="fas fa-inbox me-2"></i>No offering data found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Donation Types -->
    <div class="card mb-4">
        <div class="card-header"><strong><i class="fas fa-heart me-2"></i>Donation Types Breakdown</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 30%;">Donation Type</th>
                        <th class="text-end" style="width: 25%;">Total Amount</th>
                        <th class="text-end" style="width: 15%;">Count</th>
                        <th class="text-end" style="width: 15%;">Average</th>
                        <th class="text-end" style="width: 15%;">Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalDonations = $donationTypes->sum('total_amount');
                    @endphp
                    @forelse($donationTypes as $donation)
                    <tr>
                        <td>{{ ucfirst($donation->donation_type) }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($donation->total_amount, 0) }}</strong></td>
                        <td class="text-end">{{ $donation->transaction_count }}</td>
                        <td class="text-end">TZS {{ number_format($donation->total_amount / max($donation->transaction_count, 1), 0) }}</td>
                        <td class="text-end">{{ $totalDonations > 0 ? number_format(($donation->total_amount / $totalDonations) * 100, 1) : 0 }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted" style="padding: 20px;">
                            <i class="fas fa-inbox me-2"></i>No donation data found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pledge Types -->
    <div class="card mb-4">
        <div class="card-header"><strong><i class="fas fa-handshake me-2"></i>Pledge Types Breakdown</strong></div>
        <div class="card-body p-0">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th style="width: 20%;">Pledge Type</th>
                        <th class="text-end" style="width: 20%;">Total Pledged</th>
                        <th class="text-end" style="width: 20%;">Total Paid</th>
                        <th class="text-end" style="width: 20%;">Remaining</th>
                        <th class="text-end" style="width: 10%;">Count</th>
                        <th class="text-end" style="width: 10%;">Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pledgeTypes as $pledge)
                    @php
                        $completionRate = $pledge->total_pledged > 0 ? ($pledge->total_paid / $pledge->total_pledged) * 100 : 0;
                    @endphp
                    <tr>
                        <td>{{ ucfirst($pledge->pledge_type) }}</td>
                        <td class="text-end"><strong>TZS {{ number_format($pledge->total_pledged, 0) }}</strong></td>
                        <td class="text-end"><strong>TZS {{ number_format($pledge->total_paid, 0) }}</strong></td>
                        <td class="text-end">TZS {{ number_format($pledge->total_pledged - $pledge->total_paid, 0) }}</td>
                        <td class="text-end">{{ $pledge->pledge_count }}</td>
                        <td class="text-end">{{ number_format($completionRate, 1) }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted" style="padding: 20px;">
                            <i class="fas fa-inbox me-2"></i>No pledge data found
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

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
            Department Giving Report
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


