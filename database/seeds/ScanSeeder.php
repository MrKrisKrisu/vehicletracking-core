<?php

use Illuminate\Database\Seeder;

class ScanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create('de_DE');
        foreach (\App\Device::all() as $device) {
            for ($i = 0; $i < rand(1, 4); $i++)
                \App\Scan::create([
                    'bssid' => $device->bssid,
                    'ssid' => $device->ssid,
                    'vehicle_name' => "Fzg. " . $faker->word
                ]);
        }
    }
}
