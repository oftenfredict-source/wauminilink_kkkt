<!-- resources/views/members/partials/main-table.blade.php -->
<!-- This partial contains the filters and members table, as in the original view -->

<!-- Filters & Search - Collapsible on Mobile -->
<form method="GET" action="{{ route('members.index') }}" class="card mb-3 border-0 shadow-sm" id="filtersForm">
    <!-- Preserve tab selection -->
    @if(request('membership_type'))
        <input type="hidden" name="membership_type" value="{{ request('membership_type') }}">
    @endif
    @if(request('type'))
        <input type="hidden" name="type" value="{{ request('type') }}">
    @endif
    @if(request('archived'))
        <input type="hidden" name="archived" value="{{ request('archived') }}">
    @endif
    <!-- Filter Header -->
    <div class="card-header bg-white border-bottom p-2 px-3 filter-header" onclick="toggleFilters()">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-filter text-primary"></i>
                <span class="fw-semibold">{{ autoTranslate('Filters') }}</span>
                @if(request('search') || request('gender') || request('region') || request('district') || request('ward'))
                    <span class="badge bg-primary rounded-pill" id="activeFiltersCount">{{ (request('search') ? 1 : 0) + (request('gender') ? 1 : 0) + (request('region') ? 1 : 0) + (request('district') ? 1 : 0) + (request('ward') ? 1 : 0) }}</span>
                @endif
            </div>
            <i class="fas fa-chevron-down text-muted d-md-none" id="filterToggleIcon"></i>
        </div>
    </div>
    
    <!-- Filter Body - Collapsible on Mobile -->
    <div class="card-body p-3" id="filterBody">
        <!-- Search - Always visible and compact -->
        <div class="mb-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" class="form-control" placeholder="{{ autoTranslate('Search name, phone, email, member ID') }}">
            </div>
        </div>
        
        <!-- Advanced Filters - Compact Grid -->
        <div class="row g-2 mb-3" id="advancedFilters">
            <div class="col-6 col-md-3">
                <label class="form-label small text-muted mb-1">{{ autoTranslate('Gender') }}</label>
                <select name="gender" id="genderFilter" class="form-select form-select-sm">
                    <option value="">{{ autoTranslate('All') }}</option>
                    <option value="male" {{ request('gender')==='male' ? 'selected' : '' }}>{{ autoTranslate('Male') }}</option>
                    <option value="female" {{ request('gender')==='female' ? 'selected' : '' }}>{{ autoTranslate('Female') }}</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small text-muted mb-1">{{ autoTranslate('Region') }}</label>
                <select name="region" id="regionFilter" class="form-select form-select-sm">
                    <option value="">{{ autoTranslate('All') }}</option>
                    @foreach(($regions ?? []) as $region)
                        <option value="{{ $region }}" {{ request('region')===$region ? 'selected' : '' }}>{{ $region }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small text-muted mb-1">{{ autoTranslate('District') }}</label>
                <select name="district" id="districtFilter" class="form-select form-select-sm">
                    <option value="">{{ autoTranslate('All') }}</option>
                    @foreach(($districts ?? []) as $district)
                        <option value="{{ $district }}" {{ request('district')===$district ? 'selected' : '' }}>{{ $district }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label small text-muted mb-1">{{ autoTranslate('Ward') }}</label>
                <select name="ward" id="wardFilter" class="form-select form-select-sm">
                    <option value="">{{ autoTranslate('All') }}</option>
                    @foreach(($wards ?? []) as $ward)
                        <option value="{{ $ward }}" {{ request('ward')===$ward ? 'selected' : '' }}>{{ $ward }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <!-- Action Buttons - Compact -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">
                <i class="fas fa-filter me-1"></i>{{ autoTranslate('Apply') }}
            </button>
            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-redo me-1"></i>{{ autoTranslate('Reset') }}
            </a>
        </div>
    </div>
</form>

<script>
function toggleFilters() {
    // Only toggle on mobile devices
    if (window.innerWidth > 768) {
        return; // Don't toggle on desktop
    }
    
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (!filterBody || !filterIcon) return;
    
    // Check computed style to see if it's visible
    const computedStyle = window.getComputedStyle(filterBody);
    const isVisible = computedStyle.display !== 'none';
    
    if (isVisible) {
        filterBody.style.display = 'none';
        filterIcon.classList.remove('fa-chevron-up');
        filterIcon.classList.add('fa-chevron-down');
    } else {
        filterBody.style.display = 'block';
        filterIcon.classList.remove('fa-chevron-down');
        filterIcon.classList.add('fa-chevron-up');
    }
}

// Handle window resize
window.addEventListener('resize', function() {
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (!filterBody || !filterIcon) return;
    
    if (window.innerWidth > 768) {
        // Always show on desktop
        filterBody.style.display = 'block';
        filterIcon.style.display = 'none';
    } else {
        // On mobile, show chevron
        filterIcon.style.display = 'block';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    const filterBody = document.getElementById('filterBody');
    const filterIcon = document.getElementById('filterToggleIcon');
    
    if (!filterBody || !filterIcon) return;
    
    if (window.innerWidth <= 768) {
        // Mobile: start collapsed
        filterBody.style.display = 'none';
        filterIcon.classList.remove('fa-chevron-up');
        filterIcon.classList.add('fa-chevron-down');
    } else {
        // Desktop: always show
        filterBody.style.display = 'block';
        filterIcon.style.display = 'none';
    }
    
    // Show filters if any are active
    @if(request('search') || request('gender') || request('region') || request('district') || request('ward'))
        if (window.innerWidth <= 768) {
            toggleFilters(); // Expand if filters are active
        }
    @endif
});
</script>

<!-- Members Table -->
<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover interactive-table align-middle mb-0" id="membersTable">
                <thead class="table-light">
                    @if(!empty($showChildren) && isset($children))
                    <tr>
                        <th class="text-nowrap">#</th>
                        <th>{{ autoTranslate('Name') }}</th>
                        <th>{{ autoTranslate('Age') }}</th>
                        <th>{{ autoTranslate('Gender') }}</th>
                        <th>{{ autoTranslate('Date of Birth') }}</th>
                        <th>{{ autoTranslate('Parent/Guardian') }}</th>
                        <th>{{ autoTranslate('Age Group') }}</th>
                        <th>{{ autoTranslate('Baptism Status') }}</th>
                        <th class="text-end">
                            <span>{{ autoTranslate('Actions') }}</span>
                        </th>
                    </tr>
                    @else
                    <tr>
                        <th class="text-nowrap">#</th>
                        <th>{{ autoTranslate('Full Name') }}</th>
                        <th>{{ autoTranslate('Member ID') }}</th>
                        <th>{{ autoTranslate('Phone') }}</th>
                        <th>{{ autoTranslate('Email') }}</th>
                        <th>{{ autoTranslate('Gender') }}</th>
                        <th class="text-end">
                            <span>{{ autoTranslate('Actions') }}</span>
                        </th>
                    </tr>
                    @endif
                </thead>
                <tbody>
                    @if(!empty($showChildren) && isset($children))
                        @forelse($children as $child)
                            @php
                                $hasMemberParent = $child->hasMemberParent();
                                $parentName = $child->getParentName();
                                $parentPhone = $child->getParentPhone();
                                $parentRelationship = $child->getParentRelationship();
                                $age = $child->getAge();
                                $ageGroup = $child->getAgeGroup();
                                
                                // Age group labels and colors
                                $ageGroupLabels = [
                                    'infant' => 'Infant (< 3)',
                                    'sunday_school' => 'Sunday School (3-12)',
                                    'teenager' => 'Teenager (13-17)'
                                ];
                                
                                $ageGroupColors = [
                                    'infant' => 'secondary',
                                    'sunday_school' => 'success',
                                    'teenager' => 'warning'
                                ];
                                
                                $ageGroupLabel = $ageGroup ? ($ageGroupLabels[$ageGroup] ?? 'N/A') : 'Adult (18+)';
                                $ageGroupColor = $ageGroup ? ($ageGroupColors[$ageGroup] ?? 'secondary') : 'dark';
                            @endphp
                            <tr id="child-row-{{ $child->id }}"
                                data-name="{{ strtolower($child->full_name ?? '') }}"
                                data-gender="{{ strtolower($child->gender ?? '') }}">
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td><strong>{{ $child->full_name ?? '-' }}</strong></td>
                                <td><strong>{{ $age }} years</strong></td>
                                <td>
                                    <span class="badge bg-{{ $child->gender === 'male' ? 'primary' : 'info' }}">
                                        {{ ucfirst($child->gender ?? '-') }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($hasMemberParent)
                                            <i class="fas fa-user text-primary"></i>
                                        @else
                                            <i class="fas fa-users text-warning"></i>
                                        @endif
                                        <span class="fw-bold">{{ $parentName ?? 'N/A' }}</span>
                                    </div>
                                    @if($hasMemberParent)
                                        <span class="badge bg-success mt-1">Member</span>
                                    @else
                                        <span class="badge bg-warning text-dark mt-1">Non-Member</span>
                                        @if($parentRelationship)
                                            <div class="mt-1">
                                                <i class="fas fa-link text-muted me-1"></i>
                                                <small class="text-muted">{{ $parentRelationship }}</small>
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ageGroupColor }}">
                                        {{ $ageGroupLabel }}
                                    </span>
                                </td>
                                <td>
                                    @if($child->baptism_status)
                                        @if($child->baptism_status === 'baptized')
                                            <span class="badge bg-success">
                                                <i class="fas fa-tint me-1"></i>Baptized
                                            </span>
                                            @if($child->baptism_date)
                                                <br><small class="text-muted">{{ \Carbon\Carbon::parse($child->baptism_date)->format('M d, Y') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">Not Baptized</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="action-buttons-wrapper">
                                        <button type="button" 
                                                class="action-btn action-btn-edit" 
                                                onclick="if(window.editChild){window.editChild({{ $child->id }});}"
                                                title="Edit Child"
                                                data-child-id="{{ $child->id }}">
                                            <i class="fas fa-edit"></i>
                                            <span class="action-tooltip">Edit</span>
                                        </button>
                                        @if($hasMemberParent && $child->member)
                                            <button type="button" 
                                                    class="action-btn action-btn-view" 
                                                    onclick="if(window.viewDetails){window.viewDetails({{ $child->member->id }});}"
                                                    title="View Parent Details"
                                                    data-member-id="{{ $child->member->id }}">
                                                <i class="fas fa-eye"></i>
                                                <span class="action-tooltip">View Parent</span>
                                            </button>
                                        @else
                                            <button type="button" 
                                                    class="action-btn action-btn-view" 
                                                    disabled
                                                    title="Parent is not a church member"
                                                    style="opacity: 0.5; cursor: not-allowed;">
                                                <i class="fas fa-eye"></i>
                                                <span class="action-tooltip">Parent Not a Member</span>
                                            </button>
                                        @endif
                                        <button type="button" 
                                                class="action-btn action-btn-delete" 
                                                onclick="if(window.confirmDeleteChild){window.confirmDeleteChild({{ $child->id }});}else{console.error('confirmDeleteChild not available');alert('Delete function not available. Please refresh the page.');}"
                                                title="Delete Child"
                                                data-child-id="{{ $child->id }}">
                                            <i class="fas fa-trash"></i>
                                            <span class="action-tooltip">Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-child fa-2x mb-2 d-block"></i>
                                    No children found.
                                </td>
                            </tr>
                        @endforelse
                    @else
                        @forelse(($members ?? collect()) as $member)
                            <tr id="row-{{ $member->id }}"
                                data-name="{{ strtolower($member->full_name) }}"
                                data-memberid="{{ strtolower($member->member_id) }}"
                                data-phone="{{ strtolower($member->phone_number) }}"
                                data-email="{{ strtolower($member->email) }}"
                                data-gender="{{ strtolower($member->gender ?? '') }}"
                                data-region="{{ strtolower($member->region ?? '') }}"
                                data-district="{{ strtolower($member->district ?? '') }}"
                                data-ward="{{ strtolower($member->ward ?? '') }}">
                                <td class="text-muted">
                                    @if(method_exists($members, 'firstItem'))
                                        {{ $members->firstItem() + $loop->index }}
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </td>
                                <td>{{ $member->full_name }}</td>
                                <td><span class="badge bg-secondary">{{ $member->member_id }}</span></td>
                                <td>{{ $member->phone_number }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ ucfirst($member->gender ?? '-') }}</td>
                                <td class="text-end">
                                    <div class="action-buttons-wrapper">
                                        <!-- Active Member Actions -->
                                        <button type="button" 
                                                class="action-btn action-btn-view" 
                                                onclick="if(window.viewDetails){window.viewDetails({{ $member->id }});}else{console.error('viewDetails not available');alert('View function not available. Please refresh the page.');}"
                                                title="View Details"
                                                data-member-id="{{ $member->id }}">
                                            <i class="fas fa-eye"></i>
                                            <span class="action-tooltip">View</span>
                                        </button>
                                        <button type="button" 
                                                class="action-btn action-btn-edit" 
                                                onclick="if(window.openEdit){window.openEdit({{ $member->id }});}else{console.error('openEdit not available');alert('Edit function not available. Please refresh the page.');}"
                                                title="Edit Member"
                                                data-member-id="{{ $member->id }}">
                                            <i class="fas fa-edit"></i>
                                            <span class="action-tooltip">Edit</span>
                                        </button>
                                        @if(auth()->user()->isAdmin())
                                            <button type="button" 
                                                    class="action-btn action-btn-reset" 
                                                    onclick="if(window.resetPassword){window.resetPassword({{ $member->id }});}else{console.error('resetPassword not available');alert('Reset password function not available. Please refresh the page.');}"
                                                    title="Reset Password"
                                                    data-member-id="{{ $member->id }}">
                                                <i class="fas fa-key"></i>
                                                <span class="action-tooltip">Reset</span>
                                            </button>
                                        @endif
                                        <button type="button" 
                                                class="action-btn action-btn-delete" 
                                                onclick="if(window.confirmDelete){window.confirmDelete({{ $member->id }});}else{console.error('confirmDelete not available');alert('Delete function not available. Please refresh the page.');}"
                                                title="Delete Member"
                                                data-member-id="{{ $member->id }}">
                                            <i class="fas fa-trash"></i>
                                            <span class="action-tooltip">Delete</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                    {{ autoTranslate('No members found.') }}
                                </td>
                            </tr>
                        @endforelse
                    @endif
                </tbody>
            </table>

            <style>
                /* Modern Action Buttons Design */
                .action-buttons-wrapper {
                    display: inline-flex;
                    gap: 6px;
                    align-items: center;
                    justify-content: flex-end;
                }

                .action-btn {
                    position: relative;
                    width: 38px;
                    height: 38px;
                    padding: 0;
                    border: 2px solid;
                    border-radius: 8px;
                    background: white;
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    font-size: 14px;
                    overflow: visible;
                }

                .action-btn i {
                    transition: transform 0.3s ease;
                }

                .action-btn:hover {
                    transform: translateY(-2px) scale(1.05);
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
                }

                .action-btn:hover i {
                    transform: scale(1.1);
                }

                .action-btn:active {
                    transform: translateY(0) scale(0.98);
                }

                /* View Button - Blue */
                .action-btn-view {
                    border-color: #17a2b8;
                    color: #17a2b8;
                }

                .action-btn-view:hover {
                    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
                    border-color: #17a2b8;
                    color: white;
                    box-shadow: 0 6px 12px rgba(23, 162, 184, 0.4);
                }

                /* Edit Button - Purple */
                .action-btn-edit {
                    border-color: #667eea;
                    color: #667eea;
                }

                .action-btn-edit:hover {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    border-color: #667eea;
                    color: white;
                    box-shadow: 0 6px 12px rgba(102, 126, 234, 0.4);
                }

                /* Reset Password Button - Green */
                .action-btn-reset {
                    border-color: #28a745;
                    color: #28a745;
                }

                .action-btn-reset:hover {
                    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
                    border-color: #28a745;
                    color: white;
                    box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
                }

                /* Archive Button - Orange */
                .action-btn-archive {
                    border-color: #ffc107;
                    color: #ffc107;
                }

                .action-btn-archive:hover {
                    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
                    border-color: #ffc107;
                    color: #212529;
                    box-shadow: 0 6px 12px rgba(255, 193, 7, 0.4);
                }

                /* Restore Button - Teal */
                .action-btn-restore {
                    border-color: #20c997;
                    color: #20c997;
                }

                .action-btn-restore:hover {
                    background: linear-gradient(135deg, #20c997 0%, #1aa179 100%);
                    border-color: #20c997;
                    color: white;
                    box-shadow: 0 6px 12px rgba(32, 201, 151, 0.4);
                }

                /* Delete Button - Red */
                .action-btn-delete {
                    border-color: #dc3545;
                    color: #dc3545;
                }

                .action-btn-delete:hover {
                    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
                    border-color: #dc3545;
                    color: white;
                    box-shadow: 0 6px 12px rgba(220, 53, 69, 0.4);
                }

                /* Tooltip */
                .action-tooltip {
                    position: absolute;
                    bottom: 100%;
                    left: 50%;
                    transform: translateX(-50%) translateY(-8px);
                    background: #333;
                    color: white;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 11px;
                    white-space: nowrap;
                    opacity: 0;
                    pointer-events: none;
                    transition: opacity 0.3s ease, transform 0.3s ease;
                    z-index: 1000;
                    margin-bottom: 5px;
                }

                .action-tooltip::after {
                    content: '';
                    position: absolute;
                    top: 100%;
                    left: 50%;
                    transform: translateX(-50%);
                    border: 4px solid transparent;
                    border-top-color: #333;
                }

                .action-btn:hover .action-tooltip {
                    opacity: 1;
                    transform: translateX(-50%) translateY(0);
                }

                /* Loading state */
                .action-btn.loading {
                    pointer-events: none;
                    opacity: 0.6;
                }

                .action-btn.loading i {
                    animation: spin 1s linear infinite;
                }

                @keyframes spin {
                    from { transform: rotate(0deg); }
                    to { transform: rotate(360deg); }
                }

                /* Responsive adjustments */
                @media (max-width: 768px) {
                    .action-buttons-wrapper {
                        gap: 4px;
                    }

                    .action-btn {
                        width: 34px;
                        height: 34px;
                        font-size: 12px;
                    }

                    .action-tooltip {
                        display: none;
                    }
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
            .then(r => {
                if (r.ok) {
                    return r.json();
                } else if (r.status === 403) {
                    return r.json().then(data => {
                        throw new Error(data.message || 'You do not have permission to archive members. Please contact your administrator.');
                    });
                } else {
                    return r.json().then(data => {
                        throw new Error(data.message || `Server error: ${r.status}`);
                    }).catch(() => {
                        throw new Error(`Server error: ${r.status}`);
                    });
                }
            })
            .then(res => {
                if (res.success) {
                    Swal.fire({ icon: 'success', title: 'Member archived', timer: 1200, showConfirmButton: false }).then(()=>location.reload());
                } else {
                    Swal.fire({ icon: 'error', title: 'Archive failed', text: res.message || 'Please try again.' });
                }
            })
            .catch(error => Swal.fire({ icon: 'error', title: 'Archive failed', text: error.message || 'Network error' }));
        });
        form._archiveHandlerAttached = true;
    }
})();
</script>
