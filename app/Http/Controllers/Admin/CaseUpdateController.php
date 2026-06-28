<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseUpdate;
use App\Models\LegalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * CRUD de atualizações de processo (case_updates) — até aqui só eram criadas
 * indiretamente, ao concluir uma tarefa vinculada a um processo (Bloco 4). Esta tela
 * permite criar/editar/excluir atualizações diretamente, sem depender de uma tarefa.
 * Mesma autorização da edição do processo (LegalCasePolicy::update) — quem pode editar o
 * processo, pode gerenciar seu histórico de atualizações.
 */
class CaseUpdateController extends Controller
{
    public function store(Request $request, LegalCase $processo): RedirectResponse
    {
        $this->authorize('update', $processo);

        $validated = $this->validateUpdate($request);
        $validated['case_id'] = $processo->id;
        $validated['author_id'] = Auth::guard('web')->id();

        CaseUpdate::create($validated);

        return redirect()->route('admin.processos.show', $processo)->with('status', 'Atualização registrada.');
    }

    public function edit(LegalCase $processo, CaseUpdate $atualizacao): View
    {
        $this->authorize('update', $processo);

        abort_if($atualizacao->case_id !== $processo->id, 404);

        return view('admin.cases.update-edit', ['case' => $processo, 'caseUpdate' => $atualizacao]);
    }

    public function update(Request $request, LegalCase $processo, CaseUpdate $atualizacao): RedirectResponse
    {
        $this->authorize('update', $processo);

        abort_if($atualizacao->case_id !== $processo->id, 404);

        $atualizacao->update($this->validateUpdate($request));

        return redirect()->route('admin.processos.show', $processo)->with('status', 'Atualização editada.');
    }

    public function destroy(LegalCase $processo, CaseUpdate $atualizacao): RedirectResponse
    {
        $this->authorize('update', $processo);

        abort_if($atualizacao->case_id !== $processo->id, 404);

        $atualizacao->delete();

        return redirect()->route('admin.processos.show', $processo)->with('status', 'Atualização removida.');
    }

    private function validateUpdate(Request $request): array
    {
        $validated = $request->validate([
            'description' => ['required', 'string'],
        ]);

        $validated['notify_client'] = $request->boolean('notify_client');

        return $validated;
    }
}
