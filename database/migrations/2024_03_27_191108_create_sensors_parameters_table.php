<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sensors_parameters', function (Blueprint $table){
            $table->foreignId('sensor_id')
                  ->constrained('sensors', 'id')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreignId('parameter_id')
                  ->constrained('parameters', 'id')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors_parameters');
    }
};
