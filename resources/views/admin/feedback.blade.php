@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Feedback Moderation</h1>
<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2" method="GET" action="{{ route('admin.feedback') }}">
      <div class="col-auto">
        <label class="form-label">Visibility</label>
        <select class="form-select" name="visibility" onchange="this.form.submit()">
          <option value="">All</option>
          <option value="public" @selected(($visibility ?? '')==='public')>Public</option>
          <option value="private" @selected(($visibility ?? '')==='private')>Private</option>
        </select>
      </div>
    </form>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>When</th>
            <th>User</th>
            <th>Visibility</th>
            <th>Content</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($feedback as $item)
            <tr>
              <td class="small text-muted">{{ $item->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ $item->user->name ?? 'User #'.$item->user_id }}</td>
              <td>{{ ucfirst($item->visibility) }}</td>
              <td style="max-width: 540px">{{ $item->content }}</td>
              <td class="text-end">
                <form method="POST" action="{{ route('admin.feedback.delete', $item->id) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this feedback?')">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="text-muted">No feedback found.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $feedback->links() }}
  </div>
</div>
@endsection
