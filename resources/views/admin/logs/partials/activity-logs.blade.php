<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-md-none d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="toggleFilterSection('activityFilters')">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
        <i class="fas fa-chevron-down" id="activityFiltersIcon"></i>
    </div>
    <div class="card-body" id="activityFilters">
        <form method="GET" action="{{ route('admin.logs') }}" class="row g-3">
            <input type="hidden" name="type" value="activity">
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">User</label>
                <select name="user_id" class="form-select">
                    <option value="">All Users</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Action</label>
                <select name="action" class="form-select">
                    <option value="">All Actions</option>
                    @foreach($actions as $action)
                        <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                            {{ ucfirst($action) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 col-md-6 col-lg-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i><span class="d-none d-sm-inline">Filter</span><span class="d-sm-none">Filter</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Activity Logs Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Route</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                            <td><span class="badge bg-info">{{ ucfirst($log->action) }}</span></td>
                            <td>{{ $log->description }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td><small class="text-muted">{{ $log->route }}</small></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No activity logs found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small">
                    Showing {{ $logs->firstItem() }} to {{ $logs->lastItem() }} of {{ $logs->total() }} entries
                </div>
                <nav aria-label="Activity logs pagination">
                    <ul class="pagination mb-0">
                        {{-- Previous Page Link --}}
                        @if ($logs->onFirstPage())
                            <li class="page-item disabled" aria-disabled="true" aria-label="Previous">
                                <span class="page-link" aria-hidden="true">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </span>
                            </li>
                        @else
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->appends(request()->except('page'))->previousPageUrl() }}" rel="prev" aria-label="Previous">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            </li>
                        @endif

                        {{-- Pagination Elements --}}
                        @php
                            $currentPage = $logs->currentPage();
                            $lastPage = $logs->lastPage();
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
                                <a class="page-link" href="{{ $logs->appends(request()->except('page'))->url(1) }}">1</a>
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
                                    <a class="page-link" href="{{ $logs->appends(request()->except('page'))->url($page) }}">{{ $page }}</a>
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
                                <a class="page-link" href="{{ $logs->appends(request()->except('page'))->url($lastPage) }}">{{ $lastPage }}</a>
                            </li>
                        @endif

                        {{-- Next Page Link --}}
                        @if ($logs->hasMorePages())
                            <li class="page-item">
                                <a class="page-link" href="{{ $logs->appends(request()->except('page'))->nextPageUrl() }}" rel="next" aria-label="Next">
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
                Showing {{ $logs->count() }} of {{ $logs->total() }} entries
            </div>
        </div>
        @endif
    </div>
</div>

