<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\LegalCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaseController extends Controller
{
    /**
     * Bloco 3: o cliente só pode ver processos que pertencem a ele mesmo. Validação feita
     * aqui no controller (não basta confiar na rota) — comparamos o client_id do processo
     * com o id do cliente autenticado no guard "client" e bloqueamos com 403 caso não
     * coincida, mesmo que o ID na URL seja de um processo existente de outro cliente.
     */
    public function show(LegalCase $case): View
    {
        abort_if($case->client_id !== Auth::guard('client')->id(), 403);

        $case->load(['legalArea', 'responsibleLawyer', 'updates.author']);

        return view('portal.cases.show', ['case' => $case]);
    }
}
