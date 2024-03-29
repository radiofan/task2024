<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SensorsController extends Controller{
	public function add_measure(Request $request, int $sensor_id): JsonResponse{
		// ...
	}
	
	public function get_measures(Request $request, ?int $sensor_id = null, ?string $parameter_key = null): JsonResponse{
		// ...
	}
}
