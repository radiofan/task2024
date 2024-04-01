<?php

namespace App\Http\Controllers;

use App\Models\Sensor;
use Carbon\CarbonImmutable;
use DateTime;
use Exception;
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

	/**
	 * Возвращает результат работы метода get_only_measures
	 * или ошибку 406 в случае неверного формата данных
	 * 
	 * Из запроса извлекает
	 * <pre>
	 * 	datetime 'start', в случае отсутствия 'start' принимает значение старта текущего дня
	 * 	datetime 'end', в случае отсутствия 'end' принимает значение конца текущего дня
	 * 	string|int|int[] 'sensors' - 'all' (все сенсоры) | ID сенсора | массив ID сенсоров
	 * 	string|string[] 'parameters' - 'all' (все параметры) | ключ параметра | массив ключей параметров
	 * </pre>
	 * 
	 * @see get_only_measures
	 * 
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function get_measures(Request $request): JsonResponse{
		
		$now = CarbonImmutable::now();
		
		$start_time = now()->startOfDay();
		$end_time = now()->endOfDay();
		
		if($request->has('start')){
			try{
				$start_time = $request->date('start');
			}catch(Exception $ex){
				return response()->json()->setStatusCode(406);
			}
		}

		if($request->has('end')){
			try{
				$end_time = $request->date('end');
			}catch(Exception $ex){
				return response()->json()->setStatusCode(406);
			}
		}

		$sensors = $request->input('sensors');
		$parameters = $request->input('parameters');
		
		if(empty($sensors) || empty($parameters)){
			return response()->json([]);
		}
		
		
		return $this->get_only_measures($sensors, $parameters, $start_time, $end_time);
	}

	/**
	 * Возвращает JSON массив измерений или ошибку 406 если $sensors или $parameters имеют не верный формат
	 * 
	 * <pre>
	 * [
	 * 	sensors.id => [
	 * 		parameters.key => [
	 * 			[
	 * 				'value' => float,
	 * 				'time' => int, (timestamp)
	 * 				'microseconds' => int,
	 * 			],
	 * 			...
	 * 		],
	 * 		...
	 * 	],
	 * 	...
	 * ]
	 * <pre>
	 * 
	 * @param string|int|int[] $sensors - 'all' (все сенсоры) | ID сенсора | массив ID сенсоров
	 * @param string|string[] $parameters - 'all' (все параметры) | ключ параметра | массив ключей параметров
	 * @param DateTime $start_time - время, начиная с которого извлекать данные
	 * @param DateTime $end_time - время, заканчивая которым извлекать данные
	 * @return JsonResponse
	 */
	protected function get_only_measures($sensors, $parameters, DateTime $start_time, DateTime $end_time) :JsonResponse{
		
		$query = DB::table('measures')
			->select(['measures.*', 'parameters.key'])
			->where([
				['time', '>=', $start_time->getTimestamp()],
				['time', '<', $end_time->getTimestamp()],
			])
			->join('parameters', 'measures.parameter_id', '=', 'parameters.id')
			->orderBy('sensor_id')
			->orderBy('parameter_id')
			->orderBy('time')
			->orderBy('microseconds');
		
		
		if($sensors !== 'all'){
			
			//фильтр по сенсорам 
			
			if(is_numeric($sensors)){
				$sensors = (int) $sensors;
				$query->where('sensor_id', '=', $sensors);
				
			}else if(is_array($sensors)){
				$sensors = collect($sensors)
					->filter(function ($value, $key) {
						return is_numeric($value);
					})
					->values()
					->unique()
					->all();
				
				if(!$sensors){
					return response()->json([]);
				}

				$query->whereIn('sensor_id', $sensors);
				
			}else{
				return response()->json()->setStatusCode(406);
			}
		}
		
		if($parameters !== 'all'){

			//фильтр по параметрам
			if(is_string($parameters)){
				$query->where('parameters.key', '=', $parameters);

			}else if(is_array($parameters)){
				$parameters = collect($parameters)
					->filter(function ($value, $key) {
						return is_string($value);
					})
					->values()
					->unique()
					->all();

				if(!$parameters){
					return response()->json([]);
				}

				$query->whereIn('parameters.key', $parameters);

			}else{
				return response()->json()->setStatusCode(406);
			}
		}

		//группировка измерений
		
		$measures = $query->get();
		$measures_ret = [];

		foreach($measures as $measure){
			$sensor_id = $measure->sensor_id;
			$p_key = $measure->key;
			
			unset($measure->sensor_id, $measure->key, $measure->parameter_id);
			if(!isset($measures_ret[$sensor_id])){
				$measures_ret[$sensor_id] = [];
			}
			
			if(!isset($measures_ret[$sensor_id][$p_key])){
				$measures_ret[$sensor_id][$p_key] = [];
			}
			
			$measures_ret[$sensor_id][$p_key][] = $measure;
		}
		
		return response()->json($measures_ret);
	}
}
