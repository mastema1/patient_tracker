<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\SupportMessage;
use App\Models\FeedbackComment;
use App\Models\User;

class AdminController extends Controller
{
    public function dashboard()
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);
        $pending = User::where('role','doctor')->where('status','pending')->orderBy('created_at')->paginate(20);
        return view('admin.dashboard', compact('pending'));
    }

    public function supportInbox(Request $request)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);
        $status = $request->get('status', 'open');
        $q = SupportMessage::with('user')->latest();
        if (in_array($status, ['open','closed'])) {
            $q->where('status', $status);
        }
        $messages = $q->paginate(20)->appends(['status' => $status]);
        return view('admin.support', compact('messages','status'));
    }

    public function updateSupportStatus(Request $request, int $id)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);
        $data = $request->validate([
            'status' => ['required','in:open,closed'],
        ]);
        $msg = SupportMessage::findOrFail($id);
        $msg->status = $data['status'];
        $msg->save();
        return back()->with('status','Support message updated.');
    }

    public function feedback(Request $request)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);
        $visibility = $request->get('visibility');
        $q = FeedbackComment::with('user')->latest();
        if (in_array($visibility, ['public','private'])) {
            $q->where('visibility', $visibility);
        }
        $feedback = $q->paginate(20)->appends(['visibility' => $visibility]);
        return view('admin.feedback', compact('feedback','visibility'));
    }

    public function deleteFeedback(int $id)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);
        $fb = FeedbackComment::findOrFail($id);
        $fb->delete();
        return back()->with('status','Feedback removed.');
    }

    public function approveDoctor(int $id)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);
        $doctor = User::where('id',$id)->where('role','doctor')->firstOrFail();
        $doctor->status = 'active';
        $doctor->save();
        return back()->with('status','Doctor approved.');
    }

    public function rejectDoctor(int $id)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);
        $doctor = User::where('id',$id)->where('role','doctor')->firstOrFail();
        $doctor->status = 'rejected';
        $doctor->save();
        return back()->with('status','Doctor rejected.');
    }

    public function downloadCertificate(int $id)
    {
        $admin = Auth::user();
        abort_unless($admin && $admin->role === 'admin', 403);
        $doctor = User::where('id',$id)->where('role','doctor')->firstOrFail();
        if (!$doctor->certificate_path) {
            abort(404);
        }
        $basename = basename($doctor->certificate_path);
        return Storage::download($doctor->certificate_path, $basename);
    }
}
