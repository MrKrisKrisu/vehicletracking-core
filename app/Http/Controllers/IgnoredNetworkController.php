<?php

namespace App\Http\Controllers;

use App\IgnoredNetwork;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Device;

class IgnoredNetworkController extends Controller {

    public function create(Request $request): JsonResponse {
        if(auth()->user()->id !== 1) {
            abort(403);
        }
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

    private static $hiddenList = null;

    public static function checkIfDeviceShouldBeHidden(Device $device): bool {
        //Check if network contains hide-keyword
        if(self::$hiddenList == null) {
            self::$hiddenList = IgnoredNetwork::where('contains', 1)->select('ssid')->get()->pluck('ssid');
        }
        foreach(self::$hiddenList as $ssid) {
            if(str_contains(strtolower($device->ssid), strtolower($ssid))) {
                $device->update(['ignore' => 1]);
                return true;
            }
        }
        return false;
    }
}
