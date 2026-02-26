@extends('layouts.index')

@section('content')
    <style>
        .badge.badge-success {
            background-color: #198754 !important;
            color: white !important;
        }

        .badge.badge-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }

        .badge.badge-warning {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .badge.badge-secondary {
            background-color: #6c757d !important;
            color: white !important;
        }

        .badge.badge-info {
            background-color: #0dcaf0 !important;
            color: white !important;
        }

        .badge {
            display: inline-block !important;
            padding: 0.35em 0.65em !important;
            font-weight: 600 !important;
            border-radius: 0.25rem !important;
        }

        @media (max-width: 768px) {
            .dashboard-header h5 {
                font-size: 1rem !important;
            }

            .table {
                font-size: 0.85rem;
                min-width: 900px;
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
                                <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-2"
                                    style="width:48px; height:48px; background:rgba(255,255,255,.15);">
                                    <i class="fas fa-key text-white"></i>
                                </div>
                                <div class="lh-sm">
                                    <h5 class="mb-0 fw-semibold" style="color: white !important;">OTP Management</h5>
                                    <small style="color: white !important;">Monitor and manage One-Time Passwords</small>
                                </div>
                            </div>
                            <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter me-2"></i>Filters</h6>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('admin.otp-management') }}" class="row g-3">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Email, Name or IP..."
                            value="{{ request('search') }}">
                    </div>
                    <div class="col-12 col-md-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="used" {{ request('status') == 'used' ? 'selected' : '' }}>Used</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-5 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search me-1"></i> Apply
                        </button>
                        <a href="{{ route('admin.otp-management') }}" class="btn btn-secondary">
                            <i class="fas fa-times me-1"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- OTP Table -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">OTP Records ({{ $otps->total() }} total)</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>OTP Code</th>
                                <th>Status</th>
                                <th>IP Address</th>
                                <th>Expiration</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($otps as $otp)
                                <tr>
                                    <td>
                                        @if($otp->user)
                                            <strong>{{ $otp->user->name }}</strong><br>
                                            <small class="text-muted">{{ $otp->user->email }}</small>
                                        @else
                                            <strong>{{ $otp->email }}</strong><br>
                                            <small class="text-muted">No associated user</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ substr($otp->otp_code, 0, 2) . '****' }}</code>
                                        <button class="btn btn-link btn-sm p-0 ms-1"
                                            onclick="Swal.fire('OTP Code', 'The complete code is: {{ $otp->otp_code }}', 'info')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                    <td>
                                        @if($otp->is_used)
                                            <span class="badge badge-success">Used</span>
                                            <br><small class="text-muted">At: {{ $otp->used_at }}</small>
                                        @elseif($otp->expires_at < now())
                                            <span class="badge badge-danger">Expired</span>
                                        @else
                                            <span class="badge badge-info">Active</span>
                                            <br><small
                                                class="text-info">{{ \Carbon\Carbon::parse($otp->expires_at)->diffForHumans() }}</small>
                                        @endif
                                        @if($otp->attempts > 0)
                                            <br><small class="text-warning">Attempts: {{ $otp->attempts }}</small>
                                        @endif
                                    </td>
                                    <td><small>{{ $otp->ip_address }}</small></td>
                                    <td><small>{{ \Carbon\Carbon::parse($otp->expires_at)->format('Y-m-d H:i') }}</small></td>
                                    <td><small>{{ $otp->created_at->format('Y-m-d H:i') }}</small></td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteOtp('{{ $otp->id }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No OTP records found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $otps->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        function deleteOtp(id) {
            Swal.fire({
                title: 'Delete OTP Record?',
                text: "This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleting...',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch(`{{ url('/admin/otp-management') }}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Deleted!', data.message, 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('Error!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            Swal.fire('Error!', 'An unexpected error occurred.', 'error');
                        });
                }
            });
        }
    </script>
@endsection