<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bloco 7: cada usuário interno conecta a própria conta Google (não existe conta
        // centralizada — prazos são individuais por responsável). Tokens guardados
        // criptografados via cast "encrypted" no Model (não em texto puro no banco).
        Schema::table('users', function (Blueprint $table) {
            $table->text('google_access_token')->nullable();
            $table->text('google_refresh_token')->nullable();
            $table->timestamp('google_token_expires_at')->nullable();
            $table->timestamp('google_calendar_connected_at')->nullable();
            $table->text('google_calendar_last_error')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'google_access_token',
                'google_refresh_token',
                'google_token_expires_at',
                'google_calendar_connected_at',
                'google_calendar_last_error',
            ]);
        });
    }
};
