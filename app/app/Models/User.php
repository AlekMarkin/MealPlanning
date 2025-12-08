<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

//represents an authenticated user in the application
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    //disable timestamps (users table has no created_at/updated_at)
    public $timestamps = false;

    //attributes that are mass assignable
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    //attributes hidden from serialization
    protected $hidden = [
        'password',
    ];

    //defines attribute type casting
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}