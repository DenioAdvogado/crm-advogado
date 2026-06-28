<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // full name (individual) or company name (corporate)
            $table->enum('person_type', ['individual', 'company']);
            $table->enum('country', ['Brazil', 'Portugal']);

            // Main document: CPF/CNPJ in Brazil, NIF in Portugal (individual or company).
            $table->string('document_number');
            // Secondary document: used only for Cartão de Cidadão (CC) of individuals in
            // Portugal. Null for all other cases (Brazil, or Portugal companies).
            $table->string('secondary_document_number')->nullable();

            $table->string('phone')->nullable(); // stored with country code (+55, +351)
            $table->string('email')->nullable();

            $table->string('address_street')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable(); // state (Brazil) or district (Portugal)
            $table->string('address_zipcode')->nullable(); // CEP (Brazil) or código postal (Portugal)
            $table->string('address_country')->nullable();

            // Client portal access (Block 3). Kept on the "clients" table itself, isolated
            // from internal "users" permissions. Login email reuses the "email" column above.
            $table->string('portal_password')->nullable();

            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
