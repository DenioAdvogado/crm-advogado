<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\GoogleCalendarService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Throwable;

/**
 * Conexão individual de cada usuário interno com o Google Calendar (Bloco 7) — não existe
 * conta centralizada, cada um conecta a própria agenda.
 */
class CalendarSettingsController extends Controller
{
    public function edit(): View
    {
        return view('admin.calendar-settings.edit', [
            'user' => Auth::guard('web')->user(),
        ]);
    }

    public function connect(GoogleCalendarService $calendar): RedirectResponse
    {
        return redirect()->away($calendar->getAuthUrl());
    }

    public function callback(Request $request, GoogleCalendarService $calendar): RedirectResponse
    {
        $user = Auth::guard('web')->user();

        if ($request->filled('error')) {
            return redirect()->route('admin.configuracoes.agenda.edit')
                ->with('error', 'Autorização cancelada ou negada no Google.');
        }

        try {
            $calendar->handleCallback($user, $request->input('code'));
        } catch (Throwable $exception) {
            return redirect()->route('admin.configuracoes.agenda.edit')
                ->with('error', 'Não foi possível concluir a conexão: '.$exception->getMessage());
        }

        return redirect()->route('admin.configuracoes.agenda.edit')
            ->with('status', 'Google Calendar conectado com sucesso.');
    }

    public function disconnect(GoogleCalendarService $calendar): RedirectResponse
    {
        $calendar->disconnect(Auth::guard('web')->user());

        return redirect()->route('admin.configuracoes.agenda.edit')
            ->with('status', 'Google Calendar desconectado.');
    }
}
