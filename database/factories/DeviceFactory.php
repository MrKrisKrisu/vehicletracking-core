<?php

namespace Database\Factories;

use App\Device;
use App\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory {

    protected $model = Device::class;

    public function definition(): array {
        return [
            'bssid'      => $this->faker->macAddress,
            'ssid'       => $this->faker->word,
            'vehicle_id' => $this->faker->boolean ? Vehicle::inRandomOrder()->first()->id : null,
        ];
    }
}
