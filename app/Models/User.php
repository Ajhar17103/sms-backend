<?php

namespace App\Models;

use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password','role'];

    protected $hidden = ['password', 'remember_token'];

    public function getJWTIdentifier(){
        return $this->getKey();
    }

    public function getJWTCustomClaims(){
        return [];
    }
    public function role(){
    return $this->belongsTo(Role::class);
    }
}
