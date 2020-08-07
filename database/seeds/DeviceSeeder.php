<?php

use Illuminate\Database\Seeder;

class DeviceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create('de_DE');
        for ($i = 0; $i < 100; $i++) {
            \App\Device::create([
                'bssid' => $faker->macAddress,
                'ssid' => $faker->word
            ]);
        }
    }
}
