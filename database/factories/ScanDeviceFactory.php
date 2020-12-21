<?php

namespace Database\Factories;

use App\ScanDevice;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScanDeviceFactory extends Factory {
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScanDevice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            'user_id'     => User::factory(),
            'token'       => $this->faker->unique()->uuid,
            'name'        => $this->faker->word,
            'latitude'    => $this->faker->latitude,
            'longitude'   => $this->faker->longitude,
            'valid_until' => $this->faker->boolean ? $this->faker->dateTimeBetween('now', '+2 years') : null
        ];
    }
}
