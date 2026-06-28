<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tarefas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('servico_id')->nullable()->constrained('servicos')->cascadeOnDelete();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->cascadeOnDelete();
            $table->foreignId('responsavel_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->dateTime('prazo')->nullable();
            $table->enum('status', ['pendente', 'em_andamento', 'concluida', 'atrasada'])->default('pendente');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes('excluido_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tarefas');
    }
};
