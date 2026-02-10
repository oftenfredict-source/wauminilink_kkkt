@extends('layouts.index')

@section('title', 'Contribution Card - ' . $member->full_name)

@section('content')
<!-- Custom Print Styles -->
<style>
    @media print {
        header, .navbar, .sidebar, .btn-print, .no-print, .main-header, .breadcrumb-nav { display: none !important; }
        .card { border: none !important; box-shadow: none !important; margin: 0 !important; padding: 0 !important; }
        .container-fluid { padding: 0 !important; }
        .print-grid { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; }
        .print-grid th, .print-grid td { border: 1px solid #000 !important; padding: 4px !important; text-align: center !important; font-size: 10pt !important; height: 30px !important; }
        .header-main { background-color: #d1d8e0 !important; -webkit-print-color-adjust: exact; border: 1px solid #000 !important; margin-bottom: 0 !important; }
        .header-sub { border-left: 1px solid #000 !important; border-right: 1px solid #000 !important; margin-bottom: 0 !important; }
        .member-info-row { border: 1px solid #000 !important; border-top: none !important; margin-bottom: 10px !important; padding: 10px !important; }
        body { background: white !important; }
    }
    
    .grid-table th, .grid-table td {
        text-align: center;
        border: 1px solid #adb5bd;
        vertical-align: middle;
        padding: 10px 4px;
        min-width: 55px;
        height: 45px;
    }
    .grid-table thead th {
        background-color: #f8f9fa;
        font-weight: bold;
        color: #333;
        font-size: 0.85rem;
    }
    .week-col {
        background-color: #f1f3f5;
        font-weight: bold;
        width: 50px;
    }
    .header-main {
        background-color: #d1d8e0;
        text-align: center;
        padding: 12px;
        font-weight: bold;
        color: #1e3799;
        font-size: 1.3rem;
        border: 1px solid #adb5bd;
    }
    .header-sub {
        background-color: #fff;
        text-align: center;
        padding: 10px;
        font-weight: bold;
        color: #1e3799;
        font-size: 1.2rem;
        border-left: 1px solid #adb5bd;
        border-right: 1px solid #adb5bd;
    }
    .member-info-row {
        border-left: 1px solid #adb5bd;
        border-right: 1px solid #adb5bd;
        border-bottom: 1px solid #adb5bd;
        padding: 15px 20px;
        background-color: #fff;
    }
    .dotted-line {
        border-bottom: 1px dotted #000;
        display: inline-block;
        min-width: 100px;
        margin: 0 5px;
        padding-left: 8px;
        font-weight: bold;
        color: #000;
        text-transform: uppercase;
    }
    .info-label {
        font-weight: bold;
        color: #1e3799;
        text-transform: uppercase;
    }
</style>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-xl-11">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-md-5">
                    
                    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                        <a href="{{ route('reports.special-offerings', ['year' => $year, 'offering_type' => $offeringType]) }}" class="btn btn-outline-secondary rounded-pill">
                            <i class="fas fa-arrow-left me-2"></i>Back to Summary
                        </a>
                        <button onclick="window.print()" class="btn btn-success rounded-pill px-4 shadow-sm btn-print">
                            <i class="fas fa-print me-2"></i>Print Contribution Card
                        </button>
                    </div>

                    <!-- Printable Header Section -->
                    <div class="header-main">
                        KKKT DAYOSISI YA KASKAZINI
                    </div>
                    <div class="header-sub">
                        USHARIKA WA LONGUO
                    </div>
                    
                    <div class="member-info-row">
                        <div class="row mb-3">
                            <div class="col-md-7 mb-2 mb-md-0">
                                <span class="info-label">JINA:</span> 
                                <span class="dotted-line" style="min-width: 300px;">{{ $member->full_name }}</span>
                            </div>
                            <div class="col-md-5">
                                <span class="info-label">MTAA:</span> 
                                <span class="dotted-line" style="min-width: 150px;">{{ $member->community->name ?? '....................' }}</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5 mb-2 mb-md-0">
                                <span class="info-label">OFFERING:</span> 
                                <span class="dotted-line text-primary" style="min-width: 150px;">{{ $offeringType }}</span>
                            </div>
                            <div class="col-md-4 mb-2 mb-md-0">
                                <span class="info-label">JENGO NAMBA:</span> 
                                <span class="dotted-line" style="min-width: 80px;">{{ $member->house_number ?? '................' }}</span>
                            </div>
                            <div class="col-md-3">
                                <span class="info-label">MWAKA:</span> 
                                <span class="dotted-line" style="min-width: 60px;">{{ $year }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- The Grid -->
                    <div class="table-responsive mt-0">
                        <table class="table table-bordered grid-table print-grid mb-0">
                            <thead>
                                <tr>
                                    <th class="week-col">WIKI</th>
                                    <th>JAN</th>
                                    <th>FEB</th>
                                    <th>MARCH</th>
                                    <th>APRIL</th>
                                    <th>MAY</th>
                                    <th>JUNI</th>
                                    <th>JULAI</th>
                                    <th>AGOST</th>
                                    <th>SEPT</th>
                                    <th>OCT</th>
                                    <th>NOV</th>
                                    <th>DEC</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for($w = 1; $w <= 5; $w++)
                                <tr>
                                    <td class="week-col">{{ $w }}</td>
                                    @for($m = 1; $m <= 12; $m++)
                                    <td>
                                        @if($grid[$m][$w] > 0)
                                            <span class="fw-bold text-dark">{{ number_format($grid[$m][$w], 0) }}</span>
                                        @else
                                            &nbsp;
                                        @endif
                                    </td>
                                    @endfor
                                </tr>
                                @endfor
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td class="week-col">TOTAL</td>
                                    @for($m = 1; $m <= 12; $m++)
                                    @php
                                        $monthTotal = 0;
                                        for($w = 1; $w <= 5; $w++) {
                                            $monthTotal += $grid[$m][$w];
                                        }
                                    @endphp
                                    <td class="text-primary">{{ $monthTotal > 0 ? number_format($monthTotal, 0) : '' }}</td>
                                    @endfor
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-between no-print">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i> Calculated based on weekly breakdowns entered by Church Elders.
                        </div>
                        <div class="fw-bold">
                            Total Year Contribution: <span class="text-success fs-5">TZS {{ number_format($member->communityOfferingItems()->whereHas('offering', function($q) use ($year, $offeringType) { $q->whereYear('offering_date', $year)->where('offering_type', $offeringType); })->sum('amount'), 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
