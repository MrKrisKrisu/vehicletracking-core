<?php

namespace App\Http\Controllers;

use App\Device;
use Illuminate\Http\Request;

class ApiController extends Controller
{

    public function getNewVehicles(Request $request)
    {
        $newDevices = Device::orderBy('firstSeen', 'desc')
                            ->limit(30)
                            ->get()
                            ->map(function ($device) {
                                return [
                                    'ssid'      => $device->ssid,
                                    'last_seen' => [
                                        'display'   => $device->firstSeen->diffForHumans(),
                                        'timestamp' => $device->firstSeen
                                    ]
                                ];
                            });

        return ['data' => $newDevices];
    }

    public function getLastSeenVehicles(Request $request)
    {
        $lastScans = Device::with('vehicle')
                           ->where('vehicle_id', '<>', null)
                           ->orderBy('lastSeen', 'desc')
                           ->limit(100)
                           ->get()
                           ->map(function ($scan) {
                               return [
                                   'vehicle_name' => $scan->vehicle->vehicle_name,
                                   'last_seen'    => [
                                       'display'   => $scan->lastSeen->diffForHumans(),
                                       'timestamp' => $scan->lastSeen
                                   ]
                               ];
                           });

        return ['data' => $lastScans];
    }
}
