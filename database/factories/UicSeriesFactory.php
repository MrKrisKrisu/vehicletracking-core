<?php

namespace Database\Factories;

use App\UicSeries;
use Illuminate\Database\Eloquent\Factories\Factory;

class UicSeriesFactory extends Factory {

    protected $model = UicSeries::class;

    public function definition(): array {
        return [
            'description' => $this->faker->word,
        ];
    }
}
