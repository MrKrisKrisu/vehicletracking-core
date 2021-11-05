<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Middleware\ScanDeviceAuthentification;
use App\IntranetData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class IntranetDataController extends Controller {

    /**
     * POST: /api/v1/upload/portal
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws ValidationException
     */
    public function save(Request $request): JsonResponse {

        $validator = Validator::make($request->all(), [
            'data'  => ['required'],
            'bssid' => ['nullable']
        ]);

        if($validator->fails()) {
            return response()->json(['status' => false, 'errors' => array_values($validator->errors()->toArray())], 400);
        }
        $validated = $validator->validate();

        $intranetData = IntranetData::create([
                                                 'data'         => $validated['data'],
                                                 'scanDeviceId' => ScanDeviceAuthentification::getDevice()->id,
                                             ]);

        return response()->json([
                                    'status' => true,
                                    'id'     => $intranetData->id,
                                ]);
    }

}
