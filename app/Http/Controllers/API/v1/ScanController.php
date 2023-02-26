<?php

namespace App\Http\Controllers\API\v1;

use App\Device;
use App\Http\Controllers\IgnoredNetworkController;
use App\Http\Middleware\ScanDeviceAuthentification;
use App\Scan;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ScanController extends ApiController {

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
            '*.vehicle_name'       => ['nullable', 'max:255'],
            '*.bssid'              => ['required', 'regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/'],
            '*.ssid'               => ['nullable', 'max:255'],
            '*.signal'             => ['nullable', 'numeric'],
            '*.quality'            => ['nullable'],
            '*.frequency'          => ['nullable', 'numeric'],
            '*.bitrates'           => ['nullable', 'numeric'],
            '*.encrypted'          => ['nullable', 'boolean'],
            '*.channel'            => ['nullable', 'numeric'],
            '*.latitude'           => ['nullable', 'numeric'],
            '*.longitude'          => ['nullable', 'numeric'],
            '*.speed'              => ['nullable', 'numeric'],
            '*.connectivity_state' => ['nullable', 'max:255'],
            '*.created_at'         => ['nullable', 'date'],
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

            $scanElement['ssid'] = strlen($scanElement['ssid']) == 0 ? null : $scanElement['ssid'];
            if(!isset($scanElement['latitude'])) {
                $scanElement['latitude'] = ScanDeviceAuthentification::getDevice()?->latitude ?? null;
            }
            if(!isset($scanElement['longitude'])) {
                $scanElement['longitude'] = ScanDeviceAuthentification::getDevice()?->longitude ?? null;
            }
            $scanElement['bssid'] = strtoupper($scanElement['bssid']);

            $device = Device::updateOrCreate([
                                                 'bssid' => $scanElement['bssid'],
                                             ], [
                                                 'ssid'     => $scanElement['ssid'],
                                                 'lastSeen' => Carbon::now()->toIso8601String(),
                                             ]);

            if($device->blocked) {
                continue;
            }

            IgnoredNetworkController::checkIfDeviceShouldBeHidden($device);

            $scan = Scan::create($scanElement);
            if(isset($scan->device->vehicle)) {
                $vehicle = $scan->device->vehicle;
                if($vehicle->company->name !== 'Stationary') {
                    $verified[$vehicle->id] = [
                        'company' => $vehicle->company->name,
                        'vehicle' => $vehicle->vehicle_name,
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

        return response()->json([
                                    'status' => true,
                                    'data'   => [
                                        'verified'   => array_values($verified),
                                        'unverified' => array_values($unverified),
                                    ],
                                ]);
    }

    public function update(Request $request, int $scanId): JsonResponse {
        $validated = $request->validate([
                                            'vehicle_name'          => ['nullable'],
                                            'modified_vehicle_name' => ['nullable'],
                                        ]);

        $scan = Scan::findOrFail($scanId);
        $this->authorize('update', $scan);

        if(isset($validated['modified_vehicle_name'])) {
            $validated['modified_vehicle_name'] = str_replace(["\r\n", "\r", "\n"], ',', $validated['modified_vehicle_name']);
        }

        $scan->update($validated);
        return self::response();
    }
}
