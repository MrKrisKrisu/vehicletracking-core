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

Route::get('/', 'VehicleController@render')->middleware('auth');
Route::post('/', 'VehicleController@saveVehicle')->middleware('auth');

Route::get('/verify', 'VehicleController@verify')->middleware('auth');
Route::post('/verify', 'VehicleController@saveVerify')->middleware('auth');

Route::get('/vehicle/{vehicle_id}', [
    'uses' => 'VehicleController@renderVehicle',
    'as' => 'vehicle'
])->middleware('auth');

Route::get('/assign/', [
    'uses' => 'VehicleController@assign',
    'as' => 'assign'
])->middleware('auth');

Route::post('/assign/', [
    'uses' => 'VehicleController@saveAssignee',
    'as' => 'saveAssignee'
])->middleware('auth');

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');
