<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function doctorRequest(Request $request, int $id)
    {
        $doctor = Auth::user();
        abort_unless($doctor && $doctor->role === 'doctor', 403);

        $patient = User::where('id', $id)->where('role','patient')->firstOrFail();
        abort_unless($patient->doctor_id === $doctor->id, 403);

        $data = $request->validate([
            'scheduled_at' => ['nullable','date'],
            'reason' => ['nullable','string','max:2000'],
        ]);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'requested_by' => 'doctor',
            'status' => 'pending',
            'scheduled_at' => !empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
            'reason' => $data['reason'] ?? null,
        ]);

        return back()->with('status','Follow-up appointment request sent.');
    }

    public function patientRequest(Request $request, int $id)
    {
        $patient = Auth::user();
        abort_unless($patient && $patient->role === 'patient', 403);

        $doctor = User::where('id', $id)->where('role','doctor')->firstOrFail();

        $data = $request->validate([
            'scheduled_at' => ['nullable','date'],
            'reason' => ['nullable','string','max:2000'],
        ]);

        Appointment::create([
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'requested_by' => 'patient',
            'status' => 'pending',
            'scheduled_at' => !empty($data['scheduled_at']) ? Carbon::parse($data['scheduled_at']) : null,
            'reason' => $data['reason'] ?? null,
        ]);

        return back()->with('status','Appointment request sent.');
    }
}
