<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_id')->constrained('riders')->onDelete('cascade');
            $table->foreignId('forecast_id')->constrained('forecasts')->onDelete('cascade');
            $table->date('slot_date');
            $table->time('slot_time');
            $table->timestamps();

            // Un rider no puede apuntarse dos veces a la misma hora del mismo día
            $table->unique(['rider_id', 'slot_date', 'slot_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
