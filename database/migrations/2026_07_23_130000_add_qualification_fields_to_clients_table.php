<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Qualificação (pessoa física)
            $table->string('nationality')->nullable()->after('secondary_document_number');
            $table->string('marital_status')->nullable()->after('nationality');
            $table->boolean('stable_union')->default(false)->after('marital_status');
            $table->string('profession')->nullable()->after('stable_union');
            $table->date('birth_date')->nullable()->after('profession');
            $table->string('document_issuer')->nullable()->after('birth_date');
            $table->string('mother_name')->nullable()->after('document_issuer');
            $table->string('father_name')->nullable()->after('mother_name');
            // Endereço completo
            $table->string('address_number')->nullable()->after('address_street');
            $table->string('address_complement')->nullable()->after('address_number');
            $table->string('address_neighborhood')->nullable()->after('address_complement');
            // Pessoa jurídica
            $table->string('company_legal_name')->nullable()->after('address_country');
            $table->string('company_trade_name')->nullable()->after('company_legal_name');
            $table->string('legal_representative')->nullable()->after('company_trade_name');
            $table->string('legal_representative_document')->nullable()->after('legal_representative');
            $table->string('legal_representative_role')->nullable()->after('legal_representative_document');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'nationality', 'marital_status', 'stable_union', 'profession',
                'birth_date', 'document_issuer', 'mother_name', 'father_name',
                'address_number', 'address_complement', 'address_neighborhood',
                'company_legal_name', 'company_trade_name', 'legal_representative',
                'legal_representative_document', 'legal_representative_role',
            ]);
        });
    }
};
