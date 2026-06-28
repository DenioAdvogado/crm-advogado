<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\FinancialEntry;
use App\Models\LegalCase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinancialController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorize('view-financial');

        $entries = FinancialEntry::with(['client', 'case']);

        if ($request->filled('client_id')) {
            $entries->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('case_id')) {
            $entries->where('case_id', $request->input('case_id'));
        }

        if ($request->filled('type')) {
            $entries->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $entries->where('status', $request->input('status'));
        }

        if ($request->filled('currency')) {
            $entries->where('currency', $request->input('currency'));
        }

        if ($request->filled('date_from')) {
            $entries->whereDate('due_date', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $entries->whereDate('due_date', '<=', $request->input('date_to'));
        }

        $entries = $entries->orderByDesc('due_date')->paginate(20)->withQueryString();

        return view('admin.financial.index', [
            'entries' => $entries,
            'totals' => $this->buildTotals(),
            'clients' => Client::orderBy('name')->get(),
            'cases' => LegalCase::with('client')->orderByDesc('id')->get(),
            'filters' => $request->only(['client_id', 'case_id', 'type', 'status', 'currency', 'date_from', 'date_to']),
        ]);
    }

    public function create(): View
    {
        $this->authorize('view-financial');

        return view('admin.financial.create', $this->formOptions());
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorize('view-financial');

        $validated = $this->validateEntry($request);

        FinancialEntry::create($validated);

        return redirect()->route('admin.financeiro.index')->with('status', 'Lançamento criado com sucesso.');
    }

    public function edit(FinancialEntry $lancamento): View
    {
        $this->authorize('view-financial');

        return view('admin.financial.edit', [...$this->formOptions(), 'entry' => $lancamento]);
    }

    public function update(Request $request, FinancialEntry $lancamento): RedirectResponse
    {
        $this->authorize('view-financial');

        $validated = $this->validateEntry($request);

        // Ao marcar como "pago", a data de pagamento é preenchida automaticamente com hoje
        // se o usuário não informar uma — mas pode ser editada manualmente (campo do form).
        if ($validated['status'] === 'paid' && empty($validated['payment_date'])) {
            $validated['payment_date'] = Carbon::today()->toDateString();
        }

        if ($validated['status'] !== 'paid') {
            $validated['payment_date'] = null;
        }

        $lancamento->update($validated);

        return redirect()->route('admin.financeiro.index')->with('status', 'Lançamento atualizado com sucesso.');
    }

    private function formOptions(): array
    {
        return [
            'clients' => Client::orderBy('name')->get(),
            'cases' => LegalCase::with('client')->orderByDesc('id')->get(),
        ];
    }

    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'client_id' => ['required', Rule::exists('clients', 'id')],
            'case_id' => ['nullable', Rule::exists('cases', 'id')],
            'type' => ['required', Rule::in(['income', 'expense'])],
            'description' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', Rule::in(['BRL', 'EUR'])],
            'due_date' => ['required', 'date'],
            'payment_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['pending', 'paid', 'overdue', 'cancelled'])],
        ]);
    }

    /**
     * Totalizadores do topo da listagem (Bloco 5): nunca soma BRL com EUR — cada total é um
     * array indexado por moeda.
     */
    private function buildTotals(): array
    {
        $toReceive = FinancialEntry::where('type', 'income')
            ->where('status', 'pending')
            ->selectRaw('currency, sum(amount) as total')
            ->groupBy('currency')
            ->pluck('total', 'currency');

        $receivedThisMonth = FinancialEntry::where('type', 'income')
            ->where('status', 'paid')
            ->whereBetween('payment_date', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->selectRaw('currency, sum(amount) as total')
            ->groupBy('currency')
            ->pluck('total', 'currency');

        $overdue = FinancialEntry::where('type', 'income')
            ->where('status', 'overdue')
            ->selectRaw('currency, sum(amount) as total')
            ->groupBy('currency')
            ->pluck('total', 'currency');

        return [
            'to_receive' => $toReceive,
            'received_this_month' => $receivedThisMonth,
            'overdue' => $overdue,
        ];
    }
}
