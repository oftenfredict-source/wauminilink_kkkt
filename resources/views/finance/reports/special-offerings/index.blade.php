@extends('layouts.index')

@section('title', 'Special Offering Member Report')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <div>
                            <h4 class="fw-bold mb-1">Special Offering Member Report</h4>
                            <p class="text-muted mb-0">Summary of contributions per member for the selected year and offering type.</p>
                        </div>
                        <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary rounded-pill">
                            <i class="fas fa-arrow-left me-2"></i>Back to Reports
                        </a>
                    </div>

                    <!-- Filters -->
                    <form action="{{ route('reports.special-offerings') }}" method="GET" class="row g-3 bg-light p-3 rounded-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">Select Year</label>
                            <select name="year" class="form-select shadow-sm">
                                @for($y = date('Y'); $y >= 2024; $y--)
                                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">Offering Type</label>
                            <select name="offering_type" class="form-select shadow-sm">
                                @foreach($availableTypes as $type)
                                    <option value="{{ $type }}" {{ $offeringType == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100 shadow-sm">
                                <i class="fas fa-filter me-2"></i>Filter Results
                            </button>
                        </div>
                    </form>

                    <!-- Stats Table -->
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Member Name</th>
                                    <th>Envelope #</th>
                                    <th>Community</th>
                                    <th>Total Given</th>
                                    <th>Payments</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($memberStats as $stat)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $stat['member_name'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">{{ $stat['envelope_number'] ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $stat['community_name'] }}</td>
                                    <td>
                                        <span class="text-success fw-bold">TZS {{ number_format($stat['total_amount'], 0) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info rounded-pill">{{ $stat['transaction_count'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('reports.member-grid-report', [$stat['member_id'], 'year' => $year, 'type' => $offeringType]) }}" 
                                           class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="fas fa-th me-2"></i>View Grid Report
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-folder-open fa-3x mb-3"></i>
                                            <p>No records found for the selected filter.</p>
                                        </div>
                                    </td>
                                </tr>
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
