<?php

use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


Route::get('/vehicle/last_seen', [ApiController::class, 'getLastSeenVehicles']);
Route::get('/vehicle/new', [ApiController::class, 'getNewVehicles']);
Route::post('/vehicle/locate/', [ApiController::class, 'locate']);
Route::get('/scan/prefix/', [ApiController::class, 'prefix']);
Route::get('/company/{company_id}', [ApiController::class, 'getCompany']);
Route::get('/vehicle/{company_id}/{vehicle_id}', [ApiController::class, 'getVehicles']);
Route::post('/scan', [ApiController::class, 'scan']);
Route::post('/scan/device/registernew', [ApiController::class, 'registerNew']);

