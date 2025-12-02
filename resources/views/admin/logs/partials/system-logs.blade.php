<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-md-none d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="toggleFilterSection('systemFilters')">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
        <i class="fas fa-chevron-down" id="systemFiltersIcon"></i>
    </div>
    <div class="card-body" id="systemFilters">
        <form method="GET" action="{{ route('admin.logs') }}" class="row g-3">
            <input type="hidden" name="type" value="system">
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
            <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label">Level</label>
                <select name="level" class="form-select">
                    <option value="">All Levels</option>
                    @if(isset($levels) && $levels->count() > 0)
                        @foreach($levels as $level)
                            <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>
                                {{ ucfirst($level) }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="">All Categories</option>
                    @if(isset($categories) && $categories->count() > 0)
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ ucfirst($category) }}
                            </option>
                        @endforeach
                    @endif
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
            <div class="col-12 col-md-6 col-lg-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i><span class="d-none d-sm-inline">Filter</span><span class="d-sm-none">Filter</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- System Logs Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>User</th>
                        <th>Level</th>
                        <th>Category</th>
                        <th>Message</th>
                        <th>Device</th>
                        <th>IP Address</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->user ? $log->user->name : 'System' }}</td>
                            <td>
                                <span class="badge bg-{{ $log->level === 'error' ? 'danger' : ($log->level === 'warning' ? 'warning' : 'info') }}">
                                    {{ ucfirst($log->level) }}
                                </span>
                            </td>
                            <td>{{ $log->category ? ucfirst($log->category) : '-' }}</td>
                            <td>{{ Str::limit($log->message, 50) }}</td>
                            <td>
                                <small>
                                    {{ $log->device_type ?? '-' }}<br>
                                    {{ $log->browser ?? '-' }}<br>
                                    {{ $log->os ?? '-' }}
                                </small>
                            </td>
                            <td>{{ $log->ip_address }}</td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="showDeviceDetails({{ $log->id }})">
                                    <i class="fas fa-info-circle"></i> Details
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No system logs found.</td>
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
                <nav aria-label="System logs pagination">
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

<!-- Device Details Modal -->
<div class="modal fade" id="deviceDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Device Properties</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="deviceDetailsContent">
                <!-- Content will be loaded via AJAX -->
            </div>
        </div>
    </div>
</div>

<script>
function showDeviceDetails(logId) {
    // This would typically fetch device details via AJAX
    // For now, we'll show a placeholder
    const modal = new bootstrap.Modal(document.getElementById('deviceDetailsModal'));
    document.getElementById('deviceDetailsContent').innerHTML = '<p>Loading device details...</p>';
    modal.show();
    
    // Fetch device details via AJAX
    fetch(`{{ url('/admin/system-logs') }}/${logId}/device-details`)
        .then(response => response.json())
        .then(data => {
            let html = '<div class="row">';
            html += '<div class="col-md-6"><strong>Device Type:</strong> ' + (data.device_type || '-') + '</div>';
            html += '<div class="col-md-6"><strong>Device Name:</strong> ' + (data.device_name || '-') + '</div>';
            html += '<div class="col-md-6"><strong>Browser:</strong> ' + (data.browser || '-') + '</div>';
            html += '<div class="col-md-6"><strong>OS:</strong> ' + (data.os || '-') + '</div>';
            html += '<div class="col-md-6"><strong>MAC Address:</strong> ' + (data.mac_address || 'Not available') + '</div>';
            html += '<div class="col-md-6"><strong>Screen Resolution:</strong> ' + (data.screen_resolution || '-') + '</div>';
            html += '<div class="col-md-6"><strong>Timezone:</strong> ' + (data.timezone || '-') + '</div>';
            html += '<div class="col-md-6"><strong>Language:</strong> ' + (data.language || '-') + '</div>';
            if (data.device_properties) {
                html += '<div class="col-12 mt-3"><strong>Additional Properties:</strong><pre>' + JSON.stringify(data.device_properties, null, 2) + '</pre></div>';
            }
            html += '</div>';
            document.getElementById('deviceDetailsContent').innerHTML = html;
        })
        .catch(error => {
            document.getElementById('deviceDetailsContent').innerHTML = '<p class="text-danger">Error loading device details.</p>';
        });
}
</script>

