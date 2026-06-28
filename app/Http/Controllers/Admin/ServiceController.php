<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\View\View;

/**
 * Listagem simples de serviços (Bloco 9 — só leitura). Sem Policy dedicada: serviços não
 * tiveram regra de visibilidade própria definida em nenhum bloco anterior, então ficam
 * visíveis a qualquer usuário interno autenticado, como já é o caso da listagem de clientes.
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
}
