<?php

namespace App\Http\Controllers;

use App\Company;
use App\IgnoredNetwork;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Scan;
use App\Device;
use App\Vehicle;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller {

    public function render(Request $request): Renderable {
        $hiddenBssids = Device::join('vehicles', 'vehicles.id', '=', 'devices.vehicle_id')
                              ->join('companies', 'companies.id', '=', 'vehicles.company_id')
                              ->where('companies.name', 'Stationary')
                              ->select('devices.bssid');

        $hiddenSsids = IgnoredNetwork::select('ssid');

        $lastScansQ = Scan::with(['device'])
                          ->whereNotIn('bssid', $hiddenBssids)
                          ->whereNotIn('ssid', $hiddenSsids)
                          ->orderBy('created_at', 'desc')->limit(80);

        if(isset($request->device))
            $lastScansQ->where('scanDeviceId', $request->device);

        $lastScans = $lastScansQ->get();

        $possibleVehicles = [];
        $bssidList = [];
        foreach($lastScans as $scan)
            if(!in_array($scan->bssid, $bssidList))
                $bssidList[] = $scan->bssid;

        $scans = Scan::whereIn('bssid', $bssidList)
                     ->where('vehicle_name', '<>', null)
                     ->groupBy('bssid', 'vehicle_name')
                     ->select('bssid', 'vehicle_name')
                     ->get();

        foreach($scans as $scan) {
            if(!isset($possibleVehicles[$scan->bssid]))
                $possibleVehicles[$scan->bssid] = [];

            $scanPos = $scan->possibleVehiclesRaw();
            foreach($scanPos as $p) {
                if(!in_array($p, $possibleVehicles[$scan->bssid]))
                    $possibleVehicles[$scan->bssid][] = $p;
            }
        }

        return view('overview', [
            'lastScan'         => $lastScans,
            'possibleVehicles' => $possibleVehicles
        ]);
    }

    public function saveVehicle(Request $request): RedirectResponse {
        if(isset($request->scans)) {
            foreach($request->scans as $scanID => $v) {
                $scan = Scan::where('id', $scanID)->first();
                $scan->vehicle_name = $request->vehicle_name;
                $scan->save();
            }
        }

        return back();
    }

    public static function verify() {

        $device = Device::join('scans', 'devices.bssid', '=', 'scans.bssid')
                        ->where('devices.vehicle_id', null)
                        ->where(function($query) {
                            $query->where('devices.moveVerifyUntil', '<', Carbon::now())
                                  ->orWhere('devices.moveVerifyUntil', null);
                        })
                        ->where('scans.vehicle_name', '<>', null)
                        ->groupBy('scans.bssid')
                        ->having(DB::raw('count(*)'), '>', 1)
                        ->select('devices.*')
                        ->orderBy('devices.lastSeen', 'DESC')
                        ->first();

        $count = count(Device::join('scans', 'devices.bssid', '=', 'scans.bssid')
                             ->where('devices.vehicle_id', null)
                             ->where(function($query) {
                                 $query->where('devices.moveVerifyUntil', '<', Carbon::now())
                                       ->orWhere('devices.moveVerifyUntil', null);
                             })
                             ->where('scans.vehicle_name', '<>', null)
                             ->groupBy('scans.bssid')
                             ->having(DB::raw('count(*)'), '>', 1)
                             ->select('devices.*')
                             ->orderBy('devices.lastSeen', 'DESC')
                             ->get());

        if($device == null)
            abort(204);

        $scans = Scan::where('bssid', $device->bssid)->where('vehicle_name', '<>', null)->get();

        return view('todo', [
            'device'    => $device,
            'count'     => $count,
            'scans'     => $scans,
            'companies' => Company::all()
        ]);
    }

    public static function saveVerify(Request $request) {
        if(isset($request->modified_vehicle_name)) {

            $validated = $request->validate([
                                                'modified_scan_id'      => ['required', 'integer', 'exists:scans,id'],
                                                'modified_vehicle_name' => ['required']
                                            ]);

            $scan = Scan::find($validated['modified_scan_id']);
            $scan->modified_vehicle_name = str_replace("\r\n", ',', $validated['modified_vehicle_name']);
            $scan->update();

        } elseif($request->action == 'save') {

            $validated = $request->validate([
                                                'company_id'   => ['required', 'integer', 'exists:companies,id'],
                                                'vehicle_name' => ['required'],
                                                'bssid'        => ['required', 'exists:devices,bssid'],
                                            ]);

            $vehicle = Vehicle::where('company_id', $validated['company_id'])->where('vehicle_name', $validated['vehicle_name'])->first();

            if($vehicle == null) {
                $vehicle = new Vehicle();
                $vehicle->company_id = $validated['company_id'];
                $vehicle->vehicle_name = $validated['vehicle_name'];
                $vehicle->save();
            }

            $device = Device::where('bssid', $validated['bssid'])->first();
            $device->vehicle_id = $vehicle->id;
            $device->update();

        } elseif($request->action == 'notVerifiable') {
            $validated = $request->validate([
                                                'bssid' => ['required', 'exists:devices,bssid'],
                                            ]);

            $device = Device::where('bssid', $validated['bssid'])->firstOrFail();
            $device->moveVerifyUntil = Carbon::now()->addDays(7);
            $device->update();
        }

        return self::verify();
    }

    public static function renderVehicle($vehicle_id) {
        //$vehicle = Vehicle::with('company')->where('id', $vehicle_id)->first();
        $vehicle = DB::table('vehicles')->where('vehicles.id', '=', $vehicle_id)
                     ->join('companies', 'vehicles.company_id', '=', 'companies.id')
                     ->select('vehicles.*', 'companies.name as companyName')
                     ->first();
        $occursToday = DB::select("SELECT created_at FROM `scans` WHERE `bssid` IN (SELECT bssid FROM `devices` WHERE `vehicle_id` = :vehicleID) AND DATE(created_at) = DATE(NOW()) ORDER BY `scans`.`created_at` DESC LIMIT 50", ['vehicleID' => $vehicle_id]);
        $occursYesterday = DB::select("SELECT created_at FROM `scans` WHERE `bssid` IN (SELECT bssid FROM `devices` WHERE `vehicle_id` = :vehicleID) AND DATE(created_at) = DATE(NOW() - INTERVAL 1 DAY) ORDER BY `scans`.`created_at` DESC LIMIT 50", ['vehicleID' => $vehicle_id]);
        $occursOlder = DB::select("SELECT created_at FROM `scans` WHERE `bssid` IN (SELECT bssid FROM `devices` WHERE `vehicle_id` = :vehicleID) AND DATE(created_at) < DATE(NOW() - INTERVAL 1 DAY) ORDER BY `scans`.`created_at` DESC LIMIT 50", ['vehicleID' => $vehicle_id]);

        return view('vehicle',
                    [
                        'vehicle'         => $vehicle,
                        'occursToday'     => $occursToday,
                        'occursYesterday' => $occursYesterday,
                        'occursOlder'     => $occursOlder
                    ]
        );
    }

    public static function getPossibleVehicles(string $bssid) {
        $scans = Scan::where('bssid', $bssid)
                     ->where('vehicle_name', '<>', null)
                     ->groupBy('vehicle_name')
                     ->select('vehicle_name')
                     ->get();

        $data = [];
        foreach($scans as $scan) {
            $scanPos = $scan->possibleVehiclesRaw();
            foreach($scanPos as $p) {
                if(!in_array($p, $data))
                    $data[] = $p;
            }
        }
        sort($data);

        return $data;
    }

    public function renderCompanies(): Renderable {
        return view('companies', [
            'companies' => Company::with(['vehicles'])->where('name', '<>', 'Stationary')->get()
        ]);
    }

    public function renderCompany(int $id): Renderable {
        return view('company', [
            'company' => Company::with(['vehicles', 'vehicles.devices'])->findOrFail($id)
        ]);
    }
}
