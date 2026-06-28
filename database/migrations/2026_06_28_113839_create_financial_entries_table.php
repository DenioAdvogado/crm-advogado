<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('case_id')->nullable()->constrained('cases')->nullOnDelete();
            $table->enum('type', ['income', 'expense']);
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->enum('currency', ['BRL', 'EUR']); // BRL for Brazil, EUR for Portugal
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->enum('status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_entries');
    }
};
