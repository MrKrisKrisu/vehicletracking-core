<?php

namespace Database\Seeders;

use App\Device;
use App\ScanDevice;
use App\User;
use Database\Factories\DeviceFactory;
use Database\Factories\ScanDeviceFactory;
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
        User::factory()
            ->has(ScanDevice::factory(2), 'scanDevices')
            ->create(['email' => 'dev@dev.de']);
        Device::factory(100)->create();
        $this->call(ScanSeeder::class);
        $this->call(CompanyTableSeeder::class);
        $this->call(VehicleTableSeeder::class);
    }
}
