

<!DOCTYPE html>
<html lang="en">
    <head>
        <style>
        /* Force modal and backdrop to appear on top */
        .modal-backdrop.show {
            z-index: 2050 !important;
        }
        .modal.show {
            z-index: 2100 !important;
            display: block !important;
        }
        #archiveMemberModal {
            z-index: 2100 !important;
        }
        </style>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Waumini Link - Dashboard</title>
        <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('assets/css/datatables.min.css') }}" rel="stylesheet" />
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" />
        <script src="{{ asset('assets/js/fontawesome.min.js') }}" crossorigin="anonymous"></script>
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <style>
            .logo-white-section {
                background-color: white !important;
                border-radius: 8px;
                margin: 8px 0;
                padding: 8px 16px !important;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: all 0.3s ease;
            }
            .logo-white-section:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            .navbar-brand .logo {
                transition: all 0.3s ease;
            }
            .navbar-brand .logo:hover {
                transform: scale(1.05);
            }
            .navbar-brand {
                min-height: 60px;
                display: flex !important;
                align-items: center !important;
            }
            .sb-sidenav {
                background-color: #17082d !important;
            }
            .sb-sidenav .nav-link {
                color: white !important;
                transition: all 0.3s ease;
            }
            .sb-sidenav .nav-link:hover {
                background-color: #293846 !important;
                color: white !important;
            }
            .sb-sidenav .sb-sidenav-menu-heading {
                color: rgba(255, 255, 255, 0.6) !important;
            }
            .sb-sidenav .sb-nav-link-icon {
                color: white !important;
            }
            .sb-sidenav .sb-sidenav-collapse-arrow {
                color: white !important;
            }
            .sb-sidenav .sb-sidenav-footer {
                background-color: rgba(255, 255, 255, 0.1) !important;
                color: white !important;
            }
            .card-header {
                color: white !important;
                font-weight: 600;
            }
            .card .small.text-white-50 {
                color: white !important;
                font-weight: 500;
            }
            /* Interactive table styling for details tables */
            .table.interactive-table tbody tr {
                transition: background-color 0.2s ease, box-shadow 0.2s ease;
            }
            .table.interactive-table tbody tr:hover {
                background-color: #f8f9ff;
            }
            .table.interactive-table tbody tr td:first-child {
                border-left: 4px solid #5b2a86;
            }
            /* Slightly wider member details modal */
            #memberDetailsModal .modal-dialog { max-width: 700px; }
            #memberDetailsModal .modal-footer {
                background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%);
                border-top: 0;
                color: #ffffff;
            }
            #memberDetailsModal .modal-footer a.emca-link { color: #ffffff; text-decoration: none; }
            #memberDetailsModal .modal-footer a.emca-link:hover { text-decoration: underline; opacity: 0.95; }
            /* QR styling */
            #inlineQrImg { border: 3px solid #5b2a86; border-radius: 8px; padding: 4px; background: #ffffff; }
            #qrSpinner { width: 2.5rem; height: 2.5rem; }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <!-- Header -->
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-3 d-flex align-items-center logo-white-section" href="{{ route('dashboard.secretary') }}">
                <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo" style="height: 45px; max-width: 200px; object-fit: contain;">
            </a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <div class="navbar-text text-white me-auto ms-3" style="font-size: 1.1rem;">
                <strong>Welcome to Waumini Link</strong>
            </div>
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#!">Settings</a></li>
                        <li><a class="dropdown-item" href="#!">Activity Log</a></li>
                        <li><hr class="dropdown-divider" /></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </nav>
        <div id="layoutSidenav">
            <!-- Sidebar -->
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Main</div>
                            <a class="nav-link" href="{{ route('dashboard.secretary') }}">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            <div class="sb-sidenav-menu-heading">Management</div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseMembers" aria-expanded="false" aria-controls="collapseMembers">
                                <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                                Members
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseMembers" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="{{ route('members.view') }}"><i class="fas fa-list me-2"></i>All Members</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseEvents" aria-expanded="false" aria-controls="collapseEvents">
                                <div class="sb-nav-link-icon"><i class="fas fa-calendar-alt"></i></div>
                                Events & Services
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseEvents" aria-labelledby="headingTwo" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="#"><i class="fas fa-church me-2"></i>Sunday Services</a>
                                    <a class="nav-link" href="#"><i class="fas fa-calendar-plus me-2"></i>Special Events</a>
                                    <a class="nav-link" href="#"><i class="fas fa-birthday-cake me-2"></i>Celebrations</a>
                                </nav>
                            </div>
                            <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseFinance" aria-expanded="false" aria-controls="collapseFinance">
                                <div class="sb-nav-link-icon"><i class="fas fa-donate"></i></div>
                                Finance
                                <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                            </a>
                            <div class="collapse" id="collapseFinance" aria-labelledby="headingThree" data-bs-parent="#sidenavAccordion">
                                <nav class="sb-sidenav-menu-nested nav">
                                    <a class="nav-link" href="#"><i class="fas fa-money-bill-wave me-2"></i>Donations</a>
                                    <a class="nav-link" href="#"><i class="fas fa-receipt me-2"></i>Expenses</a>
                                    <a class="nav-link" href="#"><i class="fas fa-chart-pie me-2"></i>Financial Reports</a>
                                </nav>
                            </div>
                            <div class="sb-sidenav-menu-heading">Reports</div>
                            <a class="nav-link" href="#">
                                <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                                Analytics
                            </a>
                            <a class="nav-link" href="#">
                                <div class="sb-nav-link-icon"><i class="fas fa-file-alt"></i></div>
                                Reports
                            </a>
                            <div class="sb-sidenav-menu-heading">Settings</div>
                            <a class="nav-link" href="#">
                                <div class="sb-nav-link-icon"><i class="fas fa-cog"></i></div>
                                System Settings
                            </a>
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as:</div>
                        {{ Auth::user()->name ?? 'User' }}
                    </div>
                </nav>
            </div>
            
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <div class="d-flex flex-wrap align-items-center justify-content-between mt-4 mb-3 gap-2">
                            <h2 class="mb-0">Members</h2>
                            <div class="d-flex gap-2">
                                <a href="{{ route('members.add') }}" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Add Member</a>
                                <a href="{{ route('members.export.csv', request()->query()) }}" class="btn btn-outline-success"><i class="fas fa-file-excel me-2"></i>Export CSV</a>
                                <button class="btn btn-outline-secondary" onclick="window.print()"><i class="fas fa-print me-2"></i>Print</button>
                            </div>
                        </div>

                        <!-- Tabs and main table section -->
                        @php
                            $permanentCount = $members->where('membership_type','permanent')->count();
                            $temporaryCount = $members->where('membership_type','temporary')->count();
                            $archivedCount = ($archivedMembers ?? collect())->count();
                        @endphp
                        <style>
                            .tab-badge {
                                background: linear-gradient(90deg, #5b2a86 0%, #1f2b6c 100%);
                                color: #fff;
                                font-size: 0.95em;
                                font-weight: 600;
                                border-radius: 12px;
                                padding: 2px 10px;
                                margin-left: 6px;
                                box-shadow: 0 1px 4px rgba(91,42,134,0.10);
                                vertical-align: middle;
                                letter-spacing: 0.02em;
                                transition: background 0.2s;
                            }
                            .nav-tabs .nav-link.active .tab-badge {
                                background: linear-gradient(90deg, #1f2b6c 0%, #5b2a86 100%);
                                color: #fff;
                            }
                        </style>
                        <ul class="nav nav-tabs" id="memberTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="permanent-tab" data-bs-toggle="tab" data-bs-target="#permanent" type="button" role="tab">
                                    Permanent <span class="tab-badge">{{ $permanentCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="temporary-tab" data-bs-toggle="tab" data-bs-target="#temporary" type="button" role="tab">
                                    Temporary <span class="tab-badge">{{ $temporaryCount }}</span>
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="archived-tab" data-bs-toggle="tab" data-bs-target="#archived" type="button" role="tab">
                                    Archived <span class="tab-badge">{{ $archivedCount }}</span>
                                </button>
                            </li>
                        </ul>
                        <div class="tab-content border-bottom border-start border-end p-3" id="memberTabsContent">
                            <div class="tab-pane fade show active" id="permanent" role="tabpanel">
                                @include('members.partials.main-table', ['members' => $members->where('membership_type','permanent'), 'showArchive' => true])
                            </div>
                            <div class="tab-pane fade" id="temporary" role="tabpanel">
                                @include('members.partials.main-table', ['members' => $members->where('membership_type','temporary'), 'showArchive' => true])
                            </div>
                            <div class="tab-pane fade" id="archived" role="tabpanel">
                                @include('members.partials.main-table', ['members' => $archivedMembers ?? collect(), 'showArchive' => false, 'isArchived' => true])
                            </div>
                        </div>
                </main>

                <!-- Details Modal -->
                <div class="modal fade" id="memberDetailsModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-scrollable">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1f2b6c 0%, #5b2a86 100%); border: none;">
                                <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-id-card" aria-label="Member details"></i><span>Member Details</span></h5>
                                <div class="ms-auto d-flex gap-2 align-items-center">
                                    <button class="btn btn-sm btn-outline-light" id="btnCopyAllDetails" title="Copy all details" aria-label="Copy all details"><i class="fas fa-copy"></i></button>
                                    <div class="vr opacity-50 mx-1"></div>
                                    <button class="btn btn-sm btn-light" id="btnDownloadExcel" title="Download Excel"><i class="fas fa-file-excel text-success"></i></button>
                                    <button class="btn btn-sm btn-light" id="btnDownloadPDF" title="Download PDF"><i class="fas fa-file-pdf text-danger"></i></button>
                                    <button class="btn btn-sm btn-light" id="btnPrintDetails" title="Print"><i class="fas fa-print text-secondary"></i></button>
                                </div>
                                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light" id="memberDetailsBody">
                                <div class="text-center text-muted py-4">Loading...</div>
                            </div>
                            <div class="modal-footer d-flex justify-content-between align-items-center">
                                <div class="small">
                                    <span class="me-1">Powered by</span>
                                    <a href="https://emca.tech/#" target="_blank" rel="noopener" class="emca-link fw-semibold">EmCa Technologies</a>
                                </div>
                                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Section Chooser Modal -->
                <div class="modal fade" id="editSectionChooserModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content border-0 shadow" style="border-radius: 14px; overflow: hidden;">
                            <div class="modal-header text-white" style="background: linear-gradient(135deg,#5b2a86 0%, #0ea5ea 100%); border: none;">
                                <h6 class="modal-title"><i class="fas fa-edit me-2"></i>Select Section to Edit</h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="d-grid gap-2">
                                    <button class="btn btn-outline-primary" id="btnEditPersonal"><i class="fas fa-user me-2"></i>Personal</button>
                                    <button class="btn btn-outline-primary" id="btnEditLocation"><i class="fas fa-map-marker-alt me-2"></i>Location</button>
                                    <button class="btn btn-outline-primary" id="btnEditFamily"><i class="fas fa-home me-2"></i>Family</button>
                                        </div>
                            </div>
                        </div>
                    </div>

                <!-- Edit Personal Modal -->
                <div class="modal fade" id="memberEditPersonalModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                            <div class="modal-header bg-white border-0">
                                <h6 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-user text-primary"></i><span>Edit Personal</span></h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editPersonalForm">
                                    <input type="hidden" id="edit_personal_id">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="edit_personal_full_name" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="edit_personal_email">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="edit_personal_phone_number">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Membership Type</label>
                                            <select id="edit_personal_membership_type" class="form-select">
                                                <option value="permanent">Permanent</option>
                                                <option value="temporary">Temporary</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Gender</label>
                                            <select id="edit_personal_gender" class="form-select">
                                                <option value="">Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Date of Birth</label>
                                            <input type="date" class="form-control" id="edit_personal_date_of_birth">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">NIDA Number</label>
                                            <input type="text" class="form-control" id="edit_personal_nida_number">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tribe</label>
                                            <select id="edit_personal_tribe" class="form-select"></select>
                                        </div>
                                        <div class="col-md-6" id="edit_personal_other_tribe_group" style="display:none;">
                                            <label class="form-label">Other Tribe</label>
                                            <input type="text" class="form-control" id="edit_personal_other_tribe" placeholder="Specify tribe">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Location Modal -->
                <div class="modal fade" id="memberEditLocationModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                            <div class="modal-header bg-white border-0">
                                <h6 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-map-marker-alt text-primary"></i><span>Edit Location</span></h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editLocationForm">
                                    <input type="hidden" id="edit_location_id">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Region</label>
                                            <select id="edit_location_region" class="form-select"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">District</label>
                                            <select id="edit_location_district" class="form-select"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Ward</label>
                                            <select id="edit_location_ward" class="form-select"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Street</label>
                                            <input type="text" class="form-control" id="edit_location_street">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Address</label>
                                            <input type="text" class="form-control" id="edit_location_address">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Edit Family Modal -->
                <div class="modal fade" id="memberEditFamilyModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 18px; overflow: hidden;">
                            <div class="modal-header bg-white border-0">
                                <h6 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-home text-primary"></i><span>Edit Family</span></h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form id="editFamilyForm">
                                    <input type="hidden" id="edit_family_id">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Living with family</label>
                                            <select id="edit_family_living_with_family" class="form-select">
                                                <option value="">Select</option>
                                                <option value="yes">Yes</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>
                                        <div class="col-md-8">
                                            <label class="form-label">Family relationship</label>
                                            <input type="text" class="form-control" id="edit_family_family_relationship">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Compact Add Member Modal (icon-triggered in table header) -->
                <div class="modal fade" id="addMemberModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                            <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                <h5 class="modal-title d-flex align-items-center gap-2"><i class="fas fa-user-plus"></i><span>Register New Member</span></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body bg-light">
                                <form id="quickAddMemberForm">
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <label class="form-label">Full Name</label>
                                            <input type="text" class="form-control" id="add_full_name" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Gender</label>
                                            <select class="form-select" id="add_gender">
                                                <option value="">Select</option>
                                                <option value="male">Male</option>
                                                <option value="female">Female</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Phone</label>
                                            <input type="text" class="form-control" id="add_phone_number">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" class="form-control" id="add_email">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Region</label>
                                            <select id="add_region" class="form-select"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">District</label>
                                            <select id="add_district" class="form-select"></select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Ward</label>
                                            <select id="add_ward" class="form-select"></select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Tribe</label>
                                            <select id="add_tribe" class="form-select"></select>
                                        </div>
                                        <div class="col-md-6" id="add_other_tribe_group" style="display:none;">
                                            <label class="form-label">Other Tribe</label>
                                            <input type="text" class="form-control" id="add_other_tribe">
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2 mt-3">
                                        <button type="button" class="btn btn-outline-danger" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-primary">Register</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <footer class="bg-dark text-light py-4 mt-auto">
  <div class="container px-4">
    <div class="row align-items-center">
      <!-- Left Side -->
      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
        <small>&copy; <span id="year"></span> Waumini Link — Version 1.0</small>
      </div>

      <!-- Right Side -->
      <div class="col-md-6 text-center text-md-end">
        <small>
          Powered by 
          <a href="https://emca.tech/#" class="text-decoration-none text-info fw-semibold">
            EmCa Technologies
          </a>
        </small>
      </div>
    </div>
  </div>
                </footer>
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
            </div>
        </div>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script>
        // Archive member logic (robust, attaches only once)
        (function() {
            let archiveMemberId = null;
            window.openArchiveModal = function(id) {
                archiveMemberId = id;
                document.getElementById('archive_member_id').value = id;
                document.getElementById('archive_reason').value = '';
                var modalEl = document.getElementById('archiveMemberModal');
                // Robust modal show: Bootstrap 5, fallback to native
                if (window.bootstrap && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                    modal.show();
                } else if (modalEl.showModal) {
                    modalEl.showModal();
                } else {
                    // Fallback: force display
                    modalEl.style.display = 'block';
                    modalEl.classList.add('show');
                }
            };
            // Attach submit handler only once
            const form = document.getElementById('archiveMemberForm');
            if (form && !form._archiveHandlerAttached) {
                form.addEventListener('submit', function(e) {
                    alert('DEBUG: Archive submit handler fired!');
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
        // Activate correct tab based on ?tab=permanent|temporary|archived
        (function() {
            function getQueryParam(name) {
                const url = new URL(window.location.href);
                return url.searchParams.get(name);
            }
            const tab = getQueryParam('tab');
            if(tab && ['permanent','temporary','archived'].includes(tab)) {
                const trigger = document.getElementById(tab+'-tab');
                if(trigger) {
                    // Bootstrap 5 tab activation
                    if(window.bootstrap && bootstrap.Tab) {
                        const tabObj = new bootstrap.Tab(trigger);
                        tabObj.show();
                    } else {
                        // fallback: click
                        trigger.click();
                    }
                }
            }
        })();
        </script>
        <script>
            // Globals to share state between details/print
            let currentDetailsMember = null;
            function formatDateDisplay(value){
                if(!value) return '-';
                try{
                    const d = new Date(value);
                    if (!isNaN(d.getTime())) {
                        const dd = String(d.getDate()).padStart(2,'0');
                        const mm = String(d.getMonth()+1).padStart(2,'0');
                        const yyyy = d.getFullYear();
                        return `${dd}/${mm}/${yyyy}`;
                    }
                }catch(e){}
                const datePart = String(value).split('T')[0];
                if (datePart && datePart.includes('-')){
                    const [y,m,d] = datePart.split('-');
                    if (y && m && d) return `${d}/${m}/${y}`;
                }
                return datePart || '-';
            }
            function confirmThen(message, onConfirm){
                Swal.fire({ title: message, icon: 'question', showCancelButton: true, confirmButtonText: 'Yes', cancelButtonText: 'No', confirmButtonColor: '#5b2a86', cancelButtonColor: '#6c757d' }).then(r=>{ if(r.isConfirmed) { try{ onConfirm && onConfirm(); }catch(e){ console.error(e); } }});
            }

            function handleAction(fn){ confirmThen('Proceed with this action?', fn); return false; }

            function viewDetails(id) {
                fetch(`{{ url('/members') }}/${id}`, { headers: { 'Accept': 'application/json' } })
                    .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(m => {
                        currentDetailsMember = m;
                        // Determine if archived (by checking if member_snapshot exists)
                        let isArchived = false;
                        let snap = null;
                        let archiveReason = null;
                        if (m.member_snapshot) {
                            isArchived = true;
                            snap = m.member_snapshot;
                            archiveReason = m.reason || null;
                        }
                        const data = isArchived ? snap : m;
                        // Helper functions
                        const actionCell = (content, actionsHtml = '') => `<div class="d-flex align-items-center justify-content-between">${content}<span class="ms-2 d-inline-flex gap-2">${actionsHtml}</span></div>`;
                        const badge = (text, tone = 'secondary') => `<span class="badge bg-${tone}">${text}</span>`;
                        const copyBtn = (text, title, icon) => `<button type="button" class="btn btn-sm btn-outline-secondary" onclick="navigator.clipboard.writeText('${(text||'').toString().replace(/'/g, "&#39;") }').then(()=>Swal.fire({ icon:'success', title:'Copied', timer:900, showConfirmButton:false })).catch(()=>Swal.fire({ icon:'error', title:'Copy failed' }))" title="${title}" aria-label="${title}"><i class="${icon}"></i></button>`;
                        const mailto = (email) => {
                            if (!email) return '';
                            const raw = String(email).trim();
                            const escaped = raw.replace(/[&"<>]/g, c => ({'&':'&amp;','"':'&quot;','<':'&lt;','>':'&gt;'}[c]));
                            return `<a href="mailto:${escaped}" onclick="window.location.href=this.href; return false;" class="btn btn-sm btn-outline-primary" title="Send email" aria-label="Send email"><i class="fas fa-paper-plane"></i></a>`;
                        };
                        const telto = (phone) => {
                            if (!phone) return '';
                            const sanitized = String(phone).replace(/[^+\d]/g, '');
                            return `<a href="tel:${sanitized}" onclick="window.location.href=this.href; return false;" class="btn btn-sm btn-outline-primary" title="Call" aria-label="Call"><i class="fas fa-phone"></i></a>`;
                        };
                        const mapsBtn = (q) => q ? `<a href="#" onclick="return handleAction(()=>window.open('https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(q)}','_blank'))" class="btn btn-sm btn-outline-success" title="Open in Maps" aria-label="Open in Maps"><i class="fas fa-map-marked-alt"></i></a>` : '';
                        const row = (icon, label, value, actions = '') => `
                            <tr>
                                <td class="text-muted text-nowrap"><i class="${icon} me-2" aria-hidden="true"></i>${label}</td>
                                <td class="fw-semibold">${actionCell(value || '—', actions)}</td>
                            </tr>`;
                        // Compose QR payload with all fields
                        const lines = [
                            `Full Name: ${data.full_name || '-'}`,
                            `Member ID: ${data.member_id || '-'}`,
                            `Membership Type: ${data.membership_type || '-'}`,
                            `Member Type: ${data.member_type || '-'}`,
                            `Phone: ${data.phone_number || '-'}`,
                            `Email: ${data.email || '-'}`,
                            `Gender: ${data.gender ? data.gender.charAt(0).toUpperCase()+data.gender.slice(1) : '-'}`,
                            `Date of Birth: ${formatDateDisplay(data.date_of_birth)}`,
                            `Education Level: ${data.education_level || '-'}`,
                            `Profession: ${data.profession || '-'}`,
                            `NIDA Number: ${data.nida_number || '-'}`,
                            `Region: ${data.region || '-'}`,
                            `District: ${data.district || '-'}`,
                            `Ward: ${data.ward || '-'}`,
                            `Street: ${data.street || '-'}`,
                            `Address: ${data.address || '-'}`,
                            `Living with family: ${data.living_with_family || '-'}`,
                            `Family relationship: ${data.family_relationship || '-'}`,
                            `Tribe: ${(data.tribe || '-') + (data.other_tribe ? ` (${data.other_tribe})` : '')}`,
                        ];
                        if (data.guardian_name || data.guardian_phone || data.guardian_relationship) {
                            lines.push(`Guardian Name: ${data.guardian_name || '-'}`);
                            lines.push(`Guardian Phone: ${data.guardian_phone || '-'}`);
                            lines.push(`Guardian Relationship: ${data.guardian_relationship || '-'}`);
                        }
                        if (archiveReason) {
                            lines.push(`Archive Reason: ${archiveReason}`);
                        }
                        const qrPayload = lines.join('\n');
                        // Build HTML
                        let html = `<div id=\"memberDetailsPrint\" class=\"p-2\">
                            <div class=\"d-flex justify-content-center\">
                                <div class=\"text-center mb-3\">
                                    <img id=\"inlineQrImg\" alt=\"Member details QR\" width=\"120\" height=\"120\"/>
                                    <div class=\"text-muted small mt-1\">Scan for details</div>
                                </div>
                            </div>
                            <div class=\"small text-uppercase text-muted mt-2 mb-1\">Personal</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-user', 'Full Name', data.full_name)}
                                ${row('fas fa-id-badge', 'Member ID', data.member_id, copyBtn(data.member_id, 'Copy ID', 'fas fa-copy'))}
                                ${row('fas fa-id-card', 'Membership Type', data.membership_type)}
                                ${row('fas fa-user-tag', 'Member Type', data.member_type)}
                                ${row('fas fa-phone', 'Phone', data.phone_number, telto(data.phone_number) + copyBtn(data.phone_number, 'Copy phone', 'fas fa-copy'))}
                                ${row('fas fa-envelope', 'Email', data.email, mailto(data.email) + copyBtn(data.email, 'Copy email', 'fas fa-copy'))}
                                ${row('fas fa-venus-mars', 'Gender', data.gender ? badge(data.gender.charAt(0).toUpperCase()+data.gender.slice(1), (data.gender||'').toLowerCase()==='male' ? 'primary' : 'danger') : '—')}
                                ${row('fas fa-birthday-cake', 'Date of Birth', formatDateDisplay(data.date_of_birth))}
                                ${row('fas fa-graduation-cap', 'Education Level', data.education_level)}
                                ${row('fas fa-briefcase', 'Profession', data.profession)}
                                ${row('fas fa-id-card', 'NIDA Number', data.nida_number)}
                            </tbody></table>
                            <div class=\"small text-uppercase text-muted mt-3 mb-1\">Location</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-map', 'Region', data.region ? badge(data.region, 'secondary') : '—', mapsBtn([data.region,'Tanzania'].filter(Boolean).join(', ')))}
                                ${row('fas fa-city', 'District', data.district ? badge(data.district, 'secondary') : '—', mapsBtn([data.district,data.region,'Tanzania'].filter(Boolean).join(', ')))}
                                ${row('fas fa-location-arrow', 'Ward', data.ward ? badge(data.ward, 'secondary') : '—', mapsBtn([data.ward,data.district,data.region,'Tanzania'].filter(Boolean).join(', ')))}
                                ${row('fas fa-road', 'Street', data.street || '—', mapsBtn([data.street,data.ward,data.district,data.region,'Tanzania'].filter(Boolean).join(', ')))}
                                ${row('fas fa-address-card', 'Address', data.address || '—', mapsBtn([data.address,data.street,data.ward,data.district,data.region,'Tanzania'].filter(Boolean).join(', ')))}
                            </tbody></table>
                            <div class=\"small text-uppercase text-muted mt-3 mb-1\">Family</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-users', 'Living with family', data.living_with_family)}
                                ${row('fas fa-user-friends', 'Family relationship', data.family_relationship)}
                                ${row('fas fa-flag', 'Tribe', (data.tribe || '') + (data.other_tribe ? ` (${data.other_tribe})` : ''))}
                            </tbody></table>`;
                        // Guardian section (for temporary)
                        if (data.membership_type === 'temporary' && (data.guardian_name || data.guardian_phone || data.guardian_relationship)) {
                            html += `<div class=\"small text-uppercase text-muted mt-3 mb-1\">Guardian</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-user-shield', 'Guardian Name', data.guardian_name)}
                                ${row('fas fa-phone-square', 'Guardian Phone', data.guardian_phone)}
                                ${row('fas fa-users-cog', 'Relationship', data.guardian_relationship)}
                            </tbody></table>`;
                        }
                        // Children section (for permanent)
                        if (data.membership_type === 'permanent' && Array.isArray(data.children) && data.children.length > 0) {
                            html += `<div class=\"small text-uppercase text-muted mt-3 mb-1\">Children</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><thead><tr><th>Name</th><th>Gender</th><th>Date of Birth</th></tr></thead><tbody>`;
                            data.children.forEach(child => {
                                html += `<tr><td>${child.full_name || '-'}</td><td>${child.gender || '-'}</td><td>${formatDateDisplay(child.date_of_birth)}</td></tr>`;
                            });
                            html += `</tbody></table>`;
                        }
                        // Archive reason (for archived)
                        if (isArchived && archiveReason) {
                            html += `<div class=\"small text-uppercase text-muted mt-3 mb-1\">Archive Info</div>
                            <table class=\"table table-bordered table-striped align-middle interactive-table\"><tbody>
                                ${row('fas fa-archive', 'Reason for Archiving', archiveReason)}
                            </tbody></table>`;
                        }
                        html += `</div>`;
                        document.getElementById('memberDetailsBody').innerHTML = html;
                        const detailsModalEl = document.getElementById('memberDetailsModal');
                        const modal = new bootstrap.Modal(detailsModalEl);
                        // Set QR image src to encoded details (no link shown when scanning)
                        const qrData = encodeURIComponent(qrPayload);
                        setTimeout(() => {
                            const img = document.getElementById('inlineQrImg');
                            if (img) {
                                try {
                                    const spinner = document.createElement('div');
                                    spinner.id = 'qrSpinner';
                                    spinner.className = 'spinner-border text-primary';
                                    spinner.setAttribute('role', 'status');
                                    if (img.parentElement) img.parentElement.insertBefore(spinner, img);
                                    img.style.display = 'none';
                                    img.onload = () => { spinner && (spinner.style.display = 'none'); img.style.display = 'inline-block'; };
                                    img.onerror = () => { spinner && (spinner.style.display = 'none'); };
                                } catch (e) {}
                                img.src = `https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=${qrData}`;
                            }
                        }, 0);
                        modal.show();
                        // Attach actions for export/print
                        const btnPrint = document.getElementById('btnPrintDetails');
                        const btnCsv = document.getElementById('btnDownloadExcel');
                        const btnPdf = document.getElementById('btnDownloadPDF');
                        btnPrint && (btnPrint.onclick = () => confirmThen('Proceed to print this member details?', () => printMemberDetails()));
                        btnCsv && (btnCsv.onclick = () => confirmThen('Download details as CSV?', () => downloadMemberCSV(m)));
                        btnPdf && (btnPdf.onclick = () => confirmThen('Generate a PDF of these details?', () => downloadMemberPDF()))
                        // Copy all details
                        const btnCopyAll = document.getElementById('btnCopyAllDetails');
                        btnCopyAll && (btnCopyAll.onclick = () => confirmThen('Copy all details to clipboard?', () => { navigator.clipboard.writeText(`${qrPayload}`).then(()=>Swal.fire({ icon:'success', title:'Copied', timer:900, showConfirmButton:false })).catch(()=>Swal.fire({ icon:'error', title:'Copy failed' })); }));
                        // Header edit buttons removed per requirement
                    })
                    .catch((err) => {
                        document.getElementById('memberDetailsBody').innerHTML = `
                            <div class="text-danger">Failed to load member details. ${err && err.message ? '('+err.message+')' : ''}</div>
                            <div class="mt-2">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewDetails(${id})"><i class="fas fa-redo me-1"></i>Retry</button>
                            </div>`;
                        new bootstrap.Modal(document.getElementById('memberDetailsModal')).show();
                    });
            }

			// Ensure openEdit sets state for chooser and header buttons
 			let currentEditMember = null;
            function openEdit(id) {
 				confirmThen('Open edit for this member?', () => {
					fetch(`{{ url('/members') }}/${id}`, { headers: { 'Accept': 'application/json' } })
 					.then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
                    .then(m => {
 						currentEditMember = m;
 						const chooser = new bootstrap.Modal(document.getElementById('editSectionChooserModal'));
 						chooser.show();
 					})
 					.catch(err => {
 						Swal.fire({ icon: 'error', title: 'Failed to load member', text: err && err.message ? err.message : 'Please try again.' });
 					});
				});
 			}

            // Wire chooser buttons to open respective modals with prefill
            document.getElementById('btnEditPersonal').addEventListener('click', () => {
                if (!currentEditMember) return;
                const chooser = bootstrap.Modal.getInstance(document.getElementById('editSectionChooserModal'));
                chooser && chooser.hide();
                document.getElementById('edit_personal_id').value = currentEditMember.id;
                document.getElementById('edit_personal_full_name').value = currentEditMember.full_name || '';
                document.getElementById('edit_personal_email').value = currentEditMember.email || '';
                document.getElementById('edit_personal_phone_number').value = currentEditMember.phone_number || '';
                document.getElementById('edit_personal_gender').value = currentEditMember.gender || '';
                document.getElementById('edit_personal_date_of_birth').value = currentEditMember.date_of_birth || '';
                document.getElementById('edit_personal_nida_number').value = currentEditMember.nida_number || '';
                document.getElementById('edit_personal_membership_type').value = currentEditMember.membership_type || 'permanent';
                // tribe
                populateSelect(document.getElementById('edit_personal_tribe'), tribeList, 'Select tribe');
                const tribeEl = document.getElementById('edit_personal_tribe');
                const otherGroup = document.getElementById('edit_personal_other_tribe_group');
                const otherInput = document.getElementById('edit_personal_other_tribe');
                tribeEl.value = tribeList.includes(currentEditMember.tribe) ? currentEditMember.tribe : 'Other';
                otherGroup.style.display = tribeEl.value === 'Other' ? '' : 'none';
                otherInput.value = currentEditMember.other_tribe || '';
                tribeEl.onchange = () => { otherGroup.style.display = tribeEl.value === 'Other' ? '' : 'none'; if (tribeEl.value !== 'Other') otherInput.value = ''; };
                new bootstrap.Modal(document.getElementById('memberEditPersonalModal')).show();
            });

            document.getElementById('btnEditLocation').addEventListener('click', () => {
                if (!currentEditMember) return;
                const chooser = bootstrap.Modal.getInstance(document.getElementById('editSectionChooserModal'));
                chooser && chooser.hide();
                document.getElementById('edit_location_id').value = currentEditMember.id;
                ensureLocationsLoaded().then(() => {
                    populateSelect(document.getElementById('edit_location_region'), Object.keys(tzLocations), 'Select region');
                    const regionEl = document.getElementById('edit_location_region');
                    const districtEl = document.getElementById('edit_location_district');
                    const wardEl = document.getElementById('edit_location_ward');
                    function updateDistricts() {
                        populateSelect(districtEl, regionEl.value ? Object.keys(tzLocations[regionEl.value] || {}) : [], 'Select district');
                        updateWards();
                    }
                    function updateWards() {
                        const wards = regionEl.value && districtEl.value ? (tzLocations[regionEl.value]?.[districtEl.value] || []) : [];
                        populateSelect(wardEl, wards, 'Select ward');
                    }
                    regionEl.onchange = updateDistricts;
                    districtEl.onchange = updateWards;
                    regionEl.value = currentEditMember.region || '';
                    updateDistricts();
                    districtEl.value = currentEditMember.district || '';
                    updateWards();
                    wardEl.value = currentEditMember.ward || '';
                });
                document.getElementById('edit_location_street').value = currentEditMember.street || '';
                document.getElementById('edit_location_address').value = currentEditMember.address || '';
                new bootstrap.Modal(document.getElementById('memberEditLocationModal')).show();
            });

            document.getElementById('btnEditFamily').addEventListener('click', () => {
                if (!currentEditMember) return;
                const chooser = bootstrap.Modal.getInstance(document.getElementById('editSectionChooserModal'));
                chooser && chooser.hide();
                document.getElementById('edit_family_id').value = currentEditMember.id;
                document.getElementById('edit_family_living_with_family').value = (currentEditMember.living_with_family || '').toString().toLowerCase();
                document.getElementById('edit_family_family_relationship').value = currentEditMember.family_relationship || '';
                new bootstrap.Modal(document.getElementById('memberEditFamilyModal')).show();
            });

            // Submit handlers for section forms
            document.getElementById('editPersonalForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('edit_personal_id').value;
                const fd = new FormData();
                fd.append('full_name', document.getElementById('edit_personal_full_name').value);
                fd.append('email', document.getElementById('edit_personal_email').value);
                fd.append('phone_number', document.getElementById('edit_personal_phone_number').value);
                fd.append('membership_type', document.getElementById('edit_personal_membership_type').value);
                fd.append('gender', document.getElementById('edit_personal_gender').value);
                fd.append('date_of_birth', document.getElementById('edit_personal_date_of_birth').value);
                fd.append('nida_number', document.getElementById('edit_personal_nida_number').value);
                const tribeVal = document.getElementById('edit_personal_tribe').value;
                fd.append('tribe', tribeVal === 'Other' ? '' : tribeVal);
                fd.append('other_tribe', document.getElementById('edit_personal_other_tribe').value);
                fd.append('_method', 'PUT');
                fetch(`{{ url('/members') }}/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) { Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false }).then(()=>location.reload()); }
                        else { Swal.fire({ icon: 'error', title: 'Update failed', text: res.message || 'Please try again.' }); }
                    })
                    .catch(()=> Swal.fire({ icon: 'error', title: 'Network error' }));
            });

            document.getElementById('editLocationForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('edit_location_id').value;
                const fd = new FormData();
                fd.append('region', document.getElementById('edit_location_region').value);
                fd.append('district', document.getElementById('edit_location_district').value);
                fd.append('ward', document.getElementById('edit_location_ward').value);
                fd.append('street', document.getElementById('edit_location_street').value);
                fd.append('address', document.getElementById('edit_location_address').value);
                fd.append('_method', 'PUT');
                fetch(`{{ url('/members') }}/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) { Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false }).then(()=>location.reload()); }
                        else { Swal.fire({ icon: 'error', title: 'Update failed', text: res.message || 'Please try again.' }); }
                    })
                    .catch(()=> Swal.fire({ icon: 'error', title: 'Network error' }));
            });

            document.getElementById('editFamilyForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const id = document.getElementById('edit_family_id').value;
                const fd = new FormData();
                fd.append('living_with_family', document.getElementById('edit_family_living_with_family').value);
                fd.append('family_relationship', document.getElementById('edit_family_family_relationship').value);
                fd.append('_method', 'PUT');
                fetch(`{{ url('/members') }}/${id}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) { Swal.fire({ icon: 'success', title: 'Saved', timer: 1200, showConfirmButton: false }).then(()=>location.reload()); }
                        else { Swal.fire({ icon: 'error', title: 'Update failed', text: res.message || 'Please try again.' }); }
                    })
                    .catch(()=> Swal.fire({ icon: 'error', title: 'Network error' }));
            });

            // Quick Add modal cascading + tribe
            function setupCascadingForAdd() {
                // Always reset to a clean state first
                resetAddMemberForm();
                ensureLocationsLoaded().then(() => {
                    populateSelect(document.getElementById('add_region'), Object.keys(tzLocations), 'Select region');
                    const regionEl = document.getElementById('add_region');
                    const districtEl = document.getElementById('add_district');
                    const wardEl = document.getElementById('add_ward');
                    function updateDistricts() {
                        populateSelect(districtEl, regionEl.value ? Object.keys(tzLocations[regionEl.value] || {}) : [], 'Select district');
                        updateWards();
                    }
                    function updateWards() {
                        const wards = regionEl.value && districtEl.value ? (tzLocations[regionEl.value]?.[districtEl.value] || []) : [];
                        populateSelect(wardEl, wards, 'Select ward');
                    }
                    regionEl.onchange = updateDistricts;
                    districtEl.onchange = updateWards;
                });
                populateSelect(document.getElementById('add_tribe'), tribeList, 'Select tribe');
                const tribeEl = document.getElementById('add_tribe');
                const otherGroup = document.getElementById('add_other_tribe_group');
                const otherInput = document.getElementById('add_other_tribe');
                tribeEl.onchange = () => { const show = tribeEl.value === 'Other'; otherGroup.style.display = show ? '' : 'none'; if (!show) otherInput.value = ''; };
            }

            function resetAddMemberForm(){
                // Reset form fields
                const form = document.getElementById('quickAddMemberForm');
                if (form && typeof form.reset === 'function') { form.reset(); }
                // Hide other tribe input
                const otherGroup = document.getElementById('add_other_tribe_group');
                if (otherGroup) otherGroup.style.display = 'none';
                // Clear any existing options in selects to avoid stale state
                ['add_region','add_district','add_ward','add_tribe'].forEach(id => { const s = document.getElementById(id); if (s) s.innerHTML = ''; });
                // Remove any validation classes/messages if present
                const modal = document.getElementById('addMemberModal');
                if (modal) {
                    modal.querySelectorAll('.is-invalid, .is-valid').forEach(el => el.classList.remove('is-invalid','is-valid'));
                    modal.querySelectorAll('.invalid-feedback, .valid-feedback').forEach(el => { el.textContent = ''; el.style.display = 'none'; });
                }
            }

            document.getElementById('addMemberModal').addEventListener('show.bs.modal', setupCascadingForAdd);
            // Ensure fresh state after cancel/close
            document.getElementById('addMemberModal').addEventListener('hidden.bs.modal', resetAddMemberForm);

            document.getElementById('quickAddMemberForm').addEventListener('submit', function(e) {
                e.preventDefault();
                const fd = new FormData();
                fd.append('full_name', document.getElementById('add_full_name').value);
                fd.append('gender', document.getElementById('add_gender').value);
                fd.append('phone_number', document.getElementById('add_phone_number').value);
                fd.append('email', document.getElementById('add_email').value);
                fd.append('region', document.getElementById('add_region').value);
                fd.append('district', document.getElementById('add_district').value);
                fd.append('ward', document.getElementById('add_ward').value);
                const tribeVal = document.getElementById('add_tribe').value;
                fd.append('tribe', tribeVal === 'Other' ? '' : tribeVal);
                fd.append('other_tribe', document.getElementById('add_other_tribe').value);
                fetch(`{{ url('/members') }}`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }, body: fd })
                .then(r => r.json())
                .then(res => {
                    if (res.success) {
                            // Reset form state before reload just in case
                            resetAddMemberForm();
                            Swal.fire({ icon: 'success', title: 'Member registered', timer: 1400, showConfirmButton: false }).then(()=>location.reload());
                    } else {
                            Swal.fire({ icon: 'error', title: 'Registration failed', text: res.message || 'Please review and try again.' });
                    }
                })
                    .catch(()=> Swal.fire({ icon: 'error', title: 'Network error' }));
            });

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Delete member?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, delete',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#dc3545'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`{{ url('/members') }}/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            }
                        })
                        .then(r => r.json())
                        .then(res => {
                            if (res.success) {
                                document.getElementById(`row-${id}`)?.remove();
                                Swal.fire({ icon: 'success', title: 'Member deleted', text: 'The member was deleted successfully.', timer: 1200, showConfirmButton: false });
                            } else {
                                Swal.fire({ icon: 'error', title: 'Delete failed', text: res.message || 'Please try again.' });
                            }
                        })
                        .catch(() => Swal.fire({ icon: 'error', title: 'Error', text: 'Request failed.' }));
                    }
                });
            }

            // Simple client-side, real-time filtering
            function filterTable() {
                const q = (document.getElementById('searchInput').value || '').trim().toLowerCase();
                const gender = (document.getElementById('genderFilter').value || '').toLowerCase();
                const region = (document.getElementById('regionFilter').value || '').toLowerCase();
                const district = (document.getElementById('districtFilter').value || '').toLowerCase();
                const ward = (document.getElementById('wardFilter').value || '').toLowerCase();
                const rows = document.querySelectorAll('#membersTable tbody tr');
                let visibleIndex = 0;
                rows.forEach((row) => {
                    const textMatch = [
                        row.dataset.name,
                        row.dataset.memberid,
                        row.dataset.phone,
                        row.dataset.email
                    ].some(v => (v || '').includes(q));
                    const genderMatch = !gender || (row.dataset.gender === gender);
                    const regionMatch = !region || (row.dataset.region === region);
                    const districtMatch = !district || (row.dataset.district === district);
                    const wardMatch = !ward || (row.dataset.ward === ward);
                    const show = textMatch && genderMatch && regionMatch && districtMatch && wardMatch;
                    row.style.display = show ? '' : 'none';
                    if (show) {
                        // Re-number visible rows client-side
                        const numberCell = row.querySelector('td');
                        if (numberCell) numberCell.textContent = String(++visibleIndex);
                    }
                });
            }

            ['searchInput', 'genderFilter', 'regionFilter', 'districtFilter', 'wardFilter']
                .forEach(id => {
                    const el = document.getElementById(id);
                    if (!el) return;
                    const evt = id === 'searchInput' ? 'input' : 'change';
                    el.addEventListener(evt, filterTable);
                });

            function downloadMemberCSV(m) {
                const headers = ['Full Name','Member ID','Phone','Email','Gender','Date of Birth','NIDA Number','Region','District','Ward','Street','Address','Living with family','Family relationship','Tribe','Other tribe'];
                const values = [
                    m.full_name || '',
                    m.member_id || '',
                    m.phone_number || '',
                    m.email || '',
                    m.gender || '',
                    m.date_of_birth || '',
                    m.nida_number || '',
                    m.region || '',
                    m.district || '',
                    m.ward || '',
                    m.street || '',
                    m.address || '',
                    m.living_with_family || '',
                    m.family_relationship || '',
                    m.tribe || '',
                    m.other_tribe || ''
                ];
                const csv = [headers.join(','), values.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(',')].join('\n');
                const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = (m.member_id || 'member') + '.csv';
                a.click();
                URL.revokeObjectURL(url);
            }

            function printMemberDetails() {
                const content = document.getElementById('memberDetailsPrint');
                const w = window.open('', '_blank');
                const logoUrl = `{{ asset('assets/images/waumini_link_logo.png') }}`;
                const printedAt = new Date().toLocaleString();
                const printedBy = `{{ Auth::user()->name ?? 'User' }}`;
                const yearNow = new Date().getFullYear();
                const m = currentDetailsMember || null;
                const payload = (function(mm){ if(!mm) return ''; return [
                    'Full Name: ' + (mm.full_name || '-'),
                    'Member ID: ' + (mm.member_id || '-'),
                    'Phone: ' + (mm.phone_number || '-'),
                    'Email: ' + (mm.email || '-'),
                    'Gender: ' + (mm.gender ? mm.gender.charAt(0).toUpperCase()+mm.gender.slice(1) : '-'),
                    'Date of Birth: ' + formatDateDisplay(mm.date_of_birth),
                    'NIDA Number: ' + (mm.nida_number || '-'),
                    'Region: ' + (mm.region || '-'),
                    'District: ' + (mm.district || '-'),
                    'Ward: ' + (mm.ward || '-'),
                    'Street: ' + (mm.street || '-'),
                    'Address: ' + (mm.address || '-'),
                    'Living with family: ' + (mm.living_with_family || '-'),
                    'Family relationship: ' + (mm.family_relationship || '-'),
                    'Tribe: ' + ((mm.tribe || '-') + (mm.other_tribe ? (' ('+mm.other_tribe+')') : ''))
                ].join('\n'); })(m);

                // Prebuild section HTML using current window's data
                function row(label, value){ return '<tr><td>' + label + '</td><td><strong>' + (value ? String(value) : '—') + '</strong></td></tr>'; }
                let sectionsHtml = '';
                if (m) {
                    sectionsHtml += '<div class="section-title">Personal</div>'+
                        '<table class="table"><tbody>'+
                        row('Full Name', m.full_name)+
                        row('Member ID', m.member_id)+
                        row('Phone', m.phone_number)+
                        row('Email', m.email)+
                        row('Gender', m.gender ? (m.gender.charAt(0).toUpperCase()+m.gender.slice(1)) : '')+
                        row('Date of Birth', formatDateDisplay(m.date_of_birth))+
                        row('NIDA Number', m.nida_number)+
                        '</tbody></table>';

                    sectionsHtml += '<div class="section-title">Location</div>'+
                        '<table class="table"><tbody>'+
                        row('Region', m.region)+
                        row('District', m.district)+
                        row('Ward', m.ward)+
                        row('Street', m.street)+
                        row('Address', m.address)+
                        '</tbody></table>';

                    sectionsHtml += '<div class="section-title">Family</div>'+
                        '<table class="table"><tbody>'+
                        row('Living with family', m.living_with_family)+
                        row('Family relationship', m.family_relationship)+
                        row('Tribe', (m.tribe || '') + (m.other_tribe ? (' ('+m.other_tribe+')') : ''))+
                        '</tbody></table>';
                }

                w.document.write('<html><head><title>Member Details</title>');
                w.document.write('<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">');
                w.document.write('<style>\n@page { margin: 15mm; }\nbody{ -webkit-print-color-adjust: exact; print-color-adjust: exact; }\n.print-shell{ max-width: 980px; margin: 0 auto; }\n.header{ position:relative; padding:16px 18px; border-radius:10px; margin-bottom:18px; background: linear-gradient(135deg, #f4f6ff 0%, #ffffff 100%); border:1px solid #e9ecef; }\n.header-top{ display:flex; align-items:center; justify-content:space-between; }\n.brand{ display:flex; align-items:center; gap:12px; }\n.brand h2{ margin:0; color:#1f2b6c; }\n.badges{ display:none; }\n.qr-wrap{ text-align:right; }\n.qr{ width:120px; height:120px; border:3px solid #5b2a86; border-radius:8px; padding:4px; background:#fff }\n.section-title{ font-size:12px; letter-spacing:1px; color:#6c757d; text-transform:uppercase; margin:18px 0 6px; }\n.table{ width:100%; border-collapse:separate; border-spacing:0; }\n.table th,.table td{ padding:10px 12px; vertical-align:top; }\n.table tbody tr:nth-child(odd){ background:#fbfbfe; }\n.table tbody tr td:first-child{ width:220px; color:#6c757d; border-left:4px solid #5b2a86; }\n.footer{ margin-top:24px; padding-top:12px; border-top:1px dashed #ced4da; font-size:12px; color:#6c757d; text-align:center; }\n.footer a{ color:#5b2a86; text-decoration:none; }\n.footer a:hover{ text-decoration:underline; }\n</style>');
                w.document.write('</head><body>');
                w.document.write('<div class="print-shell">');
                // Header
                w.document.write(`<div class="header">
                     <div class="header-top">
                         <div class="brand">
                             <img src="${logoUrl}" style="height:48px"/>
                             <div>
                                 <h2 class="mb-0">Member Details</h2>
                             </div>
                         </div>
                         <div class="qr-wrap"><img id="printQrImg" class="qr" src="" alt="QR"/></div>
                     </div>
                 </div>`);

                // Sections (prebuilt)
                w.document.write(sectionsHtml);

                // Footer
                w.document.write(`<div class="footer">
                    Printed on ${printedAt} by ${printedBy} • © ${yearNow} Waumini Link • Powered by <a href="https://emca.tech/#" target="_blank" rel="noopener">EmCa Technologies</a>
                </div>`);

                w.document.write('</div>');
                // Ensure QR loads before printing
                const qrUrlPromise = getQrDataUrl(payload, 120);
                w.document.write(`<script>\n(function(){\nvar done = false;\nfunction go(){ if(done) return; done = true; try{ window.print(); }catch(e){} setTimeout(function(){ window.close(); }, 200); }\nwindow.addEventListener('load', function(){ setTimeout(go, 400); });\n})();\n<\/script>`);
                w.document.write('</body></html>');
                w.document.close();
                w.focus();
                // After doc open, set QR and wait for load
                setTimeout(function(){
                    qrUrlPromise.then(function(url){
                        const fallback = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(payload || 'Member');
                        try {
                            const img = w.document.getElementById('printQrImg');
                            if (img) {
                                img.onload = function(){ setTimeout(function(){ try{ w.print(); }catch(e){} setTimeout(function(){ try{ w.close(); }catch(e){} }, 200); }, 150); };
                                img.onerror = function(){ setTimeout(function(){ try{ w.print(); }catch(e){} setTimeout(function(){ try{ w.close(); }catch(e){} }, 200); }, 200); };
                                img.src = url || fallback;
                            } else {
                                setTimeout(function(){ try{ w.print(); }catch(e){} setTimeout(function(){ try{ w.close(); }catch(e){} }, 200); }, 300);
                            }
                        } catch(e){ setTimeout(function(){ try{ w.print(); }catch(e){} setTimeout(function(){ try{ w.close(); }catch(e){} }, 200); }, 300); }
                    });
                }, 50);
            }

            function downloadMemberPDF(){
                const m = currentDetailsMember || null;
                if (!m) return Swal.fire({ icon:'error', title:'Open details first' });
                // Build a hidden container to render into PDF
                const container = document.createElement('div');
                container.style.position = 'fixed';
                container.style.left = '-9999px';
                container.style.top = '0';
                container.style.width = '800px';
                container.innerHTML = '';
                const logoUrl = `{{ asset('assets/images/waumini_link_logo.png') }}`;
                const payload = [
                    'Full Name: ' + (m.full_name || '-'),
                    'Member ID: ' + (m.member_id || '-'),
                    'Phone: ' + (m.phone_number || '-'),
                    'Email: ' + (m.email || '-'),
                    'Gender: ' + (m.gender ? m.gender.charAt(0).toUpperCase()+m.gender.slice(1) : '-'),
                    'Date of Birth: ' + formatDateDisplay(m.date_of_birth),
                    'NIDA Number: ' + (m.nida_number || '-'),
                    'Region: ' + (m.region || '-'),
                    'District: ' + (m.district || '-'),
                    'Ward: ' + (m.ward || '-'),
                    'Street: ' + (m.street || '-'),
                    'Address: ' + (m.address || '-'),
                    'Living with family: ' + (m.living_with_family || '-'),
                    'Family relationship: ' + (m.family_relationship || '-'),
                    'Tribe: ' + ((m.tribe || '-') + (m.other_tribe ? (' ('+m.other_tribe+')') : ''))
                ].join('\n');
                // Generate data URL for QR to avoid CORS issues in PDF
                getQrDataUrl(payload, 120).then(function(qrDataUrl){
                    const qrImgSrc = qrDataUrl || ('https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(payload));
                function row(label, value){ return '<tr><td style="width:220px;color:#6c757d;border-left:4px solid #5b2a86; padding:8px 10px">' + label + '</td><td style="padding:8px 10px"><strong>' + (value ? String(value) : '—') + '</strong></td></tr>'; }
                let html = '';
                html += '<div style="border:1px solid #e9ecef;border-radius:10px;padding:14px 16px;margin-bottom:16px;background:linear-gradient(135deg,#f4f6ff 0%,#ffffff 100%)">'+
                        '<div style="display:flex;align-items:center;justify-content:space-between">'+
                        '<div style="display:flex;align-items:center;gap:12px">'+
                        '<img src="'+logoUrl+'" style="height:44px"/><div><h3 style="margin:0;color:#1f2b6c">Member Details</h3>'+ 
                        '</div></div></div>'+ 
                        '<div><img src="'+qrImgSrc+'" style="width:120px;height:120px;border:3px solid #5b2a86;border-radius:8px;padding:4px;background:#fff"/></div>'+ 
                        '</div></div>';
                html += '<div style="font-size:12px;letter-spacing:1px;color:#6c757d;text-transform:uppercase;margin:14px 0 6px">Personal</div>';
                html += '<table style="width:100%;border-collapse:separate;border-spacing:0"><tbody>'+
                        row('Full Name', m.full_name)+row('Member ID', m.member_id)+row('Phone', m.phone_number)+row('Email', m.email)+row('Gender', m.gender ? (m.gender.charAt(0).toUpperCase()+m.gender.slice(1)) : '')+row('Date of Birth', formatDateDisplay(m.date_of_birth))+row('NIDA Number', m.nida_number)+
                        '</tbody></table>';
                html += '<div style="font-size:12px;letter-spacing:1px;color:#6c757d;text-transform:uppercase;margin:14px 0 6px">Location</div>';
                html += '<table style="width:100%;border-collapse:separate;border-spacing:0"><tbody>'+
                        row('Region', m.region)+row('District', m.district)+row('Ward', m.ward)+row('Street', m.street)+row('Address', m.address)+
                        '</tbody></table>';
                html += '<div style="font-size:12px;letter-spacing:1px;color:#6c757d;text-transform:uppercase;margin:14px 0 6px">Family</div>';
                html += '<table style="width:100%;border-collapse:separate;border-spacing:0"><tbody>'+
                        row('Living with family', m.living_with_family)+row('Family relationship', m.family_relationship)+row('Tribe', (m.tribe || '') + (m.other_tribe ? (' ('+m.other_tribe+')') : ''))+
                        '</tbody></table>';
                html += '<div style="margin-top:18px;padding-top:10px;border-top:1px dashed #ced4da;font-size:12px;color:#6c757d;text-align:center">Powered by <a href="https://emca.tech/#" target="_blank" style="color:#5b2a86;text-decoration:none">EmCa Technologies</a></div>';
                container.innerHTML = html;
                document.body.appendChild(container);
                // Preload images before generating PDF
                const imgs = Array.from(container.querySelectorAll('img'));
                Promise.all(imgs.map(img => new Promise(res => { if (img.complete) return res(); img.onload = () => res(); img.onerror = () => res(); }))).then(() => {
                // Load html2pdf and generate
                function generate(){
                    window.html2pdf().set({ margin:10, filename: (m.member_id || 'member') + '.pdf', image: { type:'jpeg', quality: 0.98 }, html2canvas: { scale: 2, useCORS:true, allowTaint:true }, jsPDF: { unit:'mm', format:'a4', orientation:'portrait' } }).from(container).save().then(()=>{ document.body.removeChild(container); }).catch(()=>{ document.body.removeChild(container); Swal.fire({ icon:'error', title:'PDF failed' }); });
                }
                if (!window.html2pdf) {
                    const s = document.createElement('script');
                    s.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js';
                    s.onload = generate;
                    s.onerror = () => { document.body.removeChild(container); Swal.fire({ icon:'error', title:'Failed to load PDF lib' }); };
                    document.head.appendChild(s);
                } else {
                    generate();
                }
                });
                });
            }

            // Cascading selects for Region -> District -> Ward and Tribe
            let tzLocations = null;
            let tribeList = ['Chaga','Sukuma','Haya','Nyakyusa','Makonde','Hehe','Other'];
            function ensureLocationsLoaded() {
                if (tzLocations) return Promise.resolve(tzLocations);
                return fetch(`{{ asset('data/tanzania-locations.json') }}`)
                    .then(r => r.json())
                    .then(json => { tzLocations = json; return tzLocations; });
            }
            function populateSelect(selectEl, items, placeholder = 'Select') {
                selectEl.innerHTML = '';
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = placeholder;
                selectEl.appendChild(opt);
                (items || []).forEach(v => {
                    const o = document.createElement('option');
                    o.value = v;
                    o.textContent = v;
                    selectEl.appendChild(o);
                });
            }
            function setupCascadingForEdit(prefill = {}) {
                ensureLocationsLoaded().then(data => {
                    const regions = Object.keys(data || {});
                    const regionEl = document.getElementById('edit_region');
                    const districtEl = document.getElementById('edit_district');
                    const wardEl = document.getElementById('edit_ward');
                    populateSelect(regionEl, regions, 'Select region');
                    if (prefill.region) regionEl.value = prefill.region;
                    function updateDistricts() {
                        const r = regionEl.value;
                        const districts = r && data[r] ? Object.keys(data[r]) : [];
                        populateSelect(districtEl, districts, 'Select district');
                        updateWards();
                        if (prefill.district) districtEl.value = prefill.district;
                    }
                    function updateWards() {
                        const r = regionEl.value;
                        const d = districtEl.value;
                        const wards = r && d && data[r] && data[r][d] ? data[r][d] : [];
                        populateSelect(wardEl, wards, 'Select ward');
                        if (prefill.ward) wardEl.value = prefill.ward;
                    }
                    regionEl.onchange = updateDistricts;
                    districtEl.onchange = updateWards;
                    updateDistricts();
                });
                // Tribe
                const tribeEl = document.getElementById('edit_tribe');
                populateSelect(tribeEl, tribeList, 'Select tribe');
                if (prefill.tribe) tribeEl.value = tribeList.includes(prefill.tribe) ? prefill.tribe : 'Other';
                const otherGroup = document.getElementById('edit_other_tribe_group');
                const otherInput = document.getElementById('edit_other_tribe');
                function toggleOther() {
                    const show = tribeEl.value === 'Other';
                    otherGroup.style.display = show ? '' : 'none';
                    if (!show) otherInput.value = '';
                }
                tribeEl.onchange = toggleOther;
                toggleOther();
                if (prefill.other_tribe) { otherGroup.style.display = ''; otherInput.value = prefill.other_tribe; }
            }

            // Preload QR lib early and accessibility: focus first actionable element when modals open
            ensureQrLib();
            document.getElementById('memberDetailsModal').addEventListener('shown.bs.modal', function(){
                const first = document.getElementById('btnHeaderEditPersonal') || document.getElementById('btnPrintDetails');
                first && first.focus();
            });

            // Set footer year
            document.getElementById('year').textContent = new Date().getFullYear();

			// QR helper: load once and render
			let qrLibLoaded = false;
			function ensureQrLib() {
				return new Promise((resolve) => {
					if (qrLibLoaded || window.QRCode) { qrLibLoaded = true; return resolve(); }
					const s = document.createElement('script');
					s.src = 'https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js';
					s.onload = () => { qrLibLoaded = true; resolve(); };
					document.head.appendChild(s);
				});
			}
			function renderQrToCanvas(canvasId, text, size = 96) {
				ensureQrLib().then(() => {
					setTimeout(() => {
						const c = document.getElementById(canvasId);
						if (!c || !window.QRCode) return;
						QRCode.toCanvas(c, text, { width: size, margin: 1 }, function(err) {
							if (err) {
								console.error(err);
								const holder = c.parentElement;
								if (holder) holder.innerHTML = '<span class="badge bg-warning text-dark">QR unavailable</span>';
							}
						});
					}, 50);
				});
			}

            // Build a QR data URL for embedding (avoids CORS issues when printing/PDF)
            function getQrDataUrl(text, size = 120) {
                return new Promise((resolve) => {
                    ensureQrLib().then(() => {
                        if (window.QRCode && QRCode.toDataURL) {
                            QRCode.toDataURL(text, { width: size, margin: 1 }, function(err, url){
                                if (err) { console.error(err); resolve(''); }
                                else { resolve(url || ''); }
                            });
                        } else {
                            resolve('');
                        }
                    });
                });
            }

            // Ensure global access for action handlers
            window.viewDetails = viewDetails;
            window.openEdit = openEdit;
            window.confirmDelete = confirmDelete;
            window.printMemberDetails = printMemberDetails;
            window.downloadMemberPDF = downloadMemberPDF;
        </script>
        <!-- Archive Modal (should be included once per page, not per row) -->
        <div class="modal fade" id="archiveMemberModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Archive Member</h5>
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
                                <button type="button" id="archiveSubmitBtn" class="btn btn-warning">Archive</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <script>
        function openArchiveModal(id) {
            document.getElementById('archive_member_id').value = id;
            document.getElementById('archive_reason').value = '';
            var modalEl = document.getElementById('archiveMemberModal');
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
        document.getElementById('archiveSubmitBtn').addEventListener('click', function() {
            const id = document.getElementById('archive_member_id').value;
            const reason = document.getElementById('archive_reason').value.trim();
            if (!reason) {
                Swal.fire({ icon: 'warning', title: 'Please provide a reason.' });
                return;
            }
            // Actual archive request
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
            bootstrap.Modal.getInstance(document.getElementById('archiveMemberModal')).hide();
        });
        </script>
    </body>
</html>