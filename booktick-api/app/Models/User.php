<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

   protected $fillable = [
    'name', 'email', 'password', 'role', 'is_banned',
];

    protected $hidden = [
        'password',
        'remember_token',
    ];

  protected $casts = [
    'email_verified_at' => 'datetime',
    'password'          => 'hashed',
    'is_banned'         => 'boolean',
];
    public function readingSessions()
    {
        return $this->hasMany(ReadingSession::class);
    }
}