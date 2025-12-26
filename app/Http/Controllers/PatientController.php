<?php

namespace App\Http\Controllers;

use App\Models\SeizureLog;
use App\Models\MedicalFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PatientController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $recentLogs = $user->seizureLogs()->orderBy('timestamp','desc')->limit(5)->get();
        $logsLast30 = $user->seizureLogs()->where('timestamp','>=', now()->subDays(30))->count();
        $filesCount = $user->medicalFiles()->count();
        return view('patient.dashboard', compact('user','recentLogs','logsLast30','filesCount'));
    }

    public function seizures()
    {
        $user = Auth::user();
        $logs = $user->seizureLogs()->orderBy('timestamp','desc')->paginate(10);
        return view('patient.seizures_list', compact('logs'));
    }

    public function createSeizure()
    {
        return view('patient.seizure_form');
    }

    public function storeSeizure(Request $request)
    {
        $data = $request->validate([
            'date' => ['required','date'],
            'time' => ['required'],
            'duration' => ['required','integer','min:1','max:86400'],
            'notes' => ['nullable','string','max:2000'],
        ]);

        $timestamp = Carbon::parse($data['date'].' '.$data['time']);

        SeizureLog::create([
            'patient_id' => Auth::id(),
            'timestamp' => $timestamp,
            'duration' => $data['duration'],
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('patient.seizures')->with('status','Seizure log added.');
    }

    public function files()
    {
        $files = Auth::user()->medicalFiles()->orderBy('upload_date','desc')->paginate(10);
        return view('patient.files', compact('files'));
    }

    public function uploadFile(Request $request)
    {
        $request->validate([
            'file' => ['required','file','max:10240'],
        ]);

        $user = Auth::user();
        $file = $request->file('file');
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, ['edf','csv','pdf'])) {
            return back()->withErrors(['file' => 'Only .edf, .csv, or .pdf files are allowed.'])->withInput();
        }
        $original = $file->getClientOriginalName();
        $safeName = time().'_'.preg_replace('/[^A-Za-z0-9_\-.]/','_', $original);
        $path = $file->storeAs('medical_files/'.$user->id, $safeName);

        MedicalFile::create([
            'patient_id' => $user->id,
            'file_path' => $path,
            'upload_date' => now(),
        ]);

        return back()->with('status','File uploaded.');
    }
}
