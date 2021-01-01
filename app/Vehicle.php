<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model {

    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function company(): HasOne {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function devices(): HasMany {
        return $this->hasMany(Device::class, 'vehicle_id', 'id');
    }
}
