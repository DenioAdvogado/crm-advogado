<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('processos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('advogado_responsavel_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('numero_processo')->nullable(); // formato livre: varia entre Brasil e Portugal
            $table->foreignId('area_juridica_id')->constrained('areas_juridicas');
            $table->enum('pais', ['Brasil', 'Portugal']);
            $table->enum('status', ['em_andamento', 'concluido', 'suspenso', 'arquivado'])->default('em_andamento');
            $table->date('data_abertura');
            $table->date('prazo_atual')->nullable();
            $table->text('descricao')->nullable();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes('excluido_em');

            $table->index('numero_processo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('processos');
    }
};
