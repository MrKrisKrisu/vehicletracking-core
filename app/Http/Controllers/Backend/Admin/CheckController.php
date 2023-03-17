<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Device;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class CheckController extends Controller {

    public static function getDevicesToCheck(string $ssidQuery = null): Collection {
        $devicesQ = Device::join('scans', 'devices.bssid', '=', 'scans.bssid')
                          ->whereNull('devices.vehicle_id')
                          ->where('devices.blocked', 0)
                          ->whereNotNull('scans.vehicle_name')
                          ->groupBy([
                                        'devices.id', 'devices.bssid', 'devices.ssid', 'devices.vehicle_id',
                                        'devices.moveVerifyUntil', 'devices.ignore', 'devices.firstSeen', 'devices.lastSeen',
                                        'devices.created_at', 'devices.updated_at',
                                    ])
                          ->having(DB::raw('COUNT(*)'), '>', 2)
                          ->select([
                                       'devices.id', 'devices.bssid', 'devices.ssid', 'devices.vehicle_id',
                                       'devices.moveVerifyUntil', 'devices.ignore', 'devices.firstSeen', 'devices.lastSeen',
                                       'devices.created_at', 'devices.updated_at',
                                       DB::raw('MAX(scans.created_at) AS lastScan'),
                                   ])
                          ->orderByDesc($validated['orderBy'] ?? DB::raw('MAX(scans.created_at)'));

        if ($ssidQuery !== null) {
            $devicesQ->where('devices.ssid', 'like', '%' . $ssidQuery . '%');
        }

        return $devicesQ->get()
                        ->filter(function ($device) {
                            return $device->moveVerifyUntil === null ||
                                   ($device->lastScan !== null && Carbon::parse($device->lastScan)->isAfter($device->moveVerifyUntil));
                        });
    }
}
