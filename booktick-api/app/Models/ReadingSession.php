<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReadingSession extends Model
{
    protected $fillable = [
        'user_id',
        'book_title',
        'duration_minutes',
        'session_date',
        
    ];

    protected $casts = [
        'session_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}