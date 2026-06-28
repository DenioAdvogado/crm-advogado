<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Listagem e detalhe de processos (Bloco 9 — só leitura; CRUD de processos não existia em
 * nenhum bloco anterior e não foi pedido aqui). Finalmente conecta a LegalCasePolicy criada
 * no Bloco 2, que ficava pronta sem nenhuma rota usando-a.
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
}
