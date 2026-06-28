<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_legal_area', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('legal_area_id')->constrained('legal_areas')->cascadeOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['client_id', 'legal_area_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_legal_area');
    }
};
