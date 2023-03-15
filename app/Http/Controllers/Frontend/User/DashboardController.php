<?php

namespace App\Http\Controllers\Frontend\User;

use App\Device;
use App\Http\Controllers\Controller;
use App\IgnoredNetwork;
use App\Scan;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller {

    public function renderDashboard(): View {
        //TODO: Quick and dirty; code duplication!

        $lastScansQ = Scan::join('devices', 'devices.bssid', '=', 'scans.bssid')
                          ->with(['device', 'device.vehicle', 'device.vehicle.company', 'scanDevice'])
                          ->whereIn('scanDeviceId', auth()->user()->scanDevices->pluck('id'))
                          ->where('scans.created_at', '>', Carbon::now()->subMonths(3)->toIso8601String())
                          ->select('scans.*')
                          ->orderByDesc('scans.created_at');

        if(session()->get('show-verified', '0') == '0') {
            $lastScansQ->whereNull('devices.vehicle_id');
        }

        if(session()->get('show-hidden', '0') != '1') {
            $lastScansQ->where('scans.hidden', 0);
        }

        if(session()->get('show-ignored', '0') != '1') {
            $hiddenBssids = Device::join('vehicles', 'vehicles.id', '=', 'devices.vehicle_id')
                                  ->join('companies', 'companies.id', '=', 'vehicles.company_id')
                                  ->where('companies.name', 'Stationary')
                                  ->select('devices.bssid');

            $hiddenSsids = IgnoredNetwork::select('ssid');

            $lastScansQ->whereNotIn('scans.bssid', $hiddenBssids)
                       ->whereNotIn('scans.ssid', $hiddenSsids)
                       ->where('devices.ignore', '0');
        }

        if(isset($request->device)) {
            $lastScansQ->where('scanDeviceId', $request->device);
        }

        $lastScans = $lastScansQ->simplePaginate(80);

        $possibleVehicles = [];
        $bssidList        = [];
        foreach($lastScans as $scan) {
            if(!in_array($scan->bssid, $bssidList)) {
                $bssidList[] = $scan->bssid;
            }
        }

        $scans = Scan::whereIn('bssid', $bssidList)
                     ->where('vehicle_name', '<>', null)
                     ->groupBy('bssid', 'vehicle_name', 'modified_vehicle_name')
                     ->select('bssid', 'vehicle_name', 'modified_vehicle_name')
                     ->get();

        foreach($scans as $scan) {
            if(!isset($possibleVehicles[$scan->bssid])) {
                $possibleVehicles[$scan->bssid] = [];
            }

            $scanPos = $scan->possibleVehiclesRaw();
            foreach($scanPos as $p) {
                if(!in_array($p, $possibleVehicles[$scan->bssid])) {
                    $possibleVehicles[$scan->bssid][] = $p;
                }
            }
        }

        return view('user.dashboard', [
            'lastScan'         => $lastScans,
            'possibleVehicles' => $possibleVehicles,
        ]);
    }
}
