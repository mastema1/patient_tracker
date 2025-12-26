<?php

namespace App\Http\Controllers;

use App\Models\SeizureLog;
use App\Models\MedicalFile;
use App\Models\Hospitalization;
use App\Models\SupportMessage;
use App\Models\FeedbackComment;
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

    public function history()
    {
        $user = Auth::user();
        $hospitalizations = Hospitalization::with('facility')
            ->where('patient_id', $user->id)
            ->orderBy('start_date','desc')
            ->paginate(10);
        return view('patient.history', compact('hospitalizations'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('patient.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:255','unique:users,email,'.$user->id],
            'phone' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'password' => ['nullable','confirmed','min:8'],
        ]);
        if (!empty($data['password'])) {
            $user->password = bcrypt($data['password']);
        }
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? null;
        $user->address = $data['address'] ?? null;
        $user->save();
        return back()->with('status','Profile updated.');
    }

    public function support()
    {
        $user = Auth::user();
        $publicFeedback = FeedbackComment::where('visibility','public')->latest()->paginate(5);
        $myPrivate = FeedbackComment::where('visibility','private')->where('user_id',$user->id)->latest()->paginate(5, ['*'], 'priv_page');
        return view('patient.support', compact('publicFeedback','myPrivate'));
    }

    public function submitSupport(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required','string','max:200'],
            'message' => ['required','string','max:5000'],
            'is_private' => ['nullable','boolean'],
        ]);
        SupportMessage::create([
            'user_id' => Auth::id(),
            'subject' => $data['subject'],
            'message' => $data['message'],
            'is_private' => $request->boolean('is_private', true),
        ]);
        return back()->with('status','Support message sent.');
    }

    public function submitFeedback(Request $request)
    {
        $data = $request->validate([
            'visibility' => ['required','in:public,private'],
            'content' => ['required','string','max:2000'],
        ]);
        FeedbackComment::create([
            'user_id' => Auth::id(),
            'visibility' => $data['visibility'],
            'content' => $data['content'],
        ]);
        return back()->with('status','Feedback posted.');
    }
}
