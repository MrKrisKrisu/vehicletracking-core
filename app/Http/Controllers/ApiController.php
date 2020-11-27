<?php

namespace App\Http\Controllers;

use App\Company;
use App\Scan;
use App\ScanDevice;
use App\Vehicle;
use Carbon\Carbon;
use App\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    public function registerNew()
    {
        $scan_device = ScanDevice::create([
                                              'token' => Str::uuid()
                                          ]);

        return response((string)$scan_device->token, 200, ['Content-type: text/plain']);
    }

    /**
     * @deprecated Use POST: /api/v1/scan instead
     */
    public function scan(Request $request)
    {
        $token      = $request->header('X-Api-Token');
        $deviceID   = null;
        $scanDevice = null;
        if ($token != null) {
            $scanDevice = ScanDevice::where('token', $token)->first();
            if ($scanDevice != null)
                $deviceID = $scanDevice->id;
        }

        $data  = $request->getContent();
        $jData = json_decode($data);

        $vehicles_secured   = [];
        $vehicles_estimated = [];

        foreach ($jData as $network) {
            try {
                $scanData = [
                    'vehicle_name' => $network->vehicle_id ?? null,
                    'bssid'        => $network->bssid ?? null,
                    'ssid'         => $network->ssid ?? null,
                    'signal'       => $network->signal ?? null,
                    'quality'      => $network->quality ?? null,
                    'frequency'    => $network->frequency ?? null,
                    'bitrates'     => $network->bitrates ?? null,
                    'encrypted'    => $network->encrypted ?? null,
                    'channel'      => $network->channel ?? null,
                    'latitude'     => $network->latitude ?? null,
                    'longitude'    => $network->longitude ?? null,
                    'scanDeviceId' => $deviceID
                ];

                if (isset($network->created_at))
                    $scanData['created_at'] = $network->created_at;

                $scan = Scan::create($scanData);

                $device = Device::updateOrCreate([
                                                     'bssid' => $scan->bssid
                                                 ], [
                                                     'ssid'     => $scan->ssid,
                                                     'lastSeen' => Carbon::now(),
                                                 ]);

                if ($device != null && $device->vehicle_id != null) {
                    $vehicles_secured[] = [
                        'company_name' => $device->vehicle->company->name,
                        'vehicle_name' => $device->vehicle->vehicle_name
                    ];
                } elseif ($device != null) {
                    $scans = Scan::where('bssid', $scan->bssid)->where('vehicle_name', '<>', null)->get();
                    foreach ($scans as $scanElement) {
                        $spl = explode(',', $scanElement->vehicle_name);
                        foreach ($spl as $splElement)
                            if (!in_array($splElement, $vehicles_estimated))
                                $vehicles_estimated[] = $splElement;
                    }
                }
            } catch (\Exception $e) {
                report($e);
            }
        }
        return response(['status' => 'ok', 'vehicles' => ['secured' => $vehicles_secured, 'estimated' => $vehicles_estimated]]);
    }

    public function getVehicles($company_id, $vehicle_id)
    {
        return Vehicle::where([
                                  'company_id'   => $company_id,
                                  'vehicle_name' => $vehicle_id
                              ])->firstOrFail()->devices;
    }

    public function getCompany($company_id)
    {
        return Company::where([
                                  'id' => $company_id
                              ])->first();
    }

    public function prefix()
    {
        return \App\DevicePrefix::select('prefix', 'description')->get();
    }

    public function locate(Request $request)
    {
        $verifiedVehicles = [];
        $possibleVehicles = [];

        $json = json_decode($request->getContent());

        if (!is_array($json))
            return response('Request contains no valid json.', 400);

        foreach ($json as $bssid) {
            $device = DB::table('devices')->where('bssid', $bssid)->where('vehicle_id', '<>', null)->first();
            if ($device !== null) {
                //There are verified rows about the found bssid
                $vehicle = DB::table('vehicles')->where('id', $device->vehicle_id)->first();
                if ($vehicle != null && !in_array($vehicle->vehicle_name, $verifiedVehicles))
                    $verifiedVehicles[] = $vehicle->vehicle_name;
            } else {
                //There are no verified rows about the found bssid, so we will try to analyse the given data
                $result = DB::table('scans')->select('vehicle_name', DB::raw('COUNT(*) as cnt'))
                            ->where('bssid', $bssid)
                            ->where('vehicle_name', '<>', null)
                            ->where('vehicle_name', '<>', 0)
                            ->groupBy('vehicle_name')
                            ->orderBy('cnt', 'DESC')
                            ->limit(1)
                            ->first();

                if ($result != null && !in_array($result->vehicle_name, $possibleVehicles))
                    $possibleVehicles[] = $result->vehicle_name;
            }
        }

        return [
            'verified' => $verifiedVehicles,
            'possible' => $possibleVehicles
        ];
    }

    public function getNewVehicles(Request $request)
    {
        $newDevices = Device::orderBy('firstSeen', 'desc')->limit(30)->get();

        $data = [];
        foreach ($newDevices as $device) {
            $data[] = [
                'ssid'      => $device->ssid,
                'last_seen' => [
                    'display'   => $device->firstSeen->diffForHumans(),
                    'timestamp' => $device->firstSeen
                ]
            ];
        }

        return ['data' => $data];
    }

    public function getLastSeenVehicles(Request $request)
    {
        $lastScans = Device::with('vehicle')
                           ->where('vehicle_id', '<>', null)
                           ->orderBy('lastSeen', 'desc')
                           ->limit(100)
                           ->get();

        $data = [];
        foreach ($lastScans as $scan) {
            $data[] = [
                'vehicle_name' => $scan->vehicle->vehicle_name,
                'last_seen'    => [
                    'display'   => $scan->lastSeen->diffForHumans(),
                    'timestamp' => $scan->lastSeen
                ]
            ];
        }

        return ['data' => $data];
    }
}
