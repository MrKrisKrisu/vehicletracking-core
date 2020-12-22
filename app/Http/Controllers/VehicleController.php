<?php

namespace App\Http\Controllers;

use App\Company;
use App\IgnoredNetwork;
use App\ScanDevice;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Scan;
use App\Device;
use App\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehicleController extends Controller {

    public function render(Request $request): Renderable {
        $hiddenBssids = Device::join('vehicles', 'vehicles.id', '=', 'devices.vehicle_id')
                              ->join('companies', 'companies.id', '=', 'vehicles.company_id')
                              ->where('companies.name', 'Stationary')
                              ->select('devices.bssid');

        $hiddenSsids = IgnoredNetwork::select('ssid');
        $hiddenBssids2 = Device::where('ignore', 1)
                               ->select('bssid');

        $lastScansQ = Scan::with(['device', 'scanDevice'])
                          ->whereNotIn('bssid', $hiddenBssids)
                          ->whereNotIn('bssid', $hiddenBssids2)
                          ->whereNotIn('ssid', $hiddenSsids)
                          ->orderBy('created_at', 'desc')
                          ->limit(80);

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

        if($device == null)
            abort(204);

        $count = Device::join('scans', 'devices.bssid', '=', 'scans.bssid')
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
                       ->get()
                       ->count();

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

    public function ignoreDevice(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'bssid' => ['required', 'exists:devices,bssid'],
                                            'ssid'  => ['required', 'exists:devices,ssid'],
                                            'ban'   => ['required', Rule::in(['bssid', 'ssid'])]
                                        ]);

        if($validated['ban'] == 'bssid') {
            Device::where('bssid', $validated['bssid'])->update([
                                                                    'ignore' => 1
                                                                ]);
            return back()->with('alert-success', 'Das Netzwerk wird jetzt ignoriert.');
        } elseif($validated['ban'] == 'ssid') {
            IgnoredNetwork::create([
                                       'ssid' => $validated['ssid']
                                   ]);
            return back()->with('alert-success', 'Der Netzwerkname wird jetzt ignoriert.');
        }
    }

    public function renderIgnored() {
        return view('ignored', [
            'bssid' => Device::where('ignore', 1)->orderBy('updated_at', 'desc')->paginate(),
            'ssid'  => IgnoredNetwork::orderBy('created_at', 'desc')->paginate()
        ]);
    }

    public function unbanSSID(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'ssid' => ['required', 'exists:ignored_networks,ssid']
                                        ]);

        IgnoredNetwork::find($validated['ssid'])->delete();

        return back();
    }

    public function unbanBSSID(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'bssid' => ['required', 'exists:devices,bssid']
                                        ]);

        Device::where('bssid', $validated['bssid'])->update([
                                                                'ignore' => 0
                                                            ]);

        return back();
    }
}
