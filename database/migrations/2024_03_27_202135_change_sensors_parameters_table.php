<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
	/**
	 * Run the migrations.
	 */
	public function up(): void{
		Schema::table('sensors_parameters', function(Blueprint $table){
			$table->primary(['sensor_id', 'parameter_id']);
		});
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void{
		Schema::table('sensors_parameters', function(Blueprint $table){
			$table->dropPrimary(['sensor_id', 'parameter_id']);
		});
	}
};
