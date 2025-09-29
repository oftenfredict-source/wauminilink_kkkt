@if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    var userId = @json(session('user_id'));
                    var name = @json(session('name'));
                    var membershipType = @json(session('membership_type'));
                    var tab = (membershipType === 'temporary') ? 'temporary' : 'permanent';
                    // Show processing spinner first
                    Swal.fire({
                        title: 'Processing...',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    setTimeout(function() {
                        Swal.close();
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful',
                            html: `<div style='font-size:1.1em;text-align:left'><b>User ID:</b> ${userId}<br><b>Name:</b> ${name}<br><b>Membership Type:</b> ${membershipType}</div>`,
                            showConfirmButton: true,
                            confirmButtonText: 'OK',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then(function(result) {
                            if(result.isConfirmed) {
                                window.location.href = "{{ route('members.view') }}?tab=" + tab;
                            }
                        });
                    }, 1200); // 1.2 seconds spinner
                });
            </script>
        @endif
<!DOCTYPE html>
<html lang="en">
    <head>
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
                        function addChild(){
                            const idx = childCount;
                            const row = document.createElement('div');
                            row.className = 'row g-3 mb-2 align-items-end child-row';
                            row.innerHTML = `
                                <div class=\"col-md-5\">
                                    <label class=\"form-label\">Child ${idx+1} Full Name</label>
                                    <input type=\"text\" class=\"form-control child-fullname\" name=\"children[${idx}][full_name]\" required>
                                </div>
                                <div class=\"col-md-3\">
                                    <label class=\"form-label\">Gender</label>
                                    <select class=\"form-select child-gender\" name=\"children[${idx}][gender]\" required>
                                        <option value=\"\">Select</option>
                                        <option value=\"male\">Male</option>
                                        <option value=\"female\">Female</option>
                                    </select>
                                </div>
                                <div class=\"col-md-3\">
                                    <label class=\"form-label\">Date of Birth</label>
                                    <div class=\"input-group\">
                                        <input type=\"date\" class=\"form-control child-dob\" name=\"children[${idx}][date_of_birth]\" required>
                                        <span class=\"input-group-text child-age\" style=\"min-width:80px;display:none;\"></span>
                                    </div>
                                </div>
                                <div class=\"col-md-1 text-end\">
                                    <button type=\"button\" class=\"btn btn-danger btn-sm remove-child-btn\" title=\"Remove Child\"><i class=\"fas fa-trash\"></i></button>
                                </div>
                            `;
                            childrenContainer.appendChild(row);
                            childCount++;
                            renderChildren();
                            // Remove child handler
                            row.querySelector('.remove-child-btn').addEventListener('click', function(){
                                row.remove();
                                childCount--;
                                // Re-index names for children
                                Array.from(childrenContainer.querySelectorAll('.child-row')).forEach((r, i) => {
                                    r.querySelector('.form-label').textContent = `Child ${i+1} Full Name`;
                                    r.querySelector('.child-fullname').setAttribute('name', `children[${i}][full_name]`);
                                    r.querySelector('.child-gender').setAttribute('name', `children[${i}][gender]`);
                                    r.querySelector('.child-dob').setAttribute('name', `children[${i}][date_of_birth]`);
                                });
                            });
                        }
                letter-spacing: 0.01em;
                transition: color 0.3s, font-weight 0.3s;
            }
            .wizard-step.active .step-label {
                color: #1f2b6c;
                font-weight: 700;
            }
            .wizard-step.completed .step-label {
                color: #198754;
                font-weight: 700;
            }
            .wizard-step:not(:last-child)::after {
                content: "";
                position: absolute;
                top: 22px;
                right: -32px;
                width: 64px;
                height: 4px;
                background: linear-gradient(90deg, #5b2a86 0%, #1f2b6c 100%);
                border-radius: 2px;
                z-index: 0;
                opacity: 0.15;
                transition: background 0.3s;
            }
            .wizard-step.completed:not(:last-child)::after {
                background: linear-gradient(90deg, #198754 0%, #5b2a86 100%);
                opacity: 0.3;
            }
            @media (max-width: 767px) {
                .wizard-step:not(:last-child)::after { width: 32px; right: -18px; }
            }
            /* Transition animations for steps */
            .fade-in {
                animation: fadeInStep 0.5s cubic-bezier(.4,0,.2,1);
            }
            .fade-out {
                animation: fadeOutStep 0.5s cubic-bezier(.4,0,.2,1);
            }
            @keyframes fadeInStep {
                from { opacity: 0; transform: translateY(24px); }
                to { opacity: 1; transform: none; }
            }
            @keyframes fadeOutStep {
                from { opacity: 1; transform: none; }
                to { opacity: 0; transform: translateY(-24px); }
            }
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
                <!-- Main section improved design -->
                <main class="container-fluid px-2 px-md-5 py-4 animated fadeIn">
                    <div class="card shadow-lg border-0 rounded-4 overflow-hidden main-form-card">
                        <div class="card-header bg-gradient-primary d-flex flex-column flex-md-row justify-content-between align-items-center py-4 px-4 border-0">
                            <span class="fs-5 fw-bold text-white d-flex align-items-center">
                                <i class="fas fa-user-plus me-2"></i> <span id="stepHeaderTitle">Add Member</span>
                                <small class="ms-3 text-warning" id="liveMemberId"></small>
                            </span>
                            <div class="mt-3 mt-md-0">
                                <a href="{{ route('members.add') }}" class="btn btn-light btn-sm me-2 shadow-sm"><i class="fas fa-user-plus me-1"></i> Add Member</a>
                                <a href="{{ route('members.view') }}" class="btn btn-outline-light btn-sm shadow-sm"><i class="fas fa-list me-1"></i> All Members</a>
                            </div>
                        </div>
                        <div class="card-body bg-light px-4 py-4">
                            @if ($errors->any())
                                <div class="alert alert-danger rounded-3 shadow-sm">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <form id="addMemberForm" method="POST" action="{{ route('members.store') }}" enctype="multipart/form-data" autocomplete="off">
                                @csrf

                                <!-- Step Wizard -->
                                <div class="mb-4">
                                    <div class="d-flex justify-content-center align-items-center gap-4 flex-wrap" id="wizardSteps">
                                        <div class="wizard-step position-relative text-center active" data-step="1">
                                            <div class="step-circle bg-primary text-white shadow">1</div>
                                            <div class="step-label mt-2 small">Personal Info</div>
                                        </div>
                                        <div class="wizard-step position-relative text-center" data-step="2">
                                            <div class="step-circle bg-secondary text-white shadow">2</div>
                                            <div class="step-label mt-2 small">Other Info</div>
                                        </div>
                                        <div class="wizard-step position-relative text-center" data-step="3">
                                            <div class="step-circle bg-secondary text-white shadow">3</div>
                                            <div class="step-label mt-2 small">Family Information</div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 1: Personal Information -->
                                <div id="step1">
                                    <div class="row g-4 mb-3">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select name="membership_type" id="membership_type" class="form-select select2" required>
                                                    <option value="">Select type</option>
                                                    <option value="permanent">Permanent</option>
                                                    <option value="temporary">Temporary</option>
                                                </select>
                                                <label for="membership_type">Membership Type</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4" id="memberTypeWrapper">
                                            <div class="form-floating">
                                                <select name="member_type" id="member_type" class="form-select select2" required>
                                                    <option value="">Select type</option>
                                                    <option value="father">Father</option>
                                                    <option value="mother">Mother</option>
                                                    <option value="independent">Independent Person</option>
                                                </select>
                                                <label for="member_type">Member Type</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="full_name" id="full_name" placeholder="Full Name" required>
                                                <label for="full_name">Full Name</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select select2" name="gender" id="gender" required>
                                                    <option value="">Select</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                                <label for="gender">Gender</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="date" class="form-control" name="date_of_birth" id="date_of_birth" placeholder="Date of Birth" required>
                                                <label for="date_of_birth">Date of Birth</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select select2" name="education_level" id="education_level">
                                                    <option value="">Select</option>
                                                    <option value="primary">Primary</option>
                                                    <option value="secondary">Secondary</option>
                                                    <option value="high_level">High Level</option>
                                                    <option value="certificate">Certificate</option>
                                                    <option value="diploma">Diploma</option>
                                                    <option value="bachelor_degree">Bachelor Degree</option>
                                                    <option value="masters">Masters</option>
                                                    <option value="phd">PhD</option>
                                                    <option value="professor">Professor</option>
                                                    <option value="not_studied">Not Studied</option>
                                                </select>
                                                <label for="education_level">Education Level</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="profession" id="profession" placeholder="Profession" required>
                                                <label for="profession">Profession</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="nida_number" id="nida_number" placeholder="NIDA Number">
                                                <label for="nida_number">NIDA Number</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="btn btn-primary btn-lg px-4 shadow-sm next-step" id="nextStep1">Next <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>
                                </div>

                                <!-- Step 2: Other Information -->
                                <div id="step2" style="display:none;">
                                    <div class="row g-4 mb-4">
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <div class="input-group">
                                                    <span class="input-group-text bg-white border-end-0" id="basic-addon1">
                                                        <img src="https://flagcdn.com/w20/tz.png" alt="TZ" width="20" height="15" class="me-2">+255
                                                    </span>
                                                    <input type="text" class="form-control border-start-0" name="phone_number" id="phone_number" placeholder="7XXXXXXXX" required style="border-radius: 0 .375rem .375rem 0;">
                                                </div>
                                                <label for="phone_number">Phone</label>
                                            </div>
                                            <small class="text-muted ms-1">Format: +255 7XXXXXXXX (9 digits)</small>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <input type="email" class="form-control" name="email" id="email" placeholder="Email (optional)">
                                                <label for="email">Email (optional)</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row g-4 mb-4">
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select select2" id="region" name="region" required></select>
                                                <label for="region">Region</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select select2" id="district" name="district" required></select>
                                                <label for="district">District</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select select2" id="ward" name="ward" required></select>
                                                <label for="ward">Ward</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="street" id="street" placeholder="Street" required>
                                                <label for="street">Street</label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="address" id="address" placeholder="Eg, P O Box 1039 Moshi-Kilimanjaro" style="height: 48px;" required />
                                                <label for="address">P O Box</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-floating">
                                                <select class="form-select select2" id="tribe" name="tribe" required></select>
                                                <label for="tribe">Tribe</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3" id="otherTribeWrapper" style="display:none;">
                                            <div class="form-floating">
                                                <input type="text" class="form-control" name="other_tribe" id="other_tribe" placeholder="Please specify" required>
                                                <label for="other_tribe">Other Tribe</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 shadow-sm prev-step" id="prevStep2"><i class="fas fa-arrow-left me-1"></i>Back</button>
                                        <button type="button" class="btn btn-primary btn-lg px-4 shadow-sm next-step" id="nextStep2">Next <i class="fas fa-arrow-right ms-1"></i></button>
                                    </div>
                                </div>

                                <!-- Step 3: Family Information -->
                                <div id="step3" style="display:none;">
                                    <!-- Spouse Alive Section (conditional for mother/father) -->
                                    <div id="spouseAliveSection" class="border rounded-3 p-4 mb-4 bg-white shadow-sm" style="display:none;">
                                        <h6 class="mb-3 text-primary fw-bold" id="spouseSectionTitle"><i class="fas fa-user me-2"></i>Spouse Information</h6>
                                        <div class="mb-3">
                                            <label class="form-label" id="spouseAliveLabel">Is your spouse alive?</label>
                                            <div class="toggle-switch-wrapper">
                                                <input class="toggle-switch-input" type="checkbox" id="spouseAliveToggle" name="spouse_alive_toggle">
                                                <label class="toggle-switch-label" for="spouseAliveToggle">
                                                    <span class="toggle-switch-inner"></span>
                                                    <span class="toggle-switch-switch"></span>
                                                </label>
                                                <span class="toggle-switch-text" id="spouseAliveToggleLabel">No</span>
                                            </div>
                                            <input type="hidden" name="spouse_alive" id="spouse_alive_hidden" value="no">
                                        </div>
                                        <div id="spouseInfoFields" style="display:none;">
                                            <div class="row g-4 mb-3">
                                                <div class="col-md-6">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="spouse_full_name" id="spouse_full_name" placeholder="Full Name">
                                                        <label for="spouse_full_name">Full Name</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <input type="date" class="form-control" name="spouse_date_of_birth" id="spouse_date_of_birth" placeholder="Date of Birth">
                                                        <label for="spouse_date_of_birth">Date of Birth</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-floating">
                                                        <select class="form-select select2" name="spouse_education_level" id="spouse_education_level">
                                                            <option value="">Select</option>
                                                            <option value="primary">Primary</option>
                                                            <option value="secondary">Secondary</option>
                                                            <option value="high_level">High Level</option>
                                                            <option value="certificate">Certificate</option>
                                                            <option value="diploma">Diploma</option>
                                                            <option value="bachelor_degree">Bachelor Degree</option>
                                                            <option value="masters">Masters</option>
                                                            <option value="phd">PhD</option>
                                                            <option value="professor">Professor</option>
                                                            <option value="not_studied">Not Studied</option>
                                                        </select>
                                                        <label for="spouse_education_level">Education Level</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-4 mb-3">
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="spouse_profession" id="spouse_profession" placeholder="Profession">
                                                        <label for="spouse_profession">Profession</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="spouse_nida_number" id="spouse_nida_number" placeholder="NIDA Number">
                                                        <label for="spouse_nida_number">NIDA Number (optional)</label>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="email" class="form-control" name="spouse_email" id="spouse_email" placeholder="Email (optional)">
                                                        <label for="spouse_email">Email (optional)</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row g-4 mb-3">
                                                <div class="col-md-4">
                                                    <div class="form-floating">
                                                        <input type="text" class="form-control" name="spouse_phone_number" id="spouse_phone_number" placeholder="Phone Number">
                                                        <label for="spouse_phone_number">Phone Number</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Guardian Section -->
                                    <div id="guardianSection" class="border rounded-3 p-4 mb-4 bg-white shadow-sm" style="display:none;">
                                        <h6 class="mb-3 text-primary fw-bold"><i class="fas fa-user-shield me-2"></i>Guardian / Responsible Person</h6>
                                        <div class="row g-4">
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="guardian_name" id="guardian_name" placeholder="Guardian Name">
                                                    <label for="guardian_name">Guardian Name</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <input type="text" class="form-control" name="guardian_phone" id="guardian_phone" placeholder="Guardian Phone">
                                                    <label for="guardian_phone">Guardian Phone</label>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-floating">
                                                    <select class="form-select" name="guardian_relationship" id="guardian_relationship">
                                                        <option value="">Select</option>
                                                        <option value="Parent">Parent</option>
                                                        <option value="Relative">Relative</option>
                                                        <option value="Neighbor">Neighbor</option>
                                                        <option value="Friend">Friend</option>
                                                        <option value="Other">Other</option>
                                                    </select>
                                                    <label for="guardian_relationship">Relationship</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Children Section -->
                                    <div id="childrenSection" class="border rounded-3 p-4 mb-4 bg-white shadow-sm" style="display:none;">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <h6 class="mb-0 text-primary fw-bold"><i class="fas fa-children me-2"></i>Children</h6>
                                            <small class="text-muted">Max: 4 | Under {{ config('membership.child_max_age', 18) }} years</small>
                                        </div>
                                        <div class="mb-2">
                                            <button type="button" class="btn btn-outline-primary btn-sm" id="addChildBtn"><i class="fas fa-plus me-1"></i>Add Child</button>
                                        </div>
                                        <div id="childrenContainer"></div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <button type="button" class="btn btn-outline-secondary btn-lg px-4 shadow-sm prev-step" id="prevStep3"><i class="fas fa-arrow-left me-1"></i>Back</button>
                                        <!-- Save Member button moved to summary step -->
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </main>
                <style>
                    .main-form-card { background: linear-gradient(135deg, #f8f9fa 60%, #e9e4f0 100%); }
                    /* Advanced Toggle Switch Styles */
                    .toggle-switch-wrapper {
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                    }
                    .toggle-switch-input {
                        display: none;
                    }
                    .toggle-switch-label {
                        position: relative;
                        display: inline-block;
                        width: 56px;
                        height: 32px;
                        cursor: pointer;
                        user-select: none;
                    }
                    .toggle-switch-inner {
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background: #dc3545;
                        border-radius: 32px;
                        transition: background 0.3s;
                    }
                    .toggle-switch-switch {
                        position: absolute;
                        top: 3px;
                        left: 4px;
                        width: 26px;
                        height: 26px;
                        background: #fff;
                        border-radius: 50%;
                        box-shadow: 0 2px 6px rgba(0,0,0,0.12);
                        transition: left 0.3s;
                    }
                    .toggle-switch-input:checked + .toggle-switch-label .toggle-switch-inner {
                        background: #198754;
                    }
                    .toggle-switch-input:checked + .toggle-switch-label .toggle-switch-switch {
                        left: 26px;
                    }
                    .toggle-switch-label:after {
                        content: '';
                        display: none;
                    }
                    .toggle-switch-label {
                        margin-bottom: 0;
                    }
                    .toggle-switch-text {
                        font-weight: 600;
                        color: #5b2a86;
                        min-width: 32px;
                        text-align: left;
                        transition: color 0.3s;
                    }
                    .toggle-switch-input:checked ~ .toggle-switch-text {
                        color: #198754;
                    }
                    .toggle-switch-label .toggle-switch-inner:before {
                        content: 'No';
                        position: absolute;
                        left: 10px;
                        top: 6px;
                        color: #fff;
                        font-size: 0.95em;
                        font-weight: 500;
                        transition: opacity 0.3s;
                        opacity: 1;
                    }
                    .toggle-switch-input:checked + .toggle-switch-label .toggle-switch-inner:before {
                        content: 'Yes';
                        left: 28px;
                        color: #fff;
                        opacity: 1;
                    }
                    .bg-gradient-primary { background: linear-gradient(90deg, #5b2a86 0%, #1f2b6c 100%) !important; }
                    .step-circle {
                        width: 44px; height: 44px; display: flex; align-items: center; justify-content: center;
                        border-radius: 50%; font-size: 1.3rem; font-weight: 600; margin: 0 auto;
                        border: 3px solid #fff; box-shadow: 0 2px 8px rgba(91,42,134,0.08);
                        transition: background 0.3s, color 0.3s;
                    }
                    .wizard-step.active .step-circle { background: #5b2a86 !important; color: #fff !important; border-color: #1f2b6c; }
                    .wizard-step.completed .step-circle {
                        background: #198754 !important;
                        color: #fff !important;
                        border-color: #198754;
                        box-shadow: 0 0 0 4px #e9e4f0;
                    }
                    .wizard-step .step-label { color: #5b2a86; font-weight: 500; letter-spacing: 0.01em; }
                    .wizard-step.active .step-label { color: #1f2b6c; font-weight: 700; }
                    .wizard-step.completed .step-label {
                        color: #198754;
                        font-weight: 700;
                    }
                    .wizard-step:not(:last-child)::after {
                        content: ""; position: absolute; top: 22px; right: -32px; width: 64px; height: 4px;
                        background: linear-gradient(90deg, #5b2a86 0%, #1f2b6c 100%); border-radius: 2px;
                        z-index: 0; opacity: 0.15;
                    }
                    .wizard-step.completed:not(:last-child)::after {
                        background: linear-gradient(90deg, #198754 0%, #5b2a86 100%);
                        opacity: 0.3;
                    }
                    @media (max-width: 767px) {
                        .wizard-step:not(:last-child)::after { width: 32px; right: -18px; }
                    }
                    .form-floating > .form-control:focus, .form-floating > .form-select:focus {
                        border-color: #5b2a86; box-shadow: 0 0 0 0.2rem rgba(91,42,134,0.08);
                    }
                    .form-floating > label { color: #5b2a86; font-weight: 500; }
                    .form-floating > .form-control.is-valid, .form-floating > .form-select.is-valid {
                        border-color: #198754; box-shadow: 0 0 0 0.2rem rgba(25,135,84,0.08);
                    }
                    .form-floating > .form-control.is-invalid, .form-floating > .form-select.is-invalid {
                        border-color: #dc3545; box-shadow: 0 0 0 0.2rem rgba(220,53,69,0.08);
                    }
                    .animated.fadeIn { animation: fadeIn 0.7s cubic-bezier(.4,0,.2,1); }
                    @keyframes fadeIn { from { opacity: 0; transform: translateY(24px);} to { opacity: 1; transform: none; } }

                    /* Custom transitions */
                    .fade-in {
                        opacity: 0;
                        animation: fadeIn 0.5s forwards;
                    }
                    .fade-out {
                        opacity: 1;
                        animation: fadeOut 0.5s forwards;
                    }
                    @keyframes fadeOut {
                        0% { opacity: 1; }
                        100% { opacity: 0; }
                    }
                </style>
                <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script>
                    (function(){
                        const membershipTypeEl = document.getElementById('membership_type');
                        const memberTypeWrapper = document.getElementById('memberTypeWrapper');
                        const memberTypeEl = document.getElementById('member_type');

                        const childrenSection = document.getElementById('childrenSection');
                        const childrenContainer = document.getElementById('childrenContainer');
                        const addChildBtn = document.getElementById('addChildBtn');
                        const guardianSection = document.getElementById('guardianSection');

                        // Loading indicators for fetch requests
                        const regionEl = document.getElementById('region');
                        const districtEl = document.getElementById('district');
                        const wardEl = document.getElementById('ward');
                        regionEl.innerHTML = '<option value="">Loading regions...</option>';
                        fetch('{{ asset('data/locations.json') }}').then(r=>r.json()).then(data=>{
                            const regions = data.regions || [];
                            regionEl.innerHTML = '<option value="">Select</option>' + regions.map(r=>`<option value="${r.name}">${r.name}</option>`).join('');
                            regionEl.addEventListener('change', function(){
                                const selected = regions.find(x=>x.name===this.value);
                                const districts = selected? selected.districts: [];
                                districtEl.innerHTML = districts.length ? '<option value="">Select</option>' + districts.map(d=>`<option value="${d.name}">${d.name}</option>`).join('') : '<option value="">Loading districts...</option>';
                                wardEl.innerHTML = '<option value="">Select</option>';
                                districtEl.dispatchEvent(new Event('change'));
                            });
                            districtEl.addEventListener('change', function(){
                                const selectedRegion = regions.find(x=>x.name===regionEl.value);
                                const districts = selectedRegion? selectedRegion.districts: [];
                                const selectedDistrict = districts.find(x=>x.name===this.value);
                                const wards = selectedDistrict? selectedDistrict.wards: [];
                                wardEl.innerHTML = wards.length ? '<option value="">Select</option>' + wards.map(w=>`<option value="${w}">${w}</option>`).join('') : '<option value="">Loading wards...</option>';
                                $(wardEl).trigger('change.select2');
                            });
                            $('.select2').select2({ width: '100%' });
                            $(regionEl).on('change', ()=> $(districtEl).trigger('change.select2'));
                        });

                        // Tribes dataset with "Other" support and loading indicator
                        const tribeEl = document.getElementById('tribe');
                        const otherTribeWrapper = document.getElementById('otherTribeWrapper');
                        tribeEl.innerHTML = '<option value="">Loading tribes...</option>';
                        fetch('{{ asset('data/tribes.json') }}').then(r=>r.json()).then(data=>{
                            const tribes = data.tribes || [];
                            tribeEl.innerHTML = '<option value="">Select</option>' + tribes.map(t=>`<option value="${t}">${t}</option>`).join('');
                            tribeEl.addEventListener('change', function(){
                                if(this.value === 'Other'){
                                    otherTribeWrapper.style.display = '';
                                } else {
                                    otherTribeWrapper.style.display = 'none';
                                }
                            });
                            $('.select2').select2({ width: '100%' });
                        });

                        const step1 = document.getElementById('step1');
                        const step2 = document.getElementById('step2');
                        const step3 = document.getElementById('step3');
                        // Smoother step transitions
                        function setStepActive(step){
                            document.querySelectorAll('#wizardSteps .wizard-step').forEach((s, idx)=>{
                                const stepNum = parseInt(s.getAttribute('data-step'));
                                if(stepNum < step){
                                    s.classList.add('completed');
                                    s.classList.remove('active');
                                } else if(stepNum === step){
                                    s.classList.add('active');
                                    s.classList.remove('completed');
                                } else {
                                    s.classList.remove('active');
                                    s.classList.remove('completed');
                                }
                            });
                        }
                        function showStep(stepToShow, stepToHide) {
                            const showEl = document.getElementById('step' + stepToShow);
                            const hideEl = document.getElementById('step' + stepToHide);
                            hideEl.classList.add('fade-out');
                            showEl.classList.add('fade-in');
                            setTimeout(() => {
                                hideEl.style.display = 'none';
                                hideEl.classList.remove('fade-out');
                                showEl.style.display = '';
                                setTimeout(() => { showEl.classList.remove('fade-in'); }, 500);
                            }, 500);
                            // Only show dynamic member type title for steps 1 and 2
                            const stepHeaderTitle = document.getElementById('stepHeaderTitle');
                            if(stepToShow === 1 || stepToShow === 2){
                                const type = document.getElementById('member_type').value;
                                let memberLabel = '';
                                if(type === 'father') memberLabel = 'Father';
                                else if(type === 'mother') memberLabel = 'Mother';
                                else if(type === 'independent') memberLabel = 'Independent';
                                if(memberLabel) {
                                    stepHeaderTitle.textContent = `Personal ${memberLabel} Information`;
                                } else {
                                    stepHeaderTitle.textContent = 'Add Member';
                                }
                            } else if(stepToShow === 3) {
                                stepHeaderTitle.textContent = 'Family Information';
                            } else {
                                stepHeaderTitle.textContent = 'Add Member';
                            }
                        }

                        document.getElementById('nextStep1').addEventListener('click', function(){
                            if(!validateStep(1)) {
                                // Optionally highlight invalid fields
                                return;
                            }
                            showStep(2, 1); setStepActive(2);
                        });
                        document.getElementById('nextStep2').addEventListener('click', function(){
                            if(!validateStep(2)) {
                                return;
                            }
                            showStep(3, 2); setStepActive(3);
                        });
                        document.getElementById('prevStep2').addEventListener('click', function(){ showStep(1, 2); setStepActive(1); });
                        document.getElementById('prevStep3').addEventListener('click', function(){ showStep(2, 3); setStepActive(2); });

                        // Dynamic children add/remove
                        let childCount = 0;
                        function renderChildren(){
                            // Attach validation and age calculation for all children
                            childrenContainer.querySelectorAll('.child-fullname').forEach(el=>{ el.addEventListener('input', ()=> markValid(el, !!el.value.trim())); });
                            childrenContainer.querySelectorAll('.child-gender').forEach(el=>{ el.addEventListener('change', ()=> markValid(el, !!el.value)); });
                            childrenContainer.querySelectorAll('.child-dob').forEach((el)=>{
                                el.addEventListener('change', ()=> {
                                    const d=new Date(el.value);
                                    markValid(el, !!el.value && d<new Date());
                                    const ageSpan = el.parentElement.querySelector('.child-age');
                                    if(!!el.value && d<new Date()){
                                        const today = new Date();
                                        let age = today.getFullYear() - d.getFullYear();
                                        const m = today.getMonth() - d.getMonth();
                                        if (m < 0 || (m === 0 && today.getDate() < d.getDate())) {
                                            age--;
                                        }
                                        ageSpan.textContent = age + ' yrs';
                                        ageSpan.style.display = '';
                                    } else {
                                        ageSpan.textContent = '';
                                        ageSpan.style.display = 'none';
                                    }
                                });
                            });
                        }

                        function addChild(){
                            if(childCount >= 4) return;
                            const idx = childCount;
                            const row = document.createElement('div');
                            row.className = 'row g-3 mb-2 align-items-end child-row';
                            row.innerHTML = `
                                <div class="col-md-5">
                                    <label class="form-label">Child ${idx+1} Full Name</label>
                                    <input type="text" class="form-control child-fullname" name="children[${idx}][full_name]" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Gender</label>
                                    <select class="form-select child-gender" name="children[${idx}][gender]" required>
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Date of Birth</label>
                                    <div class="input-group">
                                        <input type="date" class="form-control child-dob" name="children[${idx}][date_of_birth]" required>
                                        <span class="input-group-text child-age" style="min-width:80px;display:none;"></span>
                                    </div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-danger btn-sm remove-child-btn" title="Remove Child"><i class="fas fa-trash"></i></button>
                                </div>
                            `;
                            childrenContainer.appendChild(row);
                            childCount++;
                            renderChildren();
                            // Remove child handler
                            row.querySelector('.remove-child-btn').addEventListener('click', function(){
                                row.remove();
                                childCount--;
                                // Re-index names for children
                                Array.from(childrenContainer.querySelectorAll('.child-row')).forEach((r, i) => {
                                    r.querySelector('.form-label').textContent = `Child ${i+1} Full Name`;
                                    r.querySelector('.child-fullname').setAttribute('name', `children[${i}][full_name]`);
                                    r.querySelector('.child-gender').setAttribute('name', `children[${i}][gender]`);
                                    r.querySelector('.child-dob').setAttribute('name', `children[${i}][date_of_birth]`);
                                });
                            });
                        }

                        addChildBtn.addEventListener('click', function(){
                            addChild();
                        });

                        // If you want to start with zero children, comment out below
                        // addChild();


                        function updateVisibility(){
                            const membership = membershipTypeEl.value;
                            const type = memberTypeEl.value;

                            // Dynamic step header title only for steps 1 and 2
                            const stepHeaderTitle = document.getElementById('stepHeaderTitle');
                            if(step1.style.display !== 'none' || step2.style.display !== 'none'){
                                let memberLabel = '';
                                if(type === 'father') memberLabel = 'Father';
                                else if(type === 'mother') memberLabel = 'Mother';
                                else if(type === 'independent') memberLabel = 'Independent';
                                if(memberLabel) {
                                    stepHeaderTitle.textContent = `Personal ${memberLabel} Information`;
                                } else {
                                    stepHeaderTitle.textContent = 'Add Member';
                                }
                            } else if(step3.style.display !== 'none'){
                                stepHeaderTitle.textContent = 'Family Information';
                            } else {
                                stepHeaderTitle.textContent = 'Add Member';
                            }

                            // Hide member type if temporary
                            if(membership === 'temporary'){
                                memberTypeWrapper.style.display = 'none';
                                memberTypeEl.value = '';
                                memberTypeEl.removeAttribute('required');
                            } else {
                                memberTypeWrapper.style.display = '';
                                memberTypeEl.setAttribute('required', 'required');
                            }

                            // Spouse section logic
                            const spouseAliveSection = document.getElementById('spouseAliveSection');
                            const spouseSectionTitle = document.getElementById('spouseSectionTitle');
                            const spouseAliveLabel = document.getElementById('spouseAliveLabel');
                            if(membership === 'permanent' && (type === 'father' || type === 'mother')){
                                spouseAliveSection.style.display = '';
                                if(type === 'father') {
                                    spouseSectionTitle.innerHTML = '<i class="fas fa-female me-2"></i>Wife Information';
                                    spouseAliveLabel.textContent = 'Is your wife alive?';
                                } else {
                                    spouseSectionTitle.innerHTML = '<i class="fas fa-male me-2"></i>Husband Information';
                                    spouseAliveLabel.textContent = 'Is your husband alive?';
                                }
                            } else {
                                spouseAliveSection.style.display = 'none';
                                document.getElementsByName('spouse_alive').forEach(r=>r.checked=false);
                                document.getElementById('spouseInfoFields').style.display = 'none';
                            }

                            // Children visible for permanent father/mother and independent persons
                            if(membership === 'permanent'){
                                if(type === 'father' || type === 'mother' || type === 'independent'){
                                    childrenSection.style.display = '';
                                } else {
                                    childrenSection.style.display = 'none';
                                    renderChildren(0);
                                }
                            } else {
                                // Temporary: no children, show guardian
                                childrenSection.style.display = 'none';
                                renderChildren(0);
                            }

                            // Guardian only for temporary
                            if(membership === 'temporary'){
                                guardianSection.style.display = '';
                                // Make guardian fields required
                                document.getElementById('guardian_name').setAttribute('required', 'required');
                                document.getElementById('guardian_phone').setAttribute('required', 'required');
                                document.getElementById('guardian_relationship').setAttribute('required', 'required');
                            } else {
                                guardianSection.style.display = 'none';
                                // Remove required from guardian fields
                                document.getElementById('guardian_name').removeAttribute('required');
                                document.getElementById('guardian_phone').removeAttribute('required');
                                document.getElementById('guardian_relationship').removeAttribute('required');
                            }
                        }
                        // Spouse alive toggle logic
                        const spouseAliveToggle = document.getElementById('spouseAliveToggle');
                        const spouseInfoFields = document.getElementById('spouseInfoFields');
                        const spouseAliveToggleLabel = document.getElementById('spouseAliveToggleLabel');
                        const spouseAliveHidden = document.getElementById('spouse_alive_hidden');
                        spouseAliveToggle.addEventListener('change', function(){
                            if(this.checked){
                                spouseInfoFields.style.display = '';
                                spouseAliveToggleLabel.textContent = 'Yes';
                                spouseAliveHidden.value = 'yes';
                            } else {
                                spouseInfoFields.style.display = 'none';
                                spouseAliveToggleLabel.textContent = 'No';
                                spouseAliveHidden.value = 'no';
                                // Optionally clear spouse info fields
                                document.getElementById('spouse_full_name').value = '';
                                document.getElementById('spouse_date_of_birth').value = '';
                                document.getElementById('spouse_education_level').value = '';
                                document.getElementById('spouse_profession').value = '';
                                document.getElementById('spouse_nida_number').value = '';
                                document.getElementById('spouse_email').value = '';
                                document.getElementById('spouse_phone_number').value = '';
                            }
                        });

                        membershipTypeEl.addEventListener('change', updateVisibility);
                        memberTypeEl.addEventListener('change', updateVisibility);
                        // childrenCountEl removed

                        // Live member ID display when typing name
                        const liveIdEl = document.getElementById('liveMemberId');
                        const nameEl = document.querySelector('input[name="full_name"]');
                        let cachedId = '';
                        function refreshMemberId(){
                            if(!nameEl.value.trim()) { liveIdEl.textContent = ''; return; }
                            if(cachedId) { liveIdEl.textContent = '('+cachedId+')'; return; }
                            fetch('{{ route('members.next_id') }}').then(r=>r.json()).then(j=>{ cachedId = j.next_id; liveIdEl.textContent = '('+cachedId+')'; });
                        }
                        nameEl.addEventListener('input', refreshMemberId);

                        // Real-time validation
                        const emailInput = document.querySelector('input[name="email"]');
                        const phoneLocalInput = document.getElementById('phone_number');
                        const dobInput = document.querySelector('input[name="date_of_birth"]');
                        function markValid(el, valid){
                            el.classList.remove('is-invalid');
                            el.classList.remove('is-valid');
                            el.classList.add(valid? 'is-valid':'is-invalid');
                        }
                        function validateEmail(){ const v=emailInput.value.trim(); if(!v) return true; const ok=/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v); markValid(emailInput, ok); return ok; }
                        function validatePhone(){ const v=phoneLocalInput.value.replace(/\s+/g,''); const ok=/^[67][0-9]{8}$/.test(v); markValid(phoneLocalInput, ok); return ok; }
                        function validateDob(){ const v=dobInput.value; if(!v) return false; const d=new Date(v); const ok=d<new Date(); markValid(dobInput, ok); return ok; }
                        emailInput.addEventListener('input', validateEmail);
                        phoneLocalInput.addEventListener('input', validatePhone);
                        dobInput.addEventListener('change', validateDob);

                        function validateStep(step){
                            if(step===1){
                                const req = ['membership_type','member_type','full_name','gender','date_of_birth','education_level','profession'];
                                let ok=true; req.forEach(n=>{ const el=document.getElementsByName(n)[0]; if(el && (el.offsetParent!==null)) { const v=el.value.trim(); const pass=!!v; markValid(el, pass); ok = ok && pass; }});
                                ok = ok && validateDob();
                                return ok;
                            }
                            if(step===2){
                                let ok = validatePhone() & validateEmail();
                                return !!ok;
                            }
                            if(step===3){
                                let ok = true;
                                // Validate guardian fields for temporary members
                                if(membershipTypeEl.value === 'temporary'){
                                    const gName = document.getElementById('guardian_name');
                                    const gPhone = document.getElementById('guardian_phone');
                                    const gRel = document.getElementById('guardian_relationship');
                                    const gNameOk = !!gName.value.trim();
                                    const gPhoneOk = !!gPhone.value.trim();
                                    const gRelOk = !!gRel.value.trim();
                                    markValid(gName, gNameOk); ok = ok && gNameOk;
                                    markValid(gPhone, gPhoneOk); ok = ok && gPhoneOk;
                                    markValid(gRel, gRelOk); ok = ok && gRelOk;
                                }
                                // children required when count > 0
                                const childRows = childrenContainer.querySelectorAll('.child-row');
                                if(childRows.length>0){
                                    childRows.forEach(row => {
                                        const fullName = row.querySelector('.child-fullname');
                                        const gender = row.querySelector('.child-gender');
                                        const dob = row.querySelector('.child-dob');
                                        const passName = !!fullName.value.trim();
                                        const passGender = !!gender.value;
                                        const d = new Date(dob.value);
                                        const passDob = !!dob.value && d<new Date();
                                        markValid(fullName, passName); ok = ok && passName;
                                        markValid(gender, passGender); ok = ok && passGender;
                                        markValid(dob, passDob); ok = ok && passDob;
                                    });
                                }
                                return ok;
                            }
                            if(step===4){
                                // summary step, always valid
                                return true;
                            }
                            return true;
                        }

                        updateVisibility();

                        // Add summary step before final submission
                        // Create summary step element
                        const summaryStep = document.createElement('div');
                        summaryStep.id = 'step4';
                        summaryStep.style.display = 'none';
                        summaryStep.innerHTML = `
                            <div class="card p-4 mb-4">
                                <h5 class="mb-3 text-primary fw-bold"><i class="fas fa-eye me-2"></i>Review Information</h5>
                                <div id="summaryContent"></div>
                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary btn-lg px-4 shadow-sm prev-step" id="prevStep4"><i class="fas fa-arrow-left me-1"></i>Back</button>
                                    <button type="submit" class="btn btn-success btn-lg px-4 shadow-sm"><i class="fas fa-save me-2"></i>Save Member</button>
                                </div>
                            </div>
                        `;
                        document.getElementById('addMemberForm').appendChild(summaryStep);

                        // Add summary step to wizardSteps
                        const wizardSteps = document.getElementById('wizardSteps');
                        const summaryWizardStep = document.createElement('div');
                        summaryWizardStep.className = 'wizard-step position-relative text-center';
                        summaryWizardStep.setAttribute('data-step', '4');
                        summaryWizardStep.innerHTML = `<div class="step-circle bg-secondary text-white shadow">4</div><div class="step-label mt-2 small">Summary</div>`;
                        wizardSteps.appendChild(summaryWizardStep);

                        // Next button on step3 shows summary
                        const nextStep3Btn = document.createElement('button');
                        nextStep3Btn.type = 'button';
                        nextStep3Btn.className = 'btn btn-primary btn-lg px-4 shadow-sm next-step';
                        nextStep3Btn.id = 'nextStep3';
                        nextStep3Btn.innerHTML = 'Next <i class="fas fa-arrow-right ms-1"></i>';
                        const step3Actions = document.getElementById('step3').querySelector('.d-flex.justify-content-between');
                        step3Actions.insertBefore(nextStep3Btn, step3Actions.lastElementChild);

                        nextStep3Btn.addEventListener('click', function(){
                            if(!validateStep(3)) { Swal.fire('Validation', 'Please complete required fields in Step 3.', 'warning'); return; }
                            // Categorize summary preview by form steps
                            const summaryContent = document.getElementById('summaryContent');
                            // Personal Information (all fields)
                            const personalFields = [
                                { label: 'Membership Type', value: membershipTypeEl.value },
                                { label: 'Member Type', value: memberTypeEl.value },
                                { label: 'Full Name', value: document.getElementById('full_name').value },
                                { label: 'Gender', value: document.getElementById('gender').value },
                                { label: 'Date of Birth', value: document.getElementById('date_of_birth').value },
                                { label: 'Education Level', value: document.getElementById('education_level').value },
                                { label: 'Profession', value: document.getElementById('profession').value },
                                { label: 'NIDA Number', value: document.getElementById('nida_number').value }
                            ];
                            // Other Information (all fields)
                            const otherFields = [
                                { label: 'Phone', value: document.getElementById('phone_number').value },
                                { label: 'Email', value: document.getElementById('email').value },
                                { label: 'Region', value: regionEl.value },
                                { label: 'District', value: districtEl.value },
                                { label: 'Ward', value: wardEl.value },
                                { label: 'Street', value: document.getElementById('street').value },
                                { label: 'Address', value: document.getElementById('address').value },
                                { label: 'Tribe', value: tribeEl.value + (tribeEl.value==='Other' ? ' ('+document.getElementById('other_tribe').value+')' : '') }
                            ];
                            // Family Information (spouse logic)
                            const familyFields = [];
                            if(membershipTypeEl.value==='permanent' && (memberTypeEl.value==='father' || memberTypeEl.value==='mother')){
                                familyFields.push({ label: (memberTypeEl.value==='father'?'Wife':'Husband')+' Alive', value: spouseAliveToggle.checked ? 'Yes' : 'No' });
                                if(spouseAliveToggle.checked){
                                    familyFields.push({ label: (memberTypeEl.value==='father'?'Wife':'Husband')+' Full Name', value: document.getElementById('spouse_full_name').value });
                                    familyFields.push({ label: (memberTypeEl.value==='father'?'Wife':'Husband')+' Date of Birth', value: document.getElementById('spouse_date_of_birth').value });
                                    familyFields.push({ label: (memberTypeEl.value==='father'?'Wife':'Husband')+' Education Level', value: document.getElementById('spouse_education_level').value });
                                    familyFields.push({ label: (memberTypeEl.value==='father'?'Wife':'Husband')+' Profession', value: document.getElementById('spouse_profession').value });
                                    familyFields.push({ label: (memberTypeEl.value==='father'?'Wife':'Husband')+' NIDA Number', value: document.getElementById('spouse_nida_number').value });
                                    familyFields.push({ label: (memberTypeEl.value==='father'?'Wife':'Husband')+' Email', value: document.getElementById('spouse_email').value });
                                    familyFields.push({ label: (memberTypeEl.value==='father'?'Wife':'Husband')+' Phone Number', value: document.getElementById('spouse_phone_number').value });
                                }
                            }
                            // Guardian Information (all fields)
                            if(membershipTypeEl.value==='temporary'){
                                familyFields.push({ label: 'Guardian Name', value: document.getElementById('guardian_name').value });
                                familyFields.push({ label: 'Guardian Phone', value: document.getElementById('guardian_phone').value });
                                familyFields.push({ label: 'Guardian Relationship', value: document.getElementById('guardian_relationship').value });
                            }
                            // Render summary by category
                            let html = '';
                            function renderFields(fields, title){
                                html += `<h6 class='fw-bold text-primary mt-3 mb-2'>${title}</h6><div class='row'>`;
                                for(let i=0; i<fields.length; i+=2){
                                    html += '<div class="col-md-6 mb-2">'
                                        +'<div class="border rounded p-2 bg-light">'
                                        +'<b>'+fields[i].label+':</b> '+(fields[i].value ? fields[i].value : '<span class="text-secondary">None</span>')+'</div></div>';
                                    if(fields[i+1]){
                                        html += '<div class="col-md-6 mb-2">'
                                            +'<div class="border rounded p-2 bg-light">'
                                            +'<b>'+fields[i+1].label+':</b> '+(fields[i+1].value ? fields[i+1].value : '<span class="text-secondary">None</span>')+'</div></div>';
                                    }
                                }
                                html += '</div>';
                            }
                            // Dynamic section titles based on member type
                            let personalTitle = 'Personal Information';
                            let otherTitle = 'Other Information';
                            if(memberTypeEl.value === 'mother') {
                                personalTitle = 'Mother Personal Information';
                                otherTitle = 'Mother Other Information';
                            } else if(memberTypeEl.value === 'father') {
                                personalTitle = 'Father Personal Information';
                                otherTitle = 'Father Other Information';
                            }
                            renderFields(personalFields, personalTitle);
                            renderFields(otherFields, otherTitle);
                            renderFields(familyFields, 'Family Information');
                            // Children
                            const childRows = childrenContainer.querySelectorAll('.child-row');
                            html += `<h6 class='fw-bold text-primary mt-3 mb-2'>Children</h6><div class="row mt-1"><div class="col-md-12"><ul>`;
                            if(childRows.length>0){
                                childRows.forEach((row, idx)=>{
                                    const name = row.querySelector('.child-fullname').value;
                                    const gender = row.querySelector('.child-gender').value;
                                    const dob = row.querySelector('.child-dob').value;
                                    html += `<li>Child ${idx+1}: <b>Name:</b> ${name ? name : '<span class=\"text-secondary\">None</span>'}, <b>Gender:</b> ${gender ? gender : '<span class=\"text-secondary\">None</span>'}, <b>DOB:</b> ${dob ? dob : '<span class=\"text-secondary\">None</span>'}</li>`;
                                });
                            } else {
                                html += '<li class="text-secondary">No children added</li>';
                            }
                            html += '</ul></div></div>';
                            summaryContent.innerHTML = html;
                            showStep(4, 3); setStepActive(4);
                        });

                        document.getElementById('prevStep4').addEventListener('click', function(){ showStep(3, 4); setStepActive(3); });

                        // On submit, place +255 prefix into value
                        document.getElementById('addMemberForm').addEventListener('submit', function(e){
                            if(!validateStep(1) || !validateStep(2) || !validateStep(3) || !validateStep(4)){
                                Swal.fire('Validation', 'Please correct the highlighted fields.', 'error');
                                e.preventDefault();
                                return false;
                            }
                            const v = phoneLocalInput.value.replace(/\s+/g,'');
                            if(v && /^[67][0-9]{8}$/.test(v)){
                                phoneLocalInput.value = '+255' + v;
                            }

                            // Show processing spinner immediately
                            Swal.fire({
                                title: 'Processing...',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            // Form will submit and backend will handle the next SweetAlert
                        });

                        // realtime validation for text/select inputs in steps 1 & 2
                        const fullNameEl = document.getElementsByName('full_name')[0];
                        fullNameEl.addEventListener('input', ()=> markValid(fullNameEl, fullNameEl.value.trim().length>=2));
                        document.getElementsByName('gender')[0].addEventListener('change', (e)=> markValid(e.target, !!e.target.value));
                        document.getElementsByName('membership_type')[0].addEventListener('change', (e)=> markValid(e.target, !!e.target.value));
                        document.getElementsByName('member_type')[0].addEventListener('change', (e)=> markValid(e.target, !!e.target.value || document.getElementsByName('membership_type')[0].value==='temporary'));
                        document.getElementsByName('education_level')[0].addEventListener('change', (e)=> markValid(e.target, !!e.target.value));
                        const professionEl = document.getElementsByName('profession')[0];
                        professionEl.addEventListener('input', ()=> markValid(professionEl, professionEl.value.trim().length >= 2));
                        // Real-time validation for mother's profession field
                        const motherProfessionEl = document.getElementsByName('mother_profession')[0];
                        if(motherProfessionEl) {
                            motherProfessionEl.addEventListener('input', ()=> markValid(motherProfessionEl, motherProfessionEl.value.trim().length >= 2));
                        }
                        document.getElementsByName('region')[0].addEventListener('change', (e)=> markValid(e.target, !!e.target.value));
                        document.getElementsByName('district')[0].addEventListener('change', (e)=> markValid(e.target, !!e.target.value));
                        document.getElementsByName('ward')[0].addEventListener('change', (e)=> markValid(e.target, !!e.target.value));

                        // Initialize Select2 on static selects too
                        $('.select2').select2({ width: '100%' });
                    })();
                </script>
                <footer class="bg-dark text-light py-4 mt-auto">
                  <div class="container px-4">
                    <div class="row align-items-center">
                      <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                        <small>&copy; <span id="year"></span> Waumini Link — Version 1.0</small>
                      </div>
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
            </div>
        </div>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script>
            document.getElementById('year').textContent = new Date().getFullYear();
        </script>
    </body>
</html>