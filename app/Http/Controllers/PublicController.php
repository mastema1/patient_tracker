<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    public function doctorProfile(int $id)
    {
        $doctor = User::where('id', $id)->where('role','doctor')->with('facilities')->firstOrFail();
        $viewer = Auth::user();
        return view('public.doctor_profile', compact('doctor','viewer'));
    }
}
