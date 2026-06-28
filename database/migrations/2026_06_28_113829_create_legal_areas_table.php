<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // applicable_country: some legal areas have different naming or rules between
        // Brazil and Portugal (e.g. "Labor Law" in Brazil vs "Labour Law" in Portugal).
        // "both" means the area applies equally to both countries.
        Schema::create('legal_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('applicable_country', ['Brazil', 'Portugal', 'Both'])->default('Both');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_areas');
    }
};
