<?php

namespace App\Http\Controllers;

use App\Models\Parameter;
use App\Models\Sensor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SensorsController extends Controller{
	public function add_measure(Request $request, Sensor $sensor): JsonResponse{
		
		//$request->post(); //получить все парамерты
		$r_body = $request->getContent();
		$parameters = [];
		parse_str($r_body, $parameters);
		
		$request_time = \DateTime::createFromFormat('U.u', $_SERVER['REQUEST_TIME_FLOAT']);
		
		if(!sizeof($parameters) || sizeof($parameters) > 1){
			Log::info(
				'Запрос на сохранение измерения не верной структуры', [
					'ip' => $request->ip(),
					'time' => $request_time,
					'sensor' => $sensor,
					'body' => $r_body
				]
			);
			return response()->json()->setStatusCode(400);
		}

		$parameter_key = key($parameters);
		$value = current($parameters);
		if(!is_numeric($value)){
			Log::info(
				'Измерение не является числом', [
					'ip' => $request->ip(),
					'time' => $request_time,
					'sensor' => $sensor,
					'measure' => $value,
					'body' => $r_body
				]
			);
			return response()->json()->setStatusCode(406);
		}
		
		DB::beginTransaction();
		
		$parameter = $sensor->get_parameter($parameter_key);
		if(is_null($parameter)){
			Log::info(
				'Текущий сенсор не имеет параметр с ключом '.$parameter_key, [
					'ip' => $request->ip(),
					'time' => $request_time,
					'sensor' => $sensor,
					'body' => $r_body
				]
			);
			
			DB::rollBack();
			return response()->json()->setStatusCode(406);
		}
		
		DB::table('measures')->insert([[
			'sensor_id' => $sensor->id,
			'parameter_id' => $parameter->id,
			'value' => (float)$value,
			'time' => $request_time->getTimestamp(),
			'microseconds' => $request_time->format('u'),
		]]);
		
		DB::commit();

		return response()->json()->setStatusCode(200);
	}
	
	public function get_measures(Request $request, ?Sensor $sensor = null, ?Parameter $parameter = null): JsonResponse{
		return response()->json();
	}
}
