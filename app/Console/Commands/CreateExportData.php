<?php

namespace App\Console\Commands;

use App\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateExportData extends Command {

    protected $signature   = 'ptt:export {path}';
    protected $description = 'Export data to json';

    public function handle(): int {
        $exportPath = $this->argument('path');

        if(!is_dir($exportPath)) {
            Log::error("path is not a directory.");
            dump("path is not a directory.");
            return 1;
        }

        $companies = Company::with(['vehicles.devices'])
                            ->where('name', '<>', 'Stationary')
                            ->get();

        foreach($companies as $company) {

            $slug   = Str::slug($company->name, '_');
            $export = [];

            foreach($company->vehicles as $vehicle) {
                $export['vehicles'][] = [
                    'name'  => $vehicle->vehicle_name,
                    'bssid' => $vehicle->devices->pluck('bssid')
                ];
            }

            $path = $exportPath . '/' . $slug . '.json';
            echo strtr('Save :count rows to :path' . PHP_EOL, [
                ':count' => count($export),
                ':path'  => $path
            ]);
            $fp = fopen($path, 'w+');
            fputs($fp, json_encode($export));
            fclose($fp);
        }

        return 0;
    }
}
