@extends('layouts.app')

@section('content')
<div class="row">
  <div class="col-12">
    <div class="card shadow-sm mb-4">
      <div class="card-body d-flex align-items-center justify-content-between">
        <h1 class="h4 mb-0"><i class="fa-solid fa-user-check me-2"></i>Doctor Verification Queue</h1>
        <a href="{{ route('admin.support') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-inbox me-2"></i>Support Inbox</a>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        @if($pending->count() === 0)
          <div class="text-center py-5 text-muted">
            <i class="fa-regular fa-circle-check fa-2x mb-2"></i>
            <div>No pending doctor verifications.</div>
          </div>
        @else
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Requested</th>
                <th>Certificate</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @foreach($pending as $doc)
              <tr>
                <td>{{ $doc->id }}</td>
                <td>{{ $doc->name }}</td>
                <td>{{ $doc->email }}</td>
                <td>{{ $doc->created_at?->format('Y-m-d H:i') }}</td>
                <td>
                  @if($doc->certificate_path)
                    <a href="{{ route('admin.doctors.certificate', $doc->id) }}" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-download me-1"></i>Download</a>
                  @else
                    <span class="badge bg-secondary">No file</span>
                  @endif
                </td>
                <td class="text-end">
                  <form class="d-inline" method="POST" action="{{ route('admin.doctors.approve', $doc->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-success"><i class="fa-solid fa-check me-1"></i>Approve</button>
                  </form>
                  <form class="d-inline" method="POST" action="{{ route('admin.doctors.reject', $doc->id) }}">
                    @csrf
                    <button class="btn btn-sm btn-danger"><i class="fa-solid fa-xmark me-1"></i>Reject</button>
                  </form>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div>
          {{ $pending->links() }}
        </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
