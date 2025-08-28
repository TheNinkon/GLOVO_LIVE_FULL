<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->onDelete('cascade');
            $table->foreignId('rider_id')->constrained('riders')->onDelete('cascade');
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->enum('status', ['active', 'ended'])->default('active');
            $table->timestamps();

            // Evitar que una cuenta activa esté asignada a más de un rider a la vez
            $table->unique(['account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
