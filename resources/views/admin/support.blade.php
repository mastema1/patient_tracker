@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Support Inbox</h1>
<div class="card mb-3">
  <div class="card-body">
    <form class="row g-2" method="GET" action="{{ route('admin.support') }}">
      <div class="col-auto">
        <label class="form-label">Status</label>
        <select class="form-select" name="status" onchange="this.form.submit()">
          <option value="open" @selected(($status ?? 'open')==='open')>Open</option>
          <option value="closed" @selected(($status ?? 'open')==='closed')>Closed</option>
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
            <th>Submitted</th>
            <th>User</th>
            <th>Subject</th>
            <th>Visibility</th>
            <th>Status</th>
            <th>Message</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($messages as $m)
            <tr>
              <td class="small text-muted">{{ $m->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ $m->user->name ?? 'User #'.$m->user_id }}</td>
              <td>{{ $m->subject }}</td>
              <td>{{ $m->is_private ? 'Private' : 'Public' }}</td>
              <td><span class="badge text-bg-{{ $m->status==='open'?'warning':'secondary' }}">{{ ucfirst($m->status) }}</span></td>
              <td style="max-width: 420px">{{ $m->message }}</td>
              <td class="text-end">
                <form method="POST" action="{{ route('admin.support.update', $m->id) }}" class="d-inline">
                  @csrf
                  <input type="hidden" name="status" value="{{ $m->status==='open'?'closed':'open' }}">
                  <button class="btn btn-sm btn-outline-primary">Mark {{ $m->status==='open'?'Closed':'Open' }}</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-muted">No support messages.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $messages->links() }}
  </div>
</div>
@endsection
