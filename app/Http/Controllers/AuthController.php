<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            // Enforce account status
            if (isset($user->status) && $user->status !== 'active') {
                $status = $user->status;
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                if ($status === 'pending') {
                    return back()->withErrors(['email' => 'Your account is under review. Please allow up to 7 days for verification.']).withInput();
                }
                if ($status === 'rejected') {
                    return back()->withErrors(['email' => 'Your account was rejected. Please contact support if you believe this is an error.']).withInput();
                }
                return back()->withErrors(['email' => 'Your account is not active.']).withInput();
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
        $doctors = User::where('role', 'doctor')->where('status','active')->orderBy('name')->get();
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
            'certificate' => ['nullable','file','mimes:pdf,jpg,jpeg,png,webp','max:5120','required_if:role,doctor'],
        ]);

        $doctorId = null;
        if ($validated['role'] === 'patient' && !empty($validated['doctor_id'])) {
            $doctor = User::where('id', $validated['doctor_id'])->where('role','doctor')->first();
            $doctorId = $doctor?->id;
        }

        $status = 'active';
        $certificatePath = null;
        if ($validated['role'] === 'doctor') {
            $status = 'pending';
            if ($request->hasFile('certificate')) {
                $file = $request->file('certificate');
                $filename = 'cert_' . Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
                $certificatePath = $file->storeAs('certificates', $filename, 'local');
            }
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'doctor_id' => $doctorId,
            'status' => $status,
            'certificate_path' => $certificatePath,
        ]);

        if ($user->role === 'patient') {
            Auth::login($user);
            $request->session()->regenerate();
            return $this->redirectByRole($user);
        }

        return redirect()->route('login')->with('status', 'Your account is under review. Please allow 7 days for our medical board to verify your certificate. You will be notified via email upon approval.');
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
