<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'nim',
        'email',
        'phone',
        'password',
        'google_id',
        'is_admin',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * Relasi: satu user bisa punya banyak postingan item
     */
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Relasi: satu user bisa punya banyak komentar
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
