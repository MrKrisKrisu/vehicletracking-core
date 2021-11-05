<?php

namespace Database\Factories;

use App\UicCountry;
use Illuminate\Database\Eloquent\Factories\Factory;

class UicCountryFactory extends Factory {

    protected $model = UicCountry::class;

    public function definition(): array {
        return [
            'description' => $this->faker->word,
        ];
    }
}
