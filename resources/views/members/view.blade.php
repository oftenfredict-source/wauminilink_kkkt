@extends('layouts.index')

@section('content')
    <div class="container-fluid px-2 px-md-5 py-4">
        <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
            <div
                class="card-header bg-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
                <span class="fs-5 fw-bold text-white d-flex align-items-center">
                    <i class="fas fa-users me-2"></i> <span>{{ __('common.all_members') }}</span>
                </span>
                <div class="mt-3 mt-md-0">
                    <a href="{{ route('members.add') }}" class="btn btn-danger btn-sm shadow-sm"><i
                            class="fas fa-user-plus me-1"></i> {{ __('common.add_member') }}</a>
                </div>
            </div>
            <div class="card-body bg-light px-4 py-4">
                <ul class="nav nav-tabs mb-4 border-0" id="memberTypeTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                            type="button" role="tab" data-type="all">
                            {{ __('common.all') }}
                            <span class="badge bg-danger rounded-pill ms-2" id="all-count">{{ $allCount ?? 0 }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="permanent-tab" data-bs-toggle="tab" data-bs-target="#permanent"
                            type="button" role="tab" data-type="permanent">
                            {{ __('common.permanent') }}
                            <span class="badge bg-danger rounded-pill ms-2"
                                id="permanent-count">{{ $permanentCount ?? 0 }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="temporary-tab" data-bs-toggle="tab" data-bs-target="#temporary"
                            type="button" role="tab" data-type="temporary">
                            {{ __('common.temporary') }}
                            <span class="badge bg-danger rounded-pill ms-2"
                                id="temporary-count">{{ $temporaryCount ?? 0 }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="children-tab" data-bs-toggle="tab" data-bs-target="#children"
                            type="button" role="tab" data-type="children">
                            {{ __('common.children') }}
                            <span class="badge bg-danger rounded-pill ms-2"
                                id="children-count">{{ $childrenCount ?? 0 }}</span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="memberTypeTabContent">
                    <div class="tab-pane fade show active" id="all" role="tabpanel">
                        @include('members.partials.main-table', ['membershipType' => 'all'])
                    </div>
                    <div class="tab-pane fade" id="permanent" role="tabpanel">
                        @include('members.partials.main-table', ['membershipType' => 'permanent'])
                    </div>
                    <div class="tab-pane fade" id="temporary" role="tabpanel">
                        @include('members.partials.main-table', ['membershipType' => 'temporary'])
                    </div>
                    <div class="tab-pane fade" id="children" role="tabpanel">
                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 text-muted">{{ __('common.children_list') }}</h6>
                            <button type="button" class="btn btn-danger btn-sm shadow-sm" data-bs-toggle="modal"
                                data-bs-target="#addChildModal">
                                <i class="fas fa-child me-1"></i> {{ __('common.add_child') }}
                            </button>
                        </div>
                        @include('members.partials.main-table', ['showChildren' => true])
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* CRITICAL FIX: Prevent sidebar from hiding content on the left side */
            @media (min-width: 992px) {

                body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav_content,
                body.sb-nav-fixed:not(.sb-sidenav-toggled) #layoutSidenav #layoutSidenav_content,
                #layoutSidenav:not(.sb-sidenav-toggled) #layoutSidenav_content {
                    padding-left: 225px !important;
                    margin-left: 0 !important;
                }

                body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav_content,
                body.sb-nav-fixed.sb-sidenav-toggled #layoutSidenav #layoutSidenav_content,
                #layoutSidenav.sb-sidenav-toggled #layoutSidenav_content {
                    padding-left: 0 !important;
                    margin-left: 0 !important;
                }
            }

            #layoutSidenav_content {
                overflow-x: visible !important;
                position: relative !important;
                box-sizing: border-box !important;
            }

            .container-fluid {
                margin-left: 0 !important;
                margin-right: 0 !important;
                max-width: 100% !important;
                box-sizing: border-box !important;
                position: relative !important;
            }

            @media (max-width: 991px) {
                #layoutSidenav_content {
                    padding-left: 0 !important;
                    margin-left: 0 !important;
                }

                .container-fluid {
                    padding-left: 1rem !important;
                    padding-right: 1rem !important;
                }
            }

            /* Tab Styling */
            .nav-tabs {
                border-bottom: 2px solid #dee2e6;
            }

            .nav-tabs .nav-link {
                border: none;
                color: #6c757d;
                font-weight: 500;
                padding: 0.75rem 1.25rem;
                position: relative;
                background: transparent;
            }

            .nav-tabs .nav-link:hover {
                border-color: transparent;
                color: #940000;
            }

            .nav-tabs .nav-link.active {
                color: #940000;
                background-color: transparent;
                border: none;
                border-bottom: 3px solid #940000;
                font-weight: 600;
            }

            .nav-tabs .nav-link .badge {
                font-size: 0.75rem;
                padding: 0.25rem 0.5rem;
                background-color: #940000 !important;
            }
        </style>
    @endpush

    @section('scripts')
        <script>
            // Member action functions - Define these globally before DOMContentLoaded
            window.viewDetails = function (id) {
                if (!id) {
                    Swal.fire({ icon: 'error', title: '{{ __('common.error') }}', text: '{{ __('common.invalid_member_id') }}' });
                    return;
                }
                // Redirect to view member page
                window.location.href = `{{ url('/members') }}/${id}`;
            };

            window.viewChildDetails = function (id) {
                if (!id) {
                    Swal.fire({ icon: 'error', title: '{{ __('common.error') }}', text: '{{ __('common.invalid_child_id') }}' });
                    return;
                }
                // Redirect to view child page
                window.location.href = `{{ url('/children') }}/${id}`;
            };

            window.openEdit = function (id) {
                if (!id) {
                    Swal.fire({ icon: 'error', title: '{{ __('common.error') }}', text: '{{ __('common.invalid_member_id') }}' });
                    return;
                }
                // Redirect to edit member page
                window.location.href = `{{ url('/members') }}/${id}/edit`;
            };

            window.confirmDelete = function (id) {
                if (!id) {
                    Swal.fire({ icon: 'error', title: '{{ __('common.error') }}', text: '{{ __('common.invalid_member_id') }}' });
                    return;
                }

                Swal.fire({
                    title: "{{ __('common.delete_member') }}?",
                    text: "{{ __('common.delete_member_confirm') }} {{ __('common.delete_warning') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: "{{ __('common.yes_delete') }}!",
                    cancelButtonText: "{{ __('common.cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Get fresh CSRF token from meta tag
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                            document.querySelector('input[name="_token"]')?.value ||
                            '{{ csrf_token() }}';

                        // Delete member
                        const formData = new FormData();
                        formData.append('reason', 'Deleted by user');
                        formData.append('_token', csrfToken);

                        fetch(`{{ url('/members') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        })
                            .then(async response => {
                                // Check for 419 CSRF token mismatch
                                if (response.status === 419) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: '{{ __('common.session_expired') }}',
                                        html: '<p>{{ __('common.session_expired_message') }}</p><p class="small mt-2">{{ __('common.refreshing_page') }}...</p>',
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                        timer: 1500
                                    });
                                    setTimeout(() => window.location.reload(), 500);
                                    return Promise.reject(new Error('Session expired'));
                                }

                                if (!response.ok) {
                                    const text = await response.text();
                                    let errorData;
                                    try {
                                        errorData = JSON.parse(text);
                                    } catch (e) {
                                        errorData = { message: text || 'Server error' };
                                    }
                                    throw new Error(errorData.message || 'Server error');
                                }

                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                } else {
                                    const text = await response.text();
                                    return { success: true, message: text || 'Member deleted successfully' };
                                }
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('{{ __('common.deleted') }}!', '{{ __('common.member_deleted_success') }}', 'success')
                                        .then(() => window.location.reload());
                                } else {
                                    Swal.fire('{{ __('common.error') }}!', data.message || '{{ __('common.failed_delete_member') }}', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                if (error.message !== 'Session expired') {
                                    Swal.fire('{{ __('common.error') }}!', error.message || '{{ __('common.failed_delete_member') }}', 'error');
                                }
                            });
                    }
                });
            };

            window.confirmDeleteChild = function (id) {
                if (!id) {
                    Swal.fire({ icon: 'error', title: '{{ __('common.error') }}', text: '{{ __('common.invalid_child_id') }}' });
                    return;
                }

                Swal.fire({
                    title: "{{ __('common.delete_child') }}?",
                    text: "{{ __('common.delete_child_confirm') }} {{ __('common.delete_warning') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: "{{ __('common.yes_delete') }}!",
                    cancelButtonText: "{{ __('common.cancel') }}"
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Get fresh CSRF token from meta tag
                        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                            document.querySelector('input[name="_token"]')?.value ||
                            '{{ csrf_token() }}';

                        // Delete child
                        const formData = new FormData();
                        formData.append('reason', 'Deleted by user');
                        formData.append('_token', csrfToken);

                        fetch(`{{ url('/children') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: formData
                        })
                            .then(async response => {
                                // Check for 419 CSRF token mismatch
                                if (response.status === 419) {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: '{{ __('common.session_expired') }}',
                                        html: '<p>{{ __('common.session_expired_message') }}</p><p class="small mt-2">{{ __('common.refreshing_page') }}...</p>',
                                        showConfirmButton: false,
                                        allowOutsideClick: false,
                                        timer: 1500
                                    });
                                    setTimeout(() => window.location.reload(), 500);
                                    return Promise.reject(new Error('Session expired'));
                                }

                                if (!response.ok) {
                                    const text = await response.text();
                                    let errorData;
                                    try {
                                        errorData = JSON.parse(text);
                                    } catch (e) {
                                        errorData = { message: text || 'Server error' };
                                    }
                                    throw new Error(errorData.message || 'Server error');
                                }

                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                } else {
                                    const text = await response.text();
                                    return { success: true, message: text || '{{ __('common.child_deleted_success') }}' };
                                }
                            })
                            .then(data => {
                                if (data.success) {
                                    Swal.fire('{{ __('common.deleted') }}!', '{{ __('common.child_deleted_success') }}', 'success')
                                        .then(() => window.location.reload());
                                } else {
                                    Swal.fire('{{ __('common.error') }}!', data.message || '{{ __('common.failed_delete_child') }}', 'error');
                                }
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                if (error.message !== 'Session expired') {
                                    Swal.fire('{{ __('common.error') }}!', error.message || '{{ __('common.failed_delete_child') }}', 'error');
                                }
                            });
                    }
                });
            };

            document.addEventListener('DOMContentLoaded', function () {
                // Flag to prevent infinite loop when setting tab programmatically
                let isSettingTabProgrammatically = false;

                // Handle tab switching
                const tabButtons = document.querySelectorAll('#memberTypeTabs button[data-bs-toggle="tab"]');

                tabButtons.forEach(button => {
                    button.addEventListener('shown.bs.tab', function (e) {
                        // Skip if we're setting the tab programmatically
                        if (isSettingTabProgrammatically) {
                            return;
                        }

                        const type = this.getAttribute('data-type');
                        const url = new URL(window.location);
                        const urlParams = url.searchParams;

                        // Check if URL already has the correct parameter
                        let shouldReload = false;

                        if (type === 'permanent' || type === 'temporary') {
                            if (urlParams.get('membership_type') !== type) {
                                shouldReload = true;
                            }
                        } else if (type === 'children') {
                            if (urlParams.get('type') !== 'children') {
                                shouldReload = true;
                            }
                        } else if (type === 'archived') {
                            if (urlParams.get('archived') !== '1') {
                                shouldReload = true;
                            }
                        }

                        // Only reload if URL parameters need to change
                        if (shouldReload) {
                            // Remove all type parameters first
                            url.searchParams.delete('membership_type');
                            url.searchParams.delete('type');
                            url.searchParams.delete('archived');

                            // Add the current type parameter
                            if (type === 'permanent' || type === 'temporary') {
                                url.searchParams.set('membership_type', type);
                            } else if (type === 'children') {
                                url.searchParams.set('type', 'children');
                            } else if (type === 'archived') {
                                url.searchParams.set('archived', '1');
                            }

                            // Reload page with new parameters
                            window.location.href = url.toString();
                        }
                    });
                });

                // Set active tab based on URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const membershipType = urlParams.get('membership_type');
                const type = urlParams.get('type');

                if (type === 'children') {
                    const childrenTab = document.getElementById('children-tab');
                    if (childrenTab && !childrenTab.classList.contains('active')) {
                        isSettingTabProgrammatically = true;
                        const tab = new bootstrap.Tab(childrenTab);
                        tab.show();
                        // Reset flag after a short delay to allow the tab to show
                        setTimeout(() => {
                            isSettingTabProgrammatically = false;
                        }, 100);
                    }
                } else if (membershipType === 'temporary') {
                    const temporaryTab = document.getElementById('temporary-tab');
                    if (temporaryTab && !temporaryTab.classList.contains('active')) {
                        isSettingTabProgrammatically = true;
                        const tab = new bootstrap.Tab(temporaryTab);
                        tab.show();
                        // Reset flag after a short delay to allow the tab to show
                        setTimeout(() => {
                            isSettingTabProgrammatically = false;
                        }, 100);
                    }
                } else if (membershipType === 'permanent') {
                    const permanentTab = document.getElementById('permanent-tab');
                    if (permanentTab && !permanentTab.classList.contains('active')) {
                        isSettingTabProgrammatically = true;
                        const tab = new bootstrap.Tab(permanentTab);
                        tab.show();
                        setTimeout(() => {
                            isSettingTabProgrammatically = false;
                        }, 100);
                    }
                } else {
                    // Default to 'all'
                    const allTab = document.getElementById('all-tab');
                    if (allTab && !allTab.classList.contains('active')) {
                        isSettingTabProgrammatically = true;
                        const tab = new bootstrap.Tab(allTab);
                        tab.show();
                        setTimeout(() => {
                            isSettingTabProgrammatically = false;
                        }, 100);
                    }
                }
            });
        </script>

        <!-- Add Child Modal -->
        <div class="modal fade" id="addChildModal" tabindex="-1" aria-labelledby="addChildModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="addChildModalLabel">
                            <i class="fas fa-child me-2"></i>{{ __('common.add_new_child') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="{{ __('common.close') }}"></button>
                    </div>
                    <form id="addChildForm">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>{{ __('common.note') }}:</strong> {{ __('common.child_parent_note') }}
                            </div>

                            <!-- Parent Selection -->
                            <div class="card mb-3 border-danger">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-users me-2"></i>{{ __('common.parent_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label for="child_parent_type" class="form-label">{{ __('common.parent_type') }} <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" id="child_parent_type" name="parent_type" required>
                                            <option value="member">{{ __('common.church_member') }}</option>
                                            <option value="non_member">{{ __('common.not_church_member') }}</option>
                                        </select>
                                    </div>

                                    <!-- Member Parent Selection -->
                                    <div id="memberParentSection">
                                        <label for="child_member_id" class="form-label">{{ __('common.select_parent_member') }}
                                            <span class="text-danger">*</span></label>
                                        <select class="form-select" id="child_member_id" name="member_id">
                                            <option value="">{{ __('common.search_parent_placeholder') }}</option>
                                        </select>
                                        <small class="text-muted">{{ __('common.couple_display_note') }}</small>
                                    </div>

                                    <!-- Non-Member Parent Information -->
                                    <div id="nonMemberParentSection" style="display: none;">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label for="child_parent_name" class="form-label">{{ __('common.parent_name') }}
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="child_parent_name"
                                                    name="parent_name" placeholder="{{ __('common.enter_parent_name') }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label for="child_parent_phone"
                                                    class="form-label">{{ __('common.phone_number') }}</label>
                                                <input type="text" class="form-control" id="child_parent_phone"
                                                    name="parent_phone" placeholder="+255...">
                                            </div>
                                            <div class="col-md-12">
                                                <label for="child_parent_relationship"
                                                    class="form-label">{{ __('common.relationship') }}</label>
                                                <input type="text" class="form-control" id="child_parent_relationship"
                                                    name="parent_relationship"
                                                    placeholder="{{ __('common.e_g_father_mother') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Child Information -->
                            <div class="card border-danger">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-child me-2"></i>Child Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="child_full_name" class="form-label">{{ __('common.full_name') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="child_full_name" name="full_name"
                                                required placeholder="{{ __('common.enter_child_name') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="child_gender" class="form-label">{{ __('common.gender') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="child_gender" name="gender" required>
                                                <option value="">{{ __('common.select') }}...</option>
                                                <option value="male">{{ __('common.male') }}</option>
                                                <option value="female">{{ __('common.female') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="child_date_of_birth" class="form-label">{{ __('common.date_of_birth') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="child_date_of_birth"
                                                name="date_of_birth" required max="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Baptism Information -->
                            <div class="card border-danger mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-tint me-2"></i>{{ __('common.baptism_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="child_baptism_status"
                                                class="form-label">{{ __('common.baptism_status') }}</label>
                                            <select class="form-select" id="child_baptism_status" name="baptism_status">
                                                <option value="">{{ __('common.select') }}...</option>
                                                <option value="baptized">{{ __('common.baptized') }}</option>
                                                <option value="not_baptized">{{ __('common.not_baptized') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4" id="child_baptism_date_wrapper" style="display: none;">
                                            <label for="child_baptism_date"
                                                class="form-label">{{ __('common.baptism_date') }}</label>
                                            <input type="date" class="form-control" id="child_baptism_date" name="baptism_date"
                                                max="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-4" id="child_baptism_location_wrapper" style="display: none;">
                                            <label for="child_baptism_location"
                                                class="form-label">{{ __('common.baptism_location') }}</label>
                                            <input type="text" class="form-control" id="child_baptism_location"
                                                name="baptism_location" placeholder="{{ __('common.church_name') }}">
                                        </div>
                                        <div class="col-md-4" id="child_baptized_by_wrapper" style="display: none;">
                                            <label for="child_baptized_by"
                                                class="form-label">{{ __('common.baptized_by') }}</label>
                                            <input type="text" class="form-control" id="child_baptized_by" name="baptized_by"
                                                placeholder="{{ __('common.pastor_name') }}">
                                        </div>
                                        <div class="col-md-4" id="child_baptism_certificate_wrapper" style="display: none;">
                                            <label for="child_baptism_certificate_number"
                                                class="form-label">{{ __('common.baptism_certificate_number') }}</label>
                                            <input type="text" class="form-control" id="child_baptism_certificate_number"
                                                name="baptism_certificate_number"
                                                placeholder="{{ __('common.certificate_number') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Welfare Status -->
                            <div class="card border-danger mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i
                                            class="fas fa-hand-holding-heart me-2"></i>{{ __('common.social_welfare_status') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="child_orphan_status"
                                                class="form-label">{{ __('common.orphan_status') }}</label>
                                            <select class="form-select" id="child_orphan_status" name="orphan_status">
                                                <option value="not_orphan">{{ __('common.not_orphan') }}</option>
                                                <option value="father_deceased">{{ __('common.father_deceased') }}</option>
                                                <option value="mother_deceased">{{ __('common.mother_deceased') }}</option>
                                                <option value="both_deceased">{{ __('common.both_deceased') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch pt-4">
                                                <input class="form-check-input" type="checkbox" id="child_disability_status"
                                                    name="disability_status" value="1">
                                                <label class="form-check-label fw-bold"
                                                    for="child_disability_status">{{ __('common.has_disability_q') }}</label>
                                            </div>
                                            <div id="child_disability_type_wrapper" class="mt-2" style="display: none;">
                                                <label for="child_disability_type"
                                                    class="form-label">{{ __('common.disability_type') }}</label>
                                                <input type="text" class="form-control" id="child_disability_type"
                                                    name="disability_type"
                                                    placeholder="{{ __('common.nature_of_disability') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch pt-4">
                                                <input class="form-check-input" type="checkbox" id="child_vulnerable_status"
                                                    name="vulnerable_status" value="1">
                                                <label class="form-check-label fw-bold"
                                                    for="child_vulnerable_status">{{ __('common.is_vulnerable_q') }}</label>
                                            </div>
                                            <div id="child_vulnerable_type_wrapper" class="mt-2" style="display: none;">
                                                <label for="child_vulnerable_type"
                                                    class="form-label">{{ __('common.vulnerability_type') }}</label>
                                                <input type="text" class="form-control" id="child_vulnerable_type"
                                                    name="vulnerable_type" placeholder="{{ __('common.e_g_poverty') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-save me-1"></i> {{ __('common.add_child') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Child Modal -->
        <div class="modal fade" id="editChildModal" tabindex="-1" aria-labelledby="editChildModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="editChildModalLabel">
                            <i class="fas fa-edit me-2"></i>{{ __('common.edit_child') }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="{{ __('common.close') }}"></button>
                    </div>
                    <form id="editChildForm">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <!-- Child Information -->
                            <div class="card border-danger">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-child me-2"></i>Child Information</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="edit_child_full_name" class="form-label">{{ __('common.full_name') }}
                                                <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="edit_child_full_name" name="full_name"
                                                required placeholder="{{ __('common.enter_child_name') }}">
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_child_gender" class="form-label">{{ __('common.gender') }} <span
                                                    class="text-danger">*</span></label>
                                            <select class="form-select" id="edit_child_gender" name="gender" required>
                                                <option value="">{{ __('common.select') }}...</option>
                                                <option value="male">{{ __('common.male') }}</option>
                                                <option value="female">{{ __('common.female') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <label for="edit_child_date_of_birth"
                                                class="form-label">{{ __('common.date_of_birth') }} <span
                                                    class="text-danger">*</span></label>
                                            <input type="date" class="form-control" id="edit_child_date_of_birth"
                                                name="date_of_birth" required max="{{ date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Baptism Information -->
                            <div class="card border-danger mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i class="fas fa-tint me-2"></i>{{ __('common.baptism_information') }}</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="edit_child_baptism_status"
                                                class="form-label">{{ __('common.baptism_status') }}</label>
                                            <select class="form-select" id="edit_child_baptism_status" name="baptism_status">
                                                <option value="">{{ __('common.select') }}...</option>
                                                <option value="baptized">{{ __('common.baptized') }}</option>
                                                <option value="not_baptized">{{ __('common.not_baptized') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4" id="edit_child_baptism_date_wrapper" style="display: none;">
                                            <label for="edit_child_baptism_date"
                                                class="form-label">{{ __('common.baptism_date') }}</label>
                                            <input type="date" class="form-control" id="edit_child_baptism_date"
                                                name="baptism_date" max="{{ date('Y-m-d') }}">
                                        </div>
                                        <div class="col-md-4" id="edit_child_baptism_location_wrapper" style="display: none;">
                                            <label for="edit_child_baptism_location"
                                                class="form-label">{{ __('common.baptism_location') }}</label>
                                            <input type="text" class="form-control" id="edit_child_baptism_location"
                                                name="baptism_location" placeholder="{{ __('common.church_name') }}">
                                        </div>
                                        <div class="col-md-4" id="edit_child_baptized_by_wrapper" style="display: none;">
                                            <label for="edit_child_baptized_by"
                                                class="form-label">{{ __('common.baptized_by') }}</label>
                                            <input type="text" class="form-control" id="edit_child_baptized_by"
                                                name="baptized_by" placeholder="{{ __('common.pastor_name') }}">
                                        </div>
                                        <div class="col-md-4" id="edit_child_baptism_certificate_wrapper"
                                            style="display: none;">
                                            <label for="edit_child_baptism_certificate_number"
                                                class="form-label">{{ __('common.baptism_certificate_number') }}</label>
                                            <input type="text" class="form-control" id="edit_child_baptism_certificate_number"
                                                name="baptism_certificate_number"
                                                placeholder="{{ __('common.certificate_number') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Social Welfare Status -->
                            <div class="card border-danger mt-3">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0"><i
                                            class="fas fa-hand-holding-heart me-2"></i>{{ __('common.social_welfare_status') }}
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label for="edit_child_orphan_status"
                                                class="form-label">{{ __('common.orphan_status') }}</label>
                                            <select class="form-select" id="edit_child_orphan_status" name="orphan_status">
                                                <option value="not_orphan">{{ __('common.not_orphan') }}</option>
                                                <option value="father_deceased">{{ __('common.father_deceased') }}</option>
                                                <option value="mother_deceased">{{ __('common.mother_deceased') }}</option>
                                                <option value="both_deceased">{{ __('common.both_deceased') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch pt-4">
                                                <input class="form-check-input" type="checkbox"
                                                    id="edit_child_disability_status" name="disability_status" value="1">
                                                <label class="form-check-label fw-bold"
                                                    for="edit_child_disability_status">{{ __('common.has_disability_q') }}</label>
                                            </div>
                                            <div id="edit_child_disability_type_wrapper" class="mt-2" style="display: none;">
                                                <label for="edit_child_disability_type"
                                                    class="form-label">{{ __('common.disability_type') }}</label>
                                                <input type="text" class="form-control" id="edit_child_disability_type"
                                                    name="disability_type"
                                                    placeholder="{{ __('common.nature_of_disability') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check form-switch pt-4">
                                                <input class="form-check-input" type="checkbox"
                                                    id="edit_child_vulnerable_status" name="vulnerable_status" value="1">
                                                <label class="form-check-label fw-bold"
                                                    for="edit_child_vulnerable_status">{{ __('common.is_vulnerable_q') }}</label>
                                            </div>
                                            <div id="edit_child_vulnerable_type_wrapper" class="mt-2" style="display: none;">
                                                <label for="edit_child_vulnerable_type"
                                                    class="form-label">{{ __('common.vulnerability_type') }}</label>
                                                <input type="text" class="form-control" id="edit_child_vulnerable_type"
                                                    name="vulnerable_type" placeholder="{{ __('common.e_g_poverty') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">{{ __('common.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> {{ __('common.update_child') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            // Add Child Modal Scripts
            document.addEventListener('DOMContentLoaded', function () {
                const parentTypeSelect = document.getElementById('child_parent_type');
                const memberParentSection = document.getElementById('memberParentSection');
                const nonMemberParentSection = document.getElementById('nonMemberParentSection');
                const memberIdSelect = document.getElementById('child_member_id');
                const parentNameInput = document.getElementById('child_parent_name');

                // Handle parent type change
                if (parentTypeSelect) {
                    parentTypeSelect.addEventListener('change', function () {
                        if (this.value === 'member') {
                            memberParentSection.style.display = 'block';
                            nonMemberParentSection.style.display = 'none';
                            if (memberIdSelect) memberIdSelect.required = true;
                            if (parentNameInput) parentNameInput.required = false;
                            // Clear non-member fields
                            document.getElementById('child_parent_name').value = '';
                            document.getElementById('child_parent_phone').value = '';
                            document.getElementById('child_parent_relationship').value = '';
                        } else {
                            memberParentSection.style.display = 'none';
                            nonMemberParentSection.style.display = 'block';
                            if (memberIdSelect) memberIdSelect.required = false;
                            if (parentNameInput) parentNameInput.required = true;
                            // Clear member selection
                            if (memberIdSelect) memberIdSelect.value = '';
                        }
                    });
                }

                // Handle baptism status change
                const baptismStatusSelect = document.getElementById('child_baptism_status');
                const baptismDateWrapper = document.getElementById('child_baptism_date_wrapper');
                const baptismLocationWrapper = document.getElementById('child_baptism_location_wrapper');
                const baptizedByWrapper = document.getElementById('child_baptized_by_wrapper');
                const baptismCertificateWrapper = document.getElementById('child_baptism_certificate_wrapper');

                if (baptismStatusSelect) {
                    baptismStatusSelect.addEventListener('change', function () {
                        if (this.value === 'baptized') {
                            if (baptismDateWrapper) baptismDateWrapper.style.display = '';
                            if (baptismLocationWrapper) baptismLocationWrapper.style.display = '';
                            if (baptizedByWrapper) baptizedByWrapper.style.display = '';
                            if (baptismCertificateWrapper) baptismCertificateWrapper.style.display = '';
                        } else {
                            if (baptismDateWrapper) baptismDateWrapper.style.display = 'none';
                            if (baptismLocationWrapper) baptismLocationWrapper.style.display = 'none';
                            if (baptizedByWrapper) baptizedByWrapper.style.display = 'none';
                            if (baptismCertificateWrapper) baptismCertificateWrapper.style.display = 'none';
                            // Clear baptism fields
                            const baptismDate = document.getElementById('child_baptism_date');
                            const baptismLocation = document.getElementById('child_baptism_location');
                            const baptizedBy = document.getElementById('child_baptized_by');
                            const baptismCertificate = document.getElementById('child_baptism_certificate_number');
                            if (baptismDate) baptismDate.value = '';
                            if (baptismLocation) baptismLocation.value = '';
                            if (baptizedBy) baptizedBy.value = '';
                            if (baptismCertificate) baptismCertificate.value = '';
                        }
                    });
                }

                // Handle child welfare toggles (add modal)
                const childDisabilityStatus = document.getElementById('child_disability_status');
                const childDisabilityTypeWrapper = document.getElementById('child_disability_type_wrapper');
                if (childDisabilityStatus && childDisabilityTypeWrapper) {
                    childDisabilityStatus.addEventListener('change', function () {
                        childDisabilityTypeWrapper.style.display = this.checked ? '' : 'none';
                        if (!this.checked) {
                            const input = document.getElementById('child_disability_type');
                            if (input) input.value = '';
                        }
                    });
                }

                const childVulnerableStatus = document.getElementById('child_vulnerable_status');
                const childVulnerableTypeWrapper = document.getElementById('child_vulnerable_type_wrapper');
                if (childVulnerableStatus && childVulnerableTypeWrapper) {
                    childVulnerableStatus.addEventListener('change', function () {
                        childVulnerableTypeWrapper.style.display = this.checked ? '' : 'none';
                        if (!this.checked) {
                            const input = document.getElementById('child_vulnerable_type');
                            if (input) input.value = '';
                        }
                    });
                }

                // Load members for dropdown - fetch from members list
                if (memberIdSelect) {
                    // Use a simple approach - load members when modal opens
                    const addChildModal = document.getElementById('addChildModal');
                    if (addChildModal) {
                        addChildModal.addEventListener('show.bs.modal', function () {
                            // Load members if not already loaded
                            if (memberIdSelect.options.length <= 1) {
                                fetch('{{ route("members.index") }}?wantsJson=1', {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data && Array.isArray(data)) {
                                            memberIdSelect.innerHTML = '<option value="">Search and select a parent...</option>';
                                            data.forEach(member => {
                                                const option = document.createElement('option');
                                                option.value = member.id;

                                                // Format display text
                                                let displayText = member.full_name;
                                                if (member.member_id) {
                                                    if (member.is_couple) {
                                                        // For couples, show both member IDs
                                                        displayText += ` (${member.member_id} & ${member.spouse_member_id || 'N/A'})`;
                                                    } else {
                                                        displayText += ` (${member.member_id})`;
                                                    }
                                                }

                                                option.textContent = displayText;
                                                option.setAttribute('data-is-couple', member.is_couple || false);
                                                if (member.is_couple && member.spouse_id) {
                                                    option.setAttribute('data-spouse-id', member.spouse_id);
                                                }
                                                memberIdSelect.appendChild(option);
                                            });
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error loading members:', error);
                                        // Fallback: try to get from current page data if available
                                    });
                            }
                        });
                    }
                }

                // Handle form submission
                const addChildForm = document.getElementById('addChildForm');
                if (addChildForm) {
                    addChildForm.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const formData = new FormData(this);
                        const parentType = document.getElementById('child_parent_type').value;

                        // Validate based on parent type
                        if (parentType === 'member') {
                            if (!formData.get('member_id')) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: 'Please select a parent member.'
                                });
                                return;
                            }
                            // Remove non-member fields
                            formData.delete('parent_name');
                            formData.delete('parent_phone');
                            formData.delete('parent_relationship');
                        } else {
                            if (!formData.get('parent_name')) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Validation Error',
                                    text: 'Please enter parent name.'
                                });
                                return;
                            }
                            // Remove member_id
                            formData.delete('member_id');
                        }

                        // Show loading
                        Swal.fire({
                            title: 'Adding Child...',
                            text: 'Please wait while we add the child.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch('{{ route("children.store") }}', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success!',
                                        text: data.message || 'Child added successfully.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Close modal
                                        const modal = bootstrap.Modal.getInstance(document.getElementById('addChildModal'));
                                        if (modal) {
                                            modal.hide();
                                        }
                                        // Reset form
                                        addChildForm.reset();
                                        // Reload page to show new child
                                        window.location.reload();
                                    });
                                } else {
                                    let errorMessage = data.message || 'Failed to add child.';
                                    if (data.errors) {
                                        const errorList = Object.values(data.errors).flat();
                                        errorMessage = errorList.join('<br>');
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        html: errorMessage
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'An error occurred while adding the child.'
                                });
                            });
                    });
                }

                // Edit Child Function
                window.editChild = function (childId) {
                    // Fetch child data
                    fetch(`{{ url('/children') }}/${childId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data && data.id) {
                                // Populate form fields
                                document.getElementById('edit_child_full_name').value = data.full_name || '';
                                document.getElementById('edit_child_gender').value = data.gender || '';
                                document.getElementById('edit_child_date_of_birth').value = data.date_of_birth ? data.date_of_birth.split('T')[0] : '';

                                // Set baptism status
                                const baptismStatus = document.getElementById('edit_child_baptism_status');
                                if (baptismStatus) {
                                    baptismStatus.value = data.baptism_status || '';
                                    // Trigger change to show/hide fields
                                    baptismStatus.dispatchEvent(new Event('change'));

                                    // Set baptism fields if baptized
                                    if (data.baptism_status === 'baptized') {
                                        document.getElementById('edit_child_baptism_date').value = data.baptism_date ? data.baptism_date.split('T')[0] : '';
                                        document.getElementById('edit_child_baptism_location').value = data.baptism_location || '';
                                        document.getElementById('edit_child_baptized_by').value = data.baptized_by || '';
                                        document.getElementById('edit_child_baptism_certificate_number').value = data.baptism_certificate_number || '';
                                    }
                                }

                                // Set form action
                                const editForm = document.getElementById('editChildForm');
                                editForm.action = `{{ url('/children') }}/${childId}`;
                                editForm.setAttribute('data-child-id', childId);

                                // Show modal
                                const editModal = new bootstrap.Modal(document.getElementById('editChildModal'));
                                editModal.show();
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') }}',
                                    text: '{{ __('common.could_not_load_child_data') }}'
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error loading child:', error);
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __('common.error') }}',
                                text: '{{ __('common.failed_to_load_child_data') }}'
                            });
                        });
                };

                // Handle edit baptism status change
                const editBaptismStatusSelect = document.getElementById('edit_child_baptism_status');
                const editBaptismDateWrapper = document.getElementById('edit_child_baptism_date_wrapper');
                const editBaptismLocationWrapper = document.getElementById('edit_child_baptism_location_wrapper');
                const editBaptizedByWrapper = document.getElementById('edit_child_baptized_by_wrapper');
                const editBaptismCertificateWrapper = document.getElementById('edit_child_baptism_certificate_wrapper');

                if (editBaptismStatusSelect) {
                    editBaptismStatusSelect.addEventListener('change', function () {
                        if (this.value === 'baptized') {
                            if (editBaptismDateWrapper) editBaptismDateWrapper.style.display = '';
                            if (editBaptismLocationWrapper) editBaptismLocationWrapper.style.display = '';
                            if (editBaptizedByWrapper) editBaptizedByWrapper.style.display = '';
                            if (editBaptismCertificateWrapper) editBaptismCertificateWrapper.style.display = '';
                        } else {
                            if (editBaptismDateWrapper) editBaptismDateWrapper.style.display = 'none';
                            if (editBaptismLocationWrapper) editBaptismLocationWrapper.style.display = 'none';
                            if (editBaptizedByWrapper) editBaptizedByWrapper.style.display = 'none';
                            if (editBaptismCertificateWrapper) editBaptismCertificateWrapper.style.display = 'none';
                            // Clear baptism fields
                            const baptismDate = document.getElementById('edit_child_baptism_date');
                            const baptismLocation = document.getElementById('edit_child_baptism_location');
                            const baptizedBy = document.getElementById('edit_child_baptized_by');
                            const baptismCertificate = document.getElementById('edit_child_baptism_certificate_number');
                            if (baptismDate) baptismDate.value = '';
                            if (baptismLocation) baptismLocation.value = '';
                            if (baptizedBy) baptizedBy.value = '';
                            if (baptismCertificate) baptismCertificate.value = '';
                        }
                    });
                }

                // Handle edit form submission
                const editChildForm = document.getElementById('editChildForm');
                if (editChildForm) {
                    editChildForm.addEventListener('submit', function (e) {
                        e.preventDefault();

                        const formData = new FormData(this);
                        const childId = this.getAttribute('data-child-id');

                        // Show loading
                        Swal.fire({
                            title: '{{ __('common.updating_child') }}...',
                            text: '{{ __('common.please_wait_updating_child') }}',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        fetch(`{{ url('/children') }}/${childId}`, {
                            method: 'PUT',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: formData
                        })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: '{{ __('common.success') }}!',
                                        text: data.message || '{{ __('common.child_updated_success') }}',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Close modal
                                        const modal = bootstrap.Modal.getInstance(document.getElementById('editChildModal'));
                                        if (modal) {
                                            modal.hide();
                                        }
                                        // Reset form
                                        editChildForm.reset();
                                        // Reload page to show updated child
                                        window.location.reload();
                                    });
                                } else {
                                    let errorMessage = data.message || '{{ __('common.failed_update_child') }}';
                                    if (data.errors) {
                                        const errorList = Object.values(data.errors).flat();
                                        errorMessage = errorList.join('<br>');
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('common.error') }}!',
                                        html: errorMessage
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: '{{ __('common.error') }}!',
                                    text: '{{ __('common.error_occurred_updating_child') }}'
                                });
                            });
                    });
                }
            });
        </script>
    @endsection
@endsection