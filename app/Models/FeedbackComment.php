<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','visibility','content'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
