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
        Schema::table('prefactura_items', function (Blueprint $table) {
            $table->decimal('cash_out_assigned', 8, 2)->default(0)->after('tips');
            $table->decimal('tips_assigned', 8, 2)->default(0)->after('cash_out_assigned');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prefactura_items', function (Blueprint $table) {
            $table->dropColumn(['cash_out_assigned', 'tips_assigned']);
        });
    }
};
