<?php

namespace App\Console\Commands;

use App\Company;
use App\Scan;
use Illuminate\Console\Command;

class CreateExportData extends Command {

    protected $signature   = 'ptt:export';
    protected $description = 'Export data to json';

    public function handle(): int {
        $exportPath = public_path() . '/data';

        echo strtr('* Export files to :path', [':path' => $exportPath]) . PHP_EOL;

        $companies = Company::with(['vehicles.devices'])
                            ->where('name', '<>', 'Stationary')
                            ->get();

        echo strtr('* Found :count companies.', [':count' => $companies->count()]) . PHP_EOL;

        foreach($companies as $company) {

            echo strtr('* Export :name', [':name' => $company->name]) . PHP_EOL;

            $export   = [
                'company' => [
                    'name' => $company->name,
                ]
            ];
            $vehicles = $company->vehicles;
            echo strtr('** Found :count vehicles', [':count' => $vehicles->count()]) . PHP_EOL;

            foreach($vehicles as $vehicle) {
                $bssids = $vehicle->devices->pluck('bssid');

                $lastPos = Scan::whereIn('bssid', $bssids)
                               ->whereNotNull('latitude')
                               ->whereNotNull('longitude')
                               ->orderByDesc('created_at')
                               ->first();

                if($lastPos !== null) {
                    $lastPos = [
                        'latitude'  => round($lastPos->latitude, 3),
                        'longitude' => round($lastPos->longitude, 3),
                        'timestamp' => $lastPos->created_at->setMinute(0)->setSecond(0)->toIso8601String()
                    ];
                }

                $vehicleExport = [
                    'name'          => $vehicle->vehicle_name,
                    'type'          => $vehicle->type,
                    'bssid'         => $bssids,
                    'last_position' => $lastPos ?? [],
                ];

                if($vehicle->hasUic) {
                    $vehicleExport['uic'] = $vehicle->uic;
                }
                $export['vehicles'][] = $vehicleExport;
            }

            $path = $exportPath . '/' . $company->slug . '.json';
            echo strtr('*** Save :count rows to :path' . PHP_EOL, [
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
