@extends('layouts.index')

@section('title', 'Member Contribution Report - ' . $member->full_name)

@section('content')
    <!-- Custom Print Styles -->
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 10mm;
            }

            header,
            .navbar,
            .sidebar,
            .btn-print,
            .no-print,
            .main-header,
            .breadcrumb-nav {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .container-fluid {
                padding: 0 !important;
            }

            .grid-container {
                break-inside: avoid;
                margin-bottom: 20px;
                width: 100% !important;
            }

            .print-grid {
                width: 100% !important;
                border-collapse: collapse !important;
                border: 1px solid #000 !important;
                table-layout: fixed !important;
            }

            .print-grid th,
            .print-grid td {
                border: 1px solid #000 !important;
                padding: 2px !important;
                text-align: center !important;
                font-size: 9pt !important;
                height: 28px !important;
                overflow: hidden !important;
            }

            .week-col {
                width: 40px !important;
            }

            .header-main {
                background-color: #f1f3f5 !important;
                -webkit-print-color-adjust: exact;
                border: 1px solid #000 !important;
                border-bottom: none !important;
                margin-bottom: 0 !important;
            }

            .header-sub {
                border-left: 1px solid #000 !important;
                border-right: 1px solid #000 !important;
                margin-bottom: 0 !important;
                border-bottom: none !important;
            }

            .member-info-row {
                border: 1px solid #000 !important;
                margin-bottom: 10px !important;
                padding: 8px !important;
            }

            body {
                background: white !important;
            }
        }

        .grid-table th,
        .grid-table td {
            text-align: center;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 8px 4px;
            min-width: 60px;
            height: 48px;
        }

        .grid-table thead th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #343a40;
            font-size: 0.8rem;
        }

        .week-col {
            background-color: #e9ecef;
            font-weight: bold;
            width: 55px;
        }

        .header-main {
            background-color: #f1f3f5;
            text-align: center;
            padding: 10px;
            font-weight: 800;
            color: #1e3799;
            font-size: 1.4rem;
            letter-spacing: 1px;
            border: 1px solid #dee2e6;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
        }

        .header-sub {
            background-color: #fff;
            text-align: center;
            padding: 8px;
            font-weight: 700;
            color: #1e3799;
            font-size: 1.2rem;
            border-left: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
        }

        .member-info-row {
            border: 1px solid #dee2e6;
            padding: 15px 20px;
            background-color: #fff;
            border-radius: 0 0 8px 8px;
            margin-bottom: 20px;
        }

        .dotted-line {
            border-bottom: 1px dotted #000;
            display: inline-block;
            min-width: 100px;
            margin-left: 5px;
            padding-left: 5px;
            font-weight: 700;
            color: #000;
        }

        .info-label {
            font-weight: 700;
            color: #343a40;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-11">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4 p-md-5">

                        <div class="d-flex justify-content-between align-items-center mb-4 no-print">
                            <div>
                                <h4 class="fw-bold mb-0">Member Contribution Grid Report</h4>
                                <p class="text-muted small mb-0">Select type and year to generate the printable report.</p>
                            </div>
                            <div class="d-flex gap-2">
                                <form action="{{ route('reports.member-grid-report', $member->id) }}" method="GET"
                                    class="d-flex gap-2">
                                    <select name="type" class="form-select form-select-sm" onchange="this.form.submit()">
                                        <option value="Sadaka ya Umoja" {{ $type == 'Sadaka ya Umoja' ? 'selected' : '' }}>
                                            Sadaka ya Umoja</option>
                                        <option value="Sadaka ya Jengo" {{ $type == 'Sadaka ya Jengo' ? 'selected' : '' }}>
                                            Sadaka ya Jengo</option>
                                        <option value="Pledges" {{ $type == 'Pledges' ? 'selected' : '' }}>Ahadi ya Bwana
                                        </option>
                                    </select>
                                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                                        @for($y = date('Y'); $y >= 2024; $y--)
                                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </form>
                                <button onclick="window.print()"
                                    class="btn btn-success rounded-pill px-4 shadow-sm btn-print">
                                    <i class="fas fa-print me-2"></i>Print Report
                                </button>
                            </div>
                        </div>

                        <!-- Printable Container -->
                        <div class="grid-container">
                            <!-- Header Section -->
                            <div class="header-main text-uppercase">
                                KKKT DAYOSISI YA KASKAZINI
                            </div>
                            <div class="header-sub text-uppercase">
                                USHARIKA WA LONGUO
                            </div>

                            <div class="member-info-row">
                                <div class="row g-3">
                                    <div class="col-md-7">
                                        <span class="info-label">JINA:</span>
                                        <span class="dotted-line w-75">{{ $member->full_name }}</span>
                                    </div>
                                    <div class="col-md-5">
                                        <span class="info-label">MTAA:</span>
                                        <span
                                            class="dotted-line w-75">{{ $member->community->name ?? '....................' }}</span>
                                    </div>
                                    <div class="col-md-7">
                                        <span class="info-label">REPORT:</span>
                                        <span class="dotted-line text-primary w-75">
                                            @if($type == 'Pledges') Ahadi ya Bwana @else {{ $type }} @endif
                                        </span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="info-label">MWAKA:</span>
                                        <span class="dotted-line w-50">{{ $year }}</span>

                                        @if($type == 'Pledges' && isset($totalPledgedAmount) && $totalPledgedAmount > 0)
                                            <div class="mt-1 small">
                                                <span class="fw-bold">Ahadi:</span> {{ number_format($totalPledgedAmount, 0) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-md-2 text-end">
                                        <span class="info-label">
                                            @if($type == 'Pledges') AHADI @elseif(str_contains($type, 'Jengo')) JENGO @else
                                            BAHASHA @endif NO:
                                        </span>
                                        <span class="dotted-line"
                                            style="min-width: 60px;">{{ $member->envelope_number ?? '.......' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- The Grid -->
                            <div class="table-responsive">
                                <table class="table table-bordered grid-table print-grid mb-0">
                                    <thead>
                                        <tr>
                                            <th class="week-col">WIKI</th>
                                            <th>JAN</th>
                                            <th>FEB</th>
                                            <th>MAR</th>
                                            <th>APR</th>
                                            <th>MEI</th>
                                            <th>JUN</th>
                                            <th>JUL</th>
                                            <th>AGO</th>
                                            <th>SEP</th>
                                            <th>OKT</th>
                                            <th>NOV</th>
                                            <th>DES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @for($w = 1; $w <= 5; $w++)
                                            <tr>
                                                <td class="week-col">{{ $w }}</td>
                                                @for($m = 1; $m <= 12; $m++)
                                                    <td class="{{ $grid[$m][$w] > 0 ? 'bg-light' : '' }}">
                                                        @if($grid[$m][$w] > 0)
                                                            <span class="fw-bold text-dark">{{ number_format($grid[$m][$w], 0) }}</span>
                                                        @else
                                                            <span class="text-white-50 opacity-0">.</span>
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
                                                    for ($w = 1; $w <= 5; $w++) {
                                                        $monthTotal += $grid[$m][$w];
                                                    }
                                                @endphp
                                                <td class="{{ $monthTotal > 0 ? 'text-primary' : '' }}">
                                                    {{ $monthTotal > 0 ? number_format($monthTotal, 0) : '' }}
                                                </td>
                                            @endfor
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>

                            <div class="mt-4 d-flex justify-content-between no-print">
                                <div class="text-muted small">
                                    <i class="fas fa-info-circle me-1"></i> Unified report including Finance and Community
                                    recordings.
                                </div>
                                <div class="fw-bold">
                                    Total Year Contribution: <span class="text-success fs-5">TZS
                                        {{ number_format($totalAmount, 0) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Repeater for printable form if it's meant to be blank (optional) -->
                        @if($totalAmount == 0)
                            <div class="mt-5 d-none d-print-block">
                                <hr class="border-secondary border-1 my-5">
                                <p class="text-center text-muted small">Blank Template Copy</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection