<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model {

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function vehicles(): HasMany {
        return $this->hasMany(Vehicle::class, 'company_id', 'id')->orderBy('vehicle_name');
    }
}
