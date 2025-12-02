@extends('layouts.index')

@section('content')
<style>
    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 10px !important;
            padding-right: 10px !important;
        }

        .dashboard-header {
            margin-bottom: 15px !important;
        }

        .dashboard-header .card-body {
            padding: 12px 15px !important;
        }

        .dashboard-header .rounded-circle {
            width: 40px !important;
            height: 40px !important;
            min-width: 40px !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 1rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 12px !important;
        }

        .dashboard-header h5 {
            font-size: 1.1rem !important;
            line-height: 1.3 !important;
            margin-bottom: 2px !important;
        }

        .dashboard-header small {
            font-size: 0.8rem !important;
            line-height: 1.2 !important;
            display: block !important;
        }

        .dashboard-header .btn {
            margin-top: 12px !important;
            padding: 8px 16px !important;
            font-size: 0.875rem !important;
            border-radius: 6px !important;
            white-space: nowrap !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            align-items: flex-start !important;
        }

        .dashboard-header .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
        }

        /* Table responsive */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            display: block;
            width: 100%;
        }

        .table {
            font-size: 0.85rem;
            min-width: 800px;
        }

        .table th,
        .table td {
            padding: 8px 4px !important;
        }

        .card-header {
            padding: 10px 15px !important;
        }

        .card-body {
            padding: 15px !important;
        }

        /* Pagination styling */
        .card-footer {
            padding: 0.75rem 1rem !important;
        }

        .pagination {
            margin: 0 !important;
        }

        .pagination .page-link {
            padding: 0.375rem 0.75rem !important;
            font-size: 0.875rem !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .dashboard-header {
            margin-bottom: 10px !important;
            border-radius: 10px !important;
        }

        .dashboard-header .card-body {
            padding: 10px 12px !important;
        }

        .dashboard-header .rounded-circle {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.9rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 10px !important;
        }

        .dashboard-header h5 {
            font-size: 0.95rem !important;
            line-height: 1.25 !important;
            margin-bottom: 1px !important;
        }

        .dashboard-header small {
            font-size: 0.72rem !important;
            line-height: 1.15 !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            width: auto !important;
            min-width: fit-content !important;
            padding: 7px 12px !important;
            font-size: 0.8rem !important;
            flex: 0 0 auto !important;
        }

        /* Stack on very small screens */
        @media (max-width: 400px) {
            .dashboard-header .d-flex.justify-content-between {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .dashboard-header .btn {
                width: 100% !important;
                margin-top: 8px !important;
            }
        }

        .table {
            font-size: 0.75rem;
            min-width: 700px;
        }

        .table th,
        .table td {
            padding: 6px 3px !important;
        }
    }
</style>

<div class="container-fluid px-4">
    <div class="row mb-3">
        <div class="col-12">
            <div class="card border-0 shadow-sm dashboard-header" style="background:#17082d;">
                <div class="card-body text-white py-2 px-3">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-2" style="width:48px; height:48px; background:rgba(255,255,255,.15);">
                                <i class="fas fa-user text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">User Activity: {{ $user->name }}</h5>
                                <small style="color: white !important;">{{ $user->email }} - {{ ucfirst($user->role) }}</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.users') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Back to Users
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Activity Logs ({{ $activities->total() }} total)</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>Route</th>
                            <th>IP Address</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activities as $activity)
                        <tr>
                            <td>{{ $activity->id }}</td>
                            <td>
                                <span class="badge badge-{{ $activity->action === 'create' ? 'success' : ($activity->action === 'delete' ? 'danger' : ($activity->action === 'approve' ? 'warning' : 'info')) }}">
                                    {{ ucfirst($activity->action) }}
                                </span>
                            </td>
                            <td>{{ $activity->description }}</td>
                            <td><small>{{ $activity->route ?? 'N/A' }}</small></td>
                            <td><small>{{ $activity->ip_address }}</small></td>
                            <td>
                                <small>{{ $activity->created_at->format('Y-m-d H:i:s') }}</small><br>
                                <small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No activities found for this user</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($activities->hasPages())
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="text-muted small">
                        Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} entries
                    </div>
                    <nav aria-label="Activity logs pagination">
                        <ul class="pagination mb-0">
                            {{-- Previous Page Link --}}
                            @if ($activities->onFirstPage())
                                <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                    <span class="page-link" aria-hidden="true">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </span>
                                </li>
                            @else
                                <li class="page-item">
                                    <a class="page-link" href="{{ $activities->appends(request()->except('page'))->previousPageUrl() }}" rel="prev" aria-label="Previous">
                                        <i class="fas fa-chevron-left"></i> Previous
                                    </a>
                                </li>
                            @endif

                            {{-- Pagination Elements --}}
                            @php
                                $currentPage = $activities->currentPage();
                                $lastPage = $activities->lastPage();
                                $startPage = max(1, $currentPage - 2);
                                $endPage = min($lastPage, $currentPage + 2);
                                
                                // Show first page if not in range
                                if ($startPage > 1) {
                                    $endPage = min($lastPage, $startPage + 4);
                                }
                                
                                // Show last page if not in range
                                if ($endPage < $lastPage) {
                                    $startPage = max(1, $endPage - 4);
                                }
                            @endphp

                            {{-- First page --}}
                            @if ($startPage > 1)
                                <li class="page-item">
                                    <a class="page-link" href="{{ $activities->appends(request()->except('page'))->url(1) }}">1</a>
                                </li>
                                @if ($startPage > 2)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                            @endif

                            {{-- Page numbers around current page --}}
                            @for ($page = $startPage; $page <= $endPage; $page++)
                                @if ($page == $currentPage)
                                    <li class="page-item active" aria-current="page">
                                        <span class="page-link">{{ $page }}</span>
                                    </li>
                                @else
                                    <li class="page-item">
                                        <a class="page-link" href="{{ $activities->appends(request()->except('page'))->url($page) }}">{{ $page }}</a>
                                    </li>
                                @endif
                            @endfor

                            {{-- Last page --}}
                            @if ($endPage < $lastPage)
                                @if ($endPage < $lastPage - 1)
                                    <li class="page-item disabled">
                                        <span class="page-link">...</span>
                                    </li>
                                @endif
                                <li class="page-item">
                                    <a class="page-link" href="{{ $activities->appends(request()->except('page'))->url($lastPage) }}">{{ $lastPage }}</a>
                                </li>
                            @endif

                            {{-- Next Page Link --}}
                            @if ($activities->hasMorePages())
                                <li class="page-item">
                                    <a class="page-link" href="{{ $activities->appends(request()->except('page'))->nextPageUrl() }}" rel="next" aria-label="Next">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            @else
                                <li class="page-item disabled" aria-disabled="true" aria-label="Next">
                                    <span class="page-link" aria-hidden="true">
                                        Next <i class="fas fa-chevron-right"></i>
                                    </span>
                                </li>
                            @endif
                        </ul>
                    </nav>
                </div>
            </div>
            @else
            <div class="card-footer">
                <div class="text-muted small">
                    Showing {{ $activities->total() }} entries
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

