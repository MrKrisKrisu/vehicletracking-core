<?php

namespace App\Http\Controllers;

use App\IgnoredNetwork;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Device;
use App\Scan;

class IgnoredNetworkController extends Controller {
    public function create(Request $request): JsonResponse {
        $validated = $request->validate([
                                            'ssid'     => ['required', 'unique:ignored_networks,ssid'],
                                            'contains' => ['nullable', 'gte:0', 'lte:1']
                                        ]);
        try {
            if(in_array(strtolower($validated['ssid']), ['wifi@db', 'fahrgastfernsehen', 'uestra_regiobus_freewlan', 'wfb intern', 'westfalenbahn', 'wifionice', 'enno_wifi'])) {
                abort(403);
            }

            $ignoredNetwork = IgnoredNetwork::firstOrCreate($validated);
            Device::where('ssid', $validated['ssid'])
                  ->update(['ignore' => 1]);
            Scan::where('ssid', $validated['ssid'])
                ->update(['hidden' => 1]);

            return response()->json(['success' => true, 'obj' => [
                'ssid'     => $ignoredNetwork->ssid,
                'contains' => $ignoredNetwork->contains
            ]]);
        } catch(Exception) {
            return response()->json(['success' => false], 400);
        }
    }
}
