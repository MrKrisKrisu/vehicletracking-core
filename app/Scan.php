<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Scan extends Model {

    protected $fillable = [
        'vehicle_id', 'vehicle_name', 'modified_vehicle_name', 'bssid', 'ssid', 'signal',
        'quality', 'frequency', 'bitrates', 'encrypted', 'channel', 'scanDeviceId',
        'created_at', 'latitude', 'longitude', 'hidden', 'connectivity_state', 'speed',
    ];
    protected $casts    = [
        'hidden' => 'boolean',
        'speed'  => 'integer',
    ];

    public function device(): BelongsTo {
        return $this->belongsTo(Device::class, 'bssid', 'bssid');
    }

    public function scanDevice(): BelongsTo {
        return $this->belongsTo(ScanDevice::class, 'scanDeviceId', 'id');
    }

    public function possibleVehiclesRaw(): array {
        $possible = [];
        foreach(explode(',', $this->modified_vehicle_name ?? $this->vehicle_name) as $p1)
            foreach(explode('-', $p1) as $p2) {
                $p2 = trim($p2);
                if(!in_array($p2, $possible))
                    $possible[] = $p2;
            }
        return $possible;
    }
}