<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Company extends Model {

    protected $hidden  = ['created_at', 'updated_at'];
    protected $appends = ['slug'];

    public function vehicles(): HasMany {
        return $this->hasMany(Vehicle::class, 'company_id', 'id')->orderBy('vehicle_name');
    }

    public function getSlugAttribute(): string {
        return Str::slug($this->name, '_');
    }
}
