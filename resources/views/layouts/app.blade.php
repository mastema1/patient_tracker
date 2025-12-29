<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NeuroMon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link href="/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light sticky-top">
  <div class="container">
    <a class="navbar-brand" href="/"><i class="fa-solid fa-brain me-2"></i>NeuroMon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        @auth
          @if(auth()->user()->role === 'patient')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" href="{{ route('patient.dashboard') }}"><i class="fa-solid fa-gauge-high me-1"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('patient.seizures*') ? 'active' : '' }}" href="{{ route('patient.seizures') }}"><i class="fa-solid fa-bolt me-1"></i>Seizures</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('patient.files') ? 'active' : '' }}" href="{{ route('patient.files') }}"><i class="fa-solid fa-folder-open me-1"></i>Files</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('patient.history') ? 'active' : '' }}" href="{{ route('patient.history') }}"><i class="fa-solid fa-clock-rotate-left me-1"></i>History</a></li>
            
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('patient.support') ? 'active' : '' }}" href="{{ route('patient.support') }}"><i class="fa-solid fa-life-ring me-1"></i>Support</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('patient.conversations*') ? 'active' : '' }}" href="{{ route('patient.conversations') }}"><i class="fa-solid fa-comments me-1"></i>Conversations</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('patient.appointments') ? 'active' : '' }}" href="{{ route('patient.appointments') }}"><i class="fa-solid fa-calendar-check me-1"></i>Appointments</a></li>
          @elseif(auth()->user()->role === 'doctor')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}"><i class="fa-solid fa-gauge me-1"></i>Dashboard</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('doctor.patients') ? 'active' : '' }}" href="{{ route('doctor.patients') }}"><i class="fa-solid fa-users me-1"></i>Patients</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('doctor.feed') ? 'active' : '' }}" href="{{ route('doctor.feed') }}"><i class="fa-solid fa-notes-medical me-1"></i>Medical Feed</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('conversations.*') ? 'active' : '' }}" href="{{ route('conversations.index') }}"><i class="fa-solid fa-comments me-1"></i>Conversations</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('doctor.appointments') ? 'active' : '' }}" href="{{ route('doctor.appointments') }}"><i class="fa-solid fa-calendar-check me-1"></i>Appointments</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Settings</a>
              <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                <li><a class="dropdown-item" href="{{ route('doctor.settings.profile') }}"><i class="fa-solid fa-user-gear me-2"></i>Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('doctor.settings.facilities') }}"><i class="fa-solid fa-hospital-user me-2"></i>Facilities</a></li>
              </ul>
            </li>
          @elseif(auth()->user()->role === 'admin')
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i class="fa-solid fa-user-check me-1"></i>Verification</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.support') ? 'active' : '' }}" href="{{ route('admin.support') }}"><i class="fa-solid fa-inbox me-1"></i>Support Inbox</a></li>
            <li class="nav-item"><a class="nav-link {{ request()->routeIs('admin.feedback') ? 'active' : '' }}" href="{{ route('admin.feedback') }}"><i class="fa-solid fa-comments me-1"></i>Feedback</a></li>
          @endif
        @endauth
      </ul>
      @auth
      <form id="global-search-form" class="me-3 search-wrap" role="search" method="GET" action="{{ route('search') }}">
        <i class="fa-solid fa-magnifying-glass search-icon"></i>
        <input id="global-search" class="form-control search-input" type="search" name="q" value="{{ request('q') }}" placeholder="Search doctors or facilities" aria-label="Search" autocomplete="off">
        <div id="search-suggest" class="search-suggest"></div>
      </form>
      @endauth
      <ul class="navbar-nav ms-auto">
        <li class="nav-item me-3 align-self-center">
          <button id="theme-toggle" class="theme-toggle" aria-label="Toggle dark mode" title="Toggle dark mode">
            <span class="toggle-track">
              <span class="toggle-thumb"></span>
              <i class="fa-solid fa-sun icon-sun"></i>
              <i class="fa-solid fa-moon icon-moon"></i>
            </span>
          </button>
        </li>
        @auth
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fa-solid fa-circle-user me-2 fs-5"></i>
              <span>{{ auth()->user()->name }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li class="px-3 py-2">
                <div class="d-flex align-items-center">
                  <i class="fa-solid fa-user-circle me-2 fs-3"></i>
                  <div>
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <div class="text-muted small">{{ auth()->user()->email }}</div>
                  </div>
                </div>
              </li>
              <li><hr class="dropdown-divider"></li>
              @if(auth()->user()->role === 'patient')
                <li><a class="dropdown-item" href="{{ route('patient.profile') }}"><i class="fa-solid fa-user-gear me-2"></i>Manage Account</a></li>
              @elseif(auth()->user()->role === 'doctor')
                <li><a class="dropdown-item" href="{{ route('doctor.settings.profile') }}"><i class="fa-solid fa-user-gear me-2"></i>Manage Account</a></li>
              @endif
              <li>
                <form method="POST" action="{{ route('logout') }}" class="px-3 py-1">
                  @csrf
                  <button class="btn btn-outline-secondary w-100"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</button>
                </form>
              </li>
            </ul>
          </li>
        @else
          <li class="nav-item"><a class="btn btn-primary" href="{{ route('login') }}"><i class="fa-solid fa-right-to-bracket me-1"></i>Login</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>
<div class="container">
  @if(session('status'))
    <div class="alert alert-success">{{ session('status') }}</div>
  @endif
  @if($errors->any())
    <div class="alert alert-danger">
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
  @yield('content')
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/ui.js" defer></script>
@yield('scripts')
</body>
</html>
