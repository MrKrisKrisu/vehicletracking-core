<?php

use App\Http\Controllers\API\v1\DeviceController;
use App\Http\Controllers\API\v1\MapController;
use App\Http\Controllers\API\v1\ScanController;
use Illuminate\Support\Facades\Route;

Route::middleware('api')->group(function() {
    Route::prefix('v1')->middleware(['auth.scan'])->group(function() {
        Route::post('/scan', [ScanController::class, 'scan']);
    });

    Route::prefix('v1')->middleware([])->group(function() {
        Route::get('/networks', [MapController::class, 'getNetworksByBbox']);
    });
});

Route::middleware('web')->group(function() {
    Route::prefix('v1/model')->middleware(['auth'])->group(function() {
        Route::apiResources([
                                'scan'   => ScanController::class,
                                'device' => DeviceController::class,
                            ]);
    });
});
