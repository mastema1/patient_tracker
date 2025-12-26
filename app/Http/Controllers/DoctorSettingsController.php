<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Facility;

class DoctorSettingsController extends Controller
{
    public function profile()
    {
        $doctor = Auth::user();
        abort_unless($doctor && $doctor->role === 'doctor', 403);
        return view('doctor.profile_settings', compact('doctor'));
    }

    public function updateProfile(Request $request)
    {
        $doctor = Auth::user();
        abort_unless($doctor && $doctor->role === 'doctor', 403);
        $data = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:255','unique:users,email,'.$doctor->id],
            'phone' => ['nullable','string','max:50'],
            'address' => ['nullable','string','max:255'],
            'specialty' => ['nullable','string','max:120'],
            'bio' => ['nullable','string','max:10000'],
            'case_categories' => ['nullable','string','max:1000'],
            'password' => ['nullable','confirmed','min:8'],
        ]);
        if (!empty($data['password'])) {
            $doctor->password = bcrypt($data['password']);
        }
        $doctor->fill(collect($data)->except(['password','password_confirmation'])->toArray());
        $doctor->save();
        return back()->with('status','Profile updated.');
    }

    public function facilities()
    {
        $doctor = Auth::user();
        abort_unless($doctor && $doctor->role === 'doctor', 403);
        $myFacilities = $doctor->facilities()->orderBy('name')->get();
        $allFacilities = Facility::orderBy('name')->limit(50)->get();
        return view('doctor.facilities', compact('doctor','myFacilities','allFacilities'));
    }

    public function attachFacility(Request $request)
    {
        $doctor = Auth::user();
        abort_unless($doctor && $doctor->role === 'doctor', 403);
        $data = $request->validate([
            'facility_id' => ['required','integer','exists:facilities,id'],
        ]);
        $doctor->facilities()->syncWithoutDetaching([$data['facility_id']]);
        return back()->with('status','Facility attached.');
    }

    public function createFacility(Request $request)
    {
        $doctor = Auth::user();
        abort_unless($doctor && $doctor->role === 'doctor', 403);
        $data = $request->validate([
            'name' => ['required','string','max:200'],
            'type' => ['required','in:cabinet,hospital,clinic'],
            'city' => ['nullable','string','max:120'],
            'address' => ['nullable','string','max:255'],
            'description' => ['nullable','string','max:2000'],
        ]);
        $facility = Facility::create($data);
        $doctor->facilities()->syncWithoutDetaching([$facility->id]);
        return back()->with('status','Facility created and attached.');
    }

    public function detachFacility(int $id)
    {
        $doctor = Auth::user();
        abort_unless($doctor && $doctor->role === 'doctor', 403);
        $doctor->facilities()->detach($id);
        return back()->with('status','Facility detached.');
    }
}
