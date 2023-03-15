<?php

namespace App\Http\Controllers;

use App\Company;
use App\Device;
use App\IgnoredNetwork;
use App\Scan;
use App\Vehicle;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class VehicleController extends Controller {

    public function render(Request $request): View {
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

        return view('overview', [
            'lastScan'         => $lastScans,
            'possibleVehicles' => $possibleVehicles,
        ]);
    }

    public function saveVehicle(Request $request): RedirectResponse {
        if(isset($request->scans)) {
            foreach($request->scans as $scanID => $v) {
                $scan = Scan::find($scanID);
                $this->authorize('update', $scan);
                $scan->update(['vehicle_name' => $request->vehicle_name]);
            }
        }

        return back()->with('query', $request->vehicle_name);
    }

    public static function verify(Request $request): View|RedirectResponse {
        if(auth()->user()->id !== 1) {
            abort(403);
        }

        $validated = $request->validate([
                                            'query'   => ['nullable', 'string', 'max:32'],
                                            'orderBy' => ['nullable', Rule::in(['ssid', 'bssid'])],
                                        ]);

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

        if(isset($validated['query'])) {
            $devicesQ->where('devices.ssid', 'like', '%' . $validated['query'] . '%');
        }

        $devices = $devicesQ->get()
                            ->filter(function($device) {
                                return $device->moveVerifyUntil === null ||
                                       ($device->lastScan !== null && Carbon::parse($device->lastScan)->isAfter($device->moveVerifyUntil));
                            });

        $count  = $devices->count();
        $device = $devices->first();

        if($device === null) {
            return redirect()->route('admin.dashboard')
                             ->with('alert-info', 'Es gibt aktuell keine Geräte zum verifizieren.');
        }

        $device->load(['scans.scanDevice']);

        $locationScans = $device->scans->where('latitude', '<>', null)->where('longitude', '<>', null);

        $scansToCheck = $device->scans
            ->whereNotNull('vehicle_name');
        //->groupBy(['vehicle_name', 'created_at']) //Filter duplicate scans like airport
        //->map(function(Collection $scans) {
        //    return $scans->first()?->first();
        //});

        return view('todo', [
            'device'        => $device,
            'scans'         => $scansToCheck,
            'count'         => $count,
            'companies'     => Company::all(),
            'locationScans' => $locationScans,
        ]);
    }

    public static function renderVehicle(int $vehicleId, int $page = 1): Renderable {
        $vehicle = Vehicle::with(['company', 'devices.scans'])->findOrFail($vehicleId);

        if($vehicle->company->name === 'Stationary') {
            abort(404);
        }

        $allScans = collect();
        foreach($vehicle->devices as $device) {
            $allScans = $allScans->merge($device->scans);
        }

        if($allScans->count() === 0) {
            abort(404);
        }

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
            'dateCount'    => $dateCount,
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
                if(!in_array($p, $data)) {
                    $data[] = $p;
                }
            }
        }
        sort($data);

        return $data;
    }

    public function renderCompanies(): View {
        return view('companies', [
            'companies' => Company::with(['vehicles'])
                                  ->where('name', '<>', 'Stationary')
                                  ->get()
                                  ->sortByDesc(function($company) {
                                      return $company->vehicles->count();
                                  }),
        ]);
    }

    public function renderCompany(int $id): View {
        $company = Company::with(['vehicles', 'vehicles.devices'])->findOrFail($id);

        $dateCount = DB::table('scans')
                       ->join('devices', 'devices.bssid', '=', 'scans.bssid')
                       ->join('vehicles', 'vehicles.id', '=', 'devices.vehicle_id')
                       ->join('companies', 'vehicles.company_id', '=', 'companies.id')
                       ->where('companies.id', $company->id)
                       ->where('scans.created_at', '>', Date::now()->subYear()->toDateString())
                       ->groupByRaw('DATE(created_at)')
                       ->select([
                                    DB::raw('DATE(scans.created_at) AS date'),
                                    DB::raw('COUNT(scans.created_at) AS count'),
                                ])
                       ->get()
                       ->groupBy('date')
                       ->map(function($row) {
                           return $row->first()->count;
                       });

        return view('company', [
            'company'   => $company,
            'dateCount' => $dateCount,
        ]);
    }

    /**
     * @throws Exception
     */
    public function ignoreDevice(Request $request): RedirectResponse {
        if(auth()->user()->id !== 1) {
            abort(403);
        }
        $validated = $request->validate([
                                            'bssid' => ['required', 'exists:devices,bssid'],
                                            'ssid'  => ['required', 'exists:devices,ssid'],
                                            'ban'   => ['required', Rule::in(['bssid', 'ssid'])],
                                        ]);

        if($validated['ban'] === 'bssid') {
            Device::where('bssid', $validated['bssid'])
                  ->update(['ignore' => 1]);
            return back()->with('alert-success', 'Das Netzwerk wird jetzt ignoriert.');
        }

        if($validated['ban'] === 'ssid') {

            if(in_array(strtolower($validated['ssid']), ['kvv-swlan', 'kvv-wlan', 'wifi@db', 'fahrgastfernsehen', 'uestra_regiobus_freewlan', 'wfb intern', 'westfalenbahn', 'wifionice', 'enno_wifi'])) {
                abort(403);
            }

            IgnoredNetwork::firstOrCreate(['ssid' => $validated['ssid']]);
            return back()->with('alert-success', 'Der Netzwerkname wird jetzt ignoriert.');
        }
        throw new Exception();
    }

    public function renderIgnored(): View {
        if(auth()->user()->id !== 1) {
            abort(403);
        }
        return view('ignored', [
            'bssid' => Device::where('ignore', 1)->orderBy('updated_at', 'desc')->paginate(),
            'ssid'  => IgnoredNetwork::orderBy('created_at', 'desc')->paginate(),
        ]);
    }

    public function unbanSSID(Request $request): RedirectResponse {
        if(auth()->user()->id !== 1) {
            abort(403);
        }
        $validated = $request->validate([
                                            'ssid' => ['required', 'exists:ignored_networks,ssid'],
                                        ]);

        IgnoredNetwork::find($validated['ssid'])->delete();

        return back();
    }

    public function unbanBSSID(Request $request): RedirectResponse {
        if(auth()->user()->id !== 1) {
            abort(403);
        }
        $validated = $request->validate([
                                            'bssid' => ['required', 'exists:devices,bssid'],
                                        ]);

        Device::where('bssid', $validated['bssid'])->update([
                                                                'ignore' => 0,
                                                            ]);

        return back();
    }

    public function saveIgnoredNetwork(Request $request): RedirectResponse {
        if(auth()->user()->id !== 1) {
            abort(403);
        }
        $validated = $request->validate([
                                            'ssid'     => ['required'],
                                            'contains' => ['nullable'],
                                        ]);

        $validated['contains'] = isset($validated['contains']) && $validated['contains'] === 'on' ? 1 : 0;
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
        $vehicle = Vehicle::find($validated['vehicle_id']);

        session()->put('lastVehicle', $vehicle);

        return back()->with('alert-success', 'Der AP wurde dem Fahrzeug zugewiesen.');
    }

    public function skipAssignment(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'id' => ['required', 'exists:devices,id'],
                                        ]);

        Device::find($validated['id'])->update([
                                                   'moveVerifyUntil' => Carbon::now(),
                                               ]);

        return back()->with('alert-success', 'Die Zuweisung wurde bis zur nächsten Lokalisierung aufgeschoben.');
    }

    public function hideAll(Request $request): RedirectResponse {
        $validated = $request->validate([
                                            'scanDeviceId' => ['required', 'exists:scan_devices,id'],
                                            'onlyWithName' => ['nullable'],
                                        ]);

        $query = Scan::where('scanDeviceId', $validated['scanDeviceId'])
                     ->where('hidden', '0');

        if(isset($validated['onlyWithName'])) {
            $query->whereNotNull('vehicle_name');
        }

        $rows = $query->update(['hidden' => 1]);

        return back()->with('alert-success', $rows . ' Scans als erledigt markiert.');
    }
}
