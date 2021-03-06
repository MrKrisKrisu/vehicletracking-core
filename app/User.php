<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = ['name', 'email', 'password',];
    protected $hidden   = ['password', 'remember_token',];
    protected $casts    = ['email_verified_at' => 'datetime'];

    public function scanDevices()
    {
        return $this->hasMany(ScanDevice::class, 'user_id', 'id');
    }
}
