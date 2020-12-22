<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model {

    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function company() {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function devices() {
        return $this->hasMany(Device::class);
    }
}
