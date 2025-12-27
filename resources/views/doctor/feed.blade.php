@extends('layouts.app')

@section('content')
<div class="feed-shell">
  <div class="feed-col">
    <h1 class="h4 mb-3">The Medical Feed</h1>
    <div class="card mb-3 elevated">
      <div class="card-body">
        <form method="POST" action="{{ route('doctor.feed.post') }}">
          @csrf
          <div class="mb-2">
            <textarea name="content" class="form-control" rows="3" placeholder="Share an anonymized case study, an insight, or ask for guidance..." required></textarea>
          </div>
          <div class="form-check mb-2">
            <input type="checkbox" class="form-check-input" name="is_anonymous" id="anon" value="1">
            <label class="form-check-label" for="anon">Post anonymously</label>
          </div>
          <button class="btn btn-primary"><i class="fa-solid fa-paper-plane me-1"></i>Publish</button>
        </form>
      </div>
    </div>

    @forelse($posts as $post)
      <div class="card mb-3 elevated">
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div class="fw-semibold">
              <i class="fa-solid fa-user-doctor me-1"></i>{{ $post->is_anonymous ? 'Anonymous Doctor' : ($post->doctor->name ?? 'Doctor') }}
            </div>
            <div class="text-muted small">{{ $post->created_at->diffForHumans() }}</div>
          </div>
          <div class="mt-2">{{ $post->content }}</div>
          <hr>
          <div class="mt-2">
            <h6 class="mb-2">Comments</h6>
            @foreach($post->comments as $c)
              <div class="p-2 border rounded mb-2">
                <div class="small text-muted"><i class="fa-solid fa-user-doctor me-1"></i>{{ $c->doctor->name ?? 'Doctor' }} — {{ $c->created_at->diffForHumans() }}</div>
                <div>{{ $c->content }}</div>
              </div>
            @endforeach
            <form method="POST" action="{{ route('doctor.feed.comment', $post->id) }}" class="mt-2">
              @csrf
              <div class="input-group">
                <input type="text" class="form-control" name="content" placeholder="Add a comment..." required>
                <button class="btn btn-outline-primary"><i class="fa-solid fa-paper-plane"></i></button>
              </div>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="card elevated"><div class="card-body text-muted">No posts yet.</div></div>
    @endforelse

    {{ $posts->links() }}
  </div>
</div>
@endsection
