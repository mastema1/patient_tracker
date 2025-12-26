@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Search Results</h1>
<form class="mb-3" method="GET" action="{{ route('search') }}">
  <div class="input-group">
    <input type="search" class="form-control" name="q" value="{{ $q }}" placeholder="Search doctors or facilities">
    <button class="btn btn-primary">Search</button>
  </div>
</form>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 mb-3">Doctors</h2>
        @forelse($doctors as $d)
          <div class="mb-3 p-2 border rounded">
            <div class="fw-semibold">{{ $d->name }}</div>
            <div class="text-muted small">{{ $d->specialty }}</div>
            <div class="small">{{ \Illuminate\Support\Str::limit($d->bio, 140) }}</div>
            <a class="btn btn-sm btn-outline-primary mt-2" href="{{ route('public.doctor.profile', $d->id) }}">View Profile</a>
          </div>
        @empty
          <div class="text-muted">No doctors found.</div>
        @endforelse
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 mb-3">Facilities</h2>
        @forelse($facilities as $f)
          <div class="mb-3 p-2 border rounded">
            <div class="fw-semibold">{{ $f->name }}</div>
            <div class="text-muted small">{{ ucfirst($f->type) }} — {{ $f->city }}</div>
            <div class="small">{{ \Illuminate\Support\Str::limit($f->description, 140) }}</div>
          </div>
        @empty
          <div class="text-muted">No facilities found.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection
