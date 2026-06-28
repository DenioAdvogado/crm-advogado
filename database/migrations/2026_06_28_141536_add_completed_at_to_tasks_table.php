<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Necessário para o painel de produtividade (Bloco 4 / reaproveitado no Bloco 8):
        // "updated_at" não é confiável para saber QUANDO a tarefa foi concluída (qualquer
        // edição muda esse campo). completed_at só é preenchido na ação de concluir tarefa.
        Schema::table('tasks', function (Blueprint $table) {
            $table->timestamp('completed_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('completed_at');
        });
    }
};
