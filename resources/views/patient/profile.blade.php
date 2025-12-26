@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">My Profile</h1>
<div class="row">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <form method="POST" action="{{ route('patient.profile.update') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" value="{{ old('address', $user->address) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">New Password</label>
              <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current">
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-control">
            </div>
          </div>
          <div class="mt-3">
            <button class="btn btn-primary">Save Changes</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="text-muted">Assigned doctor</div>
        <div class="fw-semibold">{{ $user->doctor?->name ?? '—' }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
