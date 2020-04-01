<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Scan;
use App\Device;
use App\Vehicle;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public static function render()
    {
        $lastScans = Scan::orderBy('created_at', 'desc')->limit(30)->get();
        $newDevices = Device::orderBy('firstSeen', 'desc')->limit(30)->get();
        //$vehicles = Device::with(['vehicle'])->where('vehicle_id', '<>', null)->orderByDesc('lastSeen')->limit(10) ->get();
        $vehicles = DB::select("SELECT v.id, v.vehicle_name, s.created_at FROM scans s, vehicles v, devices d WHERE s.bssid IN (SELECT bssid FROM devices WHERE vehicle_id IS NOT NULL) AND s.bssid = d.bssid AND d.vehicle_id = v.id ORDER BY s.created_at DESC LIMIT 50");

        return view('overview', ['lastScan' => $lastScans, 'newDevices' => $newDevices, 'lastVehicles' => $vehicles]);
    }

    public static function saveVehicle(Request $request) {
        $scan = Scan::where('id', $request->scanID)->first();
        $scan->vehicle_name = $request->vehicle_name;
        $scan->save();

        return self::render();
    }

    public static function renderVehicle($vehicle_id)
    {
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
                'vehicle' => $vehicle,
                'occursToday' => $occursToday,
                'occursYesterday' => $occursYesterday,
                'occursOlder' => $occursOlder
            ]
        );
    }

    public static function assign()
    {
        $scansToCheck = DB::select("SELECT * FROM `scans` WHERE `bssid` = (SELECT bssid FROM `scans` WHERE bssid IN (SELECT bssid FROM `devices` WHERE `vehicle_id` IS NULL) AND vehicle_name > 0 AND bssid IN (SELECT DISTINCT bssid FROM `scans` WHERE `vehicle_name` > 0) GROUP BY bssid ORDER BY COUNT(*) DESC LIMIT 1)");

        $bssid = null;
        foreach ($scansToCheck as $check) {
            $bssid = $check->bssid;
            break;
        }

        return view('assign', ['scansToCheck' => $scansToCheck, 'bssid' => $bssid]);
    }

    public static function saveAssignee(Request $request)
    {
        $vehicle = Vehicle::where([
            'company_id', $request->company_id,
            'vehicle_name', $request->vehicle_name
        ])->first();
dd($vehicle);
        if ($vehicle == null) {
            dd($vehicle);
        }

        return self::assign();
    }
}
