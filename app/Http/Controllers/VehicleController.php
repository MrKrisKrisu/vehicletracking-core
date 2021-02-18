<?php

namespace App\Http\Controllers;

use App\Company;
use App\Device;
use App\IgnoredNetwork;
use App\Scan;
use App\Vehicle;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class VehicleController extends Controller {

    public function render(Request $request): Renderable {
        $hiddenBssids = Device::join('vehicles', 'vehicles.id', '=', 'devices.vehicle_id')
                              ->join('companies', 'companies.id', '=', 'vehicles.company_id')
                              ->where('companies.name', 'Stationary')
                              ->select('devices.bssid');

        $hiddenSsids = IgnoredNetwork::select('ssid');

        $lastScansQ = Scan::join('devices', 'devices.bssid', '=', 'scans.bssid')
                          ->with(['device', 'device.vehicle', 'device.vehicle.company', 'scanDevice'])
                          ->where('devices.ignore', 0)
                          ->whereNotIn('scans.bssid', $hiddenBssids)
                          ->whereNotIn('scans.ssid', $hiddenSsids)
                          ->orderBy('scans.created_at', 'desc');

        if(isset($request->device))
            $lastScansQ->where('scanDeviceId', $request->device);

        $lastScans = $lastScansQ->paginate(80)->onEachSide(0);

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

    public static function verify(): Renderable|RedirectResponse {

        $devices = Device::with(['scans'])
                         ->join('scans', 'devices.bssid', '=', 'scans.bssid')
                         ->where('devices.vehicle_id', null)
                         ->where('scans.vehicle_name', '<>', null)
                         ->groupBy('scans.bssid')
                         ->having(DB::raw('count(*)'), '>', 1)
                         ->select('devices.*')
                         ->get()
                         ->filter(function($device) {
                             if($device->scans->where('vehicle_name', '<>', null)->count() < 2)
                                 return false;
                             $lastScan = $device->scans->where('vehicle_name', '<>', null)->max('created_at');
                             return $device->moveVerifyUntil == null || ($lastScan != null && $lastScan->isAfter($device->moveVerifyUntil));
                         })
                         ->sortByDesc(function($device) {
                             return $device->scans->where('vehicle_name', '<>', null)->max('created_at');
                         });

        $count = $devices->count();
        $device = $devices->first();

        if($device == null)
            return redirect()->route('dashboard')
                             ->with('alert-info', 'Es gibt aktuell keine Geräte zum verifizieren.');

        $locationScans = $device->scans->where('latitude', '<>', null)->where('longitude', '<>', null);

        return view('todo', [
            'device'        => $device,
            'count'         => $count,
            'companies'     => Company::all(),
            'locationScans' => $locationScans
        ]);
    }

    public static function saveVerify(Request $request): Renderable {
        if(isset($request->modified_vehicle_name)) {

            $validated = $request->validate([
                                                'modified_scan_id'      => ['required', 'integer', 'exists:scans,id'],
                                                'modified_vehicle_name' => ['required']
                                            ]);

            $scan = Scan::find($validated['modified_scan_id']);
            $scan->modified_vehicle_name = str_replace("\r\n", ',', $validated['modified_vehicle_name']);
            $scan->update();

        }

        return self::verify();
    }

    public static function renderVehicle(int $vehicleId, int $page = 1): Renderable {
        $vehicle = Vehicle::with(['company', 'devices.scans'])->findOrFail($vehicleId);

        if($vehicle->company->name == 'Stationary')
            abort(404);

        $allScans = collect();
        foreach($vehicle->devices as $device)
            $allScans = $allScans->merge($device->scans);

        if($allScans->count() == 0)
            abort(404);

        $found = $allScans->groupBy(function($scan) {
            return $scan->created_at->format('Y-m-d H:i');
        })->map(function($scans) {
            return $scans->sortByDesc('latitude')->first();
        })->sortByDesc('created_at')->forPage($page, 15);

        $lastPosition = $allScans->whereNotNull('latitude')
                                 ->whereNotNull('longitude')
                                 ->sortByDesc('created_at')
                                 ->first();

        $dateCount = $allScans->sortByDesc('created_at')
                              ->groupBy(function($scan) {
                                  return $scan->created_at->format('Y-m-j');
                              })
                              ->map(function($scans) {
                                  return $scans->count();
                              });

        return view('vehicle', [
            'vehicle'      => $vehicle,
            'found'        => $found,
            'lastPosition' => $lastPosition,
            'dateCount'    => $dateCount
        ]);
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

    public function saveIgnoredNetwork(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'ssid'     => ['required'],
                                            'contains' => ['nullable']
                                        ]);

        $validated['contains'] = isset($validated['contains']) && $validated['contains'] == 'on' ? 1 : 0;
        IgnoredNetwork::create($validated);

        return back();
    }

    public function createVehicle(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'company_id'   => ['required', 'exists:companies,id'],
                                            'vehicle_name' => ['required', 'max:255'],
                                            'type'         => ['nullable', Rule::in(['bus', 'tram', 'train'])],
                                        ]);

        Vehicle::create($validated);

        return back()->with('alert-success', 'Fahrzeug wurde erstellt.');
    }

    public function assignVehicle(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'id'         => ['required', 'exists:devices,id'],
                                            'vehicle_id' => ['required', 'exists:vehicles,id'],
                                        ]);

        $validated['moveVerifyUntil'] = null;

        Device::find($validated['id'])->update($validated);

        return back()->with('alert-success', 'Das Gerät wurde dem Fahrzeug zugewiesen.');
    }

    public function skipAssignment(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'id' => ['required', 'exists:devices,id']
                                        ]);

        Device::find($validated['id'])->update([
                                                   'moveVerifyUntil' => Carbon::now()
                                               ]);

        return back()->with('alert-success', 'Die Zuweisung wurde bis zur nächsten Sichtung aufgeschoben.');
    }
}
