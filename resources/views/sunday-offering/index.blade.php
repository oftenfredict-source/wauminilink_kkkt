@extends('layouts.index')

@section('title', 'Sunday Collections')

@section('content')
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-0 text-gray-800">Sunday Offering Collections</h1>
            <p class="text-muted">Manage offering sessions, verify submissions, and view history.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('sunday-offering.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Start New Session
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Recent Sessions</h6>
        </div>
        <div class="card-body">
            @if($sessions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-striped" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Mtaa / Campus</th>
                                <th>Lead Elder</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sessions as $session)
                                <tr>
                                    <td>{{ $session->collection_date->format('d M Y') }}</td>
                                    <td>{{ $session->campus->name }}</td>
                                    <td>{{ $session->leadElder->name }}</td>
                                    <td class="fw-bold">TZS {{ number_format($session->total_amount) }}</td>
                                    <td>
                                        @if($session->status === 'draft')
                                            <span class="badge bg-secondary">Draft</span>
                                        @elseif($session->status === 'submitted')
                                            <span class="badge bg-warning text-dark">Submitted</span>
                                        @elseif($session->status === 'received')
                                            <span class="badge bg-success">Received</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('sunday-offering.show', $session->id) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        @if($session->status === 'draft')
                                            <a href="{{ route('sunday-offering.entry', $session->id) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $sessions->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-3x text-gray-300 mb-3"></i>
                    <p>No collection sessions found.</p>
                    <a href="{{ route('sunday-offering.create') }}" class="btn btn-outline-primary">Create First Session</a>
                </div>
            @endif
        </div>
    </div>
@endsection