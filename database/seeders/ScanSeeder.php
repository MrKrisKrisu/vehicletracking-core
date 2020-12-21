<?php

namespace Database\Seeders;

use App\Device;
use App\Scan;
use Faker\Factory;
use Illuminate\Database\Seeder;

class ScanSeeder extends Seeder {
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $faker = Factory::create('de_DE');
        foreach(Device::all() as $device) {
            for($i = 0; $i < rand(1, 4); $i++) {
                $vehicles = ["Fzg. " . $faker->word];
                for($a = 0; $a < rand(0, 3); $a++)
                    $vehicles[] = $faker->word;
                Scan::create([
                                 'bssid'        => $device->bssid,
                                 'ssid'         => $device->ssid,
                                 'vehicle_name' => implode(',', $vehicles)
                             ]);
            }
        }
    }
}
