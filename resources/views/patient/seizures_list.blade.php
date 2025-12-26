@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Seizure Diary</h1>
  <a class="btn btn-primary" href="{{ route('patient.seizures.new') }}">Add entry</a>
</div>
<div class="card">
  <div class="card-body">
    <table class="table table-striped">
      <thead>
        <tr><th>Date</th><th>Time</th><th>Duration (s)</th><th>Notes</th></tr>
      </thead>
      <tbody>
        @forelse($logs as $log)
          <tr>
            <td>{{ $log->timestamp->format('Y-m-d') }}</td>
            <td>{{ $log->timestamp->format('H:i') }}</td>
            <td>{{ $log->duration }}</td>
            <td>{{ $log->notes }}</td>
          </tr>
        @empty
          <tr><td colspan="4" class="text-muted">No entries yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $logs->links() }}
  </div>
</div>
@endsection
