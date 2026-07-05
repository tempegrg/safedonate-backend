<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // =========================================
    // FILLABLE
    // =========================================

    protected $fillable = [

        'name',
        'email',
        'password',
        'role',
    ];

    // =========================================
    // HIDDEN
    // =========================================

    protected $hidden = [

        'password',
        'remember_token',
    ];

    // =========================================
    // CASTS
    // =========================================

    protected function casts(): array
    {
        return [

            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}