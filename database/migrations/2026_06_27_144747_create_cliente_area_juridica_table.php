<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cliente_area_juridica', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('area_juridica_id')->constrained('areas_juridicas')->cascadeOnDelete();
            $table->timestamp('criado_em')->useCurrent();

            $table->unique(['cliente_id', 'area_juridica_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_area_juridica');
    }
};
