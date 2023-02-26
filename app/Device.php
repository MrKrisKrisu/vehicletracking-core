<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model {

    use HasFactory;

    protected $fillable = ['bssid', 'ssid', 'vehicle_id', 'ignore', 'blocked', 'moveVerifyUntil', 'firstSeen', 'lastSeen'];
    protected $hidden   = ['id', 'ssid', 'firstSeen', 'vehicle_id'];
    protected $casts    = [
        'blocked'         => 'boolean',
        'moveVerifyUntil' => 'datetime',
        'firstSeen'       => 'datetime',
        'lastSeen'        => 'datetime',
    ];

    public function scans(): HasMany {
        return $this->hasMany(Scan::class, 'bssid', 'bssid');
    }

    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

}
