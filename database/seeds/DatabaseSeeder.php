<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(DeviceSeeder::class);
        $this->call(ScanSeeder::class);
        $this->call(CompanyTableSeeder::class);
        $this->call(VehicleTableSeeder::class);
    }
}
