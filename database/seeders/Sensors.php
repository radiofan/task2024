<?php

namespace Database\Seeders;

use DateTime;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Sensors extends Seeder{
    /**
     * Run the database seeds.
     */
    public function run(): void{
        
        DB::beginTransaction();
        
        $now = (new DateTime())->getTimestamp();
        
        DB::table('sensors')->upsert(
            [
                ['id' => 1, 'data' => '1', 'created_at' => $now, 'updated_at' => $now],
                ['id' => 2, 'data' => '2', 'created_at' => $now, 'updated_at' => $now],
                ['id' => 3, 'data' => '3', 'created_at' => $now, 'updated_at' => $now],
            ],
            ['id'],
            ['data', 'updated_at']
        );

        DB::table('parameters')->upsert(
            [
                ['id' => 1, 'name' => 'температура', 'key' => 'T', 'measure_unit' => '°C'],
                ['id' => 2, 'name' => 'давление', 'key' => 'P', 'measure_unit' => 'МПа'],
                ['id' => 3, 'name' => 'Скорость вращения', 'key' => 'v', 'measure_unit' => 'Об/мин'],
            ],
            ['id'],
            ['name', 'key', 'measure_unit']
        );

        DB::table('sensors_parameters')->whereIn('sensor_id', [1, 2, 3])->delete();

        DB::table('sensors_parameters')->insert(
            [
                ['sensor_id' => 1, 'parameter_id' => 1],
                ['sensor_id' => 2, 'parameter_id' => 2],
                ['sensor_id' => 3, 'parameter_id' => 3],
            ]
        );
        
        DB::commit();
    }
}
