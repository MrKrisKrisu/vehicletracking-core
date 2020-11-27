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
