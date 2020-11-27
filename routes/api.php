<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

Route::get('/vehicle/last_seen', [ApiController::class, 'getLastSeenVehicles']);
Route::get('/vehicle/new', [ApiController::class, 'getNewVehicles']);
Route::post('/vehicle/locate/', [ApiController::class, 'locate']);
Route::get('/scan/prefix/', [ApiController::class, 'prefix']);
Route::get('/company/{company_id}', [ApiController::class, 'getCompany']);
Route::get('/vehicle/{company_id}/{vehicle_id}', [ApiController::class, 'getVehicles']);
Route::post('/scan/device/registernew', [ApiController::class, 'registerNew']);
Route::post('/scan', [ApiController::class, 'scan']);

Route::prefix('v1')->middleware(['auth.scan'])->group(function () {
    Route::post('/scan', [\App\Http\Controllers\API\v1\ScanController::class, 'scan']);
});