<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forecasts', function (Blueprint $table) {
            $table->id();
            $table->string('city'); // Ciudad a la que aplica el forecast
            $table->date('week_start_date'); // Lunes de la semana del forecast
            $table->json('forecast_data'); // Aquí guardaremos la matriz de demanda
            $table->timestamps();

            // Índice para búsquedas rápidas por ciudad y semana
            $table->unique(['city', 'week_start_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forecasts');
    }
};
