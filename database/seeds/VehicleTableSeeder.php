<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Vehicle;

class VehicleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($company_id = 1; $company_id <= 2; $company_id++)
            for ($vehicle_name = 1; $vehicle_name < 10; $vehicle_name++)
                Vehicle::create([
                    'company_id' => $company_id,
                    'vehicle_name' => "Fahrzeug $vehicle_name"
                ]);

    }
}
