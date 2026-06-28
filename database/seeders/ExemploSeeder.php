<?php

namespace Database\Seeders;

use App\Models\AreaJuridica;
use App\Models\Cliente;
use App\Models\FinanceiroLancamento;
use App\Models\Processo;
use App\Models\Servico;
use App\Models\Tarefa;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ExemploSeeder extends Seeder
{
    public function run(): void
    {
        $administrador = Usuario::create([
            'nome' => 'Ana Administradora',
            'email' => 'admin@advogadointernacional.net',
            'senha' => Hash::make('senha123'),
            'telefone' => '+55 11 90000-0001',
            'nivel_acesso' => 'administrador',
            'ativo' => true,
        ]);

        $advogado = Usuario::create([
            'nome' => 'Carlos Advogado',
            'email' => 'advogado@advogadointernacional.net',
            'senha' => Hash::make('senha123'),
            'telefone' => '+55 11 90000-0002',
            'nivel_acesso' => 'advogado',
            'ativo' => true,
        ]);

        $areaTrabalhista = AreaJuridica::create([
            'nome' => 'Direito Trabalhista',
            'pais_aplicavel' => 'Brasil',
        ]);

        $areaFamilia = AreaJuridica::create([
            'nome' => 'Direito de Família',
            'pais_aplicavel' => 'Ambos',
        ]);

        $clienteBrasil = Cliente::create([
            'nome' => 'João da Silva',
            'tipo_pessoa' => 'fisica',
            'pais' => 'Brasil',
            'documento' => '123.456.789-00', // CPF
            'documento_secundario' => null,
            'telefone' => '+55 21 98888-1234',
            'email' => 'joao.silva@example.com',
            'endereco_logradouro' => 'Rua das Flores, 100',
            'endereco_cidade' => 'Rio de Janeiro',
            'endereco_estado' => 'RJ',
            'endereco_cep' => '20000-000',
            'endereco_pais' => 'Brasil',
            'senha_acesso' => Hash::make('senha123'),
            'ativo' => true,
        ]);

        $clientePortugal = Cliente::create([
            'nome' => 'Maria Santos',
            'tipo_pessoa' => 'fisica',
            'pais' => 'Portugal',
            'documento' => '123456789', // NIF
            'documento_secundario' => '12345678 9 ZZ1', // Cartão de Cidadão
            'telefone' => '+351 91 234 5678',
            'email' => 'maria.santos@example.com',
            'endereco_logradouro' => 'Rua Augusta, 50',
            'endereco_cidade' => 'Lisboa',
            'endereco_estado' => 'Lisboa',
            'endereco_cep' => '1100-048',
            'endereco_pais' => 'Portugal',
            'senha_acesso' => Hash::make('senha123'),
            'ativo' => true,
        ]);

        $clienteBrasil->areasJuridicas()->attach($areaTrabalhista->id);
        $clientePortugal->areasJuridicas()->attach($areaFamilia->id);

        $processoBrasil = Processo::create([
            'cliente_id' => $clienteBrasil->id,
            'advogado_responsavel_id' => $advogado->id,
            'numero_processo' => '0001234-56.2026.5.01.0001',
            'area_juridica_id' => $areaTrabalhista->id,
            'pais' => 'Brasil',
            'status' => 'em_andamento',
            'data_abertura' => '2026-01-15',
            'prazo_atual' => '2026-08-01',
            'descricao' => 'Reclamação trabalhista por verbas rescisórias.',
        ]);

        $processoPortugal = Processo::create([
            'cliente_id' => $clientePortugal->id,
            'advogado_responsavel_id' => $advogado->id,
            'numero_processo' => '1234/26.5T8LSB',
            'area_juridica_id' => $areaFamilia->id,
            'pais' => 'Portugal',
            'status' => 'em_andamento',
            'data_abertura' => '2026-02-10',
            'prazo_atual' => '2026-09-01',
            'descricao' => 'Processo de regulação de responsabilidades parentais.',
        ]);

        $servicoBrasil = Servico::create([
            'processo_id' => $processoBrasil->id,
            'cliente_id' => $clienteBrasil->id,
            'descricao' => 'Elaboração de petição inicial.',
            'status' => 'em_andamento',
            'prazo_execucao' => '2026-07-15',
            'responsavel_id' => $advogado->id,
        ]);

        $servicoPortugal = Servico::create([
            'processo_id' => $processoPortugal->id,
            'cliente_id' => $clientePortugal->id,
            'descricao' => 'Elaboração de requerimento inicial.',
            'status' => 'em_andamento',
            'prazo_execucao' => '2026-08-15',
            'responsavel_id' => $advogado->id,
        ]);

        Tarefa::create([
            'servico_id' => $servicoBrasil->id,
            'processo_id' => $processoBrasil->id,
            'responsavel_id' => $advogado->id,
            'titulo' => 'Revisar documentação do cliente',
            'descricao' => 'Conferir CPF, contracheques e carteira de trabalho.',
            'prazo' => '2026-07-10 17:00:00',
            'status' => 'pendente',
        ]);

        Tarefa::create([
            'servico_id' => $servicoPortugal->id,
            'processo_id' => $processoPortugal->id,
            'responsavel_id' => $advogado->id,
            'titulo' => 'Revisar documentação do cliente',
            'descricao' => 'Conferir NIF, Cartão de Cidadão e certidão de nascimento dos filhos.',
            'prazo' => '2026-08-10 17:00:00',
            'status' => 'pendente',
        ]);

        FinanceiroLancamento::create([
            'cliente_id' => $clienteBrasil->id,
            'processo_id' => $processoBrasil->id,
            'tipo' => 'receita',
            'descricao' => 'Honorários iniciais - processo trabalhista.',
            'valor' => 3500.00,
            'moeda' => 'BRL',
            'data_vencimento' => '2026-07-01',
            'data_pagamento' => null,
            'status' => 'pendente',
        ]);

        FinanceiroLancamento::create([
            'cliente_id' => $clientePortugal->id,
            'processo_id' => $processoPortugal->id,
            'tipo' => 'receita',
            'descricao' => 'Honorários iniciais - processo de família.',
            'valor' => 800.00,
            'moeda' => 'EUR',
            'data_vencimento' => '2026-08-01',
            'data_pagamento' => null,
            'status' => 'pendente',
        ]);
    }
}
