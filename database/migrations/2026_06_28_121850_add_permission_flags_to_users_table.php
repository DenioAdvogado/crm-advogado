<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Permissões configuráveis pelo administrador (Bloco 2), em vez de regras fixas
            // por access_level:
            // - can_view_all_cases: só relevante para "lawyer". Quando true, o advogado vê
            //   também os casos de outros advogados, não só os que ele é responsável.
            // - can_access_financial: só relevante para "staff". Quando true, o funcionário
            //   passa a ter acesso ao módulo financeiro (Bloco 5), que é bloqueado por padrão.
            $table->boolean('can_view_all_cases')->default(false)->after('access_level');
            $table->boolean('can_access_financial')->default(false)->after('can_view_all_cases');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['can_view_all_cases', 'can_access_financial']);
        });
    }
};
