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
 * NÃO fica hardcoded aqui de propósito — este arquivo é versionado no Git, e uma senha em
 * texto puro no código-fonte ficaria exposta no histórico do repositório para sempre,
 * mesmo que depois seja trocada. Em vez disso, a senha é lida de
 * `PRODUCTION_ADMIN_PASSWORD` no `.env` (que nunca é commitado — ver `.gitignore`).
 * Defina essa variável no `.env` de produção antes de rodar
 * `php artisan db:seed --class=ProductionSeeder --force` no VPS. Pode remover a variável
 * do `.env` depois de rodar o seeder uma vez, se preferir não deixá-la lá (o usuário pode
 * trocar a senha depois, em "Profile", uma vez logado).
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('PRODUCTION_ADMIN_PASSWORD');

        if (empty($password)) {
            throw new \RuntimeException(
                'Defina PRODUCTION_ADMIN_PASSWORD no .env antes de rodar este seeder.'
            );
        }

        User::create([
            'name' => 'Denio Gonçalves',
            'email' => 'degon.pt@gmail.com',
            'password' => Hash::make($password),
            'access_level' => 'administrator',
            'active' => true,
        ]);
    }
}
