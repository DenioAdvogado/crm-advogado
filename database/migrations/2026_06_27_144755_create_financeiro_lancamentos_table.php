<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financeiro_lancamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('processo_id')->nullable()->constrained('processos')->nullOnDelete();
            $table->enum('tipo', ['receita', 'despesa']);
            $table->string('descricao');
            $table->decimal('valor', 12, 2);
            $table->enum('moeda', ['BRL', 'EUR']); // BRL para Brasil, EUR para Portugal
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->enum('status', ['pendente', 'pago', 'atrasado', 'cancelado'])->default('pendente');
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes('excluido_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financeiro_lancamentos');
    }
};
