<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeizureLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'timestamp',
        'duration',
        'notes',
    ];

    protected $casts = [
        'timestamp' => 'datetime',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}
