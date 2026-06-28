<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder de produção (Bloco 10) — cria SÓ o usuário administrador real do escritório, sem
 * nenhum dado fictício. Diferente do SampleDataSeeder (usado em desenvolvimento/teste, com
 * clientes/processos/tarefas fake), este é o único seeder que deve rodar no banco de
 * produção do VPS.
 *
 * IMPORTANTE: nome e e-mail já preenchidos com os dados reais do administrador. A senha
 * abaixo ainda é só um placeholder — troque por uma senha forte definitiva antes de rodar
 * `php artisan db:seed --class=ProductionSeeder --force` no VPS (o usuário pode alterá-la
 * depois de logar, em "Profile", mas não suba para produção com o placeholder).
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Denio Gonçalves',
            'email' => 'degon.pt@gmail.com',
            'password' => Hash::make('PREENCHER_SENHA_FORTE_AQUI'),
            'access_level' => 'administrator',
            'active' => true,
        ]);
    }
}
