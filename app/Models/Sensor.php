<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Sensor
 * @package App\Models
 * 
 * @property int $id
 * @property string $data
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class Sensor extends Model{
	use HasFactory;

	/** @var string $table - Таблица БД, ассоциированная с моделью */
	protected $table = 'sensors';
	/** @var string $primaryKey - Первичный ключ в таблице */
	protected $primaryKey = 'id';
	/** @var string $dateFormat - формат даты в БД */
	protected $dateFormat = 'Y-m-d H:i:s';

	/**
	 * Ключ маршрута модели
	 * @return string - 'id'
	 */
	public function getRouteKeyName(): string{
		return 'id';
	}

	/**
	 * Параметры сенсора
	 * @return BelongsToMany
	 */
	public function parameters(): BelongsToMany{
		return $this->belongsToMany(Parameter::class, 'sensors_parameters');
	}

	/**
	 * Получить параметр через его ключ, если сенсор его измеряет
	 * @param string $key - ключ параметра
	 * @return Parameter|null
	 */
	public function get_parameter(string $key): ?Parameter{
		return $this->parameters()
			->where('key', $key)
			->first();
	}
}
