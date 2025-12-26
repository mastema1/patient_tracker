<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    public function dashboard()
    {
        $doctor = Auth::user();
        $patientsCount = User::where('doctor_id', $doctor->id)->count();
        return view('doctor.dashboard', compact('doctor','patientsCount'));
    }

    public function patients()
    {
        $doctor = Auth::user();
        $patients = User::where('doctor_id', $doctor->id)->orderBy('name')->paginate(15);
        return view('doctor.patients', compact('patients'));
    }

    public function patientReview($id)
    {
        $doctor = Auth::user();
        $patient = User::where('id', $id)->where('doctor_id', $doctor->id)->firstOrFail();

        $start = now()->subDays(29)->startOfDay();
        $end = now()->endOfDay();

        // Build labels and counts for last 30 days
        $labels = [];
        $counts = [];
        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            $labels[] = $d->format('Y-m-d');
            $counts[$d->format('Y-m-d')] = 0;
        }

        $logs = $patient->seizureLogs()->whereBetween('timestamp', [$start, $end])->get();
        foreach ($logs as $log) {
            $day = $log->timestamp->format('Y-m-d');
            if (isset($counts[$day])) {
                $counts[$day]++;
            }
        }

        $series = array_values($counts);
        $files = $patient->medicalFiles()->orderBy('upload_date','desc')->get();

        return view('doctor.patient_review', [
            'patient' => $patient,
            'labels' => $labels,
            'series' => $series,
            'files' => $files,
        ]);
    }
}
