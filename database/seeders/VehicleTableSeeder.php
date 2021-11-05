<?php

namespace Database\Seeders;

use App\Vehicle;
use Illuminate\Database\Seeder;

class VehicleTableSeeder extends Seeder {

    public function run(): void {
        for($company_id = 1; $company_id <= 2; $company_id++) {
            for($vehicle_name = 1; $vehicle_name < 10; $vehicle_name++) {
                Vehicle::factory()->create([
                                               'company_id'   => $company_id,
                                               'vehicle_name' => "Fahrzeug $vehicle_name"
                                           ]);
            }
        }
    }
}
