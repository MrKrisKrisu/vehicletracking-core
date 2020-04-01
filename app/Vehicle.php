<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    //protected $fillable = ['title', 'body'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public function company() {
        return $this->hasOne('App\Company', 'id', 'company_id');
    }

    public function devices() {
        return $this->hasMany('App\Device');
    }
}
