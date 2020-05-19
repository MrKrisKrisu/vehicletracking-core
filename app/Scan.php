<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $fillable = [
        'vehicle_id', 'vehicle_name', 'modified_vehicle_name', 'bssid', 'ssid', 'signal',
        'quality', 'frequency', 'bitrates', 'encrypted', 'channel', 'scanDeviceId'
    ];
}