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
        Schema::create('prefactura_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefactura_id')->constrained()->onDelete('cascade');
            $table->string('courier_id');
            $table->foreignId('rider_id')->nullable()->constrained()->onDelete('set null'); // Asignación manual
            $table->decimal('cash_out', 8, 2)->default(0);
            $table->decimal('tips', 8, 2)->default(0);
            $table->timestamps();
            $table->unique(['prefactura_id', 'courier_id'], 'prefactura_item_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefactura_items');
    }
};
