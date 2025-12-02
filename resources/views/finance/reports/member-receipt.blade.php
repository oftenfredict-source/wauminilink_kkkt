<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giving Receipt - {{ $member->full_name }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .receipt-container {
            max-width: 850px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border-radius: 12px;
        }
        
        .receipt-header {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .church-logo {
            width: 90px;
            height: 90px;
            margin: 0 auto 15px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: bold;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        }
        
        .church-name {
            font-size: 32px;
            font-weight: 700;
            color: white;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .church-tagline {
            font-size: 15px;
            color: rgba(255, 255, 255, 0.95);
            margin-bottom: 12px;
            font-weight: 500;
        }
        
        .church-details {
            font-size: 13px;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
        }
        
        .receipt-title {
            text-align: center;
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 2px;
            position: relative;
            padding-bottom: 15px;
        }
        
        .receipt-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }
        
        .member-info {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
            border-left: 5px solid #667eea;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .member-name {
            font-size: 22px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .member-name::before {
            content: '👤';
            font-size: 20px;
            -webkit-text-fill-color: #667eea;
        }
        
        .member-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            font-size: 14px;
        }
        
        .member-details > div {
            padding: 8px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
        
        .member-details strong {
            color: #667eea;
            font-weight: 600;
        }
        
        .period-info {
            text-align: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 18px;
            border-radius: 10px;
            margin-bottom: 30px;
            font-size: 17px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .period-info::before {
            content: '📅';
            font-size: 20px;
        }
        
        .giving-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .summary-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 22px;
            border-radius: 12px;
            text-align: center;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.2);
            border-color: #667eea;
        }
        
        .summary-card:nth-child(1) {
            border-top: 4px solid #28a745;
        }
        
        .summary-card:nth-child(2) {
            border-top: 4px solid #17a2b8;
        }
        
        .summary-card:nth-child(3) {
            border-top: 4px solid #ffc107;
        }
        
        .summary-card:nth-child(4) {
            border-top: 4px solid #dc3545;
        }
        
        .summary-label {
            font-size: 13px;
            color: #666;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            font-weight: 600;
        }
        
        .summary-amount {
            font-size: 26px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .transactions-section {
            margin-bottom: 35px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 18px;
            padding-bottom: 10px;
            border-bottom: 3px solid #e9ecef;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .section-title::before {
            content: '💰';
            font-size: 22px;
            -webkit-text-fill-color: #667eea;
        }
        
        .transaction-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
            font-size: 14px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        
        .transaction-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 14px 12px;
            text-align: left;
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .transaction-table th:first-child {
            border-top-left-radius: 10px;
        }
        
        .transaction-table th:last-child {
            border-top-right-radius: 10px;
        }
        
        .transaction-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            background: white;
        }
        
        .transaction-table tr:nth-child(even) td {
            background: #f8f9fa;
        }
        
        .transaction-table tr:hover td {
            background: #f0f4ff;
        }
        
        .transaction-table tr:last-child td:first-child {
            border-bottom-left-radius: 10px;
        }
        
        .transaction-table tr:last-child td:last-child {
            border-bottom-right-radius: 10px;
        }
        
        .amount {
            text-align: right;
            font-weight: 700;
            color: #28a745;
            font-size: 15px;
        }
        
        .total-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 28px;
            border-radius: 12px;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .total-section::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(180deg); }
        }
        
        .total-label {
            font-size: 18px;
            margin-bottom: 12px;
            font-weight: 600;
            opacity: 0.95;
        }
        
        .total-amount {
            font-size: 38px;
            font-weight: 700;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        .receipt-footer {
            text-align: center;
            padding: 25px 20px;
            border-top: 3px solid #e9ecef;
            color: #666;
            font-size: 13px;
            background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
            border-radius: 10px;
            margin-top: 30px;
        }
        
        .receipt-footer p {
            margin-bottom: 8px;
            line-height: 1.8;
        }
        
        .receipt-footer p:first-child {
            font-size: 15px;
            font-weight: 600;
            color: #667eea;
            margin-bottom: 12px;
        }
        
        .receipt-footer p:last-child {
            margin-bottom: 0;
            font-size: 12px;
            color: #999;
        }
        
        .print-button,
        .download-button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 14px 28px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .download-button {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
        }
        
        .print-button:hover,
        .download-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
        }
        
        .download-button:hover {
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.5);
        }
        
        .print-button:active,
        .download-button:active {
            transform: translateY(0);
        }
        
        @media screen and (max-width: 768px) {
            div[style*="position: fixed"] {
                flex-direction: column !important;
                top: 10px !important;
                right: 10px !important;
            }
            
            .print-button,
            .download-button {
                padding: 10px 16px;
                font-size: 12px;
                width: 100%;
            }
        }
        
        
        /* Mobile Responsive Styles */
        @media screen and (max-width: 768px) {
            .receipt-container {
                padding: 15px;
            }
            
            .church-logo {
                width: 60px;
                height: 60px;
                font-size: 18px;
            }
            
            .church-name {
                font-size: 22px;
            }
            
            .church-tagline {
                font-size: 12px;
            }
            
            .church-details {
                font-size: 11px;
            }
            
            .receipt-title {
                font-size: 20px;
            }
            
            .member-info {
                padding: 15px;
            }
            
            .member-name {
                font-size: 18px;
            }
            
            .member-details {
                grid-template-columns: 1fr;
                gap: 8px;
                font-size: 13px;
            }
            
            .period-info {
                padding: 12px;
                font-size: 14px;
            }
            
            .giving-summary {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            
            .summary-card {
                padding: 15px;
            }
            
            .summary-label {
                font-size: 12px;
            }
            
            .summary-amount {
                font-size: 20px;
            }
            
            .section-title {
                font-size: 16px;
            }
            
            .transaction-table {
                font-size: 11px;
                display: block;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
            }
            
            .transaction-table th,
            .transaction-table td {
                padding: 8px 4px;
                white-space: nowrap;
            }
            
            .total-section {
                padding: 15px;
            }
            
            .total-label {
                font-size: 16px;
            }
            
            .total-amount {
                font-size: 24px;
            }
            
            .print-button {
                top: 10px;
                right: 10px;
                padding: 10px 16px;
                font-size: 12px;
            }
        }
        
        @media screen and (max-width: 576px) {
            .receipt-container {
                padding: 10px;
            }
            
            .church-logo {
                width: 50px;
                height: 50px;
                font-size: 16px;
            }
            
            .church-name {
                font-size: 18px;
            }
            
            .receipt-title {
                font-size: 18px;
            }
            
            .member-name {
                font-size: 16px;
            }
            
            .member-details {
                font-size: 12px;
            }
            
            .period-info {
                font-size: 12px;
                padding: 10px;
            }
            
            .summary-amount {
                font-size: 18px;
            }
            
            .transaction-table {
                font-size: 10px;
            }
            
            .transaction-table th,
            .transaction-table td {
                padding: 6px 3px;
            }
            
            .total-amount {
                font-size: 20px;
            }
            
            .receipt-footer {
                font-size: 11px;
            }
        }
        
        /* Print Styles */
        @media print {
            /* Hide print and download buttons */
            .print-button,
            .download-button {
                display: none !important;
            }
            
            /* Remove all margins and padding */
            * {
                margin: 0;
                padding: 0;
            }
            
            body {
                margin: 0;
                padding: 0;
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                color-adjust: exact;
                font-size: 10px !important;
            }
            
            .receipt-container {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 8px !important;
                width: 100% !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            
            /* Optimize header for print - Very compact */
            .receipt-header {
                padding: 8px 10px !important;
                margin-bottom: 8px !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .church-logo {
                width: 40px !important;
                height: 40px !important;
                font-size: 14px !important;
                margin-bottom: 5px !important;
            }
            
            .church-name {
                font-size: 16px !important;
                margin-bottom: 2px !important;
            }
            
            .church-tagline {
                font-size: 9px !important;
                margin-bottom: 3px !important;
            }
            
            .church-details {
                font-size: 8px !important;
                line-height: 1.3 !important;
            }
            
            /* Optimize receipt title */
            .receipt-title {
                font-size: 14px !important;
                margin-bottom: 8px !important;
                padding-bottom: 5px !important;
            }
            
            .receipt-title::after {
                width: 50px !important;
                height: 2px !important;
            }
            
            /* Optimize member info */
            .member-info {
                padding: 8px 10px !important;
                margin-bottom: 8px !important;
            }
            
            .member-name {
                font-size: 12px !important;
                margin-bottom: 5px !important;
            }
            
            .member-name::before {
                font-size: 12px !important;
            }
            
            .member-details {
                font-size: 9px !important;
                gap: 4px !important;
                grid-template-columns: 1fr 1fr !important;
            }
            
            .member-details > div {
                padding: 3px 0 !important;
            }
            
            /* Optimize period info */
            .period-info {
                padding: 6px 8px !important;
                margin-bottom: 8px !important;
                font-size: 10px !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .period-info::before {
                font-size: 12px !important;
            }
            
            /* Optimize summary cards - Make them very compact */
            .giving-summary {
                grid-template-columns: repeat(4, 1fr) !important;
                gap: 6px !important;
                margin-bottom: 8px !important;
            }
            
            .summary-card {
                padding: 6px 4px !important;
            }
            
            .summary-label {
                font-size: 7px !important;
                margin-bottom: 3px !important;
            }
            
            .summary-amount {
                font-size: 12px !important;
            }
            
            /* Optimize transaction sections */
            .transactions-section {
                margin-bottom: 8px !important;
                page-break-inside: avoid;
            }
            
            .section-title {
                font-size: 10px !important;
                margin-bottom: 4px !important;
                padding-bottom: 3px !important;
            }
            
            .section-title::before {
                font-size: 11px !important;
            }
            
            .transaction-table {
                font-size: 7px !important;
                width: 100% !important;
                display: table !important;
                margin-bottom: 6px !important;
            }
            
            .transaction-table th {
                padding: 4px 3px !important;
                font-size: 7px !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .transaction-table td {
                padding: 3px !important;
                font-size: 7px !important;
            }
            
            .amount {
                color: #28a745 !important;
                font-weight: 700 !important;
                font-size: 7px !important;
            }
            
            /* Optimize total section */
            .total-section {
                padding: 8px !important;
                margin-bottom: 8px !important;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
                color: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .total-section::before {
                display: none !important;
            }
            
            .total-label {
                font-size: 10px !important;
                margin-bottom: 3px !important;
            }
            
            .total-amount {
                font-size: 18px !important;
            }
            
            /* Optimize footer */
            .receipt-footer {
                padding: 6px 8px !important;
                font-size: 7px !important;
                border-top: 1px solid #e9ecef !important;
                margin-top: 8px !important;
            }
            
            .receipt-footer p {
                margin-bottom: 3px !important;
                line-height: 1.4 !important;
            }
            
            .receipt-footer p:first-child {
                font-size: 8px !important;
                margin-bottom: 4px !important;
            }
            
            .receipt-footer p:last-child {
                font-size: 7px !important;
            }
            
            /* Page setup - Minimal margins */
            @page {
                size: A4;
                margin: 0.3cm;
            }
            
            /* Hide browser print headers and footers */
            @page {
                @top-left { content: ""; }
                @top-center { content: ""; }
                @top-right { content: ""; }
                @bottom-left { content: ""; }
                @bottom-center { content: ""; }
                @bottom-right { content: ""; }
            }
            
            /* Additional print margin adjustments */
            html, body {
                margin: 0 !important;
                padding: 0 !important;
            }
            
            /* Prevent page breaks inside important sections */
            .receipt-header,
            .member-info,
            .period-info,
            .total-section {
                page-break-inside: avoid;
            }
            
            /* Remove rounded corners for cleaner print */
            .member-info,
            .period-info,
            .summary-card,
            .total-section,
            .transaction-table {
                border-radius: 0 !important;
            }
            
            /* Compact table rows */
            .transaction-table tr {
                page-break-inside: avoid;
            }
            
            /* Reduce spacing in all elements */
            h1, h2, h3, h4, h5, h6 {
                margin-top: 0 !important;
                margin-bottom: 4px !important;
            }
            
            p {
                margin-bottom: 3px !important;
            }
        }
    </style>
</head>
<body>
    <div style="position: fixed; top: 20px; right: 20px; z-index: 1000; display: flex; gap: 10px;">
        <button class="print-button" onclick="window.print()">
            <i class="fas fa-print"></i> Print Receipt
        </button>
        <button class="download-button" onclick="downloadPDF()">
            <i class="fas fa-download"></i> Download PDF
        </button>
    </div>
    
    <div class="receipt-container">
        <!-- Receipt Header -->
        <div class="receipt-header">
            <div class="church-logo">WL</div>
            <div class="church-name">{{ $churchInfo['name'] }}</div>
            <div class="church-tagline">Connecting Hearts, Building Faith</div>
            <div class="church-details">
                {{ $churchInfo['address'] }}<br>
                Phone: {{ $churchInfo['phone'] }}
            </div>
        </div>
        
        <!-- Receipt Title -->
        <div class="receipt-title">Giving Receipt</div>
        
        <!-- Member Information -->
        <div class="member-info">
            <div class="member-name">{{ $member->full_name }}</div>
            <div class="member-details">
                <div><strong>Member ID:</strong> {{ $member->member_id }}</div>
                <div><strong>Phone:</strong> {{ $member->phone ?? 'N/A' }}</div>
                <div><strong>Email:</strong> {{ $member->email ?? 'N/A' }}</div>
                <div><strong>Address:</strong> {{ $member->address ?? 'N/A' }}</div>
            </div>
        </div>
        
        <!-- Period Information -->
        <div class="period-info">
            Giving Period: {{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}
        </div>
        
        <!-- Giving Summary -->
        <div class="giving-summary">
            <div class="summary-card">
                <div class="summary-label">Total Tithes</div>
                <div class="summary-amount">TZS {{ number_format($totalTithes, 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Offerings</div>
                <div class="summary-amount">TZS {{ number_format($totalOfferings, 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Donations</div>
                <div class="summary-amount">TZS {{ number_format($totalDonations, 0) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-label">Total Pledged</div>
                <div class="summary-amount">TZS {{ number_format($totalPledged, 0) }}</div>
            </div>
        </div>
        
        <!-- Detailed Transactions -->
        @if($tithes->count() > 0)
        <div class="transactions-section">
            <div class="section-title">Tithes</div>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reference</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tithes as $tithe)
                    <tr>
                        <td>{{ $tithe->tithe_date ? \Carbon\Carbon::parse($tithe->tithe_date)->format('M d, Y') : '-' }}</td>
                        <td class="amount">TZS {{ number_format($tithe->amount, 0) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}</td>
                        <td>{{ $tithe->reference_number ?? 'N/A' }}</td>
                        <td>
                            <span style="color: {{ $tithe->is_verified ? '#28a745' : '#ffc107' }}">
                                {{ $tithe->is_verified ? 'Verified' : 'Pending' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if($offerings->count() > 0)
        <div class="transactions-section">
            <div class="section-title">Offerings</div>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($offerings as $offering)
                    <tr>
                        <td>{{ $offering->offering_date ? \Carbon\Carbon::parse($offering->offering_date)->format('M d, Y') : '-' }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $offering->offering_type)) }}</td>
                        <td class="amount">TZS {{ number_format($offering->amount, 0) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $offering->payment_method)) }}</td>
                        <td>{{ $offering->reference_number ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if($donations->count() > 0)
        <div class="transactions-section">
            <div class="section-title">Donations</div>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Purpose</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reference</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($donations as $donation)
                    <tr>
                        <td>{{ $donation->donation_date ? \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') : '-' }}</td>
                        <td>{{ $donation->purpose ?? 'General' }}</td>
                        <td class="amount">TZS {{ number_format($donation->amount, 0) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}</td>
                        <td>{{ $donation->reference_number ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        @if($pledges->count() > 0)
        <div class="transactions-section">
            <div class="section-title">Pledges</div>
            <table class="transaction-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Pledged</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pledges as $pledge)
                    <tr>
                        <td>{{ $pledge->pledge_date ? \Carbon\Carbon::parse($pledge->pledge_date)->format('M d, Y') : '-' }}</td>
                        <td>{{ ucfirst($pledge->pledge_type) }}</td>
                        <td class="amount">TZS {{ number_format($pledge->pledge_amount, 0) }}</td>
                        <td class="amount">TZS {{ number_format($pledge->amount_paid, 0) }}</td>
                        <td class="amount">TZS {{ number_format($pledge->pledge_amount - $pledge->amount_paid, 0) }}</td>
                        <td>
                            <span style="color: {{ $pledge->status == 'completed' ? '#28a745' : ($pledge->status == 'overdue' ? '#dc3545' : '#ffc107') }}">
                                {{ ucfirst($pledge->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Total Summary -->
        <div class="total-section">
            <div class="total-label">Total Giving for Period</div>
            <div class="total-amount">TZS {{ number_format($totalGiving, 0) }}</div>
        </div>
        
        <!-- Receipt Footer -->
        <div class="receipt-footer">
            <p><strong>Thank you for your faithful giving!</strong></p>
            <p>This receipt serves as official documentation of your contributions to {{ $churchInfo['name'] }}.</p>
            <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
            <p>For questions about this receipt, please contact us at {{ $churchInfo['email'] }}</p>
        </div>
    </div>
    
    <!-- html2pdf.js library for PDF generation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    
    <script>
        // Print function - CSS will handle removing headers/footers where supported
        // Note: Users may need to disable "Headers and footers" in browser print settings
        function printReceipt() {
            window.print();
        }
        
        // Download PDF function
        function downloadPDF() {
            // Show loading message
            const loadingMsg = document.createElement('div');
            loadingMsg.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: rgba(0,0,0,0.8); color: white; padding: 20px 30px; border-radius: 8px; z-index: 10000; font-size: 16px;';
            loadingMsg.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generating PDF...';
            document.body.appendChild(loadingMsg);
            
            // Get the receipt container
            const element = document.querySelector('.receipt-container');
            const memberName = '{{ $member->full_name }}'.replace(/[^a-z0-9]/gi, '_');
            const fileName = `Giving_Receipt_${memberName}_{{ date('Y-m-d') }}.pdf`;
            
            // Configure PDF options
            const opt = {
                margin: [0.3, 0.3, 0.3, 0.3],
                filename: fileName,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 2,
                    useCORS: true,
                    letterRendering: true,
                    logging: false,
                    onclone: function(clonedDoc) {
                        // Replace gradients with solid colors
                        const receiptHeader = clonedDoc.querySelector('.receipt-header');
                        if (receiptHeader) {
                            receiptHeader.style.background = '#667eea';
                            receiptHeader.style.backgroundImage = 'none';
                        }
                        
                        const periodInfo = clonedDoc.querySelector('.period-info');
                        if (periodInfo) {
                            periodInfo.style.background = '#667eea';
                            periodInfo.style.backgroundImage = 'none';
                        }
                        
                        const totalSection = clonedDoc.querySelector('.total-section');
                        if (totalSection) {
                            totalSection.style.background = '#667eea';
                            totalSection.style.backgroundImage = 'none';
                        }
                        
                        const tableHeaders = clonedDoc.querySelectorAll('.transaction-table th');
                        tableHeaders.forEach(th => {
                            th.style.background = '#667eea';
                            th.style.backgroundImage = 'none';
                        });
                        
                        // Fix gradient text colors
                        const receiptTitle = clonedDoc.querySelector('.receipt-title');
                        if (receiptTitle) {
                            receiptTitle.style.color = '#667eea';
                            receiptTitle.style.webkitTextFillColor = '#667eea';
                            receiptTitle.style.background = 'none';
                            receiptTitle.style.webkitBackgroundClip = 'unset';
                            receiptTitle.style.backgroundClip = 'unset';
                        }
                        
                        const memberName = clonedDoc.querySelector('.member-name');
                        if (memberName) {
                            memberName.style.color = '#667eea';
                            memberName.style.webkitTextFillColor = '#667eea';
                            memberName.style.background = 'none';
                            memberName.style.webkitBackgroundClip = 'unset';
                            memberName.style.backgroundClip = 'unset';
                        }
                        
                        const sectionTitles = clonedDoc.querySelectorAll('.section-title');
                        sectionTitles.forEach(title => {
                            title.style.color = '#667eea';
                            title.style.webkitTextFillColor = '#667eea';
                            title.style.background = 'none';
                            title.style.webkitBackgroundClip = 'unset';
                            title.style.backgroundClip = 'unset';
                        });
                        
                        const summaryAmounts = clonedDoc.querySelectorAll('.summary-amount');
                        summaryAmounts.forEach(amount => {
                            amount.style.color = '#667eea';
                            amount.style.webkitTextFillColor = '#667eea';
                            amount.style.background = 'none';
                            amount.style.webkitBackgroundClip = 'unset';
                            amount.style.backgroundClip = 'unset';
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
                    avoid: ['.receipt-header', '.member-info', '.period-info', '.total-section']
                }
            };
            
            // Generate and download PDF
            html2pdf().set(opt).from(element).save().then(function() {
                // Remove loading message
                document.body.removeChild(loadingMsg);
                
                // Show success message
                const successMsg = document.createElement('div');
                successMsg.style.cssText = 'position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: #28a745; color: white; padding: 15px 25px; border-radius: 8px; z-index: 10000; font-size: 14px; box-shadow: 0 4px 15px rgba(40,167,69,0.4);';
                successMsg.innerHTML = '<i class="fas fa-check-circle"></i> PDF downloaded successfully!';
                document.body.appendChild(successMsg);
                
                setTimeout(function() {
                    if (document.body.contains(successMsg)) {
                        document.body.removeChild(successMsg);
                    }
                }, 2000);
            }).catch(function(error) {
                // Remove loading message
                if (document.body.contains(loadingMsg)) {
                    document.body.removeChild(loadingMsg);
                }
                
                // Show error message
                alert('Error generating PDF: ' + error.message);
                console.error('PDF generation error:', error);
            });
        }
        
        // Update print button onclick
        document.addEventListener('DOMContentLoaded', function() {
            const printBtn = document.querySelector('.print-button');
            if (printBtn) {
                printBtn.onclick = printReceipt;
            }
        });
    </script>
</body>
</html>






