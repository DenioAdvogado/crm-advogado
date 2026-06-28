<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\FinancialEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClientController extends Controller
{
    /**
     * Listagem simples de clientes (Bloco 9 — leitura). Aberta a todos os perfis internos:
     * clientes não são "donos" de um único advogado no schema (um cliente pode ter
     * processos com responsáveis diferentes), então não há uma regra de visibilidade
     * individual a aplicar aqui como existe para processos/tarefas.
     */
    public function index(): View
    {
        $clients = Client::orderBy('name')->paginate(20);

        return view('admin.clients.index', ['clients' => $clients]);
    }

    /**
     * Perfil interno do cliente (Bloco 5): dados cadastrais e, se o usuário tiver a Gate
     * "view-financial", o resumo financeiro do cliente.
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
     * CRUD completo de clientes (Bloco 10 — completa a lacuna deixada no Bloco 9, a pedido
     * explícito do usuário). Gate "manage-clients": Administrador e Advogado.
     */
    public function create(): View
    {
        $this->authorize('manage-clients');

        return view('admin.clients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage-clients');

        $validated = $this->validateClient($request);
        $validated['portal_password'] = Hash::make($validated['portal_password']);
        $validated['active'] = $request->boolean('active', true);

        Client::create($validated);

        return redirect()->route('admin.clientes.index')->with('status', 'Cliente cadastrado com sucesso.');
    }

    public function edit(Client $cliente): View
    {
        $this->authorize('manage-clients');

        return view('admin.clients.edit', ['client' => $cliente]);
    }

    public function update(Request $request, Client $cliente): RedirectResponse
    {
        $this->authorize('manage-clients');

        $validated = $this->validateClient($request, $cliente);
        $validated['active'] = $request->boolean('active');

        // Senha do portal só é alterada se o campo for preenchido — em branco mantém a atual.
        if (! empty($validated['portal_password'])) {
            $validated['portal_password'] = Hash::make($validated['portal_password']);
        } else {
            unset($validated['portal_password']);
        }

        $cliente->update($validated);

        return redirect()->route('admin.clientes.show', $cliente)->with('status', 'Cliente atualizado com sucesso.');
    }

    /**
     * Soft delete (nunca exclusão definitiva, mesmo padrão já usado para usuários no
     * Bloco 2) — só Administrador.
     */
    public function destroy(Client $cliente): RedirectResponse
    {
        $this->authorize('manage-clients');

        abort_unless(Auth::guard('web')->user()->isAdministrator(), 403);

        $cliente->delete();

        return redirect()->route('admin.clientes.index')->with('status', 'Cliente removido.');
    }

    private function validateClient(Request $request, ?Client $client = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'person_type' => ['required', Rule::in(['individual', 'company'])],
            'country' => ['required', Rule::in(['Brazil', 'Portugal'])],
            'document_number' => ['required', 'string', 'max:50'],
            'secondary_document_number' => ['nullable', 'string', 'max:50'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($client)],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_state' => ['nullable', 'string', 'max:255'],
            'address_zipcode' => ['nullable', 'string', 'max:50'],
            'address_country' => ['nullable', 'string', 'max:255'],
            'portal_password' => [$client ? 'nullable' : 'required', 'string', 'min:6'],
            'active' => ['nullable', 'boolean'],
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
