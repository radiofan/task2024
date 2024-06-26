<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler{
	/**
	 * A list of exception types with their corresponding custom log levels.
	 *
	 * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
	 */
	protected $levels = [//
	];

	/**
	 * A list of the exception types that are not reported.
	 *
	 * @var array<int, class-string<\Throwable>>
	 */
	protected $dontReport = [//
	];

	/**
	 * A list of the inputs that are never flashed to the session on validation exceptions.
	 *
	 * @var array<int, string>
	 */
	protected $dontFlash = [
		'current_password',
		'password',
		'password_confirmation',
	];

	/**
	 * Register the exception handling callbacks for the application.
	 */
	public function register(): void{
		$this->reportable(function(Throwable $e){
			//
		});
	}

	public function render($request, Throwable $exception){
		if(in_array('api', $request->route()->getAction('middleware'))){
			if(
				$exception instanceof NotFoundHttpException
				|| $exception instanceof ModelNotFoundException
			){
				return response()->json()->setStatusCode(404);
				
			}else if($exception instanceof MethodNotAllowedHttpException){
				return response()->json()->setStatusCode(405);
			}
		}
		return parent::render($request, $exception);
	}
}
