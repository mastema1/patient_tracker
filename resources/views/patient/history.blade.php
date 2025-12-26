@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Medical History</h1>
<div class="card">
  <div class="card-body">
    @forelse($hospitalizations as $h)
      <div class="p-3 mb-3 border rounded">
        <div class="d-flex justify-content-between">
          <div class="fw-semibold">{{ $h->title }}</div>
          <div class="text-muted small">{{ $h->start_date->format('Y-m-d') }} — {{ $h->end_date ? $h->end_date->format('Y-m-d') : 'Present' }}</div>
        </div>
        <div class="small text-muted">@if($h->facility) {{ $h->facility->name }} ({{ ucfirst($h->facility->type) }}, {{ $h->facility->city }}) @endif</div>
        <div class="mt-1">{{ $h->description }}</div>
      </div>
    @empty
      <div class="text-muted">No medical history recorded.</div>
    @endforelse
    {{ $hospitalizations->links() }}
  </div>
</div>
@endsection
