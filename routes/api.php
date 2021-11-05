<?php

use App\Http\Controllers\API\v1\IntranetDataController;
use App\Http\Controllers\API\v1\MapController;
use App\Http\Controllers\API\v1\ScanController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['auth.scan'])->group(function() {
    Route::post('/scan', [ScanController::class, 'scan']);
    Route::post('/upload/portal', [IntranetDataController::class, 'save']);
});

Route::prefix('v1')->middleware([])->group(function() {
    Route::get('/location', [ScanController::class, 'saveLocation']);
    Route::get('/networks', [MapController::class, 'getNetworksByBbox']);
});
