<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "emails_enviados" no texto do Bloco 6 -> "email_logs" em inglês, seguindo a
        // convenção de tabelas do projeto (CLAUDE.md). cliente_id -> client_id,
        // processo_atualizacao_id -> case_update_id, enviado_em -> sent_at,
        // status_envio -> status.
        Schema::create('email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('case_update_id')->constrained('case_updates')->cascadeOnDelete();
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_logs');
    }
};
