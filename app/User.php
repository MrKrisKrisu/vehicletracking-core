<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable {

    use Notifiable, HasFactory;

    protected $fillable = ['name', 'email', 'password',];
    protected $hidden   = ['password', 'remember_token',];
    protected $casts    = [
        'id'                => 'integer',
        'email_verified_at' => 'datetime'
    ];

    public function scanDevices(): HasMany {
        return $this->hasMany(ScanDevice::class, 'user_id', 'id');
    }
}
