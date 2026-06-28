<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $client = Auth::guard('client')->user();

        $cases = $client->cases()
            ->with('legalArea')
            ->orderByRaw('current_deadline IS NULL') // processos com prazo definido primeiro
            ->orderBy('current_deadline')
            ->get();

        $services = $client->services()
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('execution_deadline')
            ->get();

        return view('portal.dashboard', [
            'cases' => $cases,
            'services' => $services,
        ]);
    }
}
