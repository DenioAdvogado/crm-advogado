<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Gate "view-financial" (Bloco 2/5): funcionário sem "can_access_financial" não pode
 * acessar o módulo financeiro, mesmo digitando a URL diretamente.
 */
class FinancialAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_without_permission_is_blocked_from_financial_module(): void
    {
        $staff = User::factory()->create([
            'access_level' => 'staff',
            'can_access_financial' => false,
        ]);

        $response = $this->actingAs($staff, 'web')->get('/admin/financeiro');

        $response->assertForbidden();
    }

    public function test_staff_with_permission_can_access_financial_module(): void
    {
        $staff = User::factory()->create([
            'access_level' => 'staff',
            'can_access_financial' => true,
        ]);

        $response = $this->actingAs($staff, 'web')->get('/admin/financeiro');

        $response->assertOk();
    }
}
