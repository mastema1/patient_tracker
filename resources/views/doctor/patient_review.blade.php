@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Clinical Review — {{ $patient->name }}</h1>
<div class="card mb-4">
  <div class="card-body">
    <canvas id="seizureChart" height="120"></canvas>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <h2 class="h5">Medical Files</h2>
    <table class="table table-striped">
      <thead><tr><th>Filename</th><th>Uploaded</th><th></th></tr></thead>
      <tbody>
        @forelse($files as $f)
          <tr>
            <td>{{ basename($f->file_path) }}</td>
            <td>{{ $f->upload_date->format('Y-m-d H:i') }}</td>
            <td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="{{ route('files.download', $f->id) }}">Download</a></td>
          </tr>
        @empty
          <tr><td colspan="3" class="text-muted">No files.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="row g-3 mt-3">
  <div class="col-md-7">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 mb-3">Clinical Notes</h2>
        <form method="POST" action="{{ route('doctor.patient.notes.store', $patient->id) }}" class="mb-3">
          @csrf
          <div class="mb-2">
            <textarea name="content" class="form-control" rows="3" placeholder="Add a clinical note..." required></textarea>
          </div>
          <button class="btn btn-primary btn-sm">Add Note</button>
        </form>
        @foreach($notes as $note)
          <div class="border rounded p-2 mb-2">
            <div class="small text-muted">{{ $note->created_at->format('Y-m-d H:i') }}</div>
            <div>{{ $note->content }}</div>
          </div>
        @endforeach
        {{ $notes->links() }}
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 mb-3">Follow-up Appointment Request</h2>
        <form method="POST" action="{{ route('doctor.appointments.request', $patient->id) }}">
          @csrf
          <div class="mb-2">
            <label class="form-label">Suggested date/time</label>
            <input type="datetime-local" class="form-control" name="scheduled_at">
          </div>
          <div class="mb-2">
            <label class="form-label">Reason</label>
            <textarea class="form-control" name="reason" rows="2" placeholder="Optional"></textarea>
          </div>
          <button class="btn btn-outline-primary btn-sm">Send Request</button>
        </form>
        <hr>
        <h2 class="h6 mb-2">Message Patient</h2>
        <form method="POST" action="{{ route('conversations.start') }}">
          @csrf
          <input type="hidden" name="patient_id" value="{{ $patient->id }}">
          <button class="btn btn-secondary btn-sm">Open Conversation</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  const labels = @json($labels);
  const data = @json($series);
  const ctx = document.getElementById('seizureChart').getContext('2d');
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Seizures per day (last 30 days)',
        data: data,
        borderColor: '#2563eb',
        backgroundColor: 'rgba(37,99,235,.1)',
        tension: .2,
        fill: true,
      }]
    },
    options: {
      scales: {
        y: { beginAtZero: true, precision: 0 }
      }
    }
  });
</script>
@endsection
