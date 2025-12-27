@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Patient Dashboard</h1>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card elevated"><div class="card-body d-flex align-items-center justify-content-between">
      <div>
        <div class="text-muted">Seizures (last 30 days)</div>
        <div class="display-6">{{ $logsLast30 }}</div>
      </div>
      <i class="fa-solid fa-bolt fa-2x text-danger"></i>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card elevated"><div class="card-body d-flex align-items-center justify-content-between">
      <div>
        <div class="text-muted">Uploaded files</div>
        <div class="display-6">{{ $filesCount }}</div>
      </div>
      <i class="fa-solid fa-folder-open fa-2x text-primary"></i>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card elevated"><div class="card-body">
      <a class="btn btn-primary me-2" href="{{ route('patient.seizures.new') }}"><i class="fa-solid fa-plus me-1"></i>Log a seizure</a>
      <a class="btn btn-outline-secondary" href="{{ route('patient.files') }}"><i class="fa-solid fa-folder-open me-1"></i>Manage files</a>
    </div></div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-body">
    <h2 class="h5">Recent Seizure Logs</h2>
    @forelse($recentLogs as $log)
      <div class="card mb-2">
        <div class="card-body d-flex justify-content-between align-items-center">
          <div>
            <div class="fw-semibold"><i class="fa-solid fa-bolt me-1 text-danger"></i>{{ $log->timestamp->format('Y-m-d H:i') }}</div>
            <div class="text-muted small">{{ $log->notes }}</div>
          </div>
          <span class="badge bg-danger-subtle text-danger">{{ $log->duration }}s</span>
        </div>
      </div>
    @empty
      <div class="text-muted">No logs yet.</div>
    @endforelse
  </div>
</div>
@endsection
