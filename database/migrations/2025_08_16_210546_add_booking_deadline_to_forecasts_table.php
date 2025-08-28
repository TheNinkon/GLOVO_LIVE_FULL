<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('forecasts', function (Blueprint $table) {
            // Fecha y hora límite para que los riders puedan seleccionar/modificar turnos
            $table->dateTime('booking_deadline')->nullable()->after('forecast_data');
        });
    }

    public function down(): void
    {
        Schema::table('forecasts', function (Blueprint $table) {
            $table->dropColumn('booking_deadline');
        });
    }
};
