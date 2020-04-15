<?php

use App\ScanDevice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Vehicle;
use App\Device;
use App\Scan;
use App\Company;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::post('/vehicle/locate/', function (Request $request) {
    $verifiedVehicles = [];
    $possibleVehicles = [];

    $json = json_decode($request->getContent());

    if (!is_array($json))
        return response('Request contains no valid json.', 400);

    foreach ($json as $bssid) {
        $device = DB::table('devices')->where('bssid', $bssid)->where('vehicle_id', '<>', null)->first();
        if ($device !== null) {
            //There are verified rows about the found bssid
            $vehicle = DB::table('vehicles')->where('id', $device->vehicle_id)->first();
            if ($vehicle != null && !in_array($vehicle->vehicle_name, $verifiedVehicles))
                $verifiedVehicles[] = $vehicle->vehicle_name;
        } else {
            //There are no verified rows about the found bssid, so we will try to analyse the given data
            $result = DB::table('scans')->select('vehicle_name', DB::raw('COUNT(*) as cnt'))
                ->where('bssid', $bssid)
                ->where('vehicle_name', '<>', null)
                ->where('vehicle_name', '<>', 0)
                ->groupBy('vehicle_name')
                ->orderBy('cnt', 'DESC')
                ->limit(1)
                ->first();

            if ($result != null && !in_array($result->vehicle_name, $possibleVehicles))
                $possibleVehicles[] = $result->vehicle_name;
        }
    }

    return [
        'verified' => $verifiedVehicles,
        'possible' => $possibleVehicles
    ];
});

Route::get('/scan/prefix/', function () {
    return \App\DevicePrefix::select('prefix', 'description')->get();
});

Route::get('company/{company_id}', function ($company_id) {
    return Company::where([
        'id' => $company_id
    ])->first();
});

Route::get('vehicle/{company_id}/{vehicle_id}', function ($company_id, $vehicle_id) {
    return Vehicle::where([
        'company_id' => $company_id,
        'vehicle_name' => $vehicle_id
    ])->firstOrFail()->devices;
});

Route::post('scan', function (Request $request) {
    $token = $request->header('X-Api-Token');
    $deviceID = null;
    $scanDevice = null;
    if ($token != null) {
        $scanDevice = DB::table('scan_devices')->where('token', $token)->first();
        if ($scanDevice != null)
            $deviceID = $scanDevice->id;
    }

    $scan = new Scan();
    $scan->vehicle_name = $request->vehicle_id ?: null;
    $scan->bssid = $request->bssid;
    $scan->ssid = $request->ssid;
    $scan->signal = $request->signal;
    $scan->quality = $request->quality;
    $scan->frequency = $request->frequency;
    $scan->bitrates = $request->bitrates;
    $scan->encrypted = $request->encrypted;
    $scan->channel = $request->channel;
    $scan->scanDeviceId = $deviceID;
    $scan->save();

    DB::insert("INSERT INTO devices (bssid, ssid, firstSeen, lastSeen) VALUES (?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP) " .
        "ON DUPLICATE KEY UPDATE ssid = ?, lastSeen = CURRENT_TIMESTAMP", [$request->bssid, $request->ssid, $request->ssid]);

    $device = Device::where('bssid', $request->bssid)->first();

    /*
    if ($device != null && $device->vehicle_id != null) {
        $vehicle = Vehicle::find($device->vehicle_id);
        \App\Http\Controllers\TelegramController::broadcastMessage('Fahrzeug "' . $vehicle->vehicle_name . '" gesichtet (' . $scanDevice->name . ')');
    } else if ($device != null) {
        $scans = DB::table('scans')->where('bssid', $request->bssid)->where('vehicle_name', '<>', null)->get();
        $possible = [];
        foreach ($scans as $scanElement) {
            $spl = explode(',', $scanElement->vehicle_name);
            foreach ($spl as $splElement)
                if (!in_array($splElement, $possible))
                    $possible[] = $splElement;
        }
        $message = "Fahrzeug gesichtet, welches nicht genau bestimmt werden konnte. (" . $scanDevice->name . ") \r\n\r\n";
        $message .= "Mögliche Fahrzeuge: \r\n";
        foreach ($possible as $ve)
            $message .= " - $ve \r\n";
        \App\Http\Controllers\TelegramController::broadcastMessage($message);
    }*/

    return $device->vehicle;
});

Route::post('scan/device/registernew', function (Request $request) {
    $uuid = \Illuminate\Support\Str::uuid();

    $scandevice = new ScanDevice();
    $scandevice->token = $uuid;
    $scandevice->save();

    return response((String)$uuid, 200, ['Content-type: text/plain']);
});

