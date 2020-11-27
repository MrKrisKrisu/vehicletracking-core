<?php

use App\Http\Controllers\API\v1\ScanController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/vehicle/last_seen', [ApiController::class, 'getLastSeenVehicles']);
Route::get('/vehicle/new', [ApiController::class, 'getNewVehicles']);

Route::prefix('v1')->middleware(['auth.scan'])->group(function () {
    Route::post('/scan', [ScanController::class, 'scan']);
});