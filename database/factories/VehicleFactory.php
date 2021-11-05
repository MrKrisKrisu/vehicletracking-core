<?php

namespace Database\Factories;

use App\Company;
use App\UicCountry;
use App\UicSeries;
use App\UicType;
use App\Vehicle;
use Exception;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory {

    protected $model = Vehicle::class;

    /**
     * @throws Exception
     */
    public function definition(): array {
        return [
            'company_id'        => Company::all()->random()->id,
            'vehicle_name'      => $this->faker->word,
            'type'              => $this->faker->randomElement(['bus', 'tram', 'train']),
            'uic_type_code'     => UicType::all()->random()->id,
            'uic_country_code'  => UicCountry::all()->random()->id,
            'uic_series_number' => UicSeries::all()->random()->id,
            'uic_order_number'  => random_int(111, 999),
            'uic_check_number'  => random_int(0, 9),
            'uic_operator_id'   => 'D-DB',
        ];
    }
}
