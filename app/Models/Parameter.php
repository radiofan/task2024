<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class Parameter
 * @package App\Models
 * 
 *
 *
 * @property int $id
 * @property string $name
 * @property string $key
 * @property string $measure_unit
 */
class Parameter extends Model{
	use HasFactory;

	/** @var string $table - Таблица БД, ассоциированная с моделью */
	protected $table = 'parameters';
	/** @var string $primaryKey - Первичный ключ в таблице */
	protected $primaryKey = 'id';
	/** @var bool $timestamps - Требуется ли автоматическое управление столбцами created_at и updated_at */
	public $timestamps = false;

	/**
	 * Ключ маршрута модели
	 * @return string - 'key'
	 */
	public function getRouteKeyName(): string{
		return 'key';
	}
	
	/**
	 * Сенсоры, измеряющие данный параметр
	 * @return BelongsToMany
	 */
	public function sensors(): BelongsToMany{
		return $this->belongsToMany(Sensor::class, 'sensors_parameters');
	}
}
