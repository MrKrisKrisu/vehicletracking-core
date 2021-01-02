<?php

namespace App\Console\Commands;

use App\Company;
use App\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateExportData extends Command {

    protected $signature   = 'ptt:export {path}';
    protected $description = 'Export data to csv';

    public function handle(): int {
        $exportPath = $this->argument('path');

        if(!is_dir($exportPath)) {
            Log::error("path is not a directory.");
            dump("path is not a directory.");
            return;
        }

        $companies = Company::where('id', '<>', 4)->get();

        $fp = fopen($exportPath . 'companies.csv', 'w+');
        fputcsv($fp, [
            'id',
            'name',
        ]);
        foreach($companies as $company)
            fputcsv($fp, [
                'id'   => $company->id,
                'name' => $company->name,
            ]);
        fclose($fp);


        foreach($companies as $company) {
            $filename = preg_replace('/[^a-z0-9]+/', '-', strtolower($company->name));
            $filename = str_replace(['ä', 'ü', 'ö'], ['ae', 'ue', 'oe'], $filename);
            $fp = fopen($exportPath . 'company_' . $company->id . '_' . $filename . '.csv', 'w+');
            fputcsv($fp, [
                'bssid',
                'vehicle',
            ]);
            $devices = Device::join('vehicles', 'vehicles.id', '=', 'devices.vehicle_id')
                             ->where('vehicles.company_id', $company->id)
                             ->orderBy('devices.bssid', 'asc')
                             ->get();
            foreach($devices as $device) {
                fputcsv($fp, [
                    strtoupper($device->bssid),
                    $device->vehicle_name,
                ]);
            }
            fclose($fp);
        }

        return 0;
    }
}
