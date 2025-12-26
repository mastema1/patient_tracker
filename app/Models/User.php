<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'doctor_id',
        'phone',
        'address',
        'specialty',
        'bio',
        'case_categories',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function patients()
    {
        return $this->hasMany(User::class, 'doctor_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function seizureLogs()
    {
        return $this->hasMany(SeizureLog::class, 'patient_id');
    }

    public function medicalFiles()
    {
        return $this->hasMany(MedicalFile::class, 'patient_id');
    }

    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'doctor_facility', 'doctor_id', 'facility_id');
    }

    public function hospitalizations()
    {
        return $this->hasMany(Hospitalization::class, 'patient_id');
    }

    public function clinicalNotesAuthored()
    {
        return $this->hasMany(ClinicalNote::class, 'doctor_id');
    }

    public function clinicalNotesAbout()
    {
        return $this->hasMany(ClinicalNote::class, 'patient_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'doctor_id');
    }

    public function postComments()
    {
        return $this->hasMany(PostComment::class, 'doctor_id');
    }

    public function appointmentsAsDoctor()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function appointmentsAsPatient()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

    public function conversationsAsDoctor()
    {
        return $this->hasMany(Conversation::class, 'doctor_id');
    }

    public function conversationsAsPatient()
    {
        return $this->hasMany(Conversation::class, 'patient_id');
    }
}
