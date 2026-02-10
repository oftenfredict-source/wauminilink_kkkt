@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
            <h1 class="h3 mb-0">My Activity Records (Rekodi Zangu za Shughuli)</h1>
            <a href="{{ route('parish-worker.activities.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i>Record New Activity
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Date</th>
                                <th>Activity Type</th>
                                <th>Title</th>
                                <th>Status</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activities as $activity)
                                <tr>
                                    <td class="ps-4">{{ $activity->activity_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $activity->activity_type_display }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $activity->title }}</div>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 300px;">
                                            {{ $activity->description }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->status === 'completed' ? 'success' : 'warning' }}">
                                            {{ ucfirst($activity->status) }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button class="btn btn-sm btn-outline-secondary" title="View Details"
                                            data-bs-toggle="modal" data-bs-target="#activityModal{{ $activity->id }}">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        <!-- Multi-purpose Modal for Details -->
                                        <div class="modal fade" id="activityModal{{ $activity->id }}" tabindex="-1"
                                            aria-hidden="true text-start">
                                            <div class="modal-dialog">
                                                <div class="modal-content text-start">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">{{ $activity->title }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body text-start">
                                                        <p><strong>Type:</strong> {{ $activity->activity_type_display }}</p>
                                                        <p><strong>Date:</strong>
                                                            {{ $activity->activity_date->format('F d, Y') }}</p>
                                                        <p><strong>Status:</strong> <span
                                                                class="badge bg-{{ $activity->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($activity->status) }}</span>
                                                        </p>
                                                        <hr>
                                                        <p><strong>Description:</strong></p>
                                                        <p class="text-muted">{{ $activity->description }}</p>
                                                        @if($activity->notes)
                                                            <hr>
                                                            <p><strong>Additional Notes:</strong></p>
                                                            <p class="text-muted">{{ $activity->notes }}</p>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-clipboard fa-3x mb-3"></i>
                                        <p>No activity records found.</p>
                                        <a href="{{ route('parish-worker.activities.create') }}"
                                            class="btn btn-sm btn-primary">Record Your First Activity</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($activities->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    {{ $activities->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection