@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Doctor Profile Settings</h1>
<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('doctor.settings.profile.update') }}">
      @csrf
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" value="{{ old('name', $doctor->name) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="{{ old('email', $doctor->email) }}" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Phone</label>
          <input type="text" name="phone" class="form-control" value="{{ old('phone', $doctor->phone) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Address</label>
          <input type="text" name="address" class="form-control" value="{{ old('address', $doctor->address) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Specialty</label>
          <input type="text" name="specialty" class="form-control" value="{{ old('specialty', $doctor->specialty) }}">
        </div>
        <div class="col-md-6">
          <label class="form-label">Case Categories (comma separated)</label>
          <input type="text" name="case_categories" class="form-control" value="{{ old('case_categories', $doctor->case_categories) }}">
        </div>
        <div class="col-12">
          <label class="form-label">Bio</label>
          <textarea name="bio" rows="5" class="form-control">{{ old('bio', $doctor->bio) }}</textarea>
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
@endsection
