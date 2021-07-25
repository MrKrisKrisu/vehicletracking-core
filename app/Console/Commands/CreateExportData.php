<?php

namespace App\Console\Commands;

use App\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Scan;

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
            $export = [
                'company' => [
                    'name' => $company->name,
                ]
            ];

            foreach($company->vehicles as $vehicle) {
                $bssids = $vehicle->devices->pluck('bssid');

                $lastPos = Scan::whereIn('bssid', $bssids)
                               ->whereNotNull('latitude')
                               ->whereNotNull('longitude')
                               ->orderByDesc('created_at')
                               ->first();

                if($lastPos != null) {
                    $lastPos = [
                        'latitude'  => round($lastPos->latitude, 3),
                        'longitude' => round($lastPos->longitude, 3),
                        'timestamp' => $lastPos->created_at->setMinute(0)->setSecond(0)->toIso8601String()
                    ];
                }

                $export['vehicles'][] = [
                    'name'          => $vehicle->vehicle_name,
                    'type'          => $vehicle->type,
                    'bssid'         => $bssids,
                    'last_position' => $lastPos ?? [],
                ];
            }

            $path = $exportPath . '/' . $slug . '.json';
            echo strtr('Save :count rows to :path' . PHP_EOL, [
                ':count' => count($export['vehicles']),
                ':path'  => $path
            ]);
            $fp = fopen($path, 'w+');
            fputs($fp, json_encode($export, JSON_PRETTY_PRINT));
            fclose($fp);
        }

        return 0;
    }
}
