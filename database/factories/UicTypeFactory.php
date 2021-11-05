<?php

namespace Database\Factories;

use App\UicType;
use Illuminate\Database\Eloquent\Factories\Factory;

class UicTypeFactory extends Factory {

    protected $model = UicType::class;

    public function definition(): array {
        return [
            'description' => $this->faker->word,
        ];
    }
}
