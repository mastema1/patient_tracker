@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Seizure Diary</h1>
  <a class="btn btn-primary" href="{{ route('patient.seizures.new') }}"><i class="fa-solid fa-plus me-1"></i>Add entry</a>
</div>
<div class="card elevated">
  <div class="card-body">
    @forelse($logs as $log)
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
      <div class="text-muted">No entries yet.</div>
    @endforelse
    <div class="mt-2">{{ $logs->links() }}</div>
  </div>
</div>
@endsection
