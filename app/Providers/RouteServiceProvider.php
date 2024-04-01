<?php

namespace App\Providers;

use App\Models\Parameter;
use App\Models\Sensor;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider{
	/**
	 * The path to the "home" route for your application.
	 *
	 * Typically, users are redirected here after authentication.
	 *
	 * @var string
	 */
	public const HOME = '/';

	/**
	 * Define your route model bindings, pattern filters, and other route configuration.
	 */
	public function boot(): void{
		$this->configureRateLimiting();

		Route::model('sensor', Sensor::class);
		Route::model('parameter', Parameter::class);

		$this->routes(function(){
			Route::middleware('api')
				->prefix('api')
				->group(base_path('routes/api.php'));

			Route::middleware('web')
				->group(base_path('routes/web.php'));
		});
	}

	/**
	 * Configure the rate limiters for the application.
	 */
	protected function configureRateLimiting(): void{
		//todo установить лимит на получение данных, но на обработку убрать
		RateLimiter::for('api', function(){
			return Limit::none();
		});
	}
}
