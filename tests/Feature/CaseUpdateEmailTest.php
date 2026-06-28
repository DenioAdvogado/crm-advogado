<?php

namespace Tests\Feature;

use App\Jobs\SendCaseUpdateEmail;
use App\Models\CaseUpdate;
use App\Models\Client;
use App\Models\EmailLog;
use App\Models\LegalArea;
use App\Models\LegalCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * Gatilho de e-mail automático (Bloco 6): criar uma CaseUpdate com notify_client = true
 * deve gerar um EmailLog e despachar o Job de envio para a fila.
 */
class CaseUpdateEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_case_update_with_notify_client_dispatches_email_job(): void
    {
        Queue::fake();

        $area = LegalArea::create(['name' => 'Direito Trabalhista', 'applicable_country' => 'Brazil']);

        $client = Client::create([
            'name' => 'Cliente Teste',
            'person_type' => 'individual',
            'country' => 'Brazil',
            'document_number' => '12345678900',
            'email' => 'cliente@example.com',
            'portal_password' => Hash::make('senha123'),
            'active' => true,
        ]);

        $case = LegalCase::create([
            'client_id' => $client->id,
            'legal_area_id' => $area->id,
            'country' => 'Brazil',
            'status' => 'in_progress',
            'opened_at' => now(),
        ]);

        $caseUpdate = CaseUpdate::create([
            'case_id' => $case->id,
            'description' => 'Audiência marcada.',
            'notify_client' => true,
        ]);

        $this->assertDatabaseHas('email_logs', [
            'client_id' => $client->id,
            'case_update_id' => $caseUpdate->id,
            'status' => 'pending',
        ]);

        Queue::assertPushed(SendCaseUpdateEmail::class);
    }

    public function test_case_update_without_notify_client_does_not_dispatch_email(): void
    {
        Queue::fake();

        $area = LegalArea::create(['name' => 'Direito Trabalhista', 'applicable_country' => 'Brazil']);

        $client = Client::create([
            'name' => 'Cliente Teste',
            'person_type' => 'individual',
            'country' => 'Brazil',
            'document_number' => '12345678900',
            'email' => 'cliente@example.com',
            'portal_password' => Hash::make('senha123'),
            'active' => true,
        ]);

        $case = LegalCase::create([
            'client_id' => $client->id,
            'legal_area_id' => $area->id,
            'country' => 'Brazil',
            'status' => 'in_progress',
            'opened_at' => now(),
        ]);

        CaseUpdate::create([
            'case_id' => $case->id,
            'description' => 'Atualização interna, sem notificar.',
            'notify_client' => false,
        ]);

        $this->assertDatabaseCount('email_logs', 0);
        Queue::assertNotPushed(SendCaseUpdateEmail::class);
    }
}
