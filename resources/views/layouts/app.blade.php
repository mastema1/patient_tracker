<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>NeuroMon</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8fafc; }
    .navbar { background-color: #e2ecf7; }
    .card { border-color: #e5e7eb; }
    .brand { color: #0f172a; font-weight: 600; }
  </style>
</head>
<body>
<nav class="navbar navbar-expand-lg mb-4">
  <div class="container">
    <a class="navbar-brand brand" href="/">NeuroMon</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        @auth
          @if(auth()->user()->role === 'patient')
            <li class="nav-item"><a class="nav-link" href="{{ route('patient.dashboard') }}">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('patient.seizures') }}">Seizures</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('patient.files') }}">Files</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('patient.history') }}">History</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('patient.profile') }}">Profile</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('patient.support') }}">Support</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('patient.conversations') }}">Conversations</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('patient.appointments') }}">Appointments</a></li>
          @elseif(auth()->user()->role === 'doctor')
            <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}">Patients</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('doctor.feed') }}">Medical Feed</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('conversations.index') }}">Conversations</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('doctor.appointments') }}">Appointments</a></li>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">Settings</a>
              <ul class="dropdown-menu" aria-labelledby="settingsDropdown">
                <li><a class="dropdown-item" href="{{ route('doctor.settings.profile') }}">Profile</a></li>
                <li><a class="dropdown-item" href="{{ route('doctor.settings.facilities') }}">Facilities</a></li>
              </ul>
            </li>
          @elseif(auth()->user()->role === 'admin')
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.support') }}">Support Inbox</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('admin.feedback') }}">Feedback</a></li>
          @endif
        @endauth
      </ul>
      @auth
      <form class="d-flex me-3" role="search" method="GET" action="{{ route('search') }}">
        <input class="form-control me-2" type="search" name="q" value="{{ request('q') }}" placeholder="Search doctors or facilities" aria-label="Search">
        <button class="btn btn-outline-primary" type="submit">Search</button>
      </form>
      @endauth
      <ul class="navbar-nav ms-auto">
        @auth
          <li class="nav-item me-2 align-self-center">{{ auth()->user()->name }}</li>
          <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
              @csrf
              <button class="btn btn-outline-secondary">Logout</button>
            </form>
          </li>
        @else
          <li class="nav-item"><a class="btn btn-primary" href="{{ route('login') }}">Login</a></li>
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
@yield('scripts')
</body>
</html>
