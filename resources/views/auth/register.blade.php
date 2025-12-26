@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
  <div class="col-md-8">
    <div class="card shadow-sm">
      <div class="card-body">
        <h1 class="h4 mb-3">Register</h1>
        <form method="POST" action="{{ route('register.post') }}">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Name</label>
              <input type="text" class="form-control" name="name" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Password</label>
              <input type="password" class="form-control" name="password" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Confirm Password</label>
              <input type="password" class="form-control" name="password_confirmation" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Role</label>
              <select name="role" id="role" class="form-select" required>
                <option value="patient" {{ old('role')==='patient' ? 'selected' : '' }}>Patient</option>
                <option value="doctor" {{ old('role')==='doctor' ? 'selected' : '' }}>Doctor</option>
              </select>
            </div>
            <div class="col-md-6" id="doctorSelect">
              <label class="form-label">Assign Doctor (for patients)</label>
              <select name="doctor_id" class="form-select">
                <option value="">-- None --</option>
                @foreach($doctors as $doc)
                  <option value="{{ $doc->id }}" {{ old('doctor_id')==$doc->id ? 'selected' : '' }}>{{ $doc->name }} ({{ $doc->email }})</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mt-3">
            <button class="btn btn-primary">Create Account</button>
            <a class="btn btn-link" href="{{ route('login') }}">Back to login</a>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@section('scripts')
<script>
  function toggleDoctor(){
    const role = document.getElementById('role').value;
    const el = document.getElementById('doctorSelect');
    el.style.display = role === 'patient' ? 'block' : 'none';
  }
  document.getElementById('role').addEventListener('change', toggleDoctor);
  toggleDoctor();
</script>
@endsection
