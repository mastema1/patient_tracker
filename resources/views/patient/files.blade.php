@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h1 class="h4">Medical Files</h1>
</div>
<div class="card mb-4">
  <div class="card-body">
    <form method="POST" action="{{ route('patient.files.upload') }}" enctype="multipart/form-data">
      @csrf
      <div class="row g-2 align-items-end">
        <div class="col-md-6">
          <label class="form-label">Upload (.edf, .csv, .pdf | max 10MB)</label>
          <input type="file" class="form-control" name="file" accept=".edf,.csv,.pdf" required>
        </div>
        <div class="col-md-3">
          <button class="btn btn-primary">Upload</button>
        </div>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
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
          <tr><td colspan="3" class="text-muted">No files uploaded yet.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $files->links() }}
  </div>
</div>
@endsection
