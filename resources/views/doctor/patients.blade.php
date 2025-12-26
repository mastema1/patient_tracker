@extends('layouts.app')

@section('content')
<h1 class="h4 mb-3">Patients</h1>
<div class="card">
  <div class="card-body">
    <table class="table table-striped">
      <thead><tr><th>Name</th><th>Email</th><th></th></tr></thead>
      <tbody>
        @forelse($patients as $p)
          <tr>
            <td>{{ $p->name }}</td>
            <td>{{ $p->email }}</td>
            <td class="text-end"><a class="btn btn-sm btn-primary" href="{{ route('doctor.patient.review', $p->id) }}">Review</a></td>
          </tr>
        @empty
          <tr><td colspan="3" class="text-muted">No patients assigned.</td></tr>
        @endforelse
      </tbody>
    </table>
    {{ $patients->links() }}
  </div>
</div>
@endsection
