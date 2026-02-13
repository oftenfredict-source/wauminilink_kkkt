<!-- resources/views/members/partials/main-table.blade.php -->
<!-- This partial contains the filters and members table, as in the original view -->

<!-- Filters & Search - Collapsible on Mobile -->
<form method="GET" action="{{ route('members.index') }}" class="card mb-3 border-0 shadow-sm" id="filtersForm-{{ $membershipType ?? 'default' }}">
    <!-- Preserve tab selection -->
    <input type="hidden" name="membership_type" value="{{ $membershipType ?? request('membership_type') ?? 'all' }}">
    
    @if(request('type'))
        <input type="hidden" name="type" value="{{ request('type') }}">
    @endif
    @if(request('archived'))
        <input type="hidden" name="archived" value="{{ request('archived') }}">
    @endif
    <!-- Filter Header -->
    <div class="card-header bg-white border-bottom p-2 px-3 filter-header" onclick="toggleFilters('{{ $membershipType ?? 'default' }}')">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-filter text-danger"></i>
                <span class="fw-semibold">{{ __('common.filters') }}</span>
                @if(request('search') || request('gender') || request('ward') || request('campus_id') || request('community_id'))
                    <span class="badge bg-danger rounded-pill"
                        id="activeFiltersCount-{{ $membershipType ?? 'default' }}">{{ (request('search') ? 1 : 0) + (request('gender') ? 1 : 0) + (request('ward') ? 1 : 0) + (request('campus_id') ? 1 : 0) + (request('community_id') ? 1 : 0) }}</span>
                @endif
            </div>
            <i class="fas fa-chevron-down text-muted d-md-none" id="filterToggleIcon-{{ $membershipType ?? 'default' }}"></i>
        </div>
    </div>

    <!-- Filter Body - Collapsible on Mobile -->
    <div class="card-body p-3" id="filterBody-{{ $membershipType ?? 'default' }}">
        <!-- Search - Always visible and compact -->
        <div class="mb-3">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" id="searchInput-{{ $membershipType ?? 'default' }}" value="{{ request('search') }}" class="form-control"
                    placeholder="{{ __('common.search_placeholder') }}">
            </div>
        </div>

        <!-- Advanced Filters - Compact Grid -->
        <div class="row g-2 mb-3" id="advancedFilters-{{ $membershipType ?? 'default' }}">
            @if(isset($campuses) && $campuses->count() > 0)
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">{{ __('common.branch') }}</label>
                    <select name="campus_id" id="campusFilter-{{ $membershipType ?? 'default' }}" class="form-select form-select-sm filter-select">
                        <option value="">{{ __('common.all') }}</option>
                        @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}" {{ request('campus_id') == $campus->id ? 'selected' : '' }}>
                                {{ $campus->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if(isset($communities) && $communities->count() > 0)
                <div class="col-6 col-md-3">
                    <label class="form-label small text-muted mb-1">{{ __('common.community') }}</label>
                    <select name="community_id" id="communityFilter-{{ $membershipType ?? 'default' }}" class="form-select form-select-sm filter-select">
                        <option value="">{{ __('common.all') }}</option>
                        @foreach($communities as $community)
                            <option value="{{ $community->id }}" {{ request('community_id') == $community->id ? 'selected' : '' }}>
                                {{ $community->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-6 col-md-3">
                <label class="form-label small text-muted mb-1">{{ __('common.gender') }}</label>
                <select name="gender" id="genderFilter-{{ $membershipType ?? 'default' }}" class="form-select form-select-sm filter-select">
                    <option value="">{{ __('common.all') }}</option>
                    <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>{{ __('common.male') }}
                    </option>
                    <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>
                        {{ __('common.female') }}
                    </option>
                </select>
            </div>

            <div class="col-6 col-md-3">
                <label class="form-label small text-muted mb-1">{{ __('common.ward') }}</label>
                <select name="ward" id="wardFilter-{{ $membershipType ?? 'default' }}" class="form-select form-select-sm filter-select">
                    <option value="">{{ __('common.all') }}</option>
                    @foreach(($wards ?? []) as $ward)
                        <option value="{{ $ward }}" {{ request('ward') === $ward ? 'selected' : '' }}>{{ $ward }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Action Buttons - Compact -->
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-danger btn-sm flex-fill">
                <i class="fas fa-filter me-1"></i>{{ __('common.apply') }}
            </button>
            <a href="{{ route('members.index', ['membership_type' => $membershipType ?? 'all']) }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-redo me-1"></i>{{ __('common.reset') }}
            </a>
        </div>
    </div>
</form>

<script>
    // Ensure function is defined only once globally (or handle redeclaration gracefully)
    if (typeof window.toggleFilters !== 'function') {
        window.toggleFilters = function(type) {
            const body = document.getElementById('filterBody-' + type);
            const icon = document.getElementById('filterToggleIcon-' + type);
            if (body && icon) {
                if (body.style.display === 'none') {
                    body.style.display = 'block';
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                } else {
                    body.style.display = 'none';
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                }
            }
        };
    }

    // Auto-submit on change for this specific form instance
    document.addEventListener('DOMContentLoaded', function() {
        const type = '{{ $membershipType ?? "default" }}';
        const form = document.getElementById('filtersForm-' + type);
        if (form) {
            const selects = form.querySelectorAll('.filter-select');
            selects.forEach(select => {
                select.addEventListener('change', function() {
                     // Show loading state if desired
                     // Submit form
                     form.submit();
                });
            });
        }
    });
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function () {
        const filterBody = document.getElementById('filterBody-{{ $membershipType ?? "default" }}');
        const filterIcon = document.getElementById('filterToggleIcon-{{ $membershipType ?? "default" }}');

        if (!filterBody || !filterIcon) return;

        if (window.innerWidth <= 768) {
            // Mobile: start collapsed
            filterBody.style.display = 'none';
            if (filterIcon) {
                filterIcon.classList.remove('fa-chevron-up');
                filterIcon.classList.add('fa-chevron-down');
            }
        } else {
            // Desktop: always show
            filterBody.style.display = 'block';
            if (filterIcon) {
                filterIcon.style.display = 'none';
            }
        }

        // Show filters if any are active
        @if(request('search') || request('gender') || request('ward') || request('campus_id') || request('community_id'))
            if (window.innerWidth <= 768) {
                // Ensure toggleFilters is available before calling
                if (typeof window.toggleFilters === 'function') {
                    window.toggleFilters('{{ $membershipType ?? "default" }}');
                }
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
                            <th>{{ __('common.name') }}</th>
                            <th>{{ __('common.age') }}</th>
                            <th>{{ __('common.gender') }}</th>
                            <th>{{ __('common.date_of_birth') }}</th>
                            <th>{{ __('common.parent_guardian') }}</th>
                            <th>{{ __('common.age_group') }}</th>
                            <th>{{ __('common.baptism_status') }}</th>
                            <th class="text-end">
                                <span>{{ __('common.actions') }}</span>
                            </th>
                        </tr>
                    @else
                        <tr>
                            <th class="text-nowrap">#</th>
                            <th>{{ __('common.full_name') }}</th>
                            <th>{{ __('common.member_id') }}</th>
                            <th>Envelope</th>
                            <th>{{ __('common.phone') }}</th>
                            <th>{{ __('common.email') }}</th>
                            <th>{{ __('common.gender') }}</th>
                            <th class="text-end">
                                <span>{{ __('common.actions') }}</span>
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
                                    'infant' => __('common.infant') . ' (< 3)',
                                    'sunday_school' => __('common.sunday_school') . ' (3-12)',
                                    'teenager' => __('common.teenager') . ' (13-17)'
                                ];

                                $ageGroupColors = [
                                    'infant' => 'secondary',
                                    'sunday_school' => 'success',
                                    'teenager' => 'warning'
                                ];

                                $ageGroupLabel = $ageGroup ? ($ageGroupLabels[$ageGroup] ?? __('common.no_data')) : __('common.adult') . ' (18+)';
                                $ageGroupColor = $ageGroup ? ($ageGroupColors[$ageGroup] ?? 'secondary') : 'dark';
                            @endphp
                            <tr id="child-row-{{ $child->id }}" data-name="{{ strtolower($child->full_name ?? '') }}"
                                data-gender="{{ strtolower($child->gender ?? '') }}">
                                <td class="text-muted">{{ $loop->iteration }}</td>
                                <td><strong>{{ $child->full_name ?? '-' }}</strong></td>
                                <td><strong>{{ $age }} {{ __('common.years') }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $child->gender === 'male' ? 'danger' : 'dark' }}">
                                        {{ $child->gender ? __('common.' . $child->gender) : '-' }}
                                    </span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($child->date_of_birth)->format('M d, Y') }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($hasMemberParent)
                                            <i class="fas fa-user text-danger"></i>
                                        @else
                                            <i class="fas fa-users text-warning"></i>
                                        @endif
                                        <span class="fw-bold">{{ $parentName ?? __('common.no_data') }}</span>
                                    </div>
                                    @if($hasMemberParent)
                                        <span class="badge bg-success mt-1">{{ __('common.member') }}</span>
                                    @else
                                        <span class="badge bg-warning text-dark mt-1">{{ __('common.non_member') }}</span>
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
                                                <i class="fas fa-tint me-1"></i>{{ __('common.baptized') }}
                                            </span>
                                            @if($child->baptism_date)
                                                <br><small
                                                    class="text-muted">{{ \Carbon\Carbon::parse($child->baptism_date)->format('M d, Y') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-secondary">{{ __('common.not_baptized') }}</span>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="action-buttons-wrapper">
                                        <button type="button" class="action-btn action-btn-edit"
                                            onclick="if(window.editChild){window.editChild({{ $child->id }});}"
                                            title="{{ __('common.edit_child') }}" data-child-id="{{ $child->id }}">
                                            <i class="fas fa-edit"></i>
                                            <span class="action-tooltip">{{ __('common.edit') }}</span>
                                        </button>
                                        <button type="button" class="action-btn action-btn-view"
                                            onclick="if(window.viewChildDetails){window.viewChildDetails({{ $child->id }});}else{console.error('viewChildDetails not available');alert('{{ __('common.function_not_available') }}');}"
                                            title="{{ __('common.view_child_profile') }}" data-child-id="{{ $child->id }}">
                                            <i class="fas fa-eye"></i>
                                            <span class="action-tooltip">{{ __('common.view') }}</span>
                                        </button>
                                        <button type="button" class="action-btn action-btn-delete"
                                            onclick="if(window.confirmDeleteChild){window.confirmDeleteChild({{ $child->id }});}else{console.error('confirmDeleteChild not available');alert('{{ __('common.function_not_available') }}');}"
                                            title="{{ __('common.delete_child') }}" data-child-id="{{ $child->id }}">
                                            <i class="fas fa-trash"></i>
                                            <span class="action-tooltip">{{ __('common.delete') }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-child fa-2x mb-2 d-block"></i>
                                    {{ __('common.no_children_found') }}
                                </td>
                            </tr>
                        @endforelse
                    @else
                        @forelse(($members ?? collect()) as $member)
                            <tr id="row-{{ $member->id }}" data-name="{{ strtolower($member->full_name) }}"
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
                                <td><span class="badge bg-info text-dark">{{ $member->envelope_number ?? '-' }}</span></td>
                                <td>{{ $member->phone_number }}</td>
                                <td>{{ $member->email }}</td>
                                <td>{{ $member->gender ? __('common.' . $member->gender) : '-' }}</td>
                                <td class="text-end">
                                    <div class="action-buttons-wrapper">
                                        <!-- Active Member Actions -->
                                        <button type="button" class="action-btn action-btn-view"
                                            onclick="if(window.viewDetails){window.viewDetails({{ $member->id }});}else{console.error('viewDetails not available');alert('{{ __('common.function_not_available') }}');}"
                                            title="{{ __('common.view_details') }}" data-member-id="{{ $member->id }}">
                                            <i class="fas fa-eye"></i>
                                            <span class="action-tooltip">{{ __('common.view') }}</span>
                                        </button>
                                        <button type="button" class="action-btn action-btn-edit"
                                            onclick="if(window.openEdit){window.openEdit({{ $member->id }});}else{console.error('openEdit not available');alert('{{ __('common.function_not_available') }}');}"
                                            title="{{ __('common.edit_member') }}" data-member-id="{{ $member->id }}">
                                            <i class="fas fa-edit"></i>
                                            <span class="action-tooltip">{{ __('common.edit') }}</span>
                                        </button>
                                        @if(auth()->user()->isAdmin())
                                            <button type="button" class="action-btn action-btn-reset"
                                                onclick="if(window.resetPassword){window.resetPassword({{ $member->id }});}else{console.error('resetPassword not available');alert('{{ __('common.function_not_available') }}');}"
                                                title="{{ __('common.reset_password') }}" data-member-id="{{ $member->id }}">
                                                <i class="fas fa-key"></i>
                                                <span class="action-tooltip">{{ __('common.reset') }}</span>
                                            </button>
                                        @endif
                                        <button type="button" class="action-btn action-btn-delete"
                                            onclick="if(window.confirmDelete){window.confirmDelete({{ $member->id }});}else{console.error('confirmDelete not available');alert('{{ __('common.function_not_available') }}');}"
                                            title="{{ __('common.delete_member') }}" data-member-id="{{ $member->id }}">
                                            <i class="fas fa-trash"></i>
                                            <span class="action-tooltip">{{ __('common.delete') }}</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-2x mb-2 d-block"></i>
                                    {{ __('common.no_members_found') }}
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

                /* View Button - Red Gradient */
                .action-btn-view {
                    border-color: #940000;
                    color: #940000;
                }

                .action-btn-view:hover {
                    background: linear-gradient(135deg, #940000 0%, #7a0000 100%);
                    border-color: #940000;
                    color: white;
                    box-shadow: 0 6px 12px rgba(148, 0, 0, 0.4);
                }

                /* Edit Button - Dark Grey/Black */
                .action-btn-edit {
                    border-color: #333;
                    color: #333;
                }

                .action-btn-edit:hover {
                    background: linear-gradient(135deg, #333 0%, #000 100%);
                    border-color: #333;
                    color: white;
                    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.4);
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
                    from {
                        transform: rotate(0deg);
                    }

                    to {
                        transform: rotate(360deg);
                    }
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
                {{ __('common.showing_paging', ['first' => $members->firstItem(), 'last' => $members->lastItem(), 'total' => $members->total()]) }}
            </div>
            <div>
                {{ $members->withQueryString()->links() }}
            </div>
        </div>
    @elseif(isset($members) && $members instanceof \Illuminate\Support\Collection)
        <div class="card-footer d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                {{ __('common.showing_count', ['count' => $members->count(), 'total' => $members->count()]) }}
            </div>
        </div>
    @endif
</div>

<!-- Archive Modal (should be included once per page, not per row) -->
<div class="modal fade" id="archiveMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-archive me-2"></i>{{ __('common.archive_member') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="{{ __('common.close') }}"></button>
            </div>
            <div class="modal-body">
                <form id="archiveMemberForm">
                    <input type="hidden" id="archive_member_id">
                    <div class="mb-3">
                        <label for="archive_reason" class="form-label">{{ __('common.reason_for_archiving') }}</label>
                        <textarea class="form-control" id="archive_reason" name="reason" rows="3" required></textarea>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-outline-secondary"
                            data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                        <button type="submit" class="btn btn-warning">{{ __('common.archive') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    // Archive member logic (robust, attaches only once)
    (function () {
        let archiveMemberId = null;
        window.openArchiveModal = function (id) {
            archiveMemberId = id;
            document.getElementById('archive_member_id').value = id;
            document.getElementById('archive_reason').value = '';
            new bootstrap.Modal(document.getElementById('archiveMemberModal')).show();
        };
        // Attach submit handler only once
        const form = document.getElementById('archiveMemberForm');
        if (form && !form._archiveHandlerAttached) {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                const id = document.getElementById('archive_member_id').value;
                const reason = document.getElementById('archive_reason').value.trim();
                if (!reason) {
                    Swal.fire({ icon: 'warning', title: '{{ __('common.provide_reason') }}' });
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
                                throw new Error(data.message || '{{ __('common.no_permission_archive') }}');
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
                            Swal.fire({ icon: 'success', title: '{{ __('common.member_archived') }}', timer: 1200, showConfirmButton: false }).then(() => location.reload());
                        } else {
                            Swal.fire({ icon: 'error', title: '{{ __('common.archive_failed') }}', text: res.message || '{{ __('common.try_again') }}' });
                        }
                    })
                    .catch(error => Swal.fire({ icon: 'error', title: '{{ __('common.archive_failed') }}', text: error.message || '{{ __('common.network_error') }}' }));
            });
            form._archiveHandlerAttached = true;
        }
    })();
</script>