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
 * IMPORTANTE: edite o nome, e-mail e senha abaixo com os dados reais antes de rodar
 * `php artisan db:seed --class=ProductionSeeder --force` no VPS. A senha aqui é só um
 * placeholder — troque por uma senha forte definitiva (o usuário pode alterá-la depois de
 * logar, em "Profile").
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Administrador',
            'email' => 'PREENCHER_EMAIL_REAL@advogadointernacional.net',
            'password' => Hash::make('PREENCHER_SENHA_FORTE_AQUI'),
            'access_level' => 'administrator',
            'active' => true,
        ]);
    }
}
