@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-bottom p-3 d-print-none">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 text-primary fw-bold">General Secretary's Annual Report</h5>
                    <p class="text-muted small mb-0">Financial Status for the year {{ $year }}</p>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i> Print Report
                    </button>
                    <form method="GET" action="{{ route('reports.general-secretary') }}" class="d-flex gap-2">
                        <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                            @for($i = date('Y'); $i >= 2020; $i--)
                                <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="text-center mb-4">
                {{-- Hidden from print per user request: KKKT Ushirika wa Longuo --}}
                <div class="d-print-none">
                    <h6 class="fw-bold mb-1">KANISA LA KIINJILI LA KILUTHERI TANZANIA</h6>
                    <h6 class="fw-bold mb-1">JIMBO LA KILIMANJARO KATI</h6>
                    <h6 class="fw-bold mb-1">USHARIKA WA LONGUO</h6>
                </div>
                {{-- Official Title for Print --}}
                <h5 class="fw-bold mt-3 text-uppercase">TAARIFA YA MAPATO NA MATUMIZI JAN - DEC {{ $year }}</h5>
            </div>

            {{-- Tabs Navigation --}}
            <ul class="nav nav-pills mb-4 d-print-none" id="reportTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold" id="mapato-tab" data-bs-toggle="pill" data-bs-target="#mapato" type="button" role="tab">A. MAPATO</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="matumizi-tab" data-bs-toggle="pill" data-bs-target="#matumizi" type="button" role="tab">B. MATUMIZI</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold" id="summary-tab" data-bs-toggle="pill" data-bs-target="#summary" type="button" role="tab">SUMMARY (SALIO)</button>
                </li>
            </ul>

            <div class="tab-content" id="reportTabsContent">
                {{-- Tab A: Mapato --}}
                <div class="tab-pane fade show active d-print-block" id="mapato" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-uppercase">A. MAPATO</th>
                                    <th class="text-center" style="width: 15%">MAKISIO</th>
                                    <th class="text-center" style="width: 15%">HALISI</th>
                                    <th class="text-center" style="width: 15%">ZIDIO/PUNGUFU</th>
                                </tr>
                                <tr>
                                    <th colspan="2" class="bg-secondary text-white">MAPATO YA INJILI</th>
                                    <th class="bg-secondary text-white"></th>
                                    <th class="bg-secondary text-white"></th>
                                    <th class="bg-secondary text-white"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @php 
                                    $totalGospelEstimate = 0;
                                    $totalGospelActual = 0;
                                @endphp
                                @foreach($gospelIncome as $code => $data)
                                    @php
                                        $diff = $data['actual'] - $data['estimate'];
                                        $totalGospelEstimate += $data['estimate'];
                                        $totalGospelActual += $data['actual'];
                                    @endphp
                                    <tr>
                                        <td style="width: 80px" class="text-center">{{ $code }}</td>
                                        <td>{{ $data['name'] }}</td>
                                        <td class="text-end">{{ number_format($data['estimate'], 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($data['actual'], 0) }}</td>
                                        <td class="text-end {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($diff, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA NDOGO (Gospel)</td>
                                    <td class="text-end">{{ number_format($totalGospelEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGospelActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGospelActual - $totalGospelEstimate, 0) }}</td>
                                </tr>

                                <tr class="bg-light">
                                    <th colspan="5" class="py-2"></th>
                                </tr>

                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="bg-secondary text-white">MAPATO YA VIKUNDI NA KAZI ZA UMOJA</th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                    </tr>
                                </thead>
                                @php 
                                    $totalGroupEstimate = 0;
                                    $totalGroupActual = 0;
                                @endphp
                                @foreach($groupIncome as $code => $data)
                                    @php
                                        $diff = $data['actual'] - $data['estimate'];
                                        $totalGroupEstimate += $data['estimate'];
                                        $totalGroupActual += $data['actual'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $code }}</td>
                                        <td>{{ $data['name'] }}</td>
                                        <td class="text-end">{{ number_format($data['estimate'], 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($data['actual'], 0) }}</td>
                                        <td class="text-end {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($diff, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA NDOGO (Groups)</td>
                                    <td class="text-end">{{ number_format($totalGroupEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGroupActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGroupActual - $totalGroupEstimate, 0) }}</td>
                                </tr>

                                <tr class="table-primary fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA KUU MAPATO (Injili + Vikundi)</td>
                                    <td class="text-end">{{ number_format($totalGospelEstimate + $totalGroupEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGospelActual + $totalGroupActual, 0) }}</td>
                                    <td class="text-end">{{ number_format(($totalGospelActual + $totalGroupActual) - ($totalGospelEstimate + $totalGroupEstimate), 0) }}</td>
                                </tr>

                                <tr class="bg-light">
                                    <th colspan="5" class="py-2"></th>
                                </tr>

                                {{-- Building Income --}}
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="bg-secondary text-white text-uppercase">MAPATO YA MAJENGO</th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                    </tr>
                                </thead>
                                @php 
                                    $totalBuildingEstimate = 0;
                                    $totalBuildingActual = 0;
                                @endphp
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="fw-bold text-primary">SALIO ANZIA</td>
                                    <td class="text-end">0</td>
                                    <td class="text-end fw-bold">0</td>
                                    <td class="text-end text-success">0</td>
                                </tr>
                                @foreach($buildingIncome as $code => $data)
                                    @php
                                        $diff = $data['actual'] - $data['estimate'];
                                        $totalBuildingEstimate += $data['estimate'];
                                        $totalBuildingActual += $data['actual'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $code }}</td>
                                        <td>{{ $data['name'] }}</td>
                                        <td class="text-end">{{ number_format($data['estimate'], 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($data['actual'], 0) }}</td>
                                        <td class="text-end {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($diff, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA NDOGO (Majengo)</td>
                                    <td class="text-end">{{ number_format($totalBuildingEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalBuildingActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalBuildingActual - $totalBuildingEstimate, 0) }}</td>
                                </tr>
                                <tr class="table-primary fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA KUU MAJENGO</td>
                                    <td class="text-end">{{ number_format($totalBuildingEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalBuildingActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalBuildingActual - $totalBuildingEstimate, 0) }}</td>
                                </tr>

                                <tr class="bg-light">
                                    <th colspan="5" class="py-2"></th>
                                </tr>

                                {{-- Investment Income --}}
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="bg-secondary text-white text-uppercase">KITEGA UCHUMI</th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                    </tr>
                                </thead>
                                @php 
                                    $totalInvestEstimate = 0;
                                    $totalInvestActual = 0;
                                @endphp
                                <tr>
                                    <td class="text-center"></td>
                                    <td class="fw-bold text-primary">SALIO ANZIA</td>
                                    <td class="text-end">0</td>
                                    <td class="text-end fw-bold">0</td>
                                    <td class="text-end text-success">0</td>
                                </tr>
                                @foreach($investmentIncome as $code => $data)
                                    @php
                                        $diff = $data['actual'] - $data['estimate'];
                                        $totalInvestEstimate += $data['estimate'];
                                        $totalInvestActual += $data['actual'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $code }}</td>
                                        <td>{{ $data['name'] }}</td>
                                        <td class="text-end">{{ number_format($data['estimate'], 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($data['actual'], 0) }}</td>
                                        <td class="text-end {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($diff, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA NDOGO (Investment)</td>
                                    <td class="text-end">{{ number_format($totalInvestEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalInvestActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalInvestActual - $totalInvestEstimate, 0) }}</td>
                                </tr>
                                <tr class="table-primary fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA KUU KITEGA UCHUMI</td>
                                    <td class="text-end">{{ number_format($totalInvestEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalInvestActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalInvestActual - $totalInvestEstimate, 0) }}</td>
                                </tr>

                                <tr class="bg-primary text-white fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA KUU MAPATO YOTE (A)</td>
                                    <td class="text-end">{{ number_format($totalIncomeEst = $totalGospelEstimate + $totalGroupEstimate + $totalBuildingEstimate + $totalInvestEstimate, 0) }}</td>
                                    <td class="text-end text-warning">{{ number_format($totalIncomeAct = $totalGospelActual + $totalGroupActual + $totalBuildingActual + $totalInvestActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalIncomeAct - $totalIncomeEst, 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab B: Matumizi --}}
                <div class="tab-pane fade d-print-block" id="matumizi" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="bg-light">
                                <tr>
                                    <th colspan="2" class="text-uppercase">B. MATUMIZI</th>
                                    <th class="text-center" style="width: 15%">MAKISIO</th>
                                    <th class="text-center" style="width: 15%">HALISI</th>
                                    <th class="text-center" style="width: 15%">ZIDIO/PUNGUFU</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Gospel Expenses (60.xx) --}}
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="bg-secondary text-white text-uppercase">INJILI</th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                    </tr>
                                </thead>
                                @php 
                                    $totalExpGospelEst = 0;
                                    $totalExpGospelAct = 0;
                                @endphp
                                @foreach($gospelExpenses as $code => $data)
                                    @php
                                        $diff = $data['estimate'] - $data['actual']; // For expenses, estimate - actual is surplus
                                        $totalExpGospelEst += $data['estimate'];
                                        $totalExpGospelAct += $data['actual'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $code }}</td>
                                        <td>{{ $data['name'] }}</td>
                                        <td class="text-end">{{ number_format($data['estimate'], 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($data['actual'], 0) }}</td>
                                        <td class="text-end {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($diff, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA NDOGO (Injili)</td>
                                    <td class="text-end">{{ number_format($totalExpGospelEst, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpGospelAct, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpGospelEst - $totalExpGospelAct, 0) }}</td>
                                </tr>
                                <tr class="table-primary fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA KUU (Injili)</td>
                                    <td class="text-end">{{ number_format($totalExpGospelEst, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpGospelAct, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpGospelEst - $totalExpGospelAct, 0) }}</td>
                                </tr>

                                <tr class="bg-light">
                                    <th colspan="5" class="py-2"></th>
                                </tr>

                                {{-- Group Expenses (70.xx) --}}
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="bg-secondary text-white text-uppercase">MATUMIZI YA UMOJA NA IDARA</th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                    </tr>
                                </thead>
                                @php 
                                    $totalExpGroupEst = 0;
                                    $totalExpGroupAct = 0;
                                @endphp
                                @foreach($groupExpenses as $code => $data)
                                    @php
                                        $diff = $data['estimate'] - $data['actual'];
                                        $totalExpGroupEst += $data['estimate'];
                                        $totalExpGroupAct += $data['actual'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $code }}</td>
                                        <td>{{ $data['name'] }}</td>
                                        <td class="text-end">{{ number_format($data['estimate'], 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($data['actual'], 0) }}</td>
                                        <td class="text-end {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($diff, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-info fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA NDOGO (Umoja na Idara)</td>
                                    <td class="text-end">{{ number_format($totalExpGroupEst, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpGroupAct, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpGroupEst - $totalExpGroupAct, 0) }}</td>
                                </tr>
                                <tr class="table-primary fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA KUU (Umoja na Idara)</td>
                                    <td class="text-end">{{ number_format($totalExpGroupEst, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpGroupAct, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpGroupEst - $totalExpGroupAct, 0) }}</td>
                                </tr>

                                <tr class="bg-light">
                                    <th colspan="5" class="py-2"></th>
                                </tr>

                                {{-- Building Expenses (80.xx) --}}
                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2" class="bg-secondary text-white text-uppercase">MAJENGO</th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                        <th class="bg-secondary text-white"></th>
                                    </tr>
                                </thead>
                                @php 
                                    $totalExpBuildEst = 0;
                                    $totalExpBuildAct = 0;
                                @endphp
                                @foreach($buildingExpenses as $code => $data)
                                    @php
                                        $diff = $data['estimate'] - $data['actual'];
                                        $totalExpBuildEst += $data['estimate'];
                                        $totalExpBuildAct += $data['actual'];
                                    @endphp
                                    <tr>
                                        <td class="text-center">{{ $code }}</td>
                                        <td>{{ $data['name'] }}</td>
                                        <td class="text-end">{{ number_format($data['estimate'], 0) }}</td>
                                        <td class="text-end fw-bold">{{ number_format($data['actual'], 0) }}</td>
                                        <td class="text-end {{ $diff >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($diff, 0) }}
                                        </td>
                                    </tr>
                                @endforeach
                                <tr class="table-primary fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA KUU (Majengo)</td>
                                    <td class="text-end">{{ number_format($totalExpBuildEst, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpBuildAct, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpBuildEst - $totalExpBuildAct, 0) }}</td>
                                </tr>

                                <tr class="bg-primary text-white fw-bold">
                                    <td colspan="2" class="text-uppercase">JUMLA KUU MATUMIZI YOTE (B)</td>
                                    <td class="text-end">{{ number_format($totalExpEst = $totalExpGospelEst + $totalExpGroupEst + $totalExpBuildEst, 0) }}</td>
                                    <td class="text-end text-warning">{{ number_format($totalExpAct = $totalExpGospelAct + $totalExpGroupAct + $totalExpBuildAct, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalExpEst - $totalExpAct, 0) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab C: Summary (UFUPISHO) --}}
                <div class="tab-pane fade d-print-block" id="summary" role="tabpanel">
                    <div class="text-center mb-4">
                        <h5 class="fw-bold text-uppercase">UFUPISHO WA MAPATO NA MATUMIZI JAN - DEC {{ $year }}</h5>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="bg-light">
                                <tr>
                                    <th colspan="2">A. MAPATO</th>
                                    <th class="text-center" style="width: 15%">MAELIZO</th>
                                    <th class="text-center" style="width: 15%">MAKISIO</th>
                                    <th class="text-center" style="width: 15%">HALISI</th>
                                    <th class="text-center" style="width: 15%">ZIDIO/PUNGUFU</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="fw-bold bg-light">
                                    <td colspan="2">SALIO ANZIA</td>
                                    <td></td>
                                    <td></td>
                                    <td>0</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td style="width: 40px"></td>
                                    <td>INJILI</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($totalGospelEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGospelActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGospelActual - $totalGospelEstimate, 0) }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>UMOJA NA IDARA</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($totalGroupEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGroupActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalGroupActual - $totalGroupEstimate, 0) }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>MAJENGO</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($totalBuildingEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalBuildingActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalBuildingActual - $totalBuildingEstimate, 0) }}</td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td>KITEGA UCHUMI</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($totalInvestEstimate, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalInvestActual, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalInvestActual - $totalInvestEstimate, 0) }}</td>
                                </tr>
                                <tr class="table-info fw-bold">
                                    <td colspan="2">JUMLA NDOGO</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($totalIncomeEst, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalIncomeAct, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalIncomeAct - $totalIncomeEst, 0) }}</td>
                                </tr>
                                <tr class="table-primary fw-bold">
                                    <td colspan="2">JUMLA KUU</td>
                                    <td></td>
                                    <td class="text-end">{{ number_format($totalIncomeEst, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalIncomeAct, 0) }}</td>
                                    <td class="text-end">{{ number_format($totalIncomeAct - $totalIncomeEst, 0) }}</td>
                                </tr>

                                <tr class="bg-light"><td colspan="6" class="py-2"></td></tr>

                                <thead class="bg-light">
                                    <tr>
                                        <th colspan="2">B. MATUMIZI</th>
                                        <th></th>
                                        <th class="text-center">MAKISIO</th>
                                        <th class="text-center">HALISI</th>
                                        <th class="text-center">ZIDIO/PUNGUFU</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style="width: 40px"></td>
                                        <td>INJILI</td>
                                        <td></td>
                                        <td class="text-end">{{ number_format($totalExpGospelEst, 0) }}</td>
                                        <td class="text-end">{{ number_format($totalExpGospelAct, 0) }}</td>
                                        <td class="text-end">{{ number_format($totalExpGospelEst - $totalExpGospelAct, 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>UMOJA NA IDARA</td>
                                        <td></td>
                                        <td class="text-end">{{ number_format($totalExpGroupEst, 0) }}</td>
                                        <td class="text-end">{{ number_format($totalExpGroupAct, 0) }}</td>
                                        <td class="text-end">{{ number_format($totalExpGroupEst - $totalExpGroupAct, 0) }}</td>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td>MAJENGO</td>
                                        <td></td>
                                        <td class="text-end">{{ number_format($totalExpBuildEst, 0) }}</td>
                                        <td class="text-end">{{ number_format($totalExpBuildAct, 0) }}</td>
                                        <td class="text-end">{{ number_format($totalExpBuildEst - $totalExpBuildAct, 0) }}</td>
                                    </tr>
                                    <tr class="table-primary fw-bold">
                                        <td colspan="2">JUMLA</td>
                                        <td></td>
                                        <td class="text-end">{{ number_format($totalExpEst, 0) }}</td>
                                        <td class="text-end">{{ number_format($totalExpAct, 0) }}</td>
                                        <td class="text-end">{{ number_format($totalExpEst - $totalExpAct, 0) }}</td>
                                    </tr>
                                </tbody>
                        </table>

                        <div class="row mt-4">
                            <div class="col-md-6">
                                <table class="table table-bordered table-sm">
                                    <thead class="bg-primary text-white border-primary">
                                        <tr>
                                            <th colspan="2">SALIO ISHIA 31/12/{{ $year }}</th>
                                            <th class="text-end" style="width: 30%">KIASI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>INJILI</td>
                                            <td style="width: 20px"></td>
                                            <td class="text-end fw-bold">{{ number_format($totalGospelActual - $totalExpGospelAct, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td>VIKUNDI</td>
                                            <td></td>
                                            <td class="text-end fw-bold">{{ number_format($totalGroupActual - $totalExpGroupAct, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td>JUMUIYA</td>
                                            <td></td>
                                            <td class="text-end fw-bold">0</td>
                                        </tr>
                                        <tr>
                                            <td>JENGO</td>
                                            <td></td>
                                            <td class="text-end fw-bold">{{ number_format($totalBuildingActual - $totalExpBuildAct, 0) }}</td>
                                        </tr>
                                        <tr>
                                            <td>WANAWAKE</td>
                                            <td></td>
                                            <td class="text-end fw-bold">0</td>
                                        </tr>
                                        <tr>
                                            <td>KITEGA UCHUMI/SACCOS</td>
                                            <td></td>
                                            <td class="text-end fw-bold">{{ number_format($totalInvestActual, 0) }}</td>
                                        </tr>
                                        <tr class="table-primary fw-bold border-primary">
                                            <td>JUMLA</td>
                                            <td></td>
                                            <td class="text-end">{{ number_format($totalIncomeAct - $totalExpAct, 0) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Signature Section --}}
                        <div class="mt-5 pt-4 border-top">
                            <div class="row text-uppercase small fw-bold">
                                <div class="col-6 mb-5">
                                    <p class="mb-4">IMEANDALIWA NA</p>
                                    <div class="border-bottom border-dark mb-2" style="width: 80%">SAINI..........................................................</div>
                                    <div class="border-bottom border-dark mb-2" style="width: 80%">JINA...........................................................</div>
                                    <div class="border-bottom border-dark mb-2" style="width: 80%">CHEO..........................................................</div>
                                    <div class="border-bottom border-dark mb-2" style="width: 80%">TAREHE......................................................</div>
                                </div>
                                <div class="col-6 mb-5 text-end">
                                    <p class="mb-4 text-start" style="padding-left: 20%">IMEPEPITISHWA NA</p>
                                    <div class="border-bottom border-dark mb-2 ms-auto" style="width: 80%">SAINI..........................................................</div>
                                    <div class="border-bottom border-dark mb-2 ms-auto" style="width: 80%">JINA...........................................................</div>
                                    <div class="border-bottom border-dark mb-2 ms-auto" style="width: 80%">CHEO..........................................................</div>
                                    <div class="border-bottom border-dark mb-2 ms-auto" style="width: 80%">TAREHE......................................................</div>
                                </div>
                                <div class="col-6">
                                    <p class="mb-4">MWENYEKITI WA FEDHA</p>
                                    <div class="border-bottom border-dark mb-2" style="width: 80%">SAINI..........................................................</div>
                                    <div class="border-bottom border-dark mb-2" style="width: 80%">JINA...........................................................</div>
                                    <div class="border-bottom border-dark mb-2" style="width: 80%">TAREHE......................................................</div>
                                </div>
                                <div class="col-6 text-end">
                                    <p class="mb-4 text-start" style="padding-left: 20%">WAKAGUZI WA NDANI</p>
                                    <div class="border-bottom border-dark mb-4 ms-auto" style="width: 80%">1.................................................................</div>
                                    <div class="border-bottom border-dark mb-4 ms-auto" style="width: 80%">2.................................................................</div>
                                    <div class="border-bottom border-dark mb-4 ms-auto" style="width: 80%">3.................................................................</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: auto;
            margin: 10mm 15mm;
        }
        .btn, form, .d-print-none, .sb-topnav, #layoutSidenav_nav, .breadcrumb, .nav-pills {
            display: none !important;
        }
        .tab-pane {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
            page-break-after: always;
        }
        .fade {
            transition: none !important;
        }
        .card { 
            border: none !important; 
            box-shadow: none !important; 
        }
        .container-fluid { 
            padding: 0 !important; 
            margin: 0 !important;
            width: 100% !important;
        }
        #layoutSidenav_content {
            padding: 0 !important;
            margin: 0 !important;
            display: block !important;
        }
        main {
            padding: 0 !important;
        }
        body {
            background-color: white !important;
        }
    }
    .table-sm td, .table-sm th {
        padding: 0.3rem 0.5rem;
        font-size: 0.85rem;
    }
    .bg-secondary {
        background-color: #6c757d !important;
    }
</style>
@endsection
