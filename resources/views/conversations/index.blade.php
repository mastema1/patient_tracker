@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Conversations</h1>
<div class="row g-3">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        @forelse($convos as $c)
          <div class="d-flex justify-content-between align-items-center border-bottom py-2">
            <div>
              @if($user->role === 'doctor')
                <div class="fw-semibold">Patient: {{ $c->patient->name ?? ('#'.$c->patient_id) }}</div>
              @else
                <div class="fw-semibold">Doctor: {{ $c->doctor->name ?? ('#'.$c->doctor_id) }}</div>
              @endif
              <div class="small text-muted">Updated {{ $c->updated_at->diffForHumans() }}</div>
            </div>
            <div>
              <a class="btn btn-sm btn-outline-primary" href="{{ route('conversations.show', $c->id) }}">Open</a>
            </div>
          </div>
        @empty
          <div class="text-muted">No conversations yet.</div>
        @endforelse
        {{ $convos->links() }}
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <h2 class="h6 mb-2">Start a conversation</h2>
        @if($user->role === 'patient')
          <form method="POST" action="{{ route('conversations.start') }}">
            @csrf
            @if($user->doctor_id)
              <input type="hidden" name="doctor_id" value="{{ $user->doctor_id }}">
              <button class="btn btn-primary w-100">Message my doctor</button>
            @else
              <div class="mb-2">
                <label class="form-label">Doctor ID</label>
                <input type="number" class="form-control" name="doctor_id" required>
              </div>
              <button class="btn btn-primary w-100">Start</button>
            @endif
          </form>
        @else
          <form method="POST" action="{{ route('conversations.start') }}">
            @csrf
            <div class="mb-2">
              <label class="form-label">Patient ID</label>
              <input type="number" class="form-control" name="patient_id" required>
            </div>
            <button class="btn btn-primary w-100">Start</button>
          </form>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
