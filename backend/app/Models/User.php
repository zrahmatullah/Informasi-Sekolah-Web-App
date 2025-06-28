<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {

        return ['role' => $this->role->name];
    }


    public function role()
    {
        return $this->belongsTo(Role::class);
    }


    public function guru()
    {
        return $this->hasOne(Gurus::class, 'user_id');
    }


    public function siswa()
    {
        return $this->hasOne(Siswa::class);
    }
}
