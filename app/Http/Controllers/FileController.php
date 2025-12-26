<?php

namespace App\Http\Controllers;

use App\Models\MedicalFile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function download(int $id)
    {
        $file = MedicalFile::findOrFail($id);
        $user = Auth::user();

        if ($user->role === 'patient' && $file->patient_id !== $user->id) {
            abort(403);
        }

        if ($user->role === 'doctor') {
            $patient = User::find($file->patient_id);
            if (!$patient || $patient->doctor_id !== $user->id) {
                abort(403);
            }
        }

        $basename = basename($file->file_path);
        return Storage::download($file->file_path, $basename);
    }
}
