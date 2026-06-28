<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "notificar_cliente" no texto do Bloco 4 -> "notify_client" aqui, seguindo a
        // convenção de colunas em inglês (CLAUDE.md). Só grava a intenção de notificação
        // por enquanto; o envio de e-mail de fato é implementado no Bloco 6.
        Schema::table('case_updates', function (Blueprint $table) {
            $table->boolean('notify_client')->default(false)->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('case_updates', function (Blueprint $table) {
            $table->dropColumn('notify_client');
        });
    }
};
