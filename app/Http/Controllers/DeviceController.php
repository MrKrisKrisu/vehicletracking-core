<?php

namespace App\Http\Controllers;

use App\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends Controller {

    public function update(Request $request): JsonResponse {
        $validated = $request->validate([
                                            'id'     => ['required', 'exists:devices,id'],
                                            'ignore' => ['nullable', 'gte:0', 'lte:1']
                                        ]);

        $device = Device::find($validated['id']);
        $this->authorize('update', $device);
        $device->update($validated);

        return response()->json(['success' => true]);
    }
}
