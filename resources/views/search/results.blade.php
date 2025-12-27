@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Search Results</h1>
<form class="mb-3" method="GET" action="{{ route('search') }}">
  <div class="input-group">
    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
    <input type="search" class="form-control" name="q" value="{{ $q }}" placeholder="Search doctors or facilities">
    <button class="btn btn-primary">Search</button>
  </div>
  <div class="form-text">Tip: type at least 2 characters to see live suggestions in the top bar.</div>
  </form>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card h-100 elevated">
      <div class="card-body">
        <h2 class="h5 mb-3"><i class="fa-solid fa-user-doctor me-1"></i>Doctors</h2>
        @forelse($doctors as $d)
          <div class="card mb-2">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <div class="fw-semibold"><i class="fa-solid fa-user-doctor me-1"></i>{{ $d->name }}</div>
                  <div class="text-muted small">{{ $d->specialty }}</div>
                  <div class="small">{{ \Illuminate\Support\Str::limit($d->bio, 140) }}</div>
                </div>
                <a class="btn btn-sm btn-outline-primary" href="{{ route('public.doctor.profile', $d->id) }}">View</a>
              </div>
            </div>
          </div>
        @empty
          <div class="text-muted">No doctors found.</div>
        @endforelse
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100 elevated">
      <div class="card-body">
        <h2 class="h5 mb-3"><i class="fa-solid fa-hospital me-1"></i>Facilities</h2>
        @forelse($facilities as $f)
          <div class="card mb-2">
            <div class="card-body">
              <div class="fw-semibold"><i class="fa-solid fa-hospital me-1"></i>{{ $f->name }}</div>
              <div class="text-muted small">{{ ucfirst($f->type) }} — {{ $f->city }}</div>
              <div class="small">{{ \Illuminate\Support\Str::limit($f->description, 140) }}</div>
            </div>
          </div>
        @empty
          <div class="text-muted">No facilities found.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
