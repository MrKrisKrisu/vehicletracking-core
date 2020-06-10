<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $fillable = [
        'vehicle_id', 'vehicle_name', 'modified_vehicle_name', 'bssid', 'ssid', 'signal',
        'quality', 'frequency', 'bitrates', 'encrypted', 'channel', 'scanDeviceId', 'created_at'
    ];

    public function device()
    {
        return $this->belongsTo(Device::class, 'bssid', 'bssid');
    }

    public function possibleVehiclesRaw()
    {
        $possible = [];
        foreach (explode(',', $this->modified_vehicle_name ?? $this->vehicle_name) as $p1)
            foreach (explode('-', $p1) as $p2) {
                $p2 = trim($p2);
                if (!in_array($p2, $possible))
                    $possible[] = $p2;
            }
        return $possible;
    }
}