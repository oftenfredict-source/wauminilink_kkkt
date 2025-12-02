<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-header d-md-none d-flex justify-content-between align-items-center" style="cursor: pointer;" onclick="toggleFilterSection('failedLoginFilters')">
        <h6 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h6>
        <i class="fas fa-chevron-down" id="failedLoginFiltersIcon"></i>
    </div>
    <div class="card-body" id="failedLoginFilters">
        <form method="GET" action="{{ route('admin.logs') }}" class="row g-3">
            <input type="hidden" name="type" value="failed-login">
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">Email/Username</label>
                <input type="text" name="email" class="form-control" value="{{ request('email') }}" placeholder="Search by email">
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <label class="form-label">IP Address</label>
                <input type="text" name="ip_address" class="form-control" value="{{ request('ip_address') }}" placeholder="Search by IP">
            </div>
            <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>
            <div class="col-12 col-md-6 col-lg-2">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>
            <div class="col-12 col-md-6 col-lg-1">
                <label class="form-label">&nbsp;</label>
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="blocked_only" id="blockedOnly" value="1" {{ request('blocked_only') ? 'checked' : '' }}>
                    <label class="form-check-label" for="blockedOnly">Blocked Only</label>
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i><span class="d-none d-sm-inline">Filter</span><span class="d-sm-none">Filter</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Failed Login Logs Table -->
<div class="card shadow-sm">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Date/Time</th>
                        <th>Email/Username</th>
                        <th>IP Address</th>
                        <th>MAC Address</th>
                        <th>Device</th>
                        <th>Failure Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $log->email }}</td>
                            <td>
                                <code>{{ $log->ip_address }}</code>
                                @if(in_array($log->ip_address, $blockedIps ?? []))
                                    <span class="badge bg-danger ms-1">Blocked</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $log->mac_address ?? 'Not available' }}</small>
                            </td>
                            <td>
                                <small>
                                    {{ $log->device_type ?? '-' }}<br>
                                    {{ $log->browser ?? '-' }}<br>
                                    {{ $log->os ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge bg-warning text-dark">{{ $log->failure_reason }}</span>
                            </td>
                            <td>
                                @if($log->ip_blocked)
                                    <span class="badge bg-danger">Blocked</span>
                                @else
                                    <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>
                                @if(in_array($log->ip_address, $blockedIps ?? []))
                                    <button class="btn btn-sm btn-success" onclick="unblockIp('{{ $log->ip_address }}')">
                                        <i class="fas fa-unlock"></i> Unblock
                                    </button>
                                @else
                                    <button class="btn btn-sm btn-danger" onclick="blockIp('{{ $log->ip_address }}', '{{ $log->failure_reason }}')">
                                        <i class="fas fa-ban"></i> Block IP
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No failed login attempts found.</td>
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
                <nav aria-label="Failed login logs pagination">
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


<script>
function blockIp(ipAddress, reason) {
    // Show confirmation dialog with SweetAlert
    Swal.fire({
        title: 'Block IP Address?',
        html: `
            <div class="text-start">
                <p class="mb-3">Are you sure you want to block the following IP address?</p>
                <div class="alert alert-warning mb-3">
                    <strong>IP Address:</strong> <code>${ipAddress}</code>
                </div>
                <div class="mb-3">
                    <label for="swal-reason" class="form-label"><strong>Reason for blocking:</strong></label>
                    <textarea id="swal-reason" class="form-control" rows="3" placeholder="Enter reason for blocking this IP address...">${reason || 'Blocked due to failed login attempts'}</textarea>
                </div>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>This IP address will be prevented from accessing the system. You can unblock it later if needed.</small>
                </div>
            </div>
        `,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-ban me-1"></i>Block IP',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        reverseButtons: true,
        focusConfirm: false,
        preConfirm: () => {
            const reason = document.getElementById('swal-reason').value.trim();
            if (!reason) {
                Swal.showValidationMessage('Please provide a reason for blocking this IP address.');
                return false;
            }
            return {
                ip_address: ipAddress,
                reason: reason
            };
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            // Show loading state
            Swal.fire({
                title: 'Blocking IP...',
                text: 'Please wait while we block the IP address',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make the API call
            fetch('{{ route("admin.logs.block-ip") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(result.value)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'IP Blocked!',
                        html: `
                            <p>${data.message}</p>
                            <p class="text-muted small mt-2">The IP address <code>${ipAddress}</code> has been blocked successfully.</p>
                        `,
                        confirmButtonColor: '#198754',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Block Failed',
                        text: data.message || 'Failed to block IP address. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while blocking the IP address. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

function unblockIp(ipAddress) {
    // Show confirmation dialog with SweetAlert
    Swal.fire({
        title: 'Unblock IP Address?',
        html: `
            <div class="text-start">
                <p class="mb-3">Are you sure you want to unblock the following IP address?</p>
                <div class="alert alert-info mb-3">
                    <strong>IP Address:</strong> <code>${ipAddress}</code>
                </div>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <small>This will allow the IP address to access the system again.</small>
                </div>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-unlock me-1"></i>Yes, Unblock',
        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
        confirmButtonColor: '#198754',
        cancelButtonColor: '#6c757d',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading state
            Swal.fire({
                title: 'Unblocking IP...',
                text: 'Please wait while we unblock the IP address',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Make the API call
            fetch('{{ route("admin.logs.unblock-ip") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    ip_address: ipAddress
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'IP Unblocked!',
                        html: `
                            <p>${data.message}</p>
                            <p class="text-muted small mt-2">The IP address <code>${ipAddress}</code> can now access the system again.</p>
                        `,
                        confirmButtonColor: '#198754',
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Unblock Failed',
                        text: data.message || 'Failed to unblock IP address. Please try again.',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while unblocking the IP address. Please try again.',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}
</script>

