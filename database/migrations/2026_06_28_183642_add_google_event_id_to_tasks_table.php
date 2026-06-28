<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Guarda o ID do evento criado no Google Calendar do responsável (Bloco 7), para
        // permitir atualizar ou remover o mesmo evento depois, em vez de criar duplicados.
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('google_event_id')->nullable()->after('completed_at');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('google_event_id');
        });
    }
};
