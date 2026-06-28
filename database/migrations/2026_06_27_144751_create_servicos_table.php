<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('servicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->text('descricao');
            $table->enum('status', ['pendente', 'em_andamento', 'concluido'])->default('pendente');
            $table->date('prazo_execucao')->nullable();
            $table->foreignId('responsavel_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes('excluido_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicos');
    }
};
