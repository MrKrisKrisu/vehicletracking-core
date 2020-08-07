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
            for ($i = 0; $i < rand(1, 4); $i++) {
                $vehicles = ["Fzg. " . $faker->word];
                for ($a = 0; $a < rand(0, 3); $a++)
                    $vehicles[] = $faker->word;
                \App\Scan::create([
                    'bssid' => $device->bssid,
                    'ssid' => $device->ssid,
                    'vehicle_name' => implode(',', $vehicles)
                ]);
            }
        }
    }
}
