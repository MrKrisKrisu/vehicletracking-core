<?php

namespace App\Http\Controllers\API\v1;

use App\Device;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DeviceController extends ApiController {

    public function update(Request $request, int $id): JsonResponse {
        $validated = $request->validate([
                                            'blocked'         => ['nullable', 'min:0', 'max:1'],
                                            'moveVerifyUntil' => ['nullable', 'date'],
                                        ]);

        $model = Device::findOrFail($id);
        $this->authorize('update', $model);

        $model->update($validated);
        return self::response();
    }
}
