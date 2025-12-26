@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Doctor Profile</h1>
<div class="row g-3">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex justify-content-between">
          <div>
            <div class="display-6">{{ $doctor->name }}</div>
            <div class="text-muted">{{ $doctor->specialty }}</div>
          </div>
        </div>
        @if($doctor->bio)
          <hr>
          <div>{{ $doctor->bio }}</div>
        @endif
        @if($doctor->case_categories)
          <hr>
          <div class="mb-2 fw-semibold">Case categories</div>
          <div>
            @foreach(preg_split('/\s*,\s*/', $doctor->case_categories) as $cat)
              @if(trim($cat) !== '')
                <span class="badge text-bg-primary me-1 mb-1">{{ trim($cat) }}</span>
              @endif
            @endforeach
          </div>
        @endif
        @if($doctor->facilities && $doctor->facilities->count())
          <hr>
          <div class="mb-2 fw-semibold">Affiliated facilities</div>
          <ul class="mb-0">
            @foreach($doctor->facilities as $f)
              <li>{{ $f->name }} — {{ ucfirst($f->type) }} @if($f->city) ({{ $f->city }}) @endif</li>
            @endforeach
          </ul>
        @endif
      </div>
    </div>
  </div>
  <div class="col-md-4">
    @if($viewer->role === 'patient')
      <div class="card">
        <div class="card-body">
          <h2 class="h6 mb-3">Schedule an appointment</h2>
          <form method="POST" action="{{ route('patient.appointments.request', $doctor->id) }}">
            @csrf
            <div class="mb-2">
              <label class="form-label">Preferred date/time</label>
              <input type="datetime-local" class="form-control" name="scheduled_at">
            </div>
            <div class="mb-2">
              <label class="form-label">Reason</label>
              <textarea class="form-control" name="reason" rows="2" placeholder="Optional"></textarea>
            </div>
            <button class="btn btn-primary w-100">Request</button>
          </form>
        </div>
      </div>
    @endif
  </div>
</div>
@endsection
