<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/public/{device_id}', 'VehicleController@renderPublic');

Route::get('/', 'VehicleController@render');
Route::post('/', 'VehicleController@saveVehicle');

Route::get('/verify', 'VehicleController@verify');
Route::post('/verify', 'VehicleController@saveVerify');

Route::get("/stats", "StatController@renderStatpage");

Route::get('/vehicle/{vehicle_id}', [
    'uses' => 'VehicleController@renderVehicle',
    'as' => 'vehicle'
]);

Route::get('/assign/', [
    'uses' => 'VehicleController@assign',
    'as' => 'assign'
]);

Route::post('/assign/', [
    'uses' => 'VehicleController@saveAssignee',
    'as' => 'saveAssignee'
]);

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
