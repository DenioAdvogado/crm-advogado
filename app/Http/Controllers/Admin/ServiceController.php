<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\LegalCase;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * Listagem (Bloco 9) e CRUD (Bloco 10 — completa a lacuna deixada no Bloco 9, a pedido
 * explícito do usuário) de serviços. Sem Policy dedicada: serviços não tiveram regra de
 * visibilidade/edição própria definida em nenhum bloco anterior, então ficam abertos a
 * qualquer usuário interno (Gate "manage-services").
 */
class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::with(['client', 'case', 'responsible'])
            ->orderByDesc('id')
            ->paginate(20);

        return view('admin.services.index', ['services' => $services]);
    }

    public function create(): View
    {
        $this->authorize('manage-services');

        return view('admin.services.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage-services');

        Service::create($this->validateService($request));

        return redirect()->route('admin.servicos.index')->with('status', 'Serviço cadastrado com sucesso.');
    }

    public function edit(Service $servico): View
    {
        $this->authorize('manage-services');

        return view('admin.services.edit', array_merge(['service' => $servico], $this->formOptions()));
    }

    public function update(Request $request, Service $servico): RedirectResponse
    {
        $this->authorize('manage-services');

        $servico->update($this->validateService($request));

        return redirect()->route('admin.servicos.index')->with('status', 'Serviço atualizado com sucesso.');
    }

    public function destroy(Service $servico): RedirectResponse
    {
        $this->authorize('manage-services');

        $servico->delete();

        return redirect()->route('admin.servicos.index')->with('status', 'Serviço removido.');
    }

    private function validateService(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', 'exists:clients,id'],
            'case_id' => ['nullable', 'exists:cases,id'],
            'description' => ['required', 'string'],
            'status' => ['required', Rule::in(['pending', 'in_progress', 'completed'])],
            'execution_deadline' => ['nullable', 'date'],
            'responsible_id' => ['nullable', 'exists:users,id'],
        ]);
    }

    private function formOptions(): array
    {
        return [
            'clients' => Client::orderBy('name')->get(),
            'cases' => LegalCase::orderBy('case_number')->get(),
            'users' => User::orderBy('name')->get(),
        ];
    }
}
