<?php

namespace App\Http\Controllers\API\v1;

use App\Device;
use App\Http\Controllers\Controller;
use App\Http\Middleware\ScanDeviceAuthentification;
use App\IgnoredNetwork;
use App\Scan;
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

        $verified = [];
        $unverified = [];

        $validated = $validator->validate();
        foreach($validated as $scanElement) {

            if($scanElement['ssid'] != null && IgnoredNetwork::isIgnored($scanElement['ssid']))
                continue;

            $scanElement['scanDeviceId'] = ScanDeviceAuthentification::getDevice()->id;

            if($scanElement['ssid'] == '')
                $scanElement['ssid'] = null;

            $scanElement['latitude'] = ScanDeviceAuthentification::getDevice()->latitude;
            $scanElement['longitude'] = ScanDeviceAuthentification::getDevice()->longitude;

            Device::updateOrCreate([
                                       'bssid' => $scanElement['bssid']
                                   ], [
                                       'ssid'     => $scanElement['ssid'],
                                       'lastSeen' => Carbon::now()
                                   ]);

            $scan = Scan::create($scanElement);
            if(isset($scan->device->vehicle)) {
                $vehicle = $scan->device->vehicle;
                $verified[$vehicle->id] = [
                    'company' => $vehicle->company->name,
                    'vehicle' => $vehicle->vehicle_name
                ];
            } else
                foreach($scan->device->scans->whereNotNull('vehicle_name')->pluck('vehicle_name') as $possibleRow)
                    foreach(explode(',', $possibleRow) as $possible)
                        if(!in_array($possible, $unverified))
                            $unverified[] = $possible;

        }

        sort($unverified);

        return response()->json([
                                    'status' => true,
                                    'data'   => [
                                        'verified'   => array_values($verified),
                                        'unverified' => array_values($unverified)
                                    ]
                                ]);
    }

}
