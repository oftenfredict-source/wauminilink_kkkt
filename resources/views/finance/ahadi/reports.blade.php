@extends('layouts.index')

@section('content')
<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white border-bottom p-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0"><i class="fas fa-chart-pie me-2 text-primary"></i>Ahadi kwa Bwana Reports</h5>
            <div class="d-flex gap-2">
                <form method="GET" class="d-flex gap-2">
                    <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                        @foreach($availableYears as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>Year {{ $y }}</option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('finance.ahadi-pledges.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-list me-1"></i> Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Total Pledges</h6>
                    <h3 class="mb-0">{{ array_sum($statusCounts) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Fully Fulfilled</h6>
                    <h3 class="mb-0">{{ $statusCounts['fully_fulfilled'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <h6 class="text-dark-50">Partially Fulfilled</h6>
                    <h3 class="mb-0">{{ $statusCounts['partially_fulfilled'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm bg-secondary text-white">
                <div class="card-body">
                    <h6 class="text-white-50">Still Promised</h6>
                    <h3 class="mb-0">{{ $statusCounts['promised'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Item Type Summary -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Summary by Item Type</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Item Type</th>
                                    <th>Pledges</th>
                                    <th>Total Promised</th>
                                    <th>Total Fulfilled</th>
                                    <th>Progress</th>
                                    <th class="text-end pe-3">Est. Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($itemSummaries as $item)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $item->item_type }}</td>
                                    <td>{{ $item->total_pledges }}</td>
                                    <td>{{ number_format($item->total_promised, 2) }} {{ $item->unit }}</td>
                                    <td>{{ number_format($item->total_fulfilled, 2) }} {{ $item->unit }}</td>
                                    <td>
                                        @php 
                                            $pct = $item->total_promised > 0 ? ($item->total_fulfilled / $item->total_promised) * 100 : 0;
                                        @endphp
                                        <div class="progress" style="height: 10px; width: 100px;">
                                            <div class="progress-bar {{ $pct >= 100 ? 'bg-success' : 'bg-primary' }}" 
                                                 role="progressbar" style="width: {{ min($pct, 100) }}%"></div>
                                        </div>
                                        <small>{{ round($pct) }}%</small>
                                    </td>
                                    <td class="text-end pe-3">TZS {{ number_format($item->total_value, 0) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-4 text-muted">No data available for {{ $year }}</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Community Summary -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold">Summary by Jumuiya (Fellowship)</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-3">Jumuiya</th>
                                    <th>Pledges</th>
                                    <th>Fulfilled</th>
                                    <th class="text-end pe-3">Est. Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($communitySummaries as $comm)
                                <tr>
                                    <td class="ps-3 fw-bold">{{ $comm->community_name }}</td>
                                    <td>{{ $comm->total_pledges }}</td>
                                    <td>
                                        <span class="text-success" title="Fully Fulfilled">{{ $comm->fully_fulfilled_count }}</span> /
                                        <span class="text-warning" title="Partially Fulfilled">{{ $comm->partially_fulfilled_count }}</span> /
                                        <span class="text-muted" title="Promised">{{ $comm->promised_count }}</span>
                                    </td>
                                    <td class="text-end pe-3">TZS {{ number_format($comm->total_estimated_value, 0) }}</td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted">No community data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
