<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // pais_aplicavel: algumas áreas jurídicas têm nomenclatura ou regras diferentes
        // entre Brasil e Portugal (ex: "Direito do Trabalho" no Brasil vs "Direito Laboral"
        // em Portugal). "Ambos" indica que a área se aplica igualmente aos dois países.
        Schema::create('areas_juridicas', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->enum('pais_aplicavel', ['Brasil', 'Portugal', 'Ambos'])->default('Ambos');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('areas_juridicas');
    }
};
