<?php

namespace Database\Seeders;

use App\Device;
use App\ScanDevice;
use App\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder {

    public function run(): void {
        $this->call(CompanyTableSeeder::class);
        $this->call(UicSeeder::class);
        $this->call(VehicleTableSeeder::class);

        User::factory()
            ->has(ScanDevice::factory(2), 'scanDevices')
            ->create(['email' => 'dev@dev.de']);
        Device::factory(100)->create();

        $this->call(ScanSeeder::class);
    }
}
