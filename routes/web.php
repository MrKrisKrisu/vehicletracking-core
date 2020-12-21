<?php

use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function() {
    Route::get('/', [VehicleController::class, 'render']);
    Route::post('/', [VehicleController::class, 'saveVehicle']);

    Route::get('/verify', [VehicleController::class, 'verify']);
    Route::post('/verify', [VehicleController::class, 'saveVerify']);

    Route::get('/vehicle/{vehicle_id}', [VehicleController::class, 'renderVehicle'])
         ->name('vehicle');

    Route::get('/ignored', [VehicleController::class, 'renderIgnored'])
         ->name('ignored');
    Route::post('/ignoreDevice', [VehicleController::class, 'ignoreDevice'])
         ->name('ignoreDevice');
    Route::post('/unban/ssid', [VehicleController::class, 'unbanSSID'])
         ->name('unban.ssid');
    Route::post('/unban/bssid', [VehicleController::class, 'unbanBSSID'])
         ->name('unban.bssid');
});

Route::get('/company', [VehicleController::class, 'renderCompanies']);
Route::get('/company/{id}', [VehicleController::class, 'renderCompany'])->name('company');
