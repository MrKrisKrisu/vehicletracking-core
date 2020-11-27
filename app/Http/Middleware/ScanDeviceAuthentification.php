<?php

namespace App\Http\Middleware;

use App\ScanDevice;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanDeviceAuthentification
{
    private static $scanDevice;

    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('Authentication'))
            return response()->json(['error' => 'Authentication Header is missing.'], 400);

        $token = $request->header('Authentication');

        self::$scanDevice = ScanDevice::where('token', $token)->where(function ($query) {
            $query->where('valid_until', null)
                  ->orWhere('valid_until', '>', DB::raw('CURRENT_TIMESTAMP'));
        })->first();

        if (self::$scanDevice == null) {
            return response()->json(['error' => 'Authentication Header is invalid.'], 401);
        }

        return $next($request);
    }

    public static function getDevice(): ?ScanDevice
    {
        return self::$scanDevice;
    }
}
