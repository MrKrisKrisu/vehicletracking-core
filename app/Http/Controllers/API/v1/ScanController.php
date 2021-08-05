<?php

namespace App\Http\Controllers\API\v1;

use App\Device;
use App\Http\Controllers\Controller;
use App\Http\Controllers\NotificationController;
use App\Http\Middleware\ScanDeviceAuthentification;
use App\IgnoredNetwork;
use App\Scan;
use App\ScanDevice;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ScanController extends Controller {
    /**
     * POST: /api/v1/scan
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function scan(Request $request): JsonResponse {
        $validator = Validator::make($request->all(), [
            '*.vehicle_name' => ['nullable', 'max:255'],
            '*.bssid'        => ['required', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
            '*.ssid'         => ['nullable', 'max:255'],
            '*.signal'       => ['nullable', 'numeric'],
            '*.quality'      => ['nullable'],
            '*.frequency'    => ['nullable', 'numeric'],
            '*.bitrates'     => ['nullable', 'numeric'],
            '*.encrypted'    => ['nullable', 'boolean'],
            '*.channel'      => ['nullable', 'numeric'],
            '*.latitude'     => ['nullable', 'numeric'],
            '*.longitude'    => ['nullable', 'numeric'],
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'errors' => array_values($validator->errors()->toArray())], 400);
        }

        $verified   = [];
        $unverified = [];

        $validated = $validator->validate();
        foreach($validated as $scanElement) {

            $scanElement['scanDeviceId'] = ScanDeviceAuthentification::getDevice()->id;

            $scanElement['ssid'] = str_replace("\\x00", "", $scanElement['ssid']);

            $scanElement['ssid']      = strlen($scanElement['ssid']) == 0 ? null : $scanElement['ssid'];
            $scanElement['latitude']  = ScanDeviceAuthentification::getDevice()?->latitude ?? null;
            $scanElement['longitude'] = ScanDeviceAuthentification::getDevice()?->longitude ?? null;

            $scanElement['bssid'] = strtoupper($scanElement['bssid']);

            $device = Device::updateOrCreate([
                                                 'bssid' => $scanElement['bssid']
                                             ], [
                                                 'ssid'     => $scanElement['ssid'],
                                                 'lastSeen' => Carbon::now()
                                             ]);

            //Check if network contains hide-keyword
            $hiddenList = IgnoredNetwork::where('contains', 1)->select('ssid')->get()->pluck('ssid');
            foreach($hiddenList as $ssid) {
                if(str_contains(strtolower($scanElement['ssid']), strtolower($ssid)))
                    $device->update(['ignore' => 1]);
            }

            $scan = Scan::create($scanElement);
            if(isset($scan->device->vehicle)) {
                $vehicle = $scan->device->vehicle;
                if($vehicle->company->name != 'Stationary') {
                    $verified[$vehicle->id] = [
                        'company' => $vehicle->company->name,
                        'vehicle' => $vehicle->vehicle_name
                    ];
                }
            } else {
                foreach($scan->device->scans->whereNotNull('vehicle_name')->pluck('vehicle_name') as $possibleRow) {
                    foreach(explode(',', $possibleRow) as $possible) {
                        if(!in_array($possible, $unverified)) {
                            $unverified[] = $possible;
                        }
                    }
                }
            }
        }

        sort($unverified);

        if(ScanDeviceAuthentification::getDevice()->notify) {
            if(count($verified) > 0) {
                $message = '[' . ScanDeviceAuthentification::getDevice()->name . "] <b>Fahrzeug(e) lokalisiert</b>\r\n\r\n";
                foreach($verified as $vehicle)
                    $message .= $vehicle['vehicle'] . "\r\n<i>" . $vehicle['company'] . "</i>\r\n---------------\r\n";
                NotificationController::notifyRaw($message);
            }
            if(count($unverified) > 0) {
                $message = '[' . ScanDeviceAuthentification::getDevice()->name . "] <b>Unverifiziertes Fahrzeug lokalisiert</b>\r\n";
                foreach($unverified as $vehicle)
                    $message .= '- ' . $vehicle . "\r\n";
                NotificationController::notifyRaw($message);
            }
        }

        return response()->json([
                                    'status' => true,
                                    'data'   => [
                                        'verified'   => array_values($verified),
                                        'unverified' => array_values($unverified)
                                    ]
                                ]);
    }

    /**
     * GET: /api/v1/location?token&lat&lon&timestamp&hdop&altitude&speed
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function saveLocation(Request $request): JsonResponse {

        $validated = $request->validate([
                                            'token'     => ['required', 'exists:scan_devices,token'],
                                            'lat'       => ['required', 'numeric'],
                                            'lon'       => ['required', 'numeric'],
                                            'timestamp' => ['required', 'numeric'],
                                            'hdop'      => ['nullable', 'numeric'],
                                            'altitude'  => ['nullable', 'numeric'],
                                            'speed'     => ['nullable', 'numeric'],
                                        ]);

        $scanDevice = ScanDevice::where('token', $validated['token'])->firstOrFail();

        $time  = Carbon::createFromTimestampMs($validated['timestamp']);
        $count = Scan::where('scanDeviceId', $scanDevice->id)
                     ->where('latitude', null)
                     ->where('longitude', null)
                     ->where('created_at', '>=', $time->clone()->subSeconds(5))
                     ->where('created_at', '<=', $time->clone()->addSeconds(5))
                     ->update([
                                  'latitude'  => $validated['lat'],
                                  'longitude' => $validated['lon'],
                              ]);

        return response()->json([
                                    'success'  => true,
                                    'affected' => $count
                                ]);
    }

}
