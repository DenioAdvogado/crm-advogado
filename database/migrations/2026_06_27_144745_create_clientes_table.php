<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // nome completo (pessoa física) ou razão social (pessoa jurídica)
            $table->enum('tipo_pessoa', ['fisica', 'juridica']);
            $table->enum('pais', ['Brasil', 'Portugal']);

            // Documento principal: CPF/CNPJ no Brasil, NIF em Portugal (física ou jurídica).
            $table->string('documento');
            // Documento secundário: usado apenas para Cartão de Cidadão (CC) de pessoa física
            // em Portugal. Nulo para os demais casos (Brasil, ou Portugal pessoa jurídica).
            $table->string('documento_secundario')->nullable();

            $table->string('telefone')->nullable(); // armazenado com código do país (+55, +351)
            $table->string('email')->nullable();

            $table->string('endereco_logradouro')->nullable();
            $table->string('endereco_cidade')->nullable();
            $table->string('endereco_estado')->nullable(); // estado (Brasil) ou distrito (Portugal)
            $table->string('endereco_cep')->nullable(); // CEP (Brasil) ou código postal (Portugal)
            $table->string('endereco_pais')->nullable();

            // Acesso ao portal do cliente (Bloco 3). Mantido na própria tabela "clientes",
            // isolado das permissões internas da tabela "usuarios", conforme decidido no
            // Bloco 1. O e-mail de login reaproveita a coluna "email" acima.
            $table->string('senha_acesso')->nullable();

            $table->boolean('ativo')->default(true);
            $table->timestamp('criado_em')->useCurrent();
            $table->timestamp('atualizado_em')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes('excluido_em');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
