@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h1 class="h3 mb-0"><i class="fas fa-fire me-2 text-danger"></i>Candle Inventory Tracking</h1>
                                <p class="text-muted mb-0">Full history of candle purchases and distributions to campuses</p>
                            </div>
                            <a href="{{ route('dashboard.pastor') }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-1"></i>Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
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
                        <small>Total candles bought by Parish Workers</small>
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

        <!-- Filter Card -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h5 class="mb-0"><i class="fas fa-filter me-2 text-primary"></i>Filter Transactions</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('pastor.parish-worker.candles.index') }}" method="GET" class="row g-3">
                            <div class="col-md-3">
                                <label for="campus_id" class="form-label small fw-bold text-muted">Campus (Target)</label>
                                <select name="campus_id" id="campus_id" class="form-select form-select-sm">
                                    <option value="">All Locations</option>
                                    @foreach($campuses as $campus)
                                        <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                            {{ $campus->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="action_type" class="form-label small fw-bold text-muted">Action Type</label>
                                <select name="action_type" id="action_type" class="form-select form-select-sm">
                                    <option value="">All Actions</option>
                                    <option value="purchase" {{ request('action_type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                                    <option value="distribution" {{ request('action_type') == 'distribution' ? 'selected' : '' }}>Distribution</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label for="date_from" class="form-label small fw-bold text-muted">From</label>
                                <input type="date" name="date_from" id="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label for="date_to" class="form-label small fw-bold text-muted">To</label>
                                <input type="date" name="date_to" id="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="btn-group w-100 btn-group-sm">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i>Apply
                                    </button>
                                    <a href="{{ route('pastor.parish-worker.candles.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-undo"></i>
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Date</th>
                                        <th>Parish Worker</th>
                                        <th>Action Type</th>
                                        <th>Quantity</th>
                                        <th>Location / Target</th>
                                        <th>Received By</th>
                                        <th class="text-end pe-4">Cost</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($actions as $action)
                                        <tr>
                                            <td class="ps-4">{{ $action->action_date->format('M d, Y') }}</td>
                                            <td>
                                                <div class="fw-bold">{{ $action->user->name }}</div>
                                            </td>
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
                                            <td colspan="7" class="text-center py-5">
                                                <i class="fas fa-fire fa-3x mb-3 d-block text-muted"></i>
                                                No candle transactions found.
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
        </div>
    </div>
@endsection
