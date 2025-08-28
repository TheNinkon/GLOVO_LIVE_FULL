<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('glovo_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('courier_id');
            $table->string('transport')->nullable();
            $table->date('fecha');
            $table->string('ciudad')->nullable();
            $table->decimal('pedidos_entregados', 8, 2)->default(0);
            $table->decimal('cancelados', 5, 2)->default(0);
            $table->decimal('reasignaciones', 5, 2)->default(0);
            $table->decimal('no_show', 5, 2)->default(0);
            $table->decimal('horas', 8, 2)->default(0);
            $table->decimal('ratio_entrega', 5, 2)->default(0);
            $table->decimal('tiempo_promedio', 5, 2)->default(0);
            $table->decimal('ineligible', 5, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('glovo_metrics');
    }
};
