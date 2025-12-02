@extends('layouts.index')

@section('content')
<style>
    /* Collapsible section styles */
    .role-section-header {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.2s ease;
    }

    .role-section-header:hover {
        background-color: #f8f9fa !important;
    }

    .category-section-header {
        cursor: pointer;
        user-select: none;
        transition: background-color 0.2s ease;
        padding: 0.5rem 0.75rem !important;
        margin: 0 -0.75rem 0.75rem -0.75rem;
        border-radius: 0.25rem;
    }

    .category-section-header:hover {
        background-color: #f8f9fa !important;
    }

    .toggle-icon {
        transition: transform 0.3s ease;
        font-size: 0.875rem;
    }

    .toggle-icon.rotated {
        transform: rotate(180deg);
    }

    /* Ensure navbar container is visible */
    nav.sb-topnav,
    .sb-topnav {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
        width: 100% !important;
        overflow: visible !important;
    }

    /* Ensure navbar elements are visible - High specificity */
    nav.sb-topnav .navbar-text,
    nav.sb-topnav .navbar-nav,
    nav.sb-topnav #notificationDropdown,
    nav.sb-topnav #navbarDropdown,
    .sb-topnav .navbar-text,
    .sb-topnav .navbar-nav,
    .sb-topnav #notificationDropdown,
    .sb-topnav #navbarDropdown {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }

        /* Ensure navbar flex container shows all children */
        nav.sb-topnav > *,
        .sb-topnav > * {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }

        /* Ensure navbar has proper flex layout */
        nav.sb-topnav,
        .sb-topnav {
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        /* Ensure welcome message container is visible */
        nav.sb-topnav .navbar-text,
        .sb-topnav .navbar-text {
            order: 1 !important;
            flex: 1 1 auto !important;
            min-width: 0 !important;
            max-width: none !important;
        }

        /* Ensure navbar nav is visible and positioned correctly */
        nav.sb-topnav .navbar-nav,
        .sb-topnav .navbar-nav {
            order: 2 !important;
            flex: 0 0 auto !important;
        }

    /* Mobile Responsive Styles */
    @media (max-width: 768px) {
        .container-fluid {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
            padding-top: 0.25rem !important;
        }

        /* Ensure navbar elements are visible on mobile - High specificity */
        nav.sb-topnav .navbar-text,
        .sb-topnav .navbar-text {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            margin-left: 0.5rem !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            color: #ffffff !important;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.8) !important;
        }

        nav.sb-topnav .navbar-nav,
        .sb-topnav .navbar-nav {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            flex-shrink: 0 !important;
            margin-left: auto !important;
            align-items: center !important;
        }

        nav.sb-topnav #notificationDropdown,
        nav.sb-topnav #navbarDropdown,
        .sb-topnav #notificationDropdown,
        .sb-topnav #navbarDropdown {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            flex-shrink: 0 !important;
            align-items: center !important;
        }

        nav.sb-topnav .navbar-nav .nav-item,
        .sb-topnav .navbar-nav .nav-item {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            flex-shrink: 0 !important;
            align-items: center !important;
        }

        /* Ensure notification icon is visible */
        nav.sb-topnav #notificationDropdown a,
        .sb-topnav #notificationDropdown a {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.5rem !important;
        }

        /* Ensure profile icon is visible */
        nav.sb-topnav #navbarDropdown,
        .sb-topnav #navbarDropdown {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0.5rem !important;
        }

        nav.sb-topnav #navbarDropdown i,
        .sb-topnav #navbarDropdown i {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            color: #ffffff !important;
        }

        .dashboard-header {
            margin-bottom: 12px !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
        }

        .dashboard-header .card-body {
            padding: 12px 14px !important;
        }

        .dashboard-header .rounded-circle {
            width: 38px !important;
            height: 38px !important;
            min-width: 38px !important;
            flex-shrink: 0 !important;
            background: rgba(255,255,255,0.2) !important;
            border: 2px solid rgba(255,255,255,0.3) !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.95rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 12px !important;
            flex: 1 !important;
            min-width: 0 !important;
        }

        .dashboard-header .lh-sm {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        .dashboard-header h5 {
            font-size: 1rem !important;
            line-height: 1.3 !important;
            margin-bottom: 2px !important;
            font-weight: 600 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header small {
            font-size: 0.75rem !important;
            line-height: 1.2 !important;
            display: block !important;
            opacity: 0.9 !important;
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            padding: 8px 14px !important;
            font-size: 0.85rem !important;
            border-radius: 8px !important;
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            font-weight: 500 !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
            transition: all 0.2s ease !important;
        }

        .dashboard-header .btn:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.15) !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            align-items: center !important;
            flex-wrap: nowrap !important;
        }

        .dashboard-header .d-flex.justify-content-between > div:first-child {
            flex: 1 !important;
            min-width: 0 !important;
            overflow: hidden !important;
        }

        .card-header {
            padding: 0.75rem 1rem !important;
        }

        .card-body {
            padding: 1rem !important;
        }

        /* Role section header on mobile */
        .role-section-header {
            padding: 0.75rem 1rem !important;
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
        }

        .role-section-header h6 {
            font-size: 0.95rem !important;
            margin: 0 !important;
        }

        .role-section-header .badge {
            font-size: 0.75rem !important;
        }

        /* Category section header on mobile */
        .category-section-header {
            background-color: #e9ecef !important;
            margin: 0 -1rem 0.75rem -1rem !important;
            padding: 0.625rem 1rem !important;
        }

        .category-section-header h6 {
            font-size: 0.875rem !important;
            margin: 0 !important;
        }

        /* Permissions grid - stack on mobile */
        .row .col-md-4 {
            width: 100% !important;
            margin-bottom: 0.75rem !important;
        }

        .form-check {
            padding: 0.75rem !important;
            background: #f8f9fa !important;
            border-radius: 0.375rem !important;
            margin-bottom: 0.5rem !important;
        }

        .form-check-label {
            font-size: 0.875rem !important;
        }

        .form-check-label small {
            font-size: 0.75rem !important;
        }

        /* Buttons */
        .btn {
            width: 100% !important;
            margin-bottom: 0.5rem !important;
            font-size: 0.875rem !important;
            padding: 0.5rem 1rem !important;
        }

        /* Hide category headers on desktop, show on mobile */
        .category-section-header {
            display: block !important;
        }
    }

    /* Desktop: Always show sections and improve grid layout */
    @media (min-width: 769px) {
        .role-section-body,
        .category-section-body {
            display: block !important;
        }

        .role-section-header,
        .category-section-header {
            pointer-events: none !important;
            cursor: default !important;
        }

        .role-section-header:hover,
        .category-section-header:hover {
            background-color: transparent !important;
        }

        .toggle-icon {
            display: none !important;
        }

        /* Desktop grid layout improvements */
        .category-section-body.row {
            display: flex !important;
            flex-wrap: wrap !important;
        }

        .category-section-body > div {
            margin-bottom: 0.75rem !important;
        }

        .form-check {
            padding: 1rem !important;
            background: #f8f9fa !important;
            border: 1px solid #dee2e6 !important;
            border-radius: 0.5rem !important;
            transition: all 0.2s ease !important;
            height: 100% !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .form-check:hover {
            background: #e9ecef !important;
            border-color: #667eea !important;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1) !important;
        }

        .form-check-input {
            margin-top: 0.25rem !important;
            cursor: pointer !important;
        }

        .form-check-label {
            cursor: pointer !important;
            flex: 1 !important;
        }

        .form-check-label strong {
            display: block !important;
            margin-bottom: 0.25rem !important;
            color: #212529 !important;
        }

        .form-check-label small {
            display: block !important;
            line-height: 1.4 !important;
            color: #6c757d !important;
        }
    }

    /* Large desktop: More columns */
    @media (min-width: 1200px) {
        .category-section-body {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)) !important;
        }
    }

    @media (max-width: 576px) {
        .container-fluid {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
            padding-top: 0.15rem !important;
        }

        .dashboard-header {
            margin-bottom: 10px !important;
            border-radius: 10px !important;
        }

        .dashboard-header .card-body {
            padding: 10px 12px !important;
        }

        .dashboard-header .rounded-circle {
            width: 36px !important;
            height: 36px !important;
            min-width: 36px !important;
        }

        .dashboard-header .rounded-circle i {
            font-size: 0.9rem !important;
        }

        .dashboard-header .d-flex.align-items-center.gap-3 {
            gap: 10px !important;
        }

        .dashboard-header h5 {
            font-size: 0.95rem !important;
            line-height: 1.25 !important;
            margin-bottom: 1px !important;
        }

        .dashboard-header small {
            font-size: 0.72rem !important;
            line-height: 1.15 !important;
        }

        .dashboard-header .d-flex.justify-content-between {
            flex-wrap: wrap !important;
            gap: 8px !important;
        }

        .dashboard-header .btn {
            margin-top: 0 !important;
            width: auto !important;
            min-width: fit-content !important;
            padding: 7px 12px !important;
            font-size: 0.8rem !important;
            flex: 0 0 auto !important;
        }

        /* Stack on very small screens */
        @media (max-width: 400px) {
            .dashboard-header .d-flex.justify-content-between {
                flex-direction: column !important;
                align-items: stretch !important;
            }

            .dashboard-header .btn {
                width: 100% !important;
                margin-top: 8px !important;
            }
        }

        .btn {
            width: 100% !important;
            margin-bottom: 0.5rem !important;
            font-size: 0.8rem !important;
            padding: 0.45rem 0.75rem !important;
        }

        .card-header {
            padding: 0.625rem 0.75rem !important;
        }

        .card-body {
            padding: 0.75rem !important;
        }

        .role-section-header {
            padding: 0.625rem 0.75rem !important;
        }

        .role-section-header h6 {
            font-size: 0.9rem !important;
        }

        .category-section-header {
            padding: 0.5rem 0.75rem !important;
            margin: 0 -0.75rem 0.5rem -0.75rem !important;
        }

        .category-section-header h6 {
            font-size: 0.8rem !important;
        }

        .form-check {
            padding: 0.625rem !important;
        }

        .form-check-label {
            font-size: 0.8rem !important;
        }

        .form-check-label small {
            font-size: 0.7rem !important;
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
                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-white border-2" style="width:48px; height:48px; background:rgba(255,255,255,.15);">
                                <i class="fas fa-shield-alt text-white"></i>
                            </div>
                            <div class="lh-sm">
                                <h5 class="mb-0 fw-semibold" style="color: white !important;">Roles & Permissions</h5>
                                <small style="color: white !important;">Manage role-based access control</small>
                            </div>
                        </div>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> <span class="d-none d-sm-inline">Back to Dashboard</span><span class="d-sm-none">Back</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($roles as $role)
    <div class="card shadow mb-4">
        <!-- Role Section Header (Mobile Only) -->
        <div class="card-header py-3 d-md-none role-section-header" onclick="toggleRoleSection('{{ $role }}')">
            <h6 class="m-0 font-weight-bold text-primary d-flex justify-content-between align-items-center">
                <span>
                    {{ ucfirst($role) }} Role Permissions
                    <span class="badge badge-info">{{ count($rolePermissions[$role] ?? []) }} permissions</span>
                </span>
                <i class="fas fa-chevron-down toggle-icon" id="roleToggle_{{ $role }}"></i>
            </h6>
        </div>
        <!-- Role Section Header (Desktop Only) -->
        <div class="card-header py-3 d-none d-md-block">
            <h6 class="m-0 font-weight-bold text-primary">
                {{ ucfirst($role) }} Role Permissions
                <span class="badge badge-info">{{ count($rolePermissions[$role] ?? []) }} permissions</span>
            </h6>
        </div>
        <div class="card-body role-section-body" id="roleSection_{{ $role }}">
            <form method="POST" action="{{ route('admin.roles-permissions.update') }}">
                @csrf
                <input type="hidden" name="role" value="{{ $role }}">
                
                @foreach($permissions as $category => $categoryPermissions)
                <div class="mb-4 category-section">
                    <!-- Category Header (Mobile Only) -->
                    <div class="category-section-header d-md-none" onclick="toggleCategorySection('{{ $role }}_{{ $category }}')">
                        <h6 class="font-weight-bold text-uppercase text-muted mb-0 d-flex justify-content-between align-items-center">
                            <span>{{ $category }}</span>
                            <i class="fas fa-chevron-down toggle-icon" id="categoryToggle_{{ $role }}_{{ $category }}"></i>
                        </h6>
                    </div>
                    <!-- Category Header (Desktop Only) -->
                    <h6 class="font-weight-bold text-uppercase text-muted mb-3 d-none d-md-block">{{ $category }}</h6>
                    <div class="row category-section-body g-3" id="categorySection_{{ $role }}_{{ $category }}">
                        @foreach($categoryPermissions as $permission)
                        <div class="col-12 col-md-6 col-lg-4 col-xl-3 mb-2">
                            <div class="form-check h-100">
                                <input 
                                    class="form-check-input" 
                                    type="checkbox" 
                                    name="permissions[]" 
                                    value="{{ $permission->slug }}"
                                    id="perm_{{ $role }}_{{ $permission->id }}"
                                    {{ in_array($permission->slug, $rolePermissions[$role] ?? []) ? 'checked' : '' }}
                                >
                                <label class="form-check-label" for="perm_{{ $role }}_{{ $permission->id }}">
                                    <strong>{{ $permission->name }}</strong>
                                    @if($permission->description)
                                    <br><small class="text-muted d-block mt-1">{{ $permission->description }}</small>
                                    @endif
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endforeach

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> <span class="d-none d-sm-inline">Update {{ ucfirst($role) }} Permissions</span><span class="d-sm-none">Update Permissions</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endforeach
</div>

<script>
// Toggle role section on mobile
function toggleRoleSection(role) {
    const sectionBody = document.getElementById('roleSection_' + role);
    const toggleIcon = document.getElementById('roleToggle_' + role);
    
    if (sectionBody && toggleIcon) {
        if (sectionBody.style.display === 'none') {
            sectionBody.style.display = 'block';
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
            toggleIcon.classList.add('rotated');
        } else {
            sectionBody.style.display = 'none';
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.remove('rotated');
            toggleIcon.classList.add('fa-chevron-down');
        }
    }
}

// Toggle category section on mobile
function toggleCategorySection(sectionId) {
    const sectionBody = document.getElementById('categorySection_' + sectionId);
    const toggleIcon = document.getElementById('categoryToggle_' + sectionId);
    
    if (sectionBody && toggleIcon) {
        if (sectionBody.style.display === 'none') {
            sectionBody.style.display = 'block';
            toggleIcon.classList.remove('fa-chevron-down');
            toggleIcon.classList.add('fa-chevron-up');
            toggleIcon.classList.add('rotated');
        } else {
            sectionBody.style.display = 'none';
            toggleIcon.classList.remove('fa-chevron-up');
            toggleIcon.classList.remove('rotated');
            toggleIcon.classList.add('fa-chevron-down');
        }
    }
}

// Initialize sections on page load
document.addEventListener('DOMContentLoaded', function() {
    function initializeSections() {
        if (window.innerWidth <= 768) {
            // Keep role sections expanded by default on mobile (users can collapse if needed)
            const roleSections = document.querySelectorAll('.role-section-body');
            roleSections.forEach(function(section) {
                section.style.display = 'block';
            });

            // Collapse all category sections by default on mobile (users expand as needed)
            const categorySections = document.querySelectorAll('.category-section-body');
            categorySections.forEach(function(section) {
                if (section.style.display === '') {
                    section.style.display = 'none';
                }
            });
        } else {
            // Show all sections on desktop
            const roleSections = document.querySelectorAll('.role-section-body');
            roleSections.forEach(function(section) {
                section.style.display = 'block';
            });

            const categorySections = document.querySelectorAll('.category-section-body');
            categorySections.forEach(function(section) {
                section.style.display = 'block';
            });
        }
    }
    
    initializeSections();
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            initializeSections();
        }, 250);
    });
});
</script>
@endsection

