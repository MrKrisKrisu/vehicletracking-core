<?php

namespace App\Http\Controllers;

use App\Company;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Scan;
use App\Device;
use App\Vehicle;
use Illuminate\Support\Facades\DB;

class VehicleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function render()
    {
        $lastScans = Scan::orderBy('updated_at', 'desc')->limit(80)->get();

        $possibleVehicles = [];
        $bssidList = [];
        foreach ($lastScans as $scan)
            if (!in_array($scan->bssid, $bssidList))
                $bssidList[] = $scan->bssid;

        $scans = Scan::whereIn('bssid', $bssidList)
            ->where('vehicle_name', '<>', null)
            ->groupBy('bssid', 'vehicle_name')
            ->select('bssid', 'vehicle_name')
            ->get();

        foreach ($scans as $scan) {
            if (!isset($possibleVehicles[$scan->bssid]))
                $possibleVehicles[$scan->bssid] = [];

            $scanPos = $scan->possibleVehiclesRaw();
            foreach ($scanPos as $p) {
                if (!in_array($p, $possibleVehicles[$scan->bssid]))
                    $possibleVehicles[$scan->bssid][] = $p;
            }
        }

        return view('overview', [
            'lastScan' => $lastScans,
            'possibleVehicles' => $possibleVehicles
        ]);
    }

    public static function saveVehicle(Request $request)
    {
        if (isset($request->scans)) {
            foreach ($request->scans as $scanID => $v) {
                $scan = Scan::where('id', $scanID)->first();
                $scan->vehicle_name = $request->vehicle_name;
                $scan->save();
            }
        }

        return self::render();
    }

    public static function verify()
    {

        $device = Device::join('scans', 'devices.bssid', '=', 'scans.bssid')
            ->where('devices.vehicle_id', null)
            ->where(function ($query) {
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
            ->where(function ($query) {
                $query->where('devices.moveVerifyUntil', '<', Carbon::now())
                    ->orWhere('devices.moveVerifyUntil', null);
            })
            ->where('scans.vehicle_name', '<>', null)
            ->groupBy('scans.bssid')
            ->having(DB::raw('count(*)'), '>', 1)
            ->select('devices.*')
            ->orderBy('devices.lastSeen', 'DESC')
            ->get());

        if ($device == null)
            abort(204);

        $scans = Scan::where('bssid', $device->bssid)->where('vehicle_name', '<>', null)->get();

        return view('todo', [
            'device' => $device,
            'count' => $count,
            'scans' => $scans,
            'companies' => Company::all()
        ]);
    }

    public static function saveVerify(Request $request)
    {
        if (isset($request->modified_vehicle_name)) {

            $validated = $request->validate([
                'modified_scan_id' => ['required', 'integer', 'exists:scans,id'],
                'modified_vehicle_name' => ['required']
            ]);

            $scan = Scan::find($validated['modified_scan_id']);
            $scan->modified_vehicle_name = $validated['modified_vehicle_name'];
            $scan->update();

        } else if ($request->action == 'save') {

            $validated = $request->validate([
                'company_id' => ['required', 'integer', 'exists:companies,id'],
                'vehicle_name' => ['required'],
                'bssid' => ['required', 'exists:devices,bssid'],
            ]);

            $vehicle = Vehicle::where('company_id', $validated['company_id'])->where('vehicle_name', $validated['vehicle_name'])->first();

            if ($vehicle == null) {
                $vehicle = new Vehicle();
                $vehicle->company_id = $validated['company_id'];
                $vehicle->vehicle_name = $validated['vehicle_name'];
                $vehicle->save();
            }

            $device = Device::where('bssid', $validated['bssid'])->first();
            $device->vehicle_id = $vehicle->id;
            $device->update();

        } else if ($request->action == 'notVerifiable') {
            $validated = $request->validate([
                'bssid' => ['required', 'exists:devices,bssid'],
            ]);

            $device = Device::where('bssid', $validated['bssid'])->firstOrFail();
            $device->moveVerifyUntil = Carbon::now()->addDays(7);
            $device->update();
        }

        return self::verify();
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

    public static function renderPublic($device_id)
    {
        $scans = Scan::join('devices', 'scans.bssid', '=', 'devices.bssid')
            ->where('scanDeviceId', $device_id)
            ->select(
                'scans.created_at',
                DB::raw('(SELECT vehicle_name FROM `vehicles` WHERE `id` = devices.vehicle_id) as verifiedName'),
                DB::raw('(SELECT GROUP_CONCAT(vehicle_name SEPARATOR \',\') FROM `scans` WHERE scans.bssid LIKE devices.bssid AND scans.vehicle_name IS NOT NULL) as possibleVehicles')
            )
            ->orderBy('created_at', 'desc')
            ->limit(100)
            ->get();

        return view('public', ['scans' => $scans]);
    }

    public static function getPossibleVehicles(string $bssid)
    {
        $scans = Scan::where('bssid', $bssid)
            ->where('vehicle_name', '<>', null)
            ->groupBy('vehicle_name')
            ->select('vehicle_name')
            ->get();

        $data = [];
        foreach ($scans as $scan) {
            $scanPos = $scan->possibleVehiclesRaw();
            foreach ($scanPos as $p) {
                if (!in_array($p, $data))
                    $data[] = $p;
            }
        }
        sort($data);

        return $data;
    }
}
