<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $hidden = [
        'created_at', 'updated_at'
    ];

    public function vehicles() {
        return $this->hasMany('App\Vehicle', 'company_id', 'id');
    }
}
