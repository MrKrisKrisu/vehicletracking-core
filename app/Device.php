<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'bssid', 'ssid', 'firstSeen', 'lastSeen'
    ];

    protected $hidden = [
        'id', 'ssid', 'firstSeen', 'vehicle_id'
    ];

    protected $dates = [
        'firstSeen', 'lastSeen',
    ];

    /*public function vehicle() {
        return $this->belongsTo('App\Vehicle', 'id', 'vehicle_id');
    }*/

}
