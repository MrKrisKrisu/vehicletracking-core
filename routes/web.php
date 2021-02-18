<?php

use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::middleware(['auth'])->group(function() {
    Route::get('/', [VehicleController::class, 'render'])->name('dashboard');
    Route::post('/', [VehicleController::class, 'saveVehicle']);

    Route::get('/verify', [VehicleController::class, 'verify']);
    Route::post('/verify', [VehicleController::class, 'saveVerify']);

    Route::get('/notifications', [NotificationController::class, 'renderNotifications'])
         ->name('notifications');
    Route::post('/notifications', [NotificationController::class, 'switchNotifications']);

    Route::get('/ignored', [VehicleController::class, 'renderIgnored'])
         ->name('ignored');
    Route::post('/ignoreDevice', [VehicleController::class, 'ignoreDevice'])
         ->name('ignoreDevice');
    Route::post('/ignoreDevice/add', [VehicleController::class, 'saveIgnoredNetwork'])
         ->name('ignoreDevice.add');
    Route::post('/unban/ssid', [VehicleController::class, 'unbanSSID'])
         ->name('unban.ssid');
    Route::post('/unban/bssid', [VehicleController::class, 'unbanBSSID'])
         ->name('unban.bssid');

    Route::get('/location', [LocationController::class, 'renderOverview'])
         ->name('location');
    Route::post('/location/import', [LocationController::class, 'importLocations'])
         ->name('location.import');

    Route::post('/vehicle/create', [VehicleController::class, 'createVehicle'])
         ->name('vehicle.create');
    Route::post('/vehicle/assign', [VehicleController::class, 'assignVehicle'])
         ->name('vehicle.assign');
    Route::post('/vehicle/assign/skip', [VehicleController::class, 'skipAssignment'])
         ->name('vehicle.assign.skip');

    Route::get('/map/networks', [MapController::class, 'renderNetworkMap'])
         ->name('map.networks');
});

Route::view('/imprint', 'imprint')->name('imprint');
Route::get('/map', [MapController::class, 'renderMap'])->name('map');
Route::get('/sitemap', [MapController::class, 'renderSitemap']);

Route::get('/vehicle/{vehicle_id}', [VehicleController::class, 'renderVehicle'])
     ->name('vehicle');

Route::get('/company', [VehicleController::class, 'renderCompanies'])->name('companies');
Route::get('/company/{id}', [VehicleController::class, 'renderCompany'])->name('company');
