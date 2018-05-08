<?php

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

Route::prefix('carrier')
    ->group(function ($router) {
        $router->get('{uuid}', '\App\Carrier\Http\Apis\Carrier@showByUUID');
        $router->get('{usdot}', '\App\Carrier\Http\Apis\Carrier@showByUSDOT');
        $router->get('{id}', '\App\Carrier\Http\Apis\Carrier@show');
        $router->get('/', '\App\Carrier\Http\Apis\Carrier@index');
    });

Route::prefix('fleet')
    ->group(function ($router) {
        $router->get('{uuid}', '\App\Truck\Http\Apis\Fleet@showByUUID');
        $router->get('{vin}', '\App\Truck\Http\Apis\Fleet@showByVIN');
        $router->get('{usdot}', '\App\Truck\Http\Apis\Fleet@showByUSDOT');
        $router->get('{id}', '\App\Truck\Http\Apis\Fleet@show');
        $router->get('/', '\App\Truck\Http\Apis\Fleet@index');
    });

Route::prefix('truck')
    ->group(function ($router) {
        $router->get('{uuid}', '\App\Truck\Http\Apis\Truck@showByUUID');
        $router->get('{vin}', '\App\Truck\Http\Apis\Truck@showByVIN');
        $router->get('{id}', '\App\Truck\Http\Apis\Truck@show');
        $router->get('/', '\App\Truck\Http\Apis\Truck@index');
    });
