<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanDevice extends Model {
    use HasFactory;

    protected $fillable = ['user_id', 'token', 'name', 'notify', 'latitude', 'longitude', 'valid_until'];
    protected $casts    = [
        'user_id' => 'integer',
    ];

    public function user() {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
