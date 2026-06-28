<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LegalArea;
use App\Models\LegalCase;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Listagem, detalhe e CRUD de processos. A listagem/detalhe vêm do Bloco 9; o CRUD foi
 * adicionado no Bloco 10 a pedido explícito do usuário (não fazia parte do escopo original
 * de nenhum bloco). Finalmente conecta a LegalCasePolicy criada no Bloco 2, que ficava
 * pronta sem nenhuma rota usando-a.
 */
class CaseController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('viewAny', LegalCase::class);

        $user = Auth::guard('web')->user();

        $cases = LegalCase::visibleTo($user)->with(['client', 'legalArea', 'responsibleLawyer']);

        if ($request->filled('status')) {
            $cases->where('status', $request->input('status'));
        }

        $cases = $cases->orderByDesc('opened_at')->paginate(20)->withQueryString();

        return view('admin.cases.index', [
            'cases' => $cases,
            'filters' => $request->only('status'),
        ]);
    }

    public function show(LegalCase $processo): View
    {
        $this->authorize('view', $processo);

        $processo->load(['client', 'legalArea', 'responsibleLawyer', 'services', 'tasks', 'updates.author']);

        return view('admin.cases.show', ['case' => $processo]);
    }

    public function create(): View
    {
        $this->authorize('create', LegalCase::class);

        return view('admin.cases.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', LegalCase::class);

        $case = LegalCase::create($this->validateCase($request));

        return redirect()->route('admin.processos.show', $case)->with('status', 'Processo cadastrado com sucesso.');
    }

    public function edit(LegalCase $processo): View
    {
        $this->authorize('update', $processo);

        return view('admin.cases.edit', array_merge(['case' => $processo], $this->formOptions()));
    }

    public function update(Request $request, LegalCase $processo): RedirectResponse
    {
        $this->authorize('update', $processo);

        $processo->update($this->validateCase($request));

        return redirect()->route('admin.processos.show', $processo)->with('status', 'Processo atualizado com sucesso.');
    }

    public function destroy(LegalCase $processo): RedirectResponse
    {
        $this->authorize('delete', $processo);

        $processo->delete();

        return redirect()->route('admin.processos.index')->with('status', 'Processo removido.');
    }

    private function validateCase(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'responsible_lawyer_id' => ['nullable', 'exists:users,id'],
            'case_number' => ['nullable', 'string', 'max:255'],
            'legal_area_id' => ['required', 'exists:legal_areas,id'],
            'country' => ['required', Rule::in(['Brazil', 'Portugal'])],
            'status' => ['required', Rule::in(['in_progress', 'completed', 'suspended', 'archived'])],
            'opened_at' => ['required', 'date'],
            'current_deadline' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);
    }

    private function formOptions(): array
    {
        return [
            'clients' => Client::orderBy('name')->get(),
            'legalAreas' => LegalArea::orderBy('name')->get(),
            'lawyers' => User::where('access_level', 'lawyer')->orderBy('name')->get(),
        ];
    }
}
