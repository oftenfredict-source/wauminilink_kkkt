<nav class="sb-topnav navbar navbar-expand navbar-dark">
  <a class="navbar-brand ps-3 d-flex align-items-center logo-white-section" href="{{ route('dashboard') }}">
    <img src="{{ asset('assets/images/waumini_link_logo.png') }}" alt="Waumini Link Logo" class="logo"
      style="height: 45px; max-width: 200px; object-fit: contain;">
  </a>
  <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#"><i
      class="fas fa-bars"></i></button>
  <div class="navbar-text text-white me-auto ms-3" style="font-size: 1.1rem;">
    <strong>Waumini Link</strong>
  </div>
  <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
    @include('partials.language-switcher')
    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown"
        aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
      <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
        <li><a class="dropdown-item" href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i>Settings</a>
        </li>
        <li>
          <hr class="dropdown-divider" />
        </li>
        <li>
          <a class="dropdown-item" href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
          </a>
          <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
        </li>
      </ul>
    </li>
  </ul>
</nav>
<div id="layoutSidenav">
  <div id="layoutSidenav_nav">
    <nav class="sb-sidenav accordion" id="sidenavAccordion">
      <div class="sb-sidenav-menu">
        <div class="nav">
          <div class="sb-sidenav-menu-heading">Main</div>
          <a class="nav-link" href="{{ route('dashboard') }}">
            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
            Dashboard
          </a>
          <div class="sb-sidenav-menu-heading">Management</div>
          <a class="nav-link" href="{{ route('members.view') }}">
            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
            Members
          </a>
          <a class="nav-link" href="{{ route('members.add') }}">
            <div class="sb-nav-link-icon"><i class="fas fa-user-plus"></i></div>
            Add Member
          </a>
          <a class="nav-link" href="{{ route('leaders.index') }}">
            <div class="sb-nav-link-icon"><i class="fas fa-user-tie"></i></div>
            Leadership
          </a>
          <a class="nav-link" href="{{ route('campuses.index') }}">
            <div class="sb-nav-link-icon"><i class="fas fa-building"></i></div>
            Campuses
          </a>
          <div class="sb-sidenav-menu-heading">Settings</div>
          <a class="nav-link" href="{{ route('settings.index') }}">
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
</div>