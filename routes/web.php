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

    Route::get('/vehicle/{vehicle_id}', [VehicleController::class, 'renderVehicle'])->name('vehicle');

    Route::get('/assign/', [VehicleController::class, 'assign'])->name('assign');
    Route::post('/assign/', [VehicleController::class, 'saveAssignee'])->name('saveAssignee');
});


