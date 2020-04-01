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
        $vehicle = new Vehicle;
        $vehicle->company_id = 1;
        $vehicle->vehicle_id = 1001;
        $vehicle->save();

        $vehicle = new Vehicle;
        $vehicle->company_id = 1;
        $vehicle->vehicle_id = 1002;
        $vehicle->save();

        $vehicle = new Vehicle;
        $vehicle->company_id = 2;
        $vehicle->vehicle_id = 2001;
        $vehicle->save();

        $vehicle = new Vehicle;
        $vehicle->company_id = 1;
        $vehicle->vehicle_id = 2002;
        $vehicle->save();

    }
}
