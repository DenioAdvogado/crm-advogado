<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\LegalArea;
use App\Models\LegalCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Isolamento de dados do portal (Bloco 3): um cliente não pode ver o processo de outro
 * cliente, mesmo sabendo o ID e trocando a URL manualmente.
 */
class CaseIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_cannot_view_another_clients_case(): void
    {
        $area = LegalArea::create(['name' => 'Direito Trabalhista', 'applicable_country' => 'Brazil']);

        $clientA = Client::create([
            'name' => 'Cliente A',
            'person_type' => 'individual',
            'country' => 'Brazil',
            'document_number' => '11111111111',
            'email' => 'clientea@example.com',
            'portal_password' => Hash::make('senha123'),
            'active' => true,
        ]);

        $clientB = Client::create([
            'name' => 'Cliente B',
            'person_type' => 'individual',
            'country' => 'Brazil',
            'document_number' => '22222222222',
            'email' => 'clienteb@example.com',
            'portal_password' => Hash::make('senha123'),
            'active' => true,
        ]);

        $caseB = LegalCase::create([
            'client_id' => $clientB->id,
            'legal_area_id' => $area->id,
            'country' => 'Brazil',
            'status' => 'in_progress',
            'opened_at' => now(),
        ]);

        $response = $this->actingAs($clientA, 'client')->get("/portal/processos/{$caseB->id}");

        $response->assertForbidden();
    }
}
