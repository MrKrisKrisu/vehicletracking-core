<?php

use App\Http\Controllers\AirportImportController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\Frontend\Admin\CheckController;
use App\Http\Controllers\Frontend\User\DashboardController;
use App\Http\Controllers\Frontend\User\SettingsController;
use App\Http\Controllers\IgnoredNetworkController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\VehicleController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::view('/', 'user.home')
     ->name('user.home');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'renderDashboard'])
         ->name('user.dashboard');
    Route::post('/save-to-session', [SettingsController::class, 'saveToSession'])
         ->name('save-to-session');
    Route::post('/hideAll', [VehicleController::class, 'hideAll'])
         ->name('hide-all');

    Route::prefix('admin')->middleware(['admin'])->group(function () {
        Route::get('/', [VehicleController::class, 'render'])
             ->name('admin.dashboard');
        Route::post('/scans/assign', [VehicleController::class, 'saveVehicle'])
             ->name('scans.assign');

        Route::get('/verify/{deviceId?}', [VehicleController::class, 'verify'])
             ->name('admin.verify');
        Route::get('/check/list', [CheckController::class, 'listVehiclesToCheck'])
             ->name('admin.check.list');

        Route::get('/ignored', [VehicleController::class, 'renderIgnored'])
             ->name('admin.ignored');
        Route::post('/ignoreDevice', [VehicleController::class, 'ignoreDevice'])
             ->name('ignoreDevice');
        Route::post('/ignoreDevice/add', [VehicleController::class, 'saveIgnoredNetwork'])
             ->name('ignoreDevice.add');
        Route::post('/unban/ssid', [VehicleController::class, 'unbanSSID'])
             ->name('unban.ssid');
        Route::post('/unban/bssid', [VehicleController::class, 'unbanBSSID'])
             ->name('unban.bssid');

        Route::get('/import', [LocationController::class, 'renderOverview'])
             ->name('admin.location');
        Route::post('/location/import', [LocationController::class, 'importLocations'])
             ->name('location.import');

        Route::post('/import/airport', [AirportImportController::class, 'import'])
             ->name('import.airport');

        Route::post('/vehicle/create', [VehicleController::class, 'createVehicle'])
             ->name('vehicle.create');
        Route::post('/vehicle/assign', [VehicleController::class, 'assignVehicle'])
             ->name('vehicle.assign');
        Route::post('/vehicle/assign/skip', [VehicleController::class, 'skipAssignment'])
             ->name('vehicle.assign.skip');

        Route::get('/map/networks', [MapController::class, 'renderNetworkMap'])
             ->name('map.networks');

        Route::prefix('model')->group(function () {
            Route::post('/scans/update', [ScanController::class, 'update'])
                 ->name('scans.update');
            Route::post('/device/update', [DeviceController::class, 'update'])
                 ->name('old.device.update');
            Route::post('/ignoredNetwork/create', [IgnoredNetworkController::class, 'create'])
                 ->name('ignoredNetwork.create');
        });
    });

    Route::get('/settings', [SettingsController::class, 'renderSettings'])
         ->name('user.settings');
    Route::post('/settings/password', [SettingsController::class, 'changePassword'])
         ->name('user.settings.password');

    Route::get('/map', [MapController::class, 'renderMap'])->name('map');
});

Route::view('/imprint', 'imprint')->name('imprint');
Route::get('/sitemap', [MapController::class, 'renderSitemap']);
Route::get('/search', [SearchController::class, 'render']);
Route::post('/search', [SearchController::class, 'search'])->name('search.show');

Route::get('/vehicle/{vehicle_id}', [VehicleController::class, 'renderVehicle'])
     ->name('vehicle');

Route::get('/company', [VehicleController::class, 'renderCompanies'])->name('companies');
Route::get('/company/{id}', [VehicleController::class, 'renderCompany'])->name('company');
