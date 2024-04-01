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

	Route::get('/', [SensorsController::class, 'get_measures'])
		->name('get');
	
	Route::prefix('sensors')->group(function(){
		Route::match(['put', 'post'], '/{sensor}', [SensorsController::class, 'add_measure'])
			->name('add');

		Route::get('/{sensor}', [SensorsController::class, 'get_measures'])
			->name('get.sensor');

		Route::get('/{sensor}/parameters/{parameter}', [SensorsController::class, 'get_measures'])
			->name('get.sensor.parameter')
			->scopeBindings();
	});
	
})->name('measures.');

Route::fallback(function(){
	return response()->json()->setStatusCode(404);
});