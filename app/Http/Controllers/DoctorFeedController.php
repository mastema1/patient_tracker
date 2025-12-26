<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\PostComment;

class DoctorFeedController extends Controller
{
    public function index()
    {
        $posts = Post::with(['doctor','comments.doctor'])->latest()->paginate(10);
        return view('doctor.feed', compact('posts'));
    }

    public function storePost(Request $request)
    {
        $data = $request->validate([
            'content' => ['required','string','max:5000'],
            'is_anonymous' => ['nullable','boolean'],
        ]);
        Post::create([
            'doctor_id' => Auth::id(),
            'content' => $data['content'],
            'is_anonymous' => $request->boolean('is_anonymous', false),
        ]);
        return back()->with('status','Post published.');
    }

    public function storeComment(Request $request, Post $post)
    {
        $data = $request->validate([
            'content' => ['required','string','max:2000'],
        ]);
        PostComment::create([
            'post_id' => $post->id,
            'doctor_id' => Auth::id(),
            'content' => $data['content'],
        ]);
        return back()->with('status','Comment added.');
    }
}
