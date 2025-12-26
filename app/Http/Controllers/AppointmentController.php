<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Appointment;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function doctorIndex()
    {
        $doctor = Auth::user();
        abort_unless($doctor && $doctor->role === 'doctor', 403);
        $appointments = Appointment::with('patient')
            ->where('doctor_id', $doctor->id)
            ->orderBy('created_at','desc')
            ->paginate(15);
        return view('doctor.appointments', compact('appointments','doctor'));
    }

    public function patientIndex()
    {
        $patient = Auth::user();
        abort_unless($patient && $patient->role === 'patient', 403);
        $appointments = Appointment::with('doctor')
            ->where('patient_id', $patient->id)
            ->orderBy('created_at','desc')
            ->paginate(15);
        return view('patient.appointments', compact('appointments','patient'));
    }

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

    public function updateStatus(Request $request, int $id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);
        $data = $request->validate([
            'status' => ['required','in:accepted,declined,canceled,completed'],
        ]);
        if ($user->role === 'doctor') {
            abort_unless($appointment->doctor_id === $user->id, 403);
            $appointment->status = $data['status'];
            $appointment->save();
        } elseif ($user->role === 'patient') {
            abort_unless($appointment->patient_id === $user->id, 403);
            // Patients can only cancel their own appointments
            abort_unless($data['status'] === 'canceled', 403);
            $appointment->status = 'canceled';
            $appointment->save();
        } else {
            abort(403);
        }
        return back()->with('status','Appointment updated.');
    }

    public function reschedule(Request $request, int $id)
    {
        $user = Auth::user();
        $appointment = Appointment::findOrFail($id);
        $data = $request->validate([
            'scheduled_at' => ['required','date'],
        ]);
        if ($user->role === 'doctor') {
            abort_unless($appointment->doctor_id === $user->id, 403);
            $appointment->scheduled_at = Carbon::parse($data['scheduled_at']);
            $appointment->save();
        } elseif ($user->role === 'patient') {
            abort_unless($appointment->patient_id === $user->id, 403);
            // Allow patient to propose a new time only if pending
            abort_unless($appointment->status === 'pending', 403);
            $appointment->scheduled_at = Carbon::parse($data['scheduled_at']);
            $appointment->save();
        } else {
            abort(403);
        }
        return back()->with('status','Appointment rescheduled.');
    }
}
