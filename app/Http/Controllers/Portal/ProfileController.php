<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(): View
    {
        return view('portal.profile.edit', ['client' => Auth::guard('client')->user()]);
    }

    /**
     * Bloco 3: o cliente só pode editar campos não sensíveis (telefone e endereço).
     * Nome, e-mail e documento (CPF/CNPJ/NIF/CC) não fazem parte da validação aqui de
     * propósito — qualquer correção neles precisa passar por um usuário interno.
     */
    public function update(Request $request): RedirectResponse
    {
        $client = Auth::guard('client')->user();

        $validated = $request->validate([
            'phone' => ['nullable', 'string', 'max:50'],
            'address_street' => ['nullable', 'string', 'max:255'],
            'address_city' => ['nullable', 'string', 'max:255'],
            'address_state' => ['nullable', 'string', 'max:255'],
            'address_zipcode' => ['nullable', 'string', 'max:50'],
            'address_country' => ['nullable', 'string', 'max:255'],
        ]);

        $client->update($validated);

        return redirect()->route('portal.meus-dados.edit')->with('status', 'Dados atualizados com sucesso.');
    }
}
