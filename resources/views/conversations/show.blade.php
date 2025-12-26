@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Conversation</h1>
<div class="row g-3">
  <div class="col-md-8">
    <div class="card">
      <div class="card-body">
        <div class="mb-2 text-muted">
          @if($user->role === 'doctor')
            With patient: <strong>{{ $conversation->patient->name ?? ('#'.$conversation->patient_id) }}</strong>
          @else
            With doctor: <strong>{{ $conversation->doctor->name ?? ('#'.$conversation->doctor_id) }}</strong>
          @endif
        </div>
        <div class="border rounded p-2" style="max-height: 50vh; overflow-y: auto;">
          @forelse($messages as $m)
            <div class="mb-2">
              <div class="small text-muted">
                {{ $m->created_at->format('Y-m-d H:i') }} — {{ $m->sender->id === $user->id ? 'You' : $m->sender->name }}
              </div>
              @if($m->content)
                <div>{{ $m->content }}</div>
              @endif
              @if($m->attachment_path)
                <div class="mt-1">
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('conversations.download', $m->id) }}">Download attachment ({{ basename($m->attachment_path) }})</a>
                </div>
              @endif
            </div>
          @empty
            <div class="text-muted">No messages yet.</div>
          @endforelse
        </div>
        <form class="mt-3" method="POST" action="{{ route('conversations.message', $conversation->id) }}" enctype="multipart/form-data">
          @csrf
          <div class="mb-2">
            <textarea class="form-control" name="content" rows="3" placeholder="Type your message..."></textarea>
          </div>
          <div class="mb-2">
            <input class="form-control" type="file" name="attachment" accept=".pdf,.jpg,.jpeg,.png,.csv,.edf">
            <div class="form-text">Allowed types: .pdf, .jpg, .jpeg, .png, .csv, .edf. Max 20MB.</div>
          </div>
          <button class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-4">
    <div class="card">
      <div class="card-body">
        <div class="text-muted small">Conversation created {{ $conversation->created_at->diffForHumans() }}</div>
        <div class="text-muted small">Last updated {{ $conversation->updated_at->diffForHumans() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
