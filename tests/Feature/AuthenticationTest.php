<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_internal_user_can_login(): void
    {
        $user = User::factory()->create([
            'access_level' => 'administrator',
            'password' => Hash::make('senha123'),
        ]);

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'senha123',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_client_can_login_to_portal(): void
    {
        $client = Client::create([
            'name' => 'Cliente Teste',
            'person_type' => 'individual',
            'country' => 'Brazil',
            'document_number' => '12345678900',
            'email' => 'cliente.teste@example.com',
            'portal_password' => Hash::make('senha123'),
            'active' => true,
        ]);

        $response = $this->post('/portal/login', [
            'email' => $client->email,
            'password' => 'senha123',
        ]);

        $response->assertRedirect('/portal/dashboard');
        $this->assertAuthenticatedAs($client, 'client');
    }
}
