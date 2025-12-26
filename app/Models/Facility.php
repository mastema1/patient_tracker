<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','type','address','city','description'
    ];

    public function doctors()
    {
        return $this->belongsToMany(User::class, 'doctor_facility', 'facility_id', 'doctor_id');
    }
}
