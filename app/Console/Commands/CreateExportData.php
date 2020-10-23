<?php

namespace App\Console\Commands;

use App\Company;
use App\Device;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateExportData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ptt:export {path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $exportPath = $this->argument('path');

        if (!is_dir($exportPath)) {
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
        foreach ($companies as $company)
            fputcsv($fp, [
                'id'   => $company->id,
                'name' => $company->name,
            ]);
        fclose($fp);


        foreach ($companies as $company) {
            $fp = fopen($exportPath . 'company_' . $company->id . '_' . preg_replace('/[^a-z0-9]+/', '-', strtolower($company->name)) . '.csv', 'w+');
            fputcsv($fp, [
                'bssid',
                'vehicle',
            ]);
            $devices = Device::join('vehicles', 'vehicles.id', '=', 'devices.vehicle_id')
                             ->where('vehicles.company_id', $company->id)
                             ->orderBy('devices.bssid', 'asc')
                             ->get();
            foreach ($devices as $device) {
                fputcsv($fp, [
                    strtoupper($device->bssid),
                    $device->vehicle_name,
                ]);
            }
            fclose($fp);
        }
    }
}
