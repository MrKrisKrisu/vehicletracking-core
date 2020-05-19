<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public const CREATED_AT = 'firstSeen';
    public const UPDATED_AT = 'lastSeen';

    protected $fillable = [
        'bssid', 'ssid', 'firstSeen', 'lastSeen'
    ];

    protected $hidden = [
        'id', 'ssid', 'firstSeen', 'vehicle_id'
    ];

    protected $dates = [
        'firstSeen', 'lastSeen',
    ];

    public function scans()
    {
        return $this->hasMany(Scan::class, 'bssid', 'bssid');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

}
