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
        Schema::create('prefactura_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prefactura_item_id')->constrained('prefactura_items')->onDelete('cascade');
            $table->foreignId('rider_id')->constrained('riders')->onDelete('cascade');
            $table->decimal('amount', 8, 2);
            $table->enum('type', ['cash_out', 'tips']);
            $table->enum('status', ['pending', 'paid', 'deducted'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefactura_assignments');
    }
};
