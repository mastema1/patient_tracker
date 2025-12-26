@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Support & Feedback</h1>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 mb-3">Contact Support</h2>
        <form method="POST" action="{{ route('patient.support.submit') }}">
          @csrf
          <div class="mb-2">
            <label class="form-label">Subject</label>
            <input type="text" class="form-control" name="subject" required>
          </div>
          <div class="mb-2">
            <label class="form-label">Message</label>
            <textarea class="form-control" name="message" rows="4" required></textarea>
          </div>
          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="is_private" id="is_private" value="1" checked>
            <label class="form-check-label" for="is_private">Send as private</label>
          </div>
          <button class="btn btn-primary">Send</button>
        </form>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h5 mb-3">Leave Feedback</h2>
        <form method="POST" action="{{ route('patient.feedback.submit') }}">
          @csrf
          <div class="mb-2">
            <label class="form-label">Visibility</label>
            <select class="form-select" name="visibility">
              <option value="public">Public</option>
              <option value="private">Private</option>
            </select>
          </div>
          <div class="mb-2">
            <label class="form-label">Feedback</label>
            <textarea class="form-control" name="content" rows="3" required></textarea>
          </div>
          <button class="btn btn-outline-primary">Post</button>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="row g-3 mt-3">
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h6 mb-3">Public Feedback</h2>
        @foreach($publicFeedback as $f)
          <div class="border rounded p-2 mb-2">
            <div class="small text-muted">{{ $f->created_at->diffForHumans() }} by {{ $f->user->name ?? 'User' }}</div>
            <div>{{ $f->content }}</div>
          </div>
        @endforeach
        {{ $publicFeedback->links() }}
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card h-100">
      <div class="card-body">
        <h2 class="h6 mb-3">My Private Feedback</h2>
        @foreach($myPrivate as $f)
          <div class="border rounded p-2 mb-2">
            <div class="small text-muted">{{ $f->created_at->diffForHumans() }}</div>
            <div>{{ $f->content }}</div>
          </div>
        @endforeach
        {{ $myPrivate->links('pagination::bootstrap-5', ['paginator' => $myPrivate]) }}
      </div>
    </div>
  </div>
</div>
@endsection
