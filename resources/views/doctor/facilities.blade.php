@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">My Facilities</h1>
<div class="row g-3">
  <div class="col-md-7">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h6 mb-3">Attached Facilities</h2>
        @forelse($myFacilities as $f)
          <div class="border rounded p-2 mb-2 d-flex justify-content-between align-items-center">
            <div>
              <div class="fw-semibold">{{ $f->name }}</div>
              <div class="text-muted small">{{ ucfirst($f->type) }} @if($f->city) — {{ $f->city }} @endif</div>
            </div>
            <form method="POST" action="{{ route('doctor.settings.facilities.detach', $f->id) }}">
              @csrf
              @method('DELETE')
              <button class="btn btn-sm btn-outline-danger">Detach</button>
            </form>
          </div>
        @empty
          <div class="text-muted">No facilities attached.</div>
        @endforelse
      </div>
    </div>
  </div>
  <div class="col-md-5">
    <div class="card mb-3">
      <div class="card-body">
        <h2 class="h6 mb-2">Attach Existing Facility</h2>
        <form method="POST" action="{{ route('doctor.settings.facilities.attach') }}">
          @csrf
          <div class="input-group">
            <select class="form-select" name="facility_id">
              @foreach($allFacilities as $af)
                <option value="{{ $af->id }}">{{ $af->name }} ({{ ucfirst($af->type) }}@if($af->city), {{ $af->city }}@endif)</option>
              @endforeach
            </select>
            <button class="btn btn-outline-primary">Attach</button>
          </div>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-body">
        <h2 class="h6 mb-2">Create New Facility</h2>
        <form method="POST" action="{{ route('doctor.settings.facilities.create') }}">
          @csrf
          <div class="mb-2">
            <label class="form-label">Name</label>
            <input class="form-control" type="text" name="name" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Type</label>
            <select class="form-select" name="type" required>
              <option value="clinic">Clinic</option>
              <option value="hospital">Hospital</option>
              <option value="cabinet">Cabinet</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">City</label>
            <input class="form-control" type="text" name="city">
          </div>
          <div class="mb-2">
            <label class="form-label">Address</label>
            <input class="form-control" type="text" name="address">
          </div>
          <div class="mb-2">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
          </div>
          <button class="btn btn-primary">Create & Attach</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
