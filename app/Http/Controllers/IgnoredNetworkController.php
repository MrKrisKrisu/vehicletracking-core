<?php

namespace App\Http\Controllers;

use App\IgnoredNetwork;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IgnoredNetworkController extends Controller {

    public function create(Request $request): JsonResponse {
        $validated = $request->validate([
                                            'ssid'     => ['required'],
                                            'contains' => ['nullable', 'gte:0', 'lte:1']
                                        ]);
        try {
            if(in_array(strtolower($validated['ssid']), ['kvv-swlan', 'kvv-wlan', 'wifi@db', 'fahrgastfernsehen', 'uestra_regiobus_freewlan', 'wfb intern', 'westfalenbahn', 'wifionice', 'enno_wifi'])) {
                abort(403);
            }

            $ignoredNetwork = IgnoredNetwork::firstOrCreate($validated);

            return response()->json(['success' => true, 'obj' => [
                'ssid'     => $ignoredNetwork->ssid,
                'contains' => $ignoredNetwork->contains
            ]]);
        } catch(Exception) {
            return response()->json(['success' => false], 400);
        }
    }
}
