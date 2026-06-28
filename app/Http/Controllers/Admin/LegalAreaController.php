<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalArea;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD de áreas jurídicas — não existia em nenhum bloco anterior (só via seeder/banco
 * direto). Gate "manage-legal-areas": só Administrador.
 */
class LegalAreaController extends Controller
{
    public function index(): View
    {
        $legalAreas = LegalArea::orderBy('name')->paginate(20);

        return view('admin.legal-areas.index', ['legalAreas' => $legalAreas]);
    }

    public function create(): View
    {
        $this->authorize('manage-legal-areas');

        return view('admin.legal-areas.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('manage-legal-areas');

        LegalArea::create($this->validateLegalArea($request));

        return redirect()->route('admin.areas-juridicas.index')->with('status', 'Área jurídica cadastrada com sucesso.');
    }

    public function edit(LegalArea $area): View
    {
        $this->authorize('manage-legal-areas');

        return view('admin.legal-areas.edit', ['legalArea' => $area]);
    }

    public function update(Request $request, LegalArea $area): RedirectResponse
    {
        $this->authorize('manage-legal-areas');

        $area->update($this->validateLegalArea($request));

        return redirect()->route('admin.areas-juridicas.index')->with('status', 'Área jurídica atualizada com sucesso.');
    }

    public function destroy(LegalArea $area): RedirectResponse
    {
        $this->authorize('manage-legal-areas');

        if ($area->cases()->exists() || $area->clients()->exists()) {
            return back()->withErrors(['legal_area' => 'Esta área jurídica está vinculada a processos ou clientes e não pode ser removida.']);
        }

        $area->delete();

        return redirect()->route('admin.areas-juridicas.index')->with('status', 'Área jurídica removida.');
    }

    private function validateLegalArea(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'applicable_country' => ['required', Rule::in(['Brazil', 'Portugal', 'Both'])],
        ]);
    }
}
