<?php

namespace App\Http\Controllers;

use App\IgnoredNetwork;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class IgnoredNetworkController extends Controller {
    public function create(Request $request): JsonResponse {
        $validated = $request->validate([
                                            'ssid'     => ['required', 'unique:ignored_networks,ssid'],
                                            'contains' => ['nullable', 'gte:0', 'lte:1']
                                        ]);
        try {
            $ignoredNetwork = IgnoredNetwork::create($validated);
            return response()->json(['success' => true, 'obj' => [
                'ssid'     => $ignoredNetwork->ssid,
                'contains' => $ignoredNetwork->contains
            ]]);
        } catch(Exception) {
            return response()->json(['success' => false], 400);
        }
    }
}
