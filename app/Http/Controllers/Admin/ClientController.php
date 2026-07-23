<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\FinancialEntry;
use App\Models\LegalArea;
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
     * Listagem simples de clientes (Bloco 9 â leitura). Aberta a todos os perfis internos:
     * clientes nÃ£o sÃ£o "donos" de um Ãºnico advogado no schema (um cliente pode ter
     * processos com responsÃ¡veis diferentes), entÃ£o nÃ£o hÃ¡ uma regra de visibilidade
     * individual a aplicar aqui como existe para processos/tarefas.
     */
    public function index(): View
    {
        $clients = Client::orderBy('name')->paginate(20);

        return view('admin.clients.index', ['clients' => $clients]);
    }

    /**
     * Perfil interno do cliente (Bloco 5): dados cadastrais e, se o usuÃ¡rio tiver a Gate
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
     * CRUD completo de clientes (Bloco 10 â completa a lacuna deixada no Bloco 9, a pedido
     * explÃ­cito do usuÃ¡rio). Gate "manage-clients": Administrador e Advogado.
     */
    public function create(): View
    {
        $this->authorize('manage-clients');

        return view('admin.clients.create', ['legalAreas' => LegalArea::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage-clients');

        $validated = $this->validateClient($request);
        $validated['portal_password'] = Hash::make($validated['portal_password']);
        $validated['active'] = $request->boolean('active', true);
        $legalAreaIds = $validated['legal_area_ids'] ?? [];
        unset($validated['legal_area_ids']);

        $client = Client::create($validated);
        $client->legalAreas()->sync($legalAreaIds);

        return redirect()->route('admin.clientes.index')->with('status', 'Cliente cadastrado com sucesso.');
    }

    public function edit(Client $cliente): View
    {
        $this->authorize('manage-clients');

        $cliente->load('legalAreas');

        return view('admin.clients.edit', [
            'client' => $cliente,
            'legalAreas' => LegalArea::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Client $cliente): RedirectResponse
    {
        $this->authorize('manage-clients');

        $validated = $this->validateClient($request, $cliente);
        $validated['active'] = $request->boolean('active');
        $legalAreaIds = $validated['legal_area_ids'] ?? [];
        unset($validated['legal_area_ids']);

        // Senha do portal sÃ³ Ã© alterada se o campo for preenchido â em branco mantÃ©m a atual.
        if (! empty($validated['portal_password'])) {
            $validated['portal_password'] = Hash::make($validated['portal_password']);
        } else {
            unset($validated['portal_password']);
        }

        $cliente->update($validated);
        $cliente->legalAreas()->sync($legalAreaIds);

        return redirect()->route('admin.clientes.show', $cliente)->with('status', 'Cliente atualizado com sucesso.');
    }

    /**
     * Soft delete (nunca exclusÃ£o definitiva, mesmo padrÃ£o jÃ¡ usado para usuÃ¡rios no
     * Bloco 2) â sÃ³ Administrador.
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
            'nationality' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['nullable', 'string', 'max:50'],
            'stable_union' => ['nullable', 'boolean'],
            'profession' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'document_issuer' => ['nullable', 'string', 'max:100'],
            'mother_name' => ['nullable', 'string', 'max:255'],
            'father_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', Rule::unique('clients', 'email')->ignore($client)],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_number' => ['nullable', 'string', 'max:50'],
            'address_complement' => ['nullable', 'string', 'max:255'],
            'address_neighborhood' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_state' => ['nullable', 'string', 'max:255'],
            'address_zipcode' => ['nullable', 'string', 'max:50'],
            'address_country' => ['nullable', 'string', 'max:255'],
            'company_legal_name' => ['nullable', 'string', 'max:255'],
            'company_trade_name' => ['nullable', 'string', 'max:255'],
            'legal_representative' => ['nullable', 'string', 'max:255'],
            'legal_representative_document' => ['nullable', 'string', 'max:50'],
            'legal_representative_role' => ['nullable', 'string', 'max:100'],
            'portal_password' => [$client ? 'nullable' : 'required', 'string', 'min:6'],
            'active' => ['nullable', 'boolean'],
            'legal_area_ids' => ['nullable', 'array'],
            'legal_area_ids.*' => ['exists:legal_areas,id'],
        ]);
    }

    /**
     * Mesma lÃ³gica de totalizadores por moeda do mÃ³dulo financeiro (Bloco 5), aqui sÃ³
     * filtrada para um cliente especÃ­fico: a deve, jÃ¡ pagou, em atraso.
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
