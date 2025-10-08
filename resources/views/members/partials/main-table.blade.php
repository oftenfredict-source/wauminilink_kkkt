<!-- resources/views/members/partials/main-table.blade.php -->
<!-- This partial contains the filters and members table, as in the original view -->

<!-- Filters & Search -->
<form method="GET" action="{{ route('members.index') }}" class="card mb-3" id="filtersForm">
    <div class="card-body">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control" placeholder="Search name, phone, email, member ID">
            </div>
            <div class="col-md-2">
                <label class="form-label">Gender</label>
                <select name="gender" id="genderFilter" class="form-select">
                    <option value="">All</option>
                    <option value="male" {{ request('gender')==='male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ request('gender')==='female' ? 'selected' : '' }}>Female</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Region</label>
                <select name="region" id="regionFilter" class="form-select">
                    <option value="">All</option>
                    @foreach(($regions ?? []) as $region)
                        <option value="{{ $region }}" {{ request('region')===$region ? 'selected' : '' }}>{{ $region }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">District</label>
                <select name="district" id="districtFilter" class="form-select">
                    <option value="">All</option>
                    @foreach(($districts ?? []) as $district)
                        <option value="{{ $district }}" {{ request('district')===$district ? 'selected' : '' }}>{{ $district }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Ward</label>
                <select name="ward" id="wardFilter" class="form-select">
                    <option value="">All</option>
                    @foreach(($wards ?? []) as $ward)
                        <option value="{{ $ward }}" {{ request('ward')===$ward ? 'selected' : '' }}>{{ $ward }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 d-flex gap-2 mt-2">
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Apply</button>
                <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </div>
    </div>
</form>

<!-- Members Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover interactive-table align-middle mb-0 @if(!empty($isArchived)) archived-table @endif" id="membersTable">
                <thead class="table-light">
                    <tr>
                        <th class="text-nowrap">#</th>
                        <th>Full Name</th>
                        <th>Member ID</th>
                        <th>Phone</th>
                        @if(!empty($isArchived))
                            <th>Gender</th>
                            <th>Reason</th>
                        @else
                            <th>Email</th>
                            <th>Gender</th>
                        @endif
                        <th class="text-end">
                            <span>Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($members ?? collect()) as $member)
                        @if(!empty($isArchived))
                            @php $snap = $member->member_snapshot ?? []; @endphp
                        @endif
                        <tr id="row-{{ !empty($isArchived) ? $member->member_id : $member->id }}"
                            @if(!empty($isArchived)) style="background-color: #f4f4f4; color: #000;" @endif
                            data-name="{{ strtolower(!empty($isArchived) ? ($snap['full_name'] ?? '') : $member->full_name) }}"
                            data-memberid="{{ strtolower(!empty($isArchived) ? ($snap['member_id'] ?? '') : $member->member_id) }}"
                            data-phone="{{ strtolower(!empty($isArchived) ? ($snap['phone_number'] ?? '') : $member->phone_number) }}"
                            data-email="{{ strtolower(!empty($isArchived) ? ($snap['email'] ?? '') : $member->email) }}"
                            data-gender="{{ strtolower(!empty($isArchived) ? ($snap['gender'] ?? '') : ($member->gender ?? '')) }}"
                            data-region="{{ strtolower(!empty($isArchived) ? ($snap['region'] ?? '') : ($member->region ?? '')) }}"
                            data-district="{{ strtolower(!empty($isArchived) ? ($snap['district'] ?? '') : ($member->district ?? '')) }}"
                            data-ward="{{ strtolower(!empty($isArchived) ? ($snap['ward'] ?? '') : ($member->ward ?? '')) }}">
                            <td class="text-muted">
                                @if(method_exists($members, 'firstItem'))
                                    {{ $members->firstItem() + $loop->index }}
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>
                            <td>{{ !empty($isArchived) ? ($snap['full_name'] ?? '-') : $member->full_name }}</td>
                            <td><span class="badge bg-secondary">{{ !empty($isArchived) ? ($snap['member_id'] ?? '-') : $member->member_id }}</span></td>
                            <td>{{ !empty($isArchived) ? ($snap['phone_number'] ?? '-') : $member->phone_number }}</td>
                            @if(!empty($isArchived))
                                <td>{{ ucfirst($snap['gender'] ?? '-') }}</td>
                                <td>{{ $member->reason ?? '-' }}</td>
                            @else
                                <td>{{ $member->email }}</td>
                                <td>{{ ucfirst($member->gender ?? '-') }}</td>
                            @endif
                            <td class="text-end">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-info" onclick="viewDetails({{ !empty($isArchived) ? $member->member_id : $member->id }})"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-outline-primary" onclick="openEdit({{ !empty($isArchived) ? $member->member_id : $member->id }})"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-outline-danger" onclick="confirmDelete({{ !empty($isArchived) ? $member->member_id : $member->id }})"><i class="fas fa-trash"></i></button>
                                    @if(($showArchive ?? false) && empty($isArchived))
                                        <button class="btn btn-outline-warning" onclick="openArchiveModal({{ $member->id }})"><i class="fas fa-archive"></i></button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4">No members found.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if(!empty($isArchived))
            <style>
                .archived-table {
                    background: #f4f4f4 !important;
                }
                .archived-table th, .archived-table td {
                    color: #000 !important;
                    background: #f4f4f4 !important;
                }
                .archived-table tr {
                    background: #f4f4f4 !important;
                }
            </style>
            @endif

            <style>
                /* Action Button Colors */
                .btn-outline-info {
                    border-color: #17a2b8 !important;
                    color: #17a2b8 !important;
                }
                .btn-outline-info:hover {
                    background: linear-gradient(90deg, #17a2b8, #138496) !important;
                    border-color: #17a2b8 !important;
                    color: white !important;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(23, 162, 184, 0.3);
                }

                .btn-outline-primary {
                    border-color: #667eea !important;
                    color: #667eea !important;
                }
                .btn-outline-primary:hover {
                    background: linear-gradient(90deg, #667eea, #764ba2) !important;
                    border-color: #667eea !important;
                    color: white !important;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
                }

                .btn-outline-danger {
                    border-color: #dc3545 !important;
                    color: #dc3545 !important;
                }
                .btn-outline-danger:hover {
                    background: linear-gradient(90deg, #dc3545, #c82333) !important;
                    border-color: #dc3545 !important;
                    color: white !important;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
                }

                .btn-outline-warning {
                    border-color: #ffc107 !important;
                    color: #ffc107 !important;
                }
                .btn-outline-warning:hover {
                    background: linear-gradient(90deg, #ffc107, #e0a800) !important;
                    border-color: #ffc107 !important;
                    color: #212529 !important;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
                }

                .btn-outline-success {
                    border-color: #28a745 !important;
                    color: #28a745 !important;
                }
                .btn-outline-success:hover {
                    background: linear-gradient(90deg, #28a745, #218838) !important;
                    border-color: #28a745 !important;
                    color: white !important;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
                }

                /* Button group styling */
                .btn-group .btn {
                    transition: all 0.3s ease;
                    font-weight: 600;
                }

                .btn-group .btn i {
                    font-size: 0.9rem;
                }
            </style>
        </div>
    </div>
    @if(isset($members) && method_exists($members, 'firstItem'))
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $members->firstItem() }} to {{ $members->lastItem() }} of {{ $members->total() }} entries
            </div>
            <div>
                {{ $members->withQueryString()->links() }}
            </div>
        </div>
    @elseif(isset($members) && $members instanceof \Illuminate\Support\Collection)
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                Showing {{ $members->count() }} of {{ $members->count() }} entries
            </div>
        </div>
    @endif
</div>

<!-- Archive Modal (should be included once per page, not per row) -->
<div class="modal fade" id="archiveMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-archive me-2"></i>Archive Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="archiveMemberForm">
                    <input type="hidden" id="archive_member_id">
                    <div class="mb-3">
                        <label for="archive_reason" class="form-label">Reason for archiving</label>
                        <textarea class="form-control" id="archive_reason" name="reason" rows="3" required></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-warning">Archive</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
// Archive member logic (robust, attaches only once)
(function() {
    let archiveMemberId = null;
    window.openArchiveModal = function(id) {
        archiveMemberId = id;
        document.getElementById('archive_member_id').value = id;
        document.getElementById('archive_reason').value = '';
        new bootstrap.Modal(document.getElementById('archiveMemberModal')).show();
    };
    // Attach submit handler only once
    const form = document.getElementById('archiveMemberForm');
    if (form && !form._archiveHandlerAttached) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const id = document.getElementById('archive_member_id').value;
            const reason = document.getElementById('archive_reason').value.trim();
            if (!reason) {
                Swal.fire({ icon: 'warning', title: 'Please provide a reason.' });
                return;
            }
            const formData = new FormData();
            formData.append('reason', reason);
            formData.append('_method', 'DELETE');
            fetch(`{{ url('/members') }}/${id}/archive`, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Member archived', timer: 1200, showConfirmButton: false }).then(()=>location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Archive failed', text: res.message || 'Please try again.' });
                }
            })
            .catch(()=> Swal.fire({ icon: 'error', title: 'Network error' }));
        });
        form._archiveHandlerAttached = true;
    }
})();
</script>
