<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntranetData extends Model {

    use HasFactory;

    protected $fillable = ['scanDeviceId', 'data'];
    protected $casts    = [
        'scanDeviceId' => 'integer',
        'data'         => 'json',
    ];
}
