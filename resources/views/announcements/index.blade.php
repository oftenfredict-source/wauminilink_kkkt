@extends('layouts.index')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm dashboard-header" style="background:white;">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center border border-primary border-2"
                                    style="width:48px; height:48px; background:rgba(0,123,255,.1);">
                                    <i class="fas fa-bullhorn text-primary"></i>
                                </div>
                                <div class="lh-sm">
                                    <h5 class="mb-0 fw-semibold text-dark">Announcements</h5>
                                    <small class="text-muted">Manage church announcements</small>
                                </div>
                            </div>
                            <a href="{{ route('announcements.create') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-2"></i>Create Announcement
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm">
            <div class="card-body">
                @if($announcements->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Start Date</th>
                                    <th>End Date</th>
                                    <th>Created</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($announcements as $announcement)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($announcement->is_pinned)
                                                    <i class="fas fa-thumbtack text-warning me-2" title="Pinned"></i>
                                                @endif
                                                <strong>{{ $announcement->title }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-{{ $announcement->type === 'urgent' ? 'danger' : ($announcement->type === 'event' ? 'success' : ($announcement->type === 'reminder' ? 'warning' : 'info')) }}">
                                                {{ ucfirst($announcement->type) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($announcement->isCurrentlyActive())
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $announcement->start_date ? $announcement->start_date->format('M d, Y') : 'Immediate' }}
                                        </td>
                                        <td>{{ $announcement->end_date ? $announcement->end_date->format('M d, Y') : 'No expiry' }}
                                        </td>
                                        <td>{{ $announcement->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('announcements.edit', $announcement) }}"
                                                    class="btn btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-info send-sms-btn"
                                                    title="Send SMS with Filters" data-bs-toggle="modal"
                                                    data-bs-target="#sendSmsModal" data-id="{{ $announcement->id }}"
                                                    data-title="{{ $announcement->title }}">
                                                    <i class="fas fa-sms"></i>
                                                </button>
                                                <form action="{{ route('announcements.destroy', $announcement) }}" method="POST"
                                                    class="d-inline delete-announcement-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $announcements->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bullhorn fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No announcements yet. Create your first announcement!</p>
                        <a href="{{ route('announcements.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Create Announcement
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

<!-- Send SMS Modal -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="sendSmsForm" method="POST">
                @csrf
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title" id="sendSmsModalLabel"><i class="fas fa-sms me-2"></i>Send Announcement SMS
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <small><i class="fas fa-info-circle me-1"></i>Sending SMS for: <strong
                                id="modalAnnouncementTitle"></strong></small>
                    </div>

                    <h6 class="mb-3 fw-bold"><i class="fas fa-filter me-2 text-info"></i>Select Recipients (Optional
                        Filters)</h6>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-primary">Specific Members</label>
                            <select name="sms_member_ids[]" id="modal_sms_member_ids"
                                class="form-select select2-members" multiple="multiple"
                                data-placeholder="Select specific members (optional)">
                                @foreach($members as $member)
                                    <option value="{{ $member->id }}">
                                        {{ $member->full_name }} ({{ $member->phone_number }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted d-block mt-1">If you select specific members, the general filters
                                below will be ignored for those members.</small>
                        </div>
                    </div>

                    <h6 class="mb-3 small text-muted border-bottom pb-2">Or Filter by Groups</h6>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Campus</label>
                            <select name="sms_campus_id" class="form-select form-select-sm">
                                <option value="">All Campuses</option>
                                @foreach($campuses as $campus)
                                    <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Community/Mtaa</label>
                            <select name="sms_community_id" class="form-select form-select-sm">
                                <option value="">All Communities</option>
                                @foreach($communities as $community)
                                    <option value="{{ $community->id }}">{{ $community->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Gender</label>
                            <select name="sms_gender" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Age Group</label>
                            <select name="sms_age_group" class="form-select form-select-sm">
                                <option value="">All Ages</option>
                                <option value="adult">Adults (18+)</option>
                                <option value="child">Children (<18)< /option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small">Residence</label>
                            <select name="sms_residence" class="form-select form-select-sm">
                                <option value="">All</option>
                                <option value="main_area">Live in Main Area</option>
                                <option value="outside">Live Outside Main Area</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 p-3 bg-light rounded border">
                        <p class="mb-0 small text-muted">
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            <strong>Note:</strong> Leave filters as "All" to send to all members with phone numbers.
                            This message will be sent immediately.
                        </p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info text-white">
                        <i class="fas fa-paper-plane me-2"></i>Send SMS Notifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle Send SMS Modal
            const sendSmsModal = document.getElementById('sendSmsModal');
            if (sendSmsModal) {
                sendSmsModal.addEventListener('show.bs.modal', function (event) {
                    const button = event.relatedTarget;
                    const id = button.getAttribute('data-id');
                    const title = button.getAttribute('data-title');

                    const form = document.getElementById('sendSmsForm');
                    const titleSpan = document.getElementById('modalAnnouncementTitle');

                    form.action = `{{ url('admin/announcements') }}/${id}/send-sms`;
                    titleSpan.textContent = title;

                    // Reset Select2 when modal opens
                    if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 === 'function') {
                        $('#modal_sms_member_ids').val(null).trigger('change');
                    }
                });

                // Initialize Select2 for modal
                if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 === 'function') {
                    $('#modal_sms_member_ids').select2({
                        theme: 'bootstrap-5',
                        width: '100%',
                        dropdownParent: $('#sendSmsModal'),
                        placeholder: $('#modal_sms_member_ids').data('placeholder'),
                        allowClear: true
                    });
                }

                // Handle form submission with loading state
                document.getElementById('sendSmsForm').addEventListener('submit', function () {
                    Swal.fire({
                        title: 'Sending SMS...',
                        text: 'This may take a few moments depending on the number of recipients.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                });
            }

            // Handle delete announcement with SweetAlert
            const deleteForms = document.querySelectorAll('.delete-announcement-form');

            deleteForms.forEach(form => {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const form = this;
                    const announcementTitle = form.closest('tr').querySelector('td strong')?.textContent || 'this announcement';

                    Swal.fire({
                        title: 'Delete Announcement?',
                        html: `<p>Are you sure you want to delete <strong>"${announcementTitle}"</strong>?</p><p class="text-danger">This action cannot be undone.</p>`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: '<i class="fas fa-trash me-1"></i>Yes, delete it',
                        cancelButtonText: '<i class="fas fa-times me-1"></i>Cancel',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading state
                            Swal.fire({
                                title: 'Deleting...',
                                text: 'Please wait',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            // Submit the form
                            form.submit();
                        }
                    });
                });
            });
        });
    </script>
@endsection