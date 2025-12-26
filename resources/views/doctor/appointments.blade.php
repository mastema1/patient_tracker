@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Appointments</h1>
<div class="card">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>Requested</th>
            <th>Patient</th>
            <th>Requested by</th>
            <th>Scheduled at</th>
            <th>Status</th>
            <th>Reason</th>
            <th class="text-end">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($appointments as $a)
            <tr>
              <td class="small text-muted">{{ $a->created_at->format('Y-m-d H:i') }}</td>
              <td>{{ $a->patient->name ?? ('#'.$a->patient_id) }}</td>
              <td>{{ ucfirst($a->requested_by) }}</td>
              <td>{{ $a->scheduled_at ? $a->scheduled_at->format('Y-m-d H:i') : '—' }}</td>
              <td><span class="badge text-bg-{{ $a->status==='pending'?'warning':($a->status==='accepted'?'success':($a->status==='declined'?'danger':($a->status==='canceled'?'secondary':'info'))) }}">{{ ucfirst($a->status) }}</span></td>
              <td style="max-width: 360px">{{ $a->reason }}</td>
              <td class="text-end">
                <div class="d-flex gap-1 justify-content-end">
                  <form method="POST" action="{{ route('appointments.updateStatus', $a->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="accepted">
                    <button class="btn btn-sm btn-outline-success" @disabled($a->status!=='pending')>Accept</button>
                  </form>
                  <form method="POST" action="{{ route('appointments.updateStatus', $a->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="declined">
                    <button class="btn btn-sm btn-outline-danger" @disabled($a->status!=='pending')>Decline</button>
                  </form>
                  <form method="POST" action="{{ route('appointments.updateStatus', $a->id) }}">
                    @csrf
                    <input type="hidden" name="status" value="completed">
                    <button class="btn btn-sm btn-outline-primary" @disabled(!in_array($a->status,['accepted']))>Complete</button>
                  </form>
                </div>
                <form class="d-flex gap-2 align-items-center mt-1" method="POST" action="{{ route('appointments.reschedule', $a->id) }}">
                  @csrf
                  <input type="datetime-local" name="scheduled_at" class="form-control form-control-sm" style="max-width: 220px" required>
                  <button class="btn btn-sm btn-outline-secondary">Reschedule</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="7" class="text-muted">No appointments.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{ $appointments->links() }}
  </div>
</div>
@endsection
