<?php

namespace App\Http\Controllers\Auth\Portal;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        return view('auth.portal.reset-password', ['request' => $request]);
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Cliente não tem coluna remember_token (não é uma tabela de auth dedicada), então
        // só atualizamos "portal_password" aqui.
        $status = Password::broker('clients')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (Client $client) use ($request) {
                $client->forceFill([
                    'portal_password' => Hash::make($request->password),
                ])->save();

                event(new PasswordReset($client));
            }
        );

        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('portal.login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
