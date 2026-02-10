@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <h1 class="h3 mb-0"><i class="fas fa-fire me-2 text-danger"></i>Candle Inventory (Inventory ya Kandili)</h1>
            <a href="{{ route('parish-worker.candles.create') }}" class="btn btn-danger">
                <i class="fas fa-plus-circle me-2"></i>Record Purchase/Distribution
            </a>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm bg-danger text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Current Stock (On Hand)</h6>
                        <h2 class="mb-0">{{ number_format($onHand) }}</h2>
                        <small>Total candles available for distribution</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm bg-success text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Total Purchased</h6>
                        <h2 class="mb-0">{{ number_format($purchased) }}</h2>
                        <small>Total candles bought to date</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card border-0 shadow-sm bg-primary text-white">
                    <div class="card-body">
                        <h6 class="text-white-50">Total Distributed</h6>
                        <h2 class="mb-0">{{ number_format($distributed) }}</h2>
                        <small>Total candles sent to campuses</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0"><i class="fas fa-history me-2 text-muted"></i>Recent Transactions</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Action Type</th>
                                <th>Quantity</th>
                                <th>Campus / Target</th>
                                <th>Received By</th>
                                <th class="text-end pe-4">Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($actions as $action)
                                <tr>
                                    <td class="ps-4">{{ $action->action_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-{{ $action->action_type === 'purchase' ? 'success' : 'primary' }}">
                                            {{ ucfirst($action->action_type) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold">{{ number_format($action->quantity) }}</td>
                                    <td>{{ $action->campus->name ?? ($action->action_type === 'purchase' ? 'Central Stock' : '-') }}</td>
                                    <td>{{ $action->received_by ?? '-' }}</td>
                                    <td class="text-end pe-4">
                                        @if($action->cost)
                                            TSh {{ number_format($action->cost) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fas fa-fire fa-3x mb-3"></i>
                                        <p>No transactions recorded yet.</p>
                                        <a href="{{ route('parish-worker.candles.create') }}"
                                            class="btn btn-sm btn-outline-danger">Record First Entry</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($actions->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $actions->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
