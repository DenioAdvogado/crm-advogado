<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela "usuarios" é exclusiva para a equipe interna do escritório
        // (Administrador, Advogado, Funcionário). Clientes têm tabela própria ("clientes"),
        // com acesso de portal isolado das permissões internas. Login/autenticação serão
        // implementados no Bloco 2 — aqui só a estrutura de dados.
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('senha');
            $table->string('telefone')->nullable();
            $table->enum('nivel_acesso', ['administrador', 'advogado', 'funcionario']);
            $table->boolean('ativo')->default(true);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes('excluido_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
