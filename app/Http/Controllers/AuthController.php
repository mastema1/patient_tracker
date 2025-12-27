<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (in_array($user->role, ['doctor','patient','admin'])) {
                return $this->redirectByRole($user);
            }
            // Unknown role -> force logout and show login form
            Auth::logout();
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required','email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            if (!in_array($user->role, ['doctor','patient','admin'])) {
                // Unknown role, log out and show error
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors(['email' => 'Your account role is not enabled.'])->withInput();
            }
            return $this->redirectByRole($user);
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user());
        }
        $doctors = User::where('role', 'doctor')->orderBy('name')->get();
        return view('auth.register', compact('doctors'));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required','string','max:100'],
            'email' => ['required','email','max:255','unique:users,email'],
            'password' => ['required','confirmed','min:8'],
            'role' => ['required','in:patient,doctor'],
            'doctor_id' => ['nullable','integer','exists:users,id'],
        ]);

        $doctorId = null;
        if ($validated['role'] === 'patient' && !empty($validated['doctor_id'])) {
            $doctor = User::where('id', $validated['doctor_id'])->where('role','doctor')->first();
            $doctorId = $doctor?->id;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'doctor_id' => $doctorId,
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return $this->redirectByRole($user);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(User $user)
    {
        if ($user->role === 'doctor') {
            return redirect()->route('doctor.dashboard');
        }
        if ($user->role === 'patient') {
            return redirect()->route('patient.dashboard');
        }
        if ($user->role === 'admin') {
            return redirect()->route('admin.support');
        }
        // Unknown role -> back to login
        return redirect()->route('login');
    }
}
