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
