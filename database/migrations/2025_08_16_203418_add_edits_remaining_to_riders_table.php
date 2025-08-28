<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            // Número de ediciones/comodines restantes para la semana
            $table->unsignedTinyInteger('edits_remaining')->default(3)->after('weekly_contract_hours');
        });
    }

    public function down(): void
    {
        Schema::table('riders', function (Blueprint $table) {
            $table->dropColumn('edits_remaining');
        });
    }
};
