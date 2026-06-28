<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\FinancialEntry;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Listagem simples de clientes (Bloco 9 — só leitura, sem CRUD, que continua fora do
     * escopo). Aberta a todos os perfis internos: clientes não são "donos" de um único
     * advogado no schema (um cliente pode ter processos com responsáveis diferentes), então
     * não há uma regra de visibilidade individual a aplicar aqui como existe para
     * processos/tarefas.
     */
    public function index(): View
    {
        $clients = Client::orderBy('name')->paginate(20);

        return view('admin.clients.index', ['clients' => $clients]);
    }

    /**
     * Perfil interno do cliente (Bloco 5): por ora só exibe os dados cadastrais e, se o
     * usuário tiver a Gate "view-financial", o resumo financeiro do cliente. Uma tela de
     * gestão completa de clientes (CRUD) não faz parte do escopo deste bloco.
     */
    public function show(Client $cliente): View
    {
        $cliente->load(['legalAreas', 'cases']);

        $financialSummary = Gate::allows('view-financial')
            ? $this->buildClientFinancialSummary($cliente)
            : null;

        return view('admin.clients.show', [
            'client' => $cliente,
            'financialSummary' => $financialSummary,
        ]);
    }

    /**
     * Mesma lógica de totalizadores por moeda do módulo financeiro (Bloco 5), aqui só
     * filtrada para um cliente específico: a deve, já pagou, em atraso.
     */
    private function buildClientFinancialSummary(Client $client): array
    {
        $base = FinancialEntry::where('client_id', $client->id)->where('type', 'income');

        return [
            'pending' => (clone $base)->where('status', 'pending')
                ->selectRaw('currency, sum(amount) as total')->groupBy('currency')->pluck('total', 'currency'),
            'paid' => (clone $base)->where('status', 'paid')
                ->selectRaw('currency, sum(amount) as total')->groupBy('currency')->pluck('total', 'currency'),
            'overdue' => (clone $base)->where('status', 'overdue')
                ->selectRaw('currency, sum(amount) as total')->groupBy('currency')->pluck('total', 'currency'),
        ];
    }
}
