<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('responsible_lawyer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('case_number')->nullable(); // free text: format varies between Brazil and Portugal
            $table->foreignId('legal_area_id')->constrained('legal_areas');
            $table->enum('country', ['Brazil', 'Portugal']);
            $table->enum('status', ['in_progress', 'completed', 'suspended', 'archived'])->default('in_progress');
            $table->date('opened_at');
            $table->date('current_deadline')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('case_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cases');
    }
};
