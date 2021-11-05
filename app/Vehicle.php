<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model {

    use HasFactory;

    protected $fillable = [
        'company_id', 'vehicle_name', 'type',
        'uic_type_code', 'uic_country_code', 'uic_series_number',
        'uic_order_number', 'uic_check_number', 'uic_operator_id',
    ];
    protected $hidden   = ['id', 'created_at', 'updated_at'];
    protected $casts    = [
        'company_id'        => 'integer',
        'uic_type_code'     => 'integer',
        'uic_country_code'  => 'integer',
        'uic_series_number' => 'integer',
        'uic_order_number'  => 'integer',
        'uic_check_number'  => 'integer',
    ];
    protected $appends  = ['uic', 'hasUic'];

    public function company(): HasOne {
        return $this->hasOne(Company::class, 'id', 'company_id');
    }

    public function devices(): HasMany {
        return $this->hasMany(Device::class, 'vehicle_id', 'id');
    }

    public function uicType(): BelongsTo {
        return $this->belongsTo(UicType::class, 'uic_type_code', 'id');
    }

    public function uicCountry(): BelongsTo {
        return $this->belongsTo(UicCountry::class, 'uic_country_code', 'id');
    }

    public function uicSeries(): BelongsTo {
        return $this->belongsTo(UicSeries::class, 'uic_series_number', 'id');
    }

    public function getUicAttribute(): string {
        return strtr('type country series order-check operator', [
            'type'     => $this->uic_type_code ?? 'xx',
            'country'  => $this->uic_country_code ?? 'xx',
            'series'   => $this->uic_series_number ?? 'xxxx',
            'order'    => $this->uic_order_number ?? 'xxx',
            'check'    => $this->uic_check_number ?? 'x',
            'operator' => $this->uic_operator_id ?? 'x-xxx',
        ]);
    }

    public function getHasUicAttribute(): bool {
        return $this->uic_type_code !== null || $this->uic_country_code !== null || $this->uic_series_number !== null ||
               $this->uic_order_number !== null || $this->uic_check_number !== null || $this->uic_operator_id !== null;
    }
}
