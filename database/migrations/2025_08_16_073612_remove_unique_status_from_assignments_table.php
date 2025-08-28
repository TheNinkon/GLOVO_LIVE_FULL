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
        Schema::table('assignments', function (Blueprint $table) {
            // Paso 1: Eliminar la clave foránea que depende del índice.
            // El nombre por defecto en Laravel es 'nombretabla_columna_foreign'.
            $table->dropForeign('assignments_account_id_foreign');

            // Paso 2: Ahora sí, eliminar el índice único problemático.
            $table->dropUnique('assignments_account_id_status_unique');

            // Paso 3: Volver a crear la clave foránea. Laravel creará un
            // índice simple (no único) para esta columna, que es lo correcto.
            $table->foreign('account_id')
                  ->references('id')
                  ->on('accounts')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            // Para poder hacer rollback, revertimos el proceso
            $table->dropForeign('assignments_account_id_foreign');
            $table->unique(['account_id', 'status']);
            $table->foreign('account_id')
                  ->references('id')
                  ->on('accounts')
                  ->onDelete('cascade');
        });
    }
};
