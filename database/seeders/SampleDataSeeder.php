<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\FinancialEntry;
use App\Models\LegalArea;
use App\Models\LegalCase;
use App\Models\Service;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Ana Administradora',
            'email' => 'admin@advogadointernacional.net',
            'password' => Hash::make('senha123'),
            'phone' => '+55 11 90000-0001',
            'access_level' => 'administrator',
            'active' => true,
        ]);

        $lawyer = User::create([
            'name' => 'Carlos Advogado',
            'email' => 'advogado@advogadointernacional.net',
            'password' => Hash::make('senha123'),
            'phone' => '+55 11 90000-0002',
            'access_level' => 'lawyer',
            'active' => true,
        ]);

        // Funcionário de exemplo (Bloco 2): sem acesso ao financeiro por padrão
        // (can_access_financial = false), para validar a Gate "view-financial".
        User::create([
            'name' => 'Fernanda Funcionária',
            'email' => 'funcionario@advogadointernacional.net',
            'password' => Hash::make('senha123'),
            'phone' => '+55 11 90000-0003',
            'access_level' => 'staff',
            'active' => true,
            'can_access_financial' => false,
        ]);

        $laborArea = LegalArea::create([
            'name' => 'Direito Trabalhista',
            'applicable_country' => 'Brazil',
        ]);

        $familyArea = LegalArea::create([
            'name' => 'Direito de Família',
            'applicable_country' => 'Both',
        ]);

        $brazilClient = Client::create([
            'name' => 'João da Silva',
            'person_type' => 'individual',
            'country' => 'Brazil',
            'document_number' => '123.456.789-00', // CPF
            'secondary_document_number' => null,
            'phone' => '+55 21 98888-1234',
            'email' => 'joao.silva@example.com',
            'address_street' => 'Rua das Flores, 100',
            'address_city' => 'Rio de Janeiro',
            'address_state' => 'RJ',
            'address_zipcode' => '20000-000',
            'address_country' => 'Brazil',
            'portal_password' => Hash::make('senha123'),
            'active' => true,
        ]);

        $portugalClient = Client::create([
            'name' => 'Maria Santos',
            'person_type' => 'individual',
            'country' => 'Portugal',
            'document_number' => '123456789', // NIF
            'secondary_document_number' => '12345678 9 ZZ1', // Cartão de Cidadão
            'phone' => '+351 91 234 5678',
            'email' => 'maria.santos@example.com',
            'address_street' => 'Rua Augusta, 50',
            'address_city' => 'Lisboa',
            'address_state' => 'Lisboa',
            'address_zipcode' => '1100-048',
            'address_country' => 'Portugal',
            'portal_password' => Hash::make('senha123'),
            'active' => true,
        ]);

        $brazilClient->legalAreas()->attach($laborArea->id);
        $portugalClient->legalAreas()->attach($familyArea->id);

        $brazilCase = LegalCase::create([
            'client_id' => $brazilClient->id,
            'responsible_lawyer_id' => $lawyer->id,
            'case_number' => '0001234-56.2026.5.01.0001',
            'legal_area_id' => $laborArea->id,
            'country' => 'Brazil',
            'status' => 'in_progress',
            'opened_at' => '2026-01-15',
            'current_deadline' => '2026-08-01',
            'description' => 'Reclamação trabalhista por verbas rescisórias.',
        ]);

        $portugalCase = LegalCase::create([
            'client_id' => $portugalClient->id,
            'responsible_lawyer_id' => $lawyer->id,
            'case_number' => '1234/26.5T8LSB',
            'legal_area_id' => $familyArea->id,
            'country' => 'Portugal',
            'status' => 'in_progress',
            'opened_at' => '2026-02-10',
            'current_deadline' => '2026-09-01',
            'description' => 'Processo de regulação de responsabilidades parentais.',
        ]);

        $brazilService = Service::create([
            'case_id' => $brazilCase->id,
            'client_id' => $brazilClient->id,
            'description' => 'Elaboração de petição inicial.',
            'status' => 'in_progress',
            'execution_deadline' => '2026-07-15',
            'responsible_id' => $lawyer->id,
        ]);

        $portugalService = Service::create([
            'case_id' => $portugalCase->id,
            'client_id' => $portugalClient->id,
            'description' => 'Elaboração de requerimento inicial.',
            'status' => 'in_progress',
            'execution_deadline' => '2026-08-15',
            'responsible_id' => $lawyer->id,
        ]);

        Task::create([
            'service_id' => $brazilService->id,
            'case_id' => $brazilCase->id,
            'responsible_id' => $lawyer->id,
            'title' => 'Revisar documentação do cliente',
            'description' => 'Conferir CPF, contracheques e carteira de trabalho.',
            'due_date' => '2026-07-10 17:00:00',
            'status' => 'pending',
        ]);

        Task::create([
            'service_id' => $portugalService->id,
            'case_id' => $portugalCase->id,
            'responsible_id' => $lawyer->id,
            'title' => 'Revisar documentação do cliente',
            'description' => 'Conferir NIF, Cartão de Cidadão e certidão de nascimento dos filhos.',
            'due_date' => '2026-08-10 17:00:00',
            'status' => 'pending',
        ]);

        FinancialEntry::create([
            'client_id' => $brazilClient->id,
            'case_id' => $brazilCase->id,
            'type' => 'income',
            'description' => 'Honorários iniciais - processo trabalhista.',
            'amount' => 3500.00,
            'currency' => 'BRL',
            'due_date' => '2026-07-01',
            'payment_date' => null,
            'status' => 'pending',
        ]);

        FinancialEntry::create([
            'client_id' => $portugalClient->id,
            'case_id' => $portugalCase->id,
            'type' => 'income',
            'description' => 'Honorários iniciais - processo de família.',
            'amount' => 800.00,
            'currency' => 'EUR',
            'due_date' => '2026-08-01',
            'payment_date' => null,
            'status' => 'pending',
        ]);
    }
}
