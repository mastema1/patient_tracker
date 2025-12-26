<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\SupportMessage;
use App\Models\FeedbackComment;

class AdminController extends Controller
{
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
}
