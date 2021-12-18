<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model {

    use HasFactory;

    protected $fillable = ['bssid', 'ssid', 'vehicle_id', 'ignore', 'moveVerifyUntil', 'firstSeen', 'lastSeen'];
    protected $hidden   = ['id', 'ssid', 'firstSeen', 'vehicle_id'];
    protected $dates    = ['moveVerifyUntil', 'firstSeen', 'lastSeen'];
    protected $casts    = [
        'blocked' => 'boolean',
    ];

    public function scans(): HasMany {
        return $this->hasMany(Scan::class, 'bssid', 'bssid');
    }

    public function vehicle(): BelongsTo {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id');
    }

}
