@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Patient Dashboard</h1>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">Seizures (last 30 days)</div>
      <div class="display-6">{{ $logsLast30 }}</div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">Uploaded files</div>
      <div class="display-6">{{ $filesCount }}</div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <a class="btn btn-primary" href="{{ route('patient.seizures.new') }}">Log a seizure</a>
      <a class="btn btn-outline-secondary" href="{{ route('patient.files') }}">Manage files</a>
    </div></div>
  </div>
</div>

<div class="card mt-4">
  <div class="card-body">
    <h2 class="h5">Recent Seizure Logs</h2>
    <table class="table table-sm">
      <thead><tr><th>Date/Time</th><th>Duration (s)</th><th>Notes</th></tr></thead>
      <tbody>
        @forelse($recentLogs as $log)
          <tr>
            <td>{{ $log->timestamp->format('Y-m-d H:i') }}</td>
            <td>{{ $log->duration }}</td>
            <td>{{ $log->notes }}</td>
          </tr>
        @empty
          <tr><td colspan="3" class="text-muted">No logs yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
