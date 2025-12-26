@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Doctor Dashboard</h1>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <div class="text-muted">Assigned patients</div>
      <div class="display-6">{{ $patientsCount }}</div>
    </div></div>
  </div>
</div>
@endsection
