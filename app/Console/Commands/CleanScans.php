<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanScans extends Command {

    protected $signature   = 'app:clean-scans';
    protected $description = 'Remove useless scans from the database.';

    public function handle(): void {
        $affectedRows = DB::table('scans')
                          ->whereNull('vehicle_name')
                          ->whereNull('modified_vehicle_name')
                          ->whereNull('latitude')
                          ->whereNull('longitude')
                          ->delete();
        $this->info("Removed {$affectedRows} scans.");
    }
}
