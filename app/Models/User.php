<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{

     use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi ke post (user punya banyak post)
    public function post()
    {
        return $this->hasMany(Post::class);
    }

    // Relasi ke log aktivitas admin
    public function logs()
    {
        return $this->hasMany(LogActivity::class);
    }

}
