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
          @elseif(auth()->user()->role === 'doctor')
            <li class="nav-item"><a class="nav-link" href="{{ route('doctor.dashboard') }}">Dashboard</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('doctor.patients') }}">Patients</a></li>
          @endif
        @endauth
      </ul>
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
