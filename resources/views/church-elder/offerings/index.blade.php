@extends('layouts.index')

@section('title', 'My Collected Offerings')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">My Collected Offerings</h1>
            <p class="text-muted">History of all offerings you have collected and submitted.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('church-elder.offerings.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Collect New Offering
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Offering History</h6>
        </div>
        <div class="card-body">
            @if($offerings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Context</th>
                                <th>Location Name</th>
                                <th>Type</th>
                                <th>Amount (TZS)</th>
                                <th>Status</th>
                                <th>Date Submitted</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($offerings as $offering)
                                <tr>
                                    <td>{{ $offering->offering_date->format('d M Y') }}</td>
                                    <td>
                                        @if(isset($offering->community_id))
                                            <span class="badge bg-primary">Community</span>
                                        @else
                                            <span class="badge bg-success">Campus</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($offering->community_id))
                                            {{ $offering->community->name ?? 'N/A' }}
                                        @else
                                            {{ $offering->campus->name ?? 'N/A' }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($offering->offering_type))
                                            {{ ucwords(str_replace('_', ' ', $offering->offering_type)) }}
                                        @else
                                            Main Service
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ number_format($offering->amount) }}</td>
                                    <td>
                                        <span
                                            class="badge bg-{{ $offering->status === 'completed' ? 'success' : ($offering->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucwords(str_replace('_', ' ', $offering->status)) }}
                                        </span>
                                    </td>
                                    <td>{{ $offering->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-hand-holding-usd fa-4x text-gray-300 mb-3"></i>
                    <p class="mb-0">You haven't collected any offerings yet.</p>
                    <a href="{{ route('church-elder.offerings.create') }}" class="btn btn-primary mt-3">Start Collecting</a>
                </div>
            @endif
        </div>
    </div>
@endsection