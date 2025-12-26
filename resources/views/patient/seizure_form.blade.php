@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Log a Seizure</h1>
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('patient.seizures.store') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-3">
          <label class="form-label">Date</label>
          <input type="date" class="form-control" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Time</label>
          <input type="time" class="form-control" name="time" value="{{ old('time', now()->format('H:i')) }}" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Duration (seconds)</label>
          <input type="number" min="1" max="86400" class="form-control" name="duration" value="{{ old('duration') }}" required>
        </div>
        <div class="col-12">
          <label class="form-label">Notes</label>
          <textarea class="form-control" name="notes" rows="3" maxlength="2000">{{ old('notes') }}</textarea>
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary">Save</button>
        <a class="btn btn-outline-secondary" href="{{ route('patient.seizures') }}">Cancel</a>
      </div>
    </form>
  </div>
</div>
@endsection
