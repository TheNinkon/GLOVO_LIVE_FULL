<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            // Añadimos un campo booleano para el estado de bloqueo del horario
            if (!Schema::hasColumn('riders', 'schedule_is_locked')) {
                $table->boolean('schedule_is_locked')->default(false)->after('weekly_contract_hours');
            }
        });

        Schema::table('schedules', function (Blueprint $table) {
            // Añadimos un campo para marcar si el horario ha sido copiado por el admin
            // Nota: Este cambio asume que la columna 'status' ya existe.
            if (Schema::hasColumn('schedules', 'status') && !Schema::hasColumn('schedules', 'is_submitted')) {
                $table->boolean('is_submitted')->default(false)->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            if (Schema::hasColumn('riders', 'schedule_is_locked')) {
                $table->dropColumn('schedule_is_locked');
            }
        });

        Schema::table('schedules', function (Blueprint $table) {
            if (Schema::hasColumn('schedules', 'is_submitted')) {
                $table->dropColumn('is_submitted');
            }
        });
    }
};
