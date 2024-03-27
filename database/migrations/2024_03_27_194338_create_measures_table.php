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
        Schema::create('measures', function (Blueprint $table){
            $table->foreignId('sensor_id')
                  ->constrained('sensors', 'id')
                  ->onUpdate('cascade')
                  ->onDelete('cascade');

            $table->foreignId('parameter_id')
                  ->constrained('parameters', 'id')
                  ->onUpdate('cascade')
                  ->onDelete('restrict');
            
            $table->double('value');
            $table->integer('time');
            $table->integer('microseconds');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('measures');
    }
};
