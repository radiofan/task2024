<?php

use App\Http\Controllers\SensorsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('measures')->group(function(){
	Route::prefix('sensors')->group(function(){
		Route::match(['put', 'post'], '/{sensor_id}', [SensorsController::class, 'add_measure'])
			->whereNumber('sensor_id')
			->name('add_measure');

		Route::get('/{sensor_id}', [SensorsController::class, 'get_measures'])
			->whereNumber('sensor_id')
			->name('get_measures_for_sensor');

		Route::get('/{sensor_id}/parameters/{parameter}', [SensorsController::class, 'get_measures'])
			->whereNumber('sensor_id')
			->name('get_measures_for_sensor_parameter');
	});

	Route::get('/', [SensorsController::class, 'get_measures'])
		->name('get_measures');
});