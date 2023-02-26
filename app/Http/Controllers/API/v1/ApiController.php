<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller {

    public static function response(string $message = null, object $data = null, bool $success = true, int $status = 200): JsonResponse {
        return response()->json(
            data:   [
                        'success' => $success,
                        'message' => $message,
                        'data'    => $data,
                    ],
            status: $status,
        );
    }

    public static function error($message, $status = 400): JsonResponse {
        return self::response(
            message: $message,
            success: false,
            status:  $status
        );
    }
}
