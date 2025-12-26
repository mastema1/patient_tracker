<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Conversation;
use App\Models\User;
use App\Models\Message;

class ConversationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'doctor') {
            $convos = Conversation::with(['patient'])
                ->where('doctor_id', $user->id)
                ->latest('updated_at')
                ->paginate(10);
        } else {
            $convos = Conversation::with(['doctor'])
                ->where('patient_id', $user->id)
                ->latest('updated_at')
                ->paginate(10);
        }
        return view('conversations.index', compact('convos','user'));
    }

    public function start(Request $request)
    {
        $user = Auth::user();
        if ($user->role === 'patient') {
            $doctorId = $request->integer('doctor_id') ?: $user->doctor_id;
            abort_unless($doctorId, 422, 'No doctor specified.');
            $doctor = User::where('id', $doctorId)->where('role','doctor')->firstOrFail();
            // If patient has an assigned doctor, enforce it
            if ($user->doctor_id && $user->doctor_id !== $doctor->id) {
                abort(403);
            }
            $conversation = Conversation::firstOrCreate([
                'doctor_id' => $doctor->id,
                'patient_id' => $user->id,
            ]);
        } else { // doctor
            $patientId = $request->integer('patient_id');
            abort_unless($patientId, 422, 'No patient specified.');
            $patient = User::where('id', $patientId)->where('role','patient')->firstOrFail();
            abort_unless($patient->doctor_id === $user->id, 403);
            $conversation = Conversation::firstOrCreate([
                'doctor_id' => $user->id,
                'patient_id' => $patient->id,
            ]);
        }
        return redirect()->route('conversations.show', $conversation->id);
    }

    public function show(int $id)
    {
        $user = Auth::user();
        $conversation = Conversation::with(['doctor','patient','messages.sender'])
            ->findOrFail($id);
        abort_unless($conversation->doctor_id === $user->id || $conversation->patient_id === $user->id, 403);
        $messages = $conversation->messages()->orderBy('created_at','asc')->get();
        return view('conversations.show', compact('conversation','messages','user'));
    }

    public function send(Request $request, int $id)
    {
        $user = Auth::user();
        $conversation = Conversation::findOrFail($id);
        abort_unless($conversation->doctor_id === $user->id || $conversation->patient_id === $user->id, 403);

        $content = trim((string) $request->get('content', ''));
        $hasFile = $request->hasFile('attachment');
        if ($content === '' && !$hasFile) {
            return back()->withErrors(['content' => 'Message or attachment is required.']);
        }

        $attachmentPath = null;
        if ($hasFile) {
            $request->validate([
                'attachment' => ['file','max:20480'], // 20MB
            ]);
            $ext = strtolower($request->file('attachment')->getClientOriginalExtension());
            $allowed = ['pdf','jpg','jpeg','png','csv','edf'];
            if (!in_array($ext, $allowed)) {
                return back()->withErrors(['attachment' => 'Allowed types: .pdf, .jpg, .jpeg, .png, .csv, .edf']);
            }
            $orig = $request->file('attachment')->getClientOriginalName();
            $safe = time().'_'.preg_replace('/[^A-Za-z0-9_\-.]/','_', $orig);
            $attachmentPath = $request->file('attachment')->storeAs('chat_attachments/'.$conversation->id, $safe);
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'content' => $content !== '' ? $content : null,
            'attachment_path' => $attachmentPath,
        ]);

        $conversation->touch();

        return back();
    }

    public function download(int $messageId)
    {
        $user = Auth::user();
        $message = Message::with(['conversation'])->findOrFail($messageId);
        $conversation = $message->conversation;
        abort_unless($conversation->doctor_id === $user->id || $conversation->patient_id === $user->id, 403);
        abort_unless($message->attachment_path, 404);
        $name = basename($message->attachment_path);
        return Storage::download($message->attachment_path, $name);
    }
}
