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
            
            /* Custom sidebar styling */
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
            
            /* Card header titles styling */
            .card-header {
                color: white !important;
                font-weight: 600;
            }
            
            /* Statistics card labels styling */
            .card .small.text-white-50 {
                color: white !important;
                font-weight: 500;
            }
        </style>
    </head>
    <body class="sb-nav-fixed">
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-3 d-flex align-items-center logo-white-section" href="{{ route('dashboard.secretary') }}">
                <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo" style="height: 45px; max-width: 200px; object-fit: contain;">
            </a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <!-- Welcome Message -->
            <div class="navbar-text text-white me-auto ms-3" style="font-size: 1.1rem;">
                <strong>Welcome to Waumini Link</strong>
            </div>
            <!-- Navbar Search-->
            <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
                <div class="input-group">
                    <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
                    <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
                </div>
            </form>
            <!-- Navbar-->
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

                                    <a class="nav-link" href="{{ route('members.add') }}">
            <i class="fas fa-user-plus me-2"></i>Add New Member
        </a>
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
                                    <a class="nav-link" href="{{ route('services.sunday.index') }}"><i class="fas fa-church me-2"></i>Sunday Services</a>
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
                        <h1 class="mt-4"></h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active"></li>
                        </ol>
                        
                        <!-- Welcome Section -->
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center">
                                            @if(Auth::user()->profile_picture)
                                                <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" 
                                                     alt="Profile Picture" 
                                                     class="rounded-circle me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary text-white" 
                                                     style="width: 50px; height: 50px; font-size: 20px; font-weight: bold;">
                                                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                                </div>
                                            @endif
                                            <div>
                                                <h4 class="mb-1">👋 Welcome back, <span class="text-primary">{{ Auth::user()->name ?? 'User' }}</span>!</h4>
                                                <p class="text-muted mb-0">Here's what's happening at Waumini Link today.</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <div class="text-muted small">
                                            <i class="fas fa-calendar me-1"></i>
                                            <span id="current-date"></span>
                                        </div>
                                        <div class="text-muted small">
                                            <i class="fas fa-clock me-1"></i>
                                            <span id="current-time"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-primary text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="small text-white-50">Total Members</div>
                                                <div class="h4 mb-0">{{ number_format($totalMembers) }}</div>
                                            </div>
                                            <div class="ms-3">
                                                <i class="fas fa-users fa-2x text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="{{ route('members.view') }}">View Members</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-warning text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="small text-white-50">Active Events</div>
                                                <div class="h4 mb-0">8</div>
                                            </div>
                                            <div class="ms-3">
                                                <i class="fas fa-calendar-alt fa-2x text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Events</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-success text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="small text-white-50">This Month's Donations</div>
                                                <div class="h4 mb-0">$12,450</div>
                                            </div>
                                            <div class="ms-3">
                                                <i class="fas fa-donate fa-2x text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Donations</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xl-3 col-md-6">
                                <div class="card bg-info text-white mb-4">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="flex-grow-1">
                                                <div class="small text-white-50">Upcoming Services</div>
                                                <div class="h4 mb-0">3</div>
                                            </div>
                                            <div class="ms-3">
                                                <i class="fas fa-church fa-2x text-white-50"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex align-items-center justify-content-between">
                                        <a class="small text-white stretched-link" href="#">View Services</a>
                                        <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Quick Actions -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <i class="fas fa-bolt me-1"></i>
                                        Quick Actions
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <a class="btn btn-outline-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3" href="{{ route('members.add') }}">
                                                    <i class="fas fa-user-plus fa-2x mb-2"></i>
                                                    <span>Add New Member</span>
                                                </a>
                                            </div>
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <button class="btn btn-outline-success w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                                    <i class="fas fa-calendar-plus fa-2x mb-2"></i>
                                                    <span>Create Event</span>
                                                </button>
                                            </div>
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <button class="btn btn-outline-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                                    <i class="fas fa-donate fa-2x mb-2"></i>
                                                    <span>Record Donation</span>
                                                </button>
                                            </div>
                                            <div class="col-lg-3 col-md-6 mb-3">
                                                <button class="btn btn-outline-info w-100 h-100 d-flex flex-column align-items-center justify-content-center py-3">
                                                    <i class="fas fa-chart-line fa-2x mb-2"></i>
                                                    <span>Generate Report</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-area me-1"></i>
                                        Area Chart Example
                                    </div>
                                    <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                                </div>
                            </div>
                            <div class="col-xl-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Bar Chart Example
                                    </div>
                                    <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                                </div>
                            </div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Example
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Office</th>
                                            <th>Age</th>
                                            <th>Start date</th>
                                            <th>Salary</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Name</th>
                                            <th>Position</th>
                                            <th>Office</th>
                                            <th>Age</th>
                                            <th>Start date</th>
                                            <th>Salary</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <tr>
                                            <td>Tiger Nixon</td>
                                            <td>System Architect</td>
                                            <td>Edinburgh</td>
                                            <td>61</td>
                                            <td>2011/04/25</td>
                                            <td>$320,800</td>
                                        </tr>
                                        <tr>
                                            <td>Garrett Winters</td>
                                            <td>Accountant</td>
                                            <td>Tokyo</td>
                                            <td>63</td>
                                            <td>2011/07/25</td>
                                            <td>$170,750</td>
                                        </tr>
                                        <tr>
                                            <td>Ashton Cox</td>
                                            <td>Junior Technical Author</td>
                                            <td>San Francisco</td>
                                            <td>66</td>
                                            <td>2009/01/12</td>
                                            <td>$86,000</td>
                                        </tr>
                                        <tr>
                                            <td>Cedric Kelly</td>
                                            <td>Senior Javascript Developer</td>
                                            <td>Edinburgh</td>
                                            <td>22</td>
                                            <td>2012/03/29</td>
                                            <td>$433,060</td>
                                        </tr>
                                        <tr>
                                            <td>Airi Satou</td>
                                            <td>Accountant</td>
                                            <td>Tokyo</td>
                                            <td>33</td>
                                            <td>2008/11/28</td>
                                            <td>$162,700</td>
                                        </tr>
                                        <tr>
                                            <td>Brielle Williamson</td>
                                            <td>Integration Specialist</td>
                                            <td>New York</td>
                                            <td>61</td>
                                            <td>2012/12/02</td>
                                            <td>$372,000</td>
                                        </tr>
                                        <tr>
                                            <td>Herrod Chandler</td>
                                            <td>Sales Assistant</td>
                                            <td>San Francisco</td>
                                            <td>59</td>
                                            <td>2012/08/06</td>
                                            <td>$137,500</td>
                                        </tr>
                                        <tr>
                                            <td>Rhona Davidson</td>
                                            <td>Integration Specialist</td>
                                            <td>Tokyo</td>
                                            <td>55</td>
                                            <td>2010/10/14</td>
                                            <td>$327,900</td>
                                        </tr>
                                        <tr>
                                            <td>Colleen Hurst</td>
                                            <td>Javascript Developer</td>
                                            <td>San Francisco</td>
                                            <td>39</td>
                                            <td>2009/09/15</td>
                                            <td>$205,500</td>
                                        </tr>
                                        <tr>
                                            <td>Sonya Frost</td>
                                            <td>Software Engineer</td>
                                            <td>Edinburgh</td>
                                            <td>23</td>
                                            <td>2008/12/13</td>
                                            <td>$103,600</td>
                                        </tr>
                                        <tr>
                                            <td>Jena Gaines</td>
                                            <td>Office Manager</td>
                                            <td>London</td>
                                            <td>30</td>
                                            <td>2008/12/19</td>
                                            <td>$90,560</td>
                                        </tr>
                                        <tr>
                                            <td>Quinn Flynn</td>
                                            <td>Support Lead</td>
                                            <td>Edinburgh</td>
                                            <td>22</td>
                                            <td>2013/03/03</td>
                                            <td>$342,000</td>
                                        </tr>
                                        <tr>
                                            <td>Charde Marshall</td>
                                            <td>Regional Director</td>
                                            <td>San Francisco</td>
                                            <td>36</td>
                                            <td>2008/10/16</td>
                                            <td>$470,600</td>
                                        </tr>
                                        <tr>
                                            <td>Haley Kennedy</td>
                                            <td>Senior Marketing Designer</td>
                                            <td>London</td>
                                            <td>43</td>
                                            <td>2012/12/18</td>
                                            <td>$313,500</td>
                                        </tr>
                                        <tr>
                                            <td>Tatyana Fitzpatrick</td>
                                            <td>Regional Director</td>
                                            <td>London</td>
                                            <td>19</td>
                                            <td>2010/03/17</td>
                                            <td>$385,750</td>
                                        </tr>
                                        <tr>
                                            <td>Michael Silva</td>
                                            <td>Marketing Designer</td>
                                            <td>London</td>
                                            <td>66</td>
                                            <td>2012/11/27</td>
                                            <td>$198,500</td>
                                        </tr>
                                        <tr>
                                            <td>Paul Byrd</td>
                                            <td>Chief Financial Officer (CFO)</td>
                                            <td>New York</td>
                                            <td>64</td>
                                            <td>2010/06/09</td>
                                            <td>$725,000</td>
                                        </tr>
                                        <tr>
                                            <td>Gloria Little</td>
                                            <td>Systems Administrator</td>
                                            <td>New York</td>
                                            <td>59</td>
                                            <td>2009/04/10</td>
                                            <td>$237,500</td>
                                        </tr>
                                        <tr>
                                            <td>Bradley Greer</td>
                                            <td>Software Engineer</td>
                                            <td>London</td>
                                            <td>41</td>
                                            <td>2012/10/13</td>
                                            <td>$132,000</td>
                                        </tr>
                                        <tr>
                                            <td>Dai Rios</td>
                                            <td>Personnel Lead</td>
                                            <td>Edinburgh</td>
                                            <td>35</td>
                                            <td>2012/09/26</td>
                                            <td>$217,500</td>
                                        </tr>
                                        <tr>
                                            <td>Jenette Caldwell</td>
                                            <td>Development Lead</td>
                                            <td>New York</td>
                                            <td>30</td>
                                            <td>2011/09/03</td>
                                            <td>$345,000</td>
                                        </tr>
                                        <tr>
                                            <td>Yuri Berry</td>
                                            <td>Chief Marketing Officer (CMO)</td>
                                            <td>New York</td>
                                            <td>40</td>
                                            <td>2009/06/25</td>
                                            <td>$675,000</td>
                                        </tr>
                                        <tr>
                                            <td>Caesar Vance</td>
                                            <td>Pre-Sales Support</td>
                                            <td>New York</td>
                                            <td>21</td>
                                            <td>2011/12/12</td>
                                            <td>$106,450</td>
                                        </tr>
                                        <tr>
                                            <td>Doris Wilder</td>
                                            <td>Sales Assistant</td>
                                            <td>Sidney</td>
                                            <td>23</td>
                                            <td>2010/09/20</td>
                                            <td>$85,600</td>
                                        </tr>
                                        <tr>
                                            <td>Angelica Ramos</td>
                                            <td>Chief Executive Officer (CEO)</td>
                                            <td>London</td>
                                            <td>47</td>
                                            <td>2009/10/09</td>
                                            <td>$1,200,000</td>
                                        </tr>
                                        <tr>
                                            <td>Gavin Joyce</td>
                                            <td>Developer</td>
                                            <td>Edinburgh</td>
                                            <td>42</td>
                                            <td>2010/12/22</td>
                                            <td>$92,575</td>
                                        </tr>
                                        <tr>
                                            <td>Jennifer Chang</td>
                                            <td>Regional Director</td>
                                            <td>Singapore</td>
                                            <td>28</td>
                                            <td>2010/11/14</td>
                                            <td>$357,650</td>
                                        </tr>
                                        <tr>
                                            <td>Brenden Wagner</td>
                                            <td>Software Engineer</td>
                                            <td>San Francisco</td>
                                            <td>28</td>
                                            <td>2011/06/07</td>
                                            <td>$206,850</td>
                                        </tr>
                                        <tr>
                                            <td>Fiona Green</td>
                                            <td>Chief Operating Officer (COO)</td>
                                            <td>San Francisco</td>
                                            <td>48</td>
                                            <td>2010/03/11</td>
                                            <td>$850,000</td>
                                        </tr>
                                        <tr>
                                            <td>Shou Itou</td>
                                            <td>Regional Marketing</td>
                                            <td>Tokyo</td>
                                            <td>20</td>
                                            <td>2011/08/14</td>
                                            <td>$163,000</td>
                                        </tr>
                                        <tr>
                                            <td>Michelle House</td>
                                            <td>Integration Specialist</td>
                                            <td>Sidney</td>
                                            <td>37</td>
                                            <td>2011/06/02</td>
                                            <td>$95,400</td>
                                        </tr>
                                        <tr>
                                            <td>Suki Burks</td>
                                            <td>Developer</td>
                                            <td>London</td>
                                            <td>53</td>
                                            <td>2009/10/22</td>
                                            <td>$114,500</td>
                                        </tr>
                                        <tr>
                                            <td>Prescott Bartlett</td>
                                            <td>Technical Author</td>
                                            <td>London</td>
                                            <td>27</td>
                                            <td>2011/05/07</td>
                                            <td>$145,000</td>
                                        </tr>
                                        <tr>
                                            <td>Gavin Cortez</td>
                                            <td>Team Leader</td>
                                            <td>San Francisco</td>
                                            <td>22</td>
                                            <td>2008/10/26</td>
                                            <td>$235,500</td>
                                        </tr>
                                        <tr>
                                            <td>Martena Mccray</td>
                                            <td>Post-Sales support</td>
                                            <td>Edinburgh</td>
                                            <td>46</td>
                                            <td>2011/03/09</td>
                                            <td>$324,050</td>
                                        </tr>
                                        <tr>
                                            <td>Unity Butler</td>
                                            <td>Marketing Designer</td>
                                            <td>San Francisco</td>
                                            <td>47</td>
                                            <td>2009/12/09</td>
                                            <td>$85,675</td>
                                        </tr>
                                        <tr>
                                            <td>Howard Hatfield</td>
                                            <td>Office Manager</td>
                                            <td>San Francisco</td>
                                            <td>51</td>
                                            <td>2008/12/16</td>
                                            <td>$164,500</td>
                                        </tr>
                                        <tr>
                                            <td>Hope Fuentes</td>
                                            <td>Secretary</td>
                                            <td>San Francisco</td>
                                            <td>41</td>
                                            <td>2010/02/12</td>
                                            <td>$109,850</td>
                                        </tr>
                                        <tr>
                                            <td>Vivian Harrell</td>
                                            <td>Financial Controller</td>
                                            <td>San Francisco</td>
                                            <td>62</td>
                                            <td>2009/02/14</td>
                                            <td>$452,500</td>
                                        </tr>
                                        <tr>
                                            <td>Timothy Mooney</td>
                                            <td>Office Manager</td>
                                            <td>London</td>
                                            <td>37</td>
                                            <td>2008/12/11</td>
                                            <td>$136,200</td>
                                        </tr>
                                        <tr>
                                            <td>Jackson Bradshaw</td>
                                            <td>Director</td>
                                            <td>New York</td>
                                            <td>65</td>
                                            <td>2008/09/26</td>
                                            <td>$645,750</td>
                                        </tr>
                                        <tr>
                                            <td>Olivia Liang</td>
                                            <td>Support Engineer</td>
                                            <td>Singapore</td>
                                            <td>64</td>
                                            <td>2011/02/03</td>
                                            <td>$234,500</td>
                                        </tr>
                                        <tr>
                                            <td>Bruno Nash</td>
                                            <td>Software Engineer</td>
                                            <td>London</td>
                                            <td>38</td>
                                            <td>2011/05/03</td>
                                            <td>$163,500</td>
                                        </tr>
                                        <tr>
                                            <td>Sakura Yamamoto</td>
                                            <td>Support Engineer</td>
                                            <td>Tokyo</td>
                                            <td>37</td>
                                            <td>2009/08/19</td>
                                            <td>$139,575</td>
                                        </tr>
                                        <tr>
                                            <td>Thor Walton</td>
                                            <td>Developer</td>
                                            <td>New York</td>
                                            <td>61</td>
                                            <td>2013/08/11</td>
                                            <td>$98,540</td>
                                        </tr>
                                        <tr>
                                            <td>Finn Camacho</td>
                                            <td>Support Engineer</td>
                                            <td>San Francisco</td>
                                            <td>47</td>
                                            <td>2009/07/07</td>
                                            <td>$87,500</td>
                                        </tr>
                                        <tr>
                                            <td>Serge Baldwin</td>
                                            <td>Data Coordinator</td>
                                            <td>Singapore</td>
                                            <td>64</td>
                                            <td>2012/04/09</td>
                                            <td>$138,575</td>
                                        </tr>
                                        <tr>
                                            <td>Zenaida Frank</td>
                                            <td>Software Engineer</td>
                                            <td>New York</td>
                                            <td>63</td>
                                            <td>2010/01/04</td>
                                            <td>$125,250</td>
                                        </tr>
                                        <tr>
                                            <td>Zorita Serrano</td>
                                            <td>Software Engineer</td>
                                            <td>San Francisco</td>
                                            <td>56</td>
                                            <td>2012/06/01</td>
                                            <td>$115,000</td>
                                        </tr>
                                        <tr>
                                            <td>Jennifer Acosta</td>
                                            <td>Junior Javascript Developer</td>
                                            <td>Edinburgh</td>
                                            <td>43</td>
                                            <td>2013/02/01</td>
                                            <td>$75,650</td>
                                        </tr>
                                        <tr>
                                            <td>Cara Stevens</td>
                                            <td>Sales Assistant</td>
                                            <td>New York</td>
                                            <td>46</td>
                                            <td>2011/12/06</td>
                                            <td>$145,600</td>
                                        </tr>
                                        <tr>
                                            <td>Hermione Butler</td>
                                            <td>Regional Director</td>
                                            <td>London</td>
                                            <td>47</td>
                                            <td>2011/03/21</td>
                                            <td>$356,250</td>
                                        </tr>
                                        <tr>
                                            <td>Lael Greer</td>
                                            <td>Systems Administrator</td>
                                            <td>London</td>
                                            <td>21</td>
                                            <td>2009/02/27</td>
                                            <td>$103,500</td>
                                        </tr>
                                        <tr>
                                            <td>Jonas Alexander</td>
                                            <td>Developer</td>
                                            <td>San Francisco</td>
                                            <td>30</td>
                                            <td>2010/07/14</td>
                                            <td>$86,500</td>
                                        </tr>
                                        <tr>
                                            <td>Shad Decker</td>
                                            <td>Regional Director</td>
                                            <td>Edinburgh</td>
                                            <td>51</td>
                                            <td>2008/11/13</td>
                                            <td>$183,000</td>
                                        </tr>
                                        <tr>
                                            <td>Michael Bruce</td>
                                            <td>Javascript Developer</td>
                                            <td>Singapore</td>
                                            <td>29</td>
                                            <td>2011/06/27</td>
                                            <td>$183,000</td>
                                        </tr>
                                        <tr>
                                            <td>Donna Snider</td>
                                            <td>Customer Support</td>
                                            <td>New York</td>
                                            <td>27</td>
                                            <td>2011/01/25</td>
                                            <td>$112,000</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </main>
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

<script>
  // Auto update year
  document.getElementById("year").textContent = new Date().getFullYear();
</script>

            </div>
        </div>
        <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
        <script src="{{ asset('assets/js/chart.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/chart-area-demo.js') }}"></script>
        <script src="{{ asset('js/chart-bar-demo.js') }}"></script>
        <script src="{{ asset('assets/js/datatables.min.js') }}" crossorigin="anonymous"></script>
        <script src="{{ asset('js/datatables-simple-demo.js') }}"></script>
        
        <!-- Date and Time Display -->
        <script>
            function updateDateTime() {
                const now = new Date();
                
                // Format date
                const dateOptions = { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                };
                const formattedDate = now.toLocaleDateString('en-US', dateOptions);
                
                // Format time
                const timeOptions = { 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit',
                    hour12: true 
                };
                const formattedTime = now.toLocaleTimeString('en-US', timeOptions);
                
                // Update elements
                const dateElement = document.getElementById('current-date');
                const timeElement = document.getElementById('current-time');
                
                if (dateElement) dateElement.textContent = formattedDate;
                if (timeElement) timeElement.textContent = formattedTime;
            }
            
            // Update immediately and then every second
            updateDateTime();
            setInterval(updateDateTime, 1000);
        </script>

        <!-- Add Member Modal -->
        <div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <!-- Modal Header with Gradient Background -->
                    <div class="modal-header text-white position-relative" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 2rem;">
                        <div class="d-flex align-items-center">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                <i class="fas fa-user-plus fa-2x"></i>
                            </div>
                            <div>
                                <h4 class="modal-title mb-1 fw-bold" id="addMemberModalLabel">Add New Member</h4>
                                <p class="mb-0 opacity-75">Register a new member to Waumini Link</p>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form id="addMemberForm">
                        <div class="modal-body p-0">
                            <!-- Progress Indicator -->
                            <div class="progress-container bg-light p-4 border-bottom">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="progress-step active" data-step="1">
                                        <div class="step-circle">1</div>
                                        <span class="step-label">Personal Info</span>
                                    </div>
                                    <div class="progress-line"></div>
                                    <div class="progress-step" data-step="2">
                                        <div class="step-circle">2</div>
                                        <span class="step-label">Location</span>
                                    </div>
                                    <div class="progress-line"></div>
                                    <div class="progress-step" data-step="3">
                                        <div class="step-circle">3</div>
                                        <span class="step-label">Family Info</span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-4">
                                <!-- Step 1: Personal Information -->
                                <div class="form-step active" id="step1">
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <h5 class="mb-0 fw-bold text-dark">Personal Information</h5>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <!-- Member ID Display (Read-only) -->
                                        <div class="col-12">
                                            <div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px;">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-id-card text-primary me-3"></i>
                                                    <div>
                                                        <strong>Member ID:</strong> 
                                                        <span id="generatedMemberId" class="text-primary fw-bold">Will be generated automatically</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-lg" id="fullName" name="full_name" placeholder="Full Name" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="fullName" class="fw-semibold">Full Name <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please enter a valid full name.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email Address" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="email" class="fw-semibold">Email Address <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please enter a valid email address.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="tel" class="form-control form-control-lg" id="phoneNumber" name="phone_number" placeholder="+255 Phone Number" value="+255" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="phoneNumber" class="fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please enter a valid phone number (9 digits after +255).</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="date" class="form-control form-control-lg" id="dateOfBirth" name="date_of_birth" required max="" style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="dateOfBirth" class="fw-semibold">Date of Birth <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please enter a valid date of birth (cannot be today or in the future).</div>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <select class="form-select form-select-lg" id="gender" name="gender" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                </select>
                                                <label for="gender" class="fw-semibold">Gender <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please select a gender.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-lg" id="nidaNumber" name="nida_number" placeholder="NIDA Number" style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="nidaNumber" class="fw-semibold">NIDA Number</label>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating position-relative">
                                                <input type="text" class="form-control form-control-lg" id="tribe" name="tribe" placeholder="Search tribe..." required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;" autocomplete="off">
                                                <label for="tribe" class="fw-semibold">Tribe <span class="text-danger">*</span></label>
                                                <div id="tribeDropdown" class="dropdown-menu w-100 shadow-lg" style="max-height: 250px; overflow-y: auto; display: none; border-radius: 12px; border: none;">
                                                    <!-- Tribe options will be populated here -->
                                                </div>
                                                <div class="invalid-feedback">Please select a tribe.</div>
                                            </div>
                                        </div>
                                        
                                        <!-- Other Tribe Input (Hidden by default) -->
                                        <div class="col-md-6" id="otherTribeDiv" style="display: none;">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-lg" id="otherTribe" name="other_tribe" placeholder="Specify other tribe..." style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="otherTribe" class="fw-semibold">Specify Other Tribe</label>
                                                <div class="invalid-feedback">Please specify the tribe name.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Location Information -->
                                <div class="form-step" id="step2" style="display: none;">
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-map-marker-alt text-success"></i>
                                                </div>
                                                <h5 class="mb-0 fw-bold text-dark">Location Information</h5>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="form-floating position-relative">
                                                <input type="text" class="form-control form-control-lg" id="region" name="region" placeholder="Search region..." required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;" autocomplete="off">
                                                <label for="region" class="fw-semibold">Region <span class="text-danger">*</span></label>
                                                <div id="regionDropdown" class="dropdown-menu w-100 shadow-lg" style="max-height: 250px; overflow-y: auto; display: block; border-radius: 12px; border: none;">
                                                    <!-- Region options will be populated here -->
                                                </div>
                                                <div class="invalid-feedback">Please select a valid region.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating position-relative">
                                                <input type="text" class="form-control form-control-lg" id="district" name="district" placeholder="Search district..." disabled required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;" autocomplete="off">
                                                <label for="district" class="fw-semibold">District <span class="text-danger">*</span></label>
                                                <div id="districtDropdown" class="dropdown-menu w-100 shadow-lg" style="max-height: 250px; overflow-y: auto; display: none; border-radius: 12px; border: none;">
                                                    <!-- District options will be populated here -->
                                                </div>
                                                <div class="invalid-feedback">Please select a district.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating position-relative">
                                                <input type="text" class="form-control form-control-lg" id="ward" name="ward" placeholder="Search ward..." disabled required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;" autocomplete="off">
                                                <label for="ward" class="fw-semibold">Ward <span class="text-danger">*</span></label>
                                                <div id="wardDropdown" class="dropdown-menu w-100 shadow-lg" style="max-height: 250px; overflow-y: auto; display: none; border-radius: 12px; border: none;">
                                                    <!-- Ward options will be populated here -->
                                                </div>
                                                <div class="invalid-feedback">Please select a ward.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-floating">
                                                <input type="text" class="form-control form-control-lg" id="street" name="street" placeholder="Street" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                <label for="street" class="fw-semibold">Street <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please enter the street name.</div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="form-floating">
                                                <textarea class="form-control form-control-lg" id="address" name="address" placeholder="Full Address" required style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease; height: 100px;"></textarea>
                                                <label for="address" class="fw-semibold">Full Address <span class="text-danger">*</span></label>
                                                <div class="invalid-feedback">Please enter the full address.</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Family Information -->
                                <div class="form-step" id="step3" style="display: none;">
                                    <div class="row mb-4">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
                                                    <i class="fas fa-users text-warning"></i>
                                                </div>
                                                <h5 class="mb-0 fw-bold text-dark">Family Information</h5>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                                                <div class="card-body p-4">
                                                    <h6 class="fw-bold mb-3">Is the believer living with family?</h6>
                                                    <div class="d-flex gap-3">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="living_with_family" id="livingWithFamilyYes" value="yes" required style="transform: scale(1.2);">
                                                            <label class="form-check-label fw-semibold" for="livingWithFamilyYes">
                                                                <i class="fas fa-check-circle text-success me-2"></i>Yes
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="radio" name="living_with_family" id="livingWithFamilyNo" value="no" required style="transform: scale(1.2);">
                                                            <label class="form-check-label fw-semibold" for="livingWithFamilyNo">
                                                                <i class="fas fa-times-circle text-danger me-2"></i>No
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="invalid-feedback mt-2">Please specify if the believer is living with family.</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6" id="familyRelationshipDiv" style="display: none;">
                                            <div class="form-floating">
                                                <select class="form-select form-select-lg" id="familyRelationship" name="family_relationship" style="border-radius: 12px; border: 2px solid #e9ecef; transition: all 0.3s ease;">
                                                    <option value="">Select Relationship</option>
                                                    <option value="father">👨 Father</option>
                                                    <option value="mother">👩 Mother</option>
                                                    <option value="uncle">👨‍🦱 Uncle</option>
                                                    <option value="aunt">👩‍🦱 Aunt</option>
                                                    <option value="brother">👦 Brother</option>
                                                    <option value="sister">👧 Sister</option>
                                                    <option value="grandfather">👴 Grandfather</option>
                                                    <option value="grandmother">👵 Grandmother</option>
                                                    <option value="other">👤 Other</option>
                                                </select>
                                                <label for="familyRelationship" class="fw-semibold">Living with whom?</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer bg-light border-0 p-4">
                            <div class="d-flex justify-content-between w-100">
                                <button type="button" class="btn btn-outline-danger btn-lg px-4" data-bs-dismiss="modal" style="border-radius: 12px;">
                                    <i class="fas fa-times me-2"></i>Cancel
                                </button>
                                
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-primary btn-lg px-4" id="prevStep" style="display: none; border-radius: 12px;">
                                        <i class="fas fa-arrow-left me-2"></i>Previous
                                    </button>
                                    <button type="button" class="btn btn-primary btn-lg px-4" id="nextStep" style="border-radius: 12px;">
                                        Next <i class="fas fa-arrow-right ms-2"></i>
                                    </button>
                                    <button type="submit" class="btn btn-success btn-lg px-4" id="submitBtn" style="display: none; border-radius: 12px;">
                                        <i class="fas fa-save me-2"></i>Add Member
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <style>
            /* Custom Styles for Amazing Design */
            .form-control:focus, .form-select:focus {
                border-color: #667eea !important;
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
                transform: translateY(-2px);
            }
            
            .form-control, .form-select {
                transition: all 0.3s ease;
            }
            
            .form-control:hover, .form-select:hover {
                border-color: #667eea;
                transform: translateY(-1px);
            }
            
            .progress-container {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            }
            
            .progress-step {
                display: flex;
                flex-direction: column;
                align-items: center;
                position: relative;
                opacity: 0.5;
                transition: all 0.3s ease;
            }
            
            .progress-step.active {
                opacity: 1;
            }
            
            .step-circle {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: #e9ecef;
                color: #6c757d;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                margin-bottom: 8px;
                transition: all 0.3s ease;
            }
            
            .progress-step.active .step-circle {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                transform: scale(1.1);
            }
            
            .step-label {
                font-size: 0.875rem;
                font-weight: 600;
                color: #6c757d;
            }
            
            .progress-step.active .step-label {
                color: #667eea;
            }
            
            .progress-line {
                flex: 1;
                height: 2px;
                background: #e9ecef;
                margin: 0 10px;
                margin-top: 20px;
            }
            
            .form-step {
                animation: fadeIn 0.5s ease-in-out;
            }
            
            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(20px); }
                to { opacity: 1; transform: translateY(0); }
            }
            
            .dropdown-item {
                padding: 12px 16px;
                transition: all 0.2s ease;
            }
            
            .dropdown-item:hover {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                transform: translateX(5px);
            }
            
            .card {
                transition: all 0.3s ease;
            }
            
            .card:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
            }
            
            .btn {
                transition: all 0.3s ease;
                font-weight: 600;
            }
            
            .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            }
            
            .modal-content {
                animation: modalSlideIn 0.3s ease-out;
            }
            
            @keyframes modalSlideIn {
                from { 
                    opacity: 0; 
                    transform: scale(0.9) translateY(-50px); 
                }
                to { 
                    opacity: 1; 
                    transform: scale(1) translateY(0); 
                }
            }

            /* Validation States */
            .form-control.is-valid, .form-select.is-valid {
                border-color: #28a745 !important;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%2328a745' d='M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 12px center;
                background-size: 20px 20px;
                padding-right: 45px;
            }

            .form-control.is-invalid, .form-select.is-invalid {
                border-color: #dc3545 !important;
                background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='%23dc3545' d='M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z'/%3e%3cpath fill='%23dc3545' d='M5.354 4.646a.5.5 0 1 0-.708.708L7.293 8l-2.647 2.646a.5.5 0 0 0 .708.708L8 8.707l2.646 2.647a.5.5 0 0 0 .708-.708L8.707 8l2.647-2.646a.5.5 0 0 0-.708-.708L8 7.293 5.354 4.646z'/%3e%3c/svg%3e");
                background-repeat: no-repeat;
                background-position: right 12px center;
                background-size: 20px 20px;
                padding-right: 45px;
            }


            /* Input Group Styles */
            .input-group-text {
                transition: all 0.3s ease;
            }

            .input-group:focus-within .input-group-text {
                border-color: #667eea;
                background-color: #f8f9ff;
            }

            /* Phone Number Validation */
            .form-control:focus {
                box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25) !important;
            }

            .form-control.is-valid:focus {
                box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
            }

            .form-control.is-invalid:focus {
                box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
            }

            /* Responsive adjustments for the add member modal */
            @media (max-width: 576px) {
                .modal-dialog {
                    max-width: 95% !important;
                    margin: 0.75rem auto;
                }
                .modal-header {
                    padding: 1.25rem !important;
                }
                .modal-body {
                    padding: 1rem !important;
                }
                .form-floating > label {
                    font-size: 0.9rem;
                }
                .btn.btn-lg {
                    padding: 0.6rem 1rem !important;
                    font-size: 1rem !important;
                }
                .progress-container {
                    padding: 1rem !important;
                }
                .progress-step .step-circle {
                    width: 34px;
                    height: 34px;
                }
                .progress-line {
                    margin: 0 6px;
                }
            }
        </style>

        <script>
            // Load Tanzania locations dynamically
            let tanzanianRegions = {};
            fetch("{{ asset('data/tanzania-locations.json') }}")
                .then(res => res.json())
                .then(json => {
                    // Transform into { Region: { districts: [names...] } } for compatibility
                    const out = {};
                    if (json && Array.isArray(json.regions)) {
                        json.regions.forEach(r => {
                            out[r.name] = { districts: (r.districts || []).map(d => d.name) };
                        });
                    }
                    tanzanianRegions = out;
                    initializeRegionDropdown();
                })
                .catch(() => {
                    // Fallback minimal dataset
                    tanzanianRegions = {
                        'Dar es Salaam': { districts: ['Ilala', 'Kinondoni', 'Temeke', 'Ubungo', 'Kigamboni'] },
                        'Arusha': { districts: ['Arusha City', 'Meru', 'Karatu', 'Ngorongoro'] },
                        'Kilimanjaro': { districts: ['Moshi Urban', 'Moshi Rural', 'Hai'] }
                    };
                    initializeRegionDropdown();
                });

            // Sample wards data (you can expand this)
            const wardsData = {
    'Arusha City': ['Central', 'Sekei', 'Themi', 'Unga Limited', 'Kaloleni', 'Levolosi', 'Daraja Mbili'],
    'Ilala': ['Buguruni', 'Kariakoo', 'Kisutu', 'Mchafukoge', 'Gerezani', 'Vingunguti', 'Tabata'],
    'Kinondoni': ['Kawe', 'Msasani', 'Oyster Bay', 'Sinza', 'Kijitonyama', 'Makumbusho', 'Mwananyamala'],
    'Temeke': ['Chamazi', 'Kigamboni', 'Mbagala', 'Tandika', 'Kurasini', 'Chang’ombe', 'Azimio'],
    'Ubungo': ['Msumi', 'Saranga', 'Makuburi', 'Kimara', 'Mbezi', 'Goba', 'Mwananyamala'],
    'Kigamboni': ['Kibada', 'Kisarawe II', 'Tungi', 'Kimbiji', 'Mbutu', 'Somangila', 'Vijibweni'],
    
    'Dodoma Urban': ['Tambukareli', 'Majengo', 'Hazina', 'Ntyuka', 'Kikuyu Kusini', 'Kiwanja cha Ndege'],
    'Mbeya City': ['Iyunga', 'Iziwa', 'Maanga', 'Sisimba', 'Itagano', 'Forest', 'Ilemi'],
    'Tanga City': ['Chumbageni', 'Njoro', 'Makorora', 'Ngamiani Kaskazini', 'Ngamiani Kusini', 'Central'],
    'Mwanza City': ['Pasiansi', 'Nyamagana', 'Isamilo', 'Mbugani', 'Igoma', 'Mirongo', 'Mkolani'],
    'Zanzibar Urban': ['Mji Mkongwe', 'Amani', 'Rahaleo', 'Kwahani', 'Kilimahewa', 'Magomeni', 'Mwembeshauri'],
    
    // Manyara Region
    'Babati Rural': [
        'Arri','Ayasanda','Bashnet','Boay','Dabil','Dareda','Duru','Endakiso','Gallapo','Gidas',
        'Kiru','Madunga','Magara','Magugu','Mamire','Mwada','Nar','Nkaiti','Qash','Riroda','Ufana'
    ],
    'Babati Urban': [
        'Babati','Bagara','Bonga','Maisaka','Mutuka','Nangara','Sigino','Singe'
    ],
    'Hanang': [
        'Balagidalalu','Bassodesh','Bassotu','Dirma','Endasak','Endasiwold','Ganana','Gehandu',
        'Gendabi','Getanuwas','Gidahababieg','Gisambalang','Giting','Hidet','Hirbadaw','Katesh',
        'Laghanga','Lalaji','Masakta','Maskron','Masqaroda','Mogitu','Nangwa','Simbay','Sirop'
    ],
    'Kiteto': [
        'Bwagamoyo','Dongo','Dosidosi','Engusero','Kibaya','Kijungu','Lengatei','Makame','Matui',
        'Ndedo','Njoro','Olboloti','Partimbo','Songambele','Sunya','Loolera','Magungu','Chapakazi',
        'Namelock','Ndirigishi','Kaloleni','Bwawani','Laiseri'
    ],
    'Mbulu': [
        'Ayamaami','Ayamohe','Bargish','Bashay','Daudi','Dinamu','Dongobesh','Endagikot','Endamilay',
        'Eshkesh','Gehandu','Geterer','Gidihim','Gunyoda','Hayderer','Haydom','Imboru','Kainam',
        'Maghang','Marang','Maretadu','Masieda','Masqaroda','Murray','Nahasey','Nambis','Sanu Baray',
        'Tlawi','Tumati','Uhuru','Yaeda Ampa','Yaeda Chini'
    ],
    'Simanjiro': [
        'Orkesumet','Naberera','Loiborsiret','Emboreet','Terrat','Oljoro','Shambarai','Mrerani',
        'Msitu wa Tembo','Ngorika','Liborsoit','Ruvu Remiti','Kitwai','Komolo','Naisinyai',
        'Endiamtu','Endonyongijape'
    ],

    // Kilimanjaro Region
    'Moshi Urban': [
        'Kaloleni', 'Kiboriloni', 'Rau', 'Shirimatunda', 'Majengo', 'Pasua', 'Mji Mpya', 'Boma Mbuzi',
        'Bondeni', 'Kiusa', 'Kiomboi', 'Korongoni', 'Longuo B', 'Majengo', 'Mawenzi', 'Mfumuni',
        'Miembeni', 'Msaranga', 'Ng’ambo', 'Njoro', 'Pasua', 'Rau', 'Shirimatunda', 'Soweto'
    ],
    'Moshi Rural': [
        'Arusha Chini', 'Kahe', 'Kahe Mashariki', 'Kibosho Kati', 'Kibosho Magharibi', 'Kibosho Mashariki',
        'Kilema Kaskazini', 'Kilema Kati', 'Kilema Kusini', 'Kimochi', 'Kindi', 'Kirima', 'Kisima',
        'Kisiwani', 'Kwabada', 'Kwafungo', 'Kwakifua', 'Kwemingoji', 'Kwemkabala', 'Kwezitu', 'Lusanga',
        'Magila', 'Magoroto', 'Makole', 'Maji ya Chai', 'Mshiri', 'Mshikamano', 'Njoro', 'Pasua', 'Rau'
    ],
    'Hai': [
        'Bombo', 'Bumilayinga', 'Chomvu', 'Dindira', 'Himo', 'Kilimanjaro', 'Kwasadala', 'Mikumi',
        'Mlimani', 'Moshi', 'Njoro', 'Rau', 'Shirimatunda', 'Soweto', 'Uru', 'Uru Mashariki', 'Uru Kusini'
    ],
    'Siha': [
        'Kilimanjaro', 'Kindi', 'Kirima', 'Kisima', 'Kisongo', 'Kivulini', 'Kiwira', 'Lushoto', 'Maji ya Chai',
        'Mshikamano', 'Njoro', 'Pasua', 'Rau', 'Shirimatunda', 'Soweto'
    ],
    'Rombo': [
        'Bombo', 'Bumilayinga', 'Chomvu', 'Dindira', 'Himo', 'Kilimanjaro', 'Kwasadala', 'Mikumi',
        'Mlimani', 'Moshi', 'Njoro', 'Rau', 'Shirimatunda', 'Soweto', 'Uru', 'Uru Mashariki', 'Uru Kusini'
    ],
    'Mwanga': [
        'Bombo', 'Bumilayinga', 'Chomvu', 'Dindira', 'Himo', 'Kilimanjaro', 'Kwasadala', 'Mikumi',
        'Mlimani', 'Moshi', 'Njoro', 'Rau', 'Shirimatunda', 'Soweto', 'Uru', 'Uru Mashariki', 'Uru Kusini'
    ],
    'Same': [
        'Bombo', 'Bumilayinga', 'Chomvu', 'Dindira', 'Himo', 'Kilimanjaro', 'Kwasadala', 'Mikumi',
        'Mlimani', 'Moshi', 'Njoro', 'Rau', 'Shirimatunda', 'Soweto', 'Uru', 'Uru Mashariki', 'Uru Kusini'
    ]
};



           // Tanzanian tribes data
const tanzanianTribes = [
    'Sukuma', 'Nyamwezi', 'Chagga', 'Haya', 'Makonde', 'Nyakyusa', 'Hehe', 'Gogo', 'Ha', 'Zaramo',
    'Bena', 'Pare', 'Ngoni', 'Luguru', 'Shambaa', 'Digo', 'Tumbuka', 'Sangu', 'Kinga', 'Kimbu',
    'Fipa', 'Safwa', 'Iraqw', 'Gorowa', 'Burunge', 'Alagwa', 'Datoga', 'Hadza', 'Sandawe',
    'Ngindo', 'Ndali', 'Pangwa', 'Matengo', 'Ngasa', 'Nyaturu', 'Rufiji', 'Ndendeule', 'Mwera',
    'Ngindo', 'Kuria', 'Jita', 'Simbiti', 'Ikizu', 'Zinza', 'Hadzabe', 'Barabaig', 'Manyema',
    'Kwavi', 'Ngasa', 'Suba', 'Ndali', 'Kisankasa', 'Mbugwe', 'Rangi', 'Sukuma-Nyamwezi Cluster',
    'Segeju', 'Shihiri', 'Ngulu', 'Pimbwe', 'Holoholo', 'Tongwe', 'Rungwa', 'Konongo', 'Nyiha',
    'Ndamba', 'Mbunga', 'Ngindo', 'Kinga', 'Manda', 'Ndendeule', 'Malila', 'Nyiha', 'Hangaza',
    'Ikoma', 'Ngoreme', 'Nindi', 'Kerewe', 'Sumbwa', 'Ruhha', 'Nyiramba', 'Ikizu', 'Mwanji',
    'Other' // kept this catch-all for unlisted tribes
];


            // Initialize all dropdowns on page load
            function initializeRegionDropdown() {
                const dropdown = document.getElementById('regionDropdown');
                const regions = Object.keys(tanzanianRegions);
                
                dropdown.innerHTML = regions.map(region => 
                    `<a class="dropdown-item" href="#" data-region="${region}">${region}</a>`
                ).join('');
            }

            function initializeTribeDropdown() {
                const dropdown = document.getElementById('tribeDropdown');
                
                dropdown.innerHTML = tanzanianTribes.map(tribe => 
                    `<a class="dropdown-item" href="#" data-tribe="${tribe}">${tribe}</a>`
                ).join('');
            }

            // Region search functionality
            document.getElementById('region').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const dropdown = document.getElementById('regionDropdown');
                const regions = Object.keys(tanzanianRegions);
                
                if (searchTerm.length > 0) {
                    const filteredRegions = regions.filter(region => 
                        region.toLowerCase().includes(searchTerm)
                    );
                    
                    dropdown.innerHTML = filteredRegions.map(region => 
                        `<a class="dropdown-item" href="#" data-region="${region}">${region}</a>`
                    ).join('');
                } else {
                    dropdown.innerHTML = regions.map(region => 
                        `<a class="dropdown-item" href="#" data-region="${region}">${region}</a>`
                    ).join('');
                }
                dropdown.style.display = 'block';
            });

            // Show dropdown when region input is focused
            document.getElementById('region').addEventListener('focus', function() {
                document.getElementById('regionDropdown').style.display = 'block';
            });

            // Tribe search functionality
            document.getElementById('tribe').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const dropdown = document.getElementById('tribeDropdown');
                const otherTribeDiv = document.getElementById('otherTribeDiv');
                const otherTribeInput = document.getElementById('otherTribe');
                
                // Check if user typed "Other" directly
                if (this.value.toLowerCase() === 'other') {
                    otherTribeDiv.style.display = 'block';
                    otherTribeInput.required = true;
                    dropdown.style.display = 'none';
                } else {
                    otherTribeDiv.style.display = 'none';
                    otherTribeInput.required = false;
                    otherTribeInput.value = '';
                    otherTribeInput.classList.remove('is-valid', 'is-invalid');
                    
                    if (searchTerm.length > 0) {
                        const filteredTribes = tanzanianTribes.filter(tribe => 
                            tribe.toLowerCase().includes(searchTerm)
                        );
                        
                        dropdown.innerHTML = filteredTribes.map(tribe => 
                            `<a class="dropdown-item" href="#" data-tribe="${tribe}">${tribe}</a>`
                        ).join('');
                    } else {
                        dropdown.innerHTML = tanzanianTribes.map(tribe => 
                            `<a class="dropdown-item" href="#" data-tribe="${tribe}">${tribe}</a>`
                        ).join('');
                    }
                    dropdown.style.display = 'block';
                }
            });

            // Show tribe dropdown when focused
            document.getElementById('tribe').addEventListener('focus', function() {
                document.getElementById('tribeDropdown').style.display = 'block';
            });

            // Handle tribe selection
            document.getElementById('tribeDropdown').addEventListener('click', function(e) {
                if (e.target.classList.contains('dropdown-item')) {
                    e.preventDefault();
                    const selectedTribe = e.target.getAttribute('data-tribe');
                    const tribeInput = document.getElementById('tribe');
                    tribeInput.value = selectedTribe;
                    this.style.display = 'none';
                    
                    // Trigger validation for tribe field
                    const isValid = selectedTribe.trim().length >= 2;
                    tribeInput.classList.toggle('is-invalid', !isValid);
                    tribeInput.classList.toggle('is-valid', isValid && selectedTribe.length > 0);
                    
                    // Show/hide other tribe input based on selection
                    const otherTribeDiv = document.getElementById('otherTribeDiv');
                    const otherTribeInput = document.getElementById('otherTribe');
                    
                    if (selectedTribe === 'Other') {
                        otherTribeDiv.style.display = 'block';
                        otherTribeInput.required = true;
                    } else {
                        otherTribeDiv.style.display = 'none';
                        otherTribeInput.required = false;
                        otherTribeInput.value = '';
                        otherTribeInput.classList.remove('is-valid', 'is-invalid');
                    }
                }
            });

            // Handle region selection
            document.getElementById('regionDropdown').addEventListener('click', function(e) {
                if (e.target.classList.contains('dropdown-item')) {
                    e.preventDefault();
                    const selectedRegion = e.target.getAttribute('data-region');
                    const regionInput = document.getElementById('region');
                    regionInput.value = selectedRegion;
                    this.style.display = 'none';
                    
                    // Trigger validation for region field
                    const isValid = selectedRegion.trim().length >= 2;
                    regionInput.classList.toggle('is-invalid', !isValid);
                    regionInput.classList.toggle('is-valid', isValid && selectedRegion.length > 0);
                    
                    // Populate districts
                    populateDistricts(selectedRegion);
                }
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('#region')) {
                    document.getElementById('regionDropdown').style.display = 'none';
                }
            });

            function populateDistricts(region) {
                const districtInput = document.getElementById('district');
                const districtDropdown = document.getElementById('districtDropdown');
                const wardInput = document.getElementById('ward');
                const wardDropdown = document.getElementById('wardDropdown');
                
                // Clear existing values
                districtInput.value = '';
                wardInput.value = '';
                
                // Enable district input
                districtInput.disabled = false;
                wardInput.disabled = true;
                
                // Populate districts dropdown
                if (tanzanianRegions[region]) {
                    districtDropdown.innerHTML = tanzanianRegions[region].districts.map(district => 
                        `<a class="dropdown-item" href="#" data-district="${district}">${district}</a>`
                    ).join('');
                }
            }

            // District search functionality
            document.getElementById('district').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const dropdown = document.getElementById('districtDropdown');
                const region = document.getElementById('region').value;
                
                if (tanzanianRegions[region]) {
                    const districts = tanzanianRegions[region].districts;
                    const filteredDistricts = districts.filter(district => 
                        district.toLowerCase().includes(searchTerm)
                    );
                    
                    dropdown.innerHTML = filteredDistricts.map(district => 
                        `<a class="dropdown-item" href="#" data-district="${district}">${district}</a>`
                    ).join('');
                }
                dropdown.style.display = 'block';
            });

            // Show district dropdown when focused
            document.getElementById('district').addEventListener('focus', function() {
                if (!this.disabled) {
                    document.getElementById('districtDropdown').style.display = 'block';
                }
            });

            // Handle district selection
            document.getElementById('districtDropdown').addEventListener('click', function(e) {
                if (e.target.classList.contains('dropdown-item')) {
                    e.preventDefault();
                    const selectedDistrict = e.target.getAttribute('data-district');
                    const districtInput = document.getElementById('district');
                    districtInput.value = selectedDistrict;
                    this.style.display = 'none';
                    
                    // Trigger validation for district field
                    const isValid = selectedDistrict.trim().length >= 2;
                    districtInput.classList.toggle('is-invalid', !isValid);
                    districtInput.classList.toggle('is-valid', isValid && selectedDistrict.length > 0);
                    
                    // Populate wards
                    populateWards(selectedDistrict);
                }
            });

            function populateWards(district) {
                const wardInput = document.getElementById('ward');
                const wardDropdown = document.getElementById('wardDropdown');
                
                // Clear existing value
                wardInput.value = '';
                
                // Enable ward input
                wardInput.disabled = false;
                
                // Populate wards dropdown
                if (wardsData[district]) {
                    wardDropdown.innerHTML = wardsData[district].map(ward => 
                        `<a class="dropdown-item" href="#" data-ward="${ward}">${ward}</a>`
                    ).join('');
                } else {
                    // If no specific wards data, add some sample wards
                    const sampleWards = ['Ward 1', 'Ward 2', 'Ward 3', 'Ward 4'];
                    wardDropdown.innerHTML = sampleWards.map(ward => 
                        `<a class="dropdown-item" href="#" data-ward="${ward}">${ward}</a>`
                    ).join('');
                }
            }

            // Ward search functionality
            document.getElementById('ward').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const dropdown = document.getElementById('wardDropdown');
                const district = document.getElementById('district').value;
                
                let wards = [];
                if (wardsData[district]) {
                    wards = wardsData[district];
                } else {
                    wards = ['Ward 1', 'Ward 2', 'Ward 3', 'Ward 4'];
                }
                
                const filteredWards = wards.filter(ward => 
                    ward.toLowerCase().includes(searchTerm)
                );
                
                dropdown.innerHTML = filteredWards.map(ward => 
                    `<a class="dropdown-item" href="#" data-ward="${ward}">${ward}</a>`
                ).join('');
                dropdown.style.display = 'block';
            });

            // Show ward dropdown when focused
            document.getElementById('ward').addEventListener('focus', function() {
                if (!this.disabled) {
                    document.getElementById('wardDropdown').style.display = 'block';
                }
            });

            // Handle ward selection
            document.getElementById('wardDropdown').addEventListener('click', function(e) {
                if (e.target.classList.contains('dropdown-item')) {
                    e.preventDefault();
                    const selectedWard = e.target.getAttribute('data-ward');
                    const wardInput = document.getElementById('ward');
                    wardInput.value = selectedWard;
                    this.style.display = 'none';
                    
                    // Trigger validation for ward field
                    const isValid = selectedWard.trim().length >= 2;
                    wardInput.classList.toggle('is-invalid', !isValid);
                    wardInput.classList.toggle('is-valid', isValid && selectedWard.length > 0);
                }
            });

            // Handle family relationship visibility
            document.querySelectorAll('input[name="living_with_family"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    const familyDiv = document.getElementById('familyRelationshipDiv');
                    const familySelect = document.getElementById('familyRelationship');
                    
                    if (this.value === 'yes') {
                        familyDiv.style.display = 'block';
                        familySelect.required = true;
                    } else {
                        familyDiv.style.display = 'none';
                        familySelect.required = false;
                        familySelect.value = '';
                    }
                });
            });

            // Multi-step form functionality
            let currentStep = 1;
            const totalSteps = 3;

            function showStep(step) {
                // Hide all steps
                document.querySelectorAll('.form-step').forEach(stepEl => {
                    stepEl.style.display = 'none';
                });
                
                // Show current step
                document.getElementById(`step${step}`).style.display = 'block';
                
                // Update progress indicators
                document.querySelectorAll('.progress-step').forEach((stepEl, index) => {
                    if (index + 1 <= step) {
                        stepEl.classList.add('active');
                    } else {
                        stepEl.classList.remove('active');
                    }
                });
                
                // Update navigation buttons
                const prevBtn = document.getElementById('prevStep');
                const nextBtn = document.getElementById('nextStep');
                const submitBtn = document.getElementById('submitBtn');
                
                prevBtn.style.display = step > 1 ? 'block' : 'none';
                
                if (step < totalSteps) {
                    nextBtn.style.display = 'block';
                    submitBtn.style.display = 'none';
                } else {
                    nextBtn.style.display = 'none';
                    submitBtn.style.display = 'block';
                }
            }

            // Next step button
            document.getElementById('nextStep').addEventListener('click', function() {
                if (validateCurrentStep()) {
                    currentStep++;
                    showStep(currentStep);
                }
            });

            // Previous step button
            document.getElementById('prevStep').addEventListener('click', function() {
                currentStep--;
                showStep(currentStep);
            });

            // Validate current step
            function validateCurrentStep() {
                const currentStepEl = document.getElementById(`step${currentStep}`);
                const requiredFields = currentStepEl.querySelectorAll('[required]');
                
                for (let field of requiredFields) {
                    if (!field.value.trim()) {
                        field.focus();
                        field.style.borderColor = '#dc3545';
                        setTimeout(() => {
                            field.style.borderColor = '#e9ecef';
                        }, 3000);
                        return false;
                    }
                }
                return true;
            }

            // Handle form submission
            document.getElementById('addMemberForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (validateCurrentStep()) {
                    // Collect form data
                    const formData = new FormData(this);
                    
                    // Debug: Log form data
                    console.log('Form data being sent:');
                    for (let [key, value] of formData.entries()) {
                        console.log(key + ': ' + value);
                    }
                    
                    // Show loading state
                    const submitBtn = document.getElementById('submitBtn');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding Member...';
                    submitBtn.disabled = true;
                    
                    // Submit to backend
                    fetch('{{ route("members.store") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update total members count in the card
                            if (data.totalMembers !== undefined) {
                                const totalMembersElement = document.querySelector('.card.bg-primary .h4.mb-0');
                                if (totalMembersElement) {
                                    // Add animation effect
                                    totalMembersElement.style.transform = 'scale(1.1)';
                                    totalMembersElement.style.transition = 'transform 0.3s ease';
                                    
                                    // Update the count
                                    totalMembersElement.textContent = data.totalMembers.toLocaleString();
                                    
                                    // Reset animation
                                    setTimeout(() => {
                                        totalMembersElement.style.transform = 'scale(1)';
                                    }, 300);
                                }
                            }
                            
                            // Show SweetAlert success popup
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: data.message,
                                confirmButtonText: 'OK',
                                confirmButtonColor: '#28a745',
                                timer: 3000,
                                timerProgressBar: true,
                                showConfirmButton: true
                            }).then(() => {
                                // Reset form and close modal
                                this.reset();
                                document.getElementById('familyRelationshipDiv').style.display = 'none';
                                document.getElementById('otherTribeDiv').style.display = 'none';
                                document.getElementById('district').disabled = true;
                                document.getElementById('ward').disabled = true;
                                currentStep = 1;
                                showStep(1);
                                
                                // Close modal
                                const modal = bootstrap.Modal.getInstance(document.getElementById('addMemberModal'));
                                modal.hide();
                            });
                        } else {
                            // Show detailed validation errors
                            let errorMessage = data.message;
                            if (data.errors) {
                                const errorList = Object.values(data.errors).flat().join('<br>');
                                errorMessage = `<div style="text-align: left;">${errorList}</div>`;
                            }
                            
                            Swal.fire({
                                icon: 'error',
                                title: 'Validation Error!',
                                html: errorMessage,
                                confirmButtonText: 'Try Again',
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Show SweetAlert error popup
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An error occurred while submitting the form. Please try again.',
                            confirmButtonText: 'OK',
                            confirmButtonColor: '#dc3545'
                        });
                    })
                    .finally(() => {
                        // Reset button
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                }
            });

            // Real-time validation functions
            function validateEmail(email) {
                const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return re.test(email);
            }

            function validatePhone(phone) {
                const re = /^\+255[0-9]{9}$/;
                return re.test(phone) && phone.length === 13; // +255 + 9 digits = 13 total
            }

            function validateName(name) {
                return name.trim().length >= 2;
            }

            function validateNIDA(nida) {
                if (!nida) return true; // Optional field
                const re = /^[0-9]{20}$/;
                return re.test(nida);
            }

            function validateDateOfBirth(dateString) {
                if (!dateString) return false;
                const selectedDate = new Date(dateString);
                const today = new Date();
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                
                // Date should be before yesterday (not today or future)
                return selectedDate < yesterday;
            }

            // Real-time validation for form fields
            document.getElementById('fullName').addEventListener('input', function() {
                const isValid = validateName(this.value);
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            document.getElementById('email').addEventListener('input', function() {
                const isValid = validateEmail(this.value);
                this.classList.toggle('is-invalid', this.value.length > 0 && !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            // Gender validation
            document.getElementById('gender').addEventListener('change', function() {
                const isValid = this.value !== '';
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid);
            });

            // Phone number input handling
            document.getElementById('phoneNumber').addEventListener('input', function() {
                // Ensure +255 prefix is always present
                if (!this.value.startsWith('+255')) {
                    this.value = '+255' + this.value.replace(/^\+255/, '');
                }
                
                const isValid = validatePhone(this.value);
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 4);
            });

            // Prevent deletion of +255 prefix
            document.getElementById('phoneNumber').addEventListener('keydown', function(e) {
                if (this.selectionStart <= 4 && (e.key === 'Backspace' || e.key === 'Delete')) {
                    e.preventDefault();
                }
            });

            // Handle paste events
            document.getElementById('phoneNumber').addEventListener('paste', function(e) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                const cleanText = pastedText.replace(/\D/g, ''); // Remove non-digits
                this.value = '+255' + cleanText;
                
                const isValid = validatePhone(this.value);
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 4);
            });

            document.getElementById('nidaNumber').addEventListener('input', function() {
                const isValid = validateNIDA(this.value);
                this.classList.toggle('is-invalid', this.value.length > 0 && !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            document.getElementById('dateOfBirth').addEventListener('change', function() {
                const isValid = validateDateOfBirth(this.value);
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            // Tribe field validation
            document.getElementById('tribe').addEventListener('input', function() {
                const isValid = this.value.trim().length >= 2;
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            // Other tribe input validation
            document.getElementById('otherTribe').addEventListener('input', function() {
                const isValid = this.value.trim().length >= 2;
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            // Region field validation
            document.getElementById('region').addEventListener('input', function() {
                const isValid = this.value.trim().length >= 2;
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            // District field validation
            document.getElementById('district').addEventListener('input', function() {
                const isValid = this.value.trim().length >= 2;
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            // Ward field validation
            document.getElementById('ward').addEventListener('input', function() {
                const isValid = this.value.trim().length >= 2;
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            // Street field validation
            document.getElementById('street').addEventListener('input', function() {
                const isValid = this.value.trim().length >= 2;
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });

            // Address field validation
            document.getElementById('address').addEventListener('input', function() {
                const isValid = this.value.trim().length >= 5;
                this.classList.toggle('is-invalid', !isValid);
                this.classList.toggle('is-valid', isValid && this.value.length > 0);
            });


            // Enhanced validation for current step
            function validateCurrentStep() {
                const currentStepEl = document.getElementById(`step${currentStep}`);
                const requiredFields = currentStepEl.querySelectorAll('[required]');
                let isValid = true;

                for (let field of requiredFields) {
                    let fieldValid = true;
                    
                    if (field.type === 'email' && field.value) {
                        fieldValid = validateEmail(field.value);
                    } else if (field.id === 'phoneNumber') {
                        fieldValid = validatePhone(field.value);
                    } else if (field.id === 'fullName') {
                        fieldValid = validateName(field.value);
                    } else if (field.id === 'nidaNumber' && field.value) {
                        fieldValid = validateNIDA(field.value);
                    } else if (field.id === 'dateOfBirth') {
                        fieldValid = validateDateOfBirth(field.value);
                    } else if (field.id === 'gender') {
                        fieldValid = field.value !== '';
                    } else if (field.id === 'tribe') {
                        fieldValid = field.value.trim().length >= 2;
                    } else if (field.id === 'region') {
                        fieldValid = field.value.trim().length >= 2;
                    } else if (field.id === 'district') {
                        fieldValid = field.value.trim().length >= 2;
                    } else if (field.id === 'ward') {
                        fieldValid = field.value.trim().length >= 2;
                    } else if (field.id === 'street') {
                        fieldValid = field.value.trim().length >= 2;
                    } else if (field.id === 'address') {
                        fieldValid = field.value.trim().length >= 5;
                    } else if (field.id === 'otherTribe') {
                        fieldValid = field.value.trim().length >= 2;
                    } else if (field.value.trim() === '') {
                        fieldValid = false;
                    }

                    field.classList.toggle('is-invalid', !fieldValid);
                    field.classList.toggle('is-valid', fieldValid && field.value.length > 0);
                    
                    if (!fieldValid) {
                        isValid = false;
                        field.focus();
                    }
                }
                return isValid;
            }

            // Generate and display member ID when modal opens
            document.getElementById('addMemberModal').addEventListener('show.bs.modal', function() {
                generateMemberId();
                setDateOfBirthMaxDate();
            });

            // Set max date for date of birth (yesterday)
            function setDateOfBirthMaxDate() {
                const dateInput = document.getElementById('dateOfBirth');
                const today = new Date();
                const yesterday = new Date(today);
                yesterday.setDate(yesterday.getDate() - 1);
                
                // Format date as YYYY-MM-DD
                const maxDate = yesterday.toISOString().split('T')[0];
                dateInput.setAttribute('max', maxDate);
            }

            // Function to generate member ID
            function generateMemberId() {
                const year = new Date().getFullYear();
                const characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
                let randomPart = '';
                
                // Generate 5 random alphanumeric characters
                for (let i = 0; i < 5; i++) {
                    randomPart += characters.charAt(Math.floor(Math.random() * characters.length));
                }
                
                const memberId = year + randomPart + '-WL';
                document.getElementById('generatedMemberId').textContent = memberId;
            }

            // Initialize form
            showStep(1);
            initializeRegionDropdown();
            initializeTribeDropdown();
        </script>
    </body>
</html>
