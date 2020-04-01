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

Route::get('/', 'VehicleController@render');
Route::post('/', 'VehicleController@saveVehicle');

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

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
