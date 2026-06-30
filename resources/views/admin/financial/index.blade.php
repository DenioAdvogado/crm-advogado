<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Financeiro') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-sm text-gray-500 mb-2">{{ __('Total a receber') }}</h4>
                    @forelse ($totals['to_receive'] as $currency => $total)
                        <p class="font-medium">{{ $currency }} {{ number_format((float) $total, 2, ",", ".") }}</p>
                    @empty
                        <p class="text-sm text-gray-400">—</p>
                    @endforelse
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-sm text-gray-500 mb-2">{{ __('Recebido este mês') }}</h4>
                    @forelse ($totals['received_this_month'] as $currency => $total)
                        <p class="font-medium">{{ $currency }} {{ number_format((float) $total, 2, ",", ".") }}</p>
                    @empty
                        <p class="text-sm text-gray-400">—</p>
                    @endforelse
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-sm text-gray-500 mb-2">{{ __('Total em atraso') }}</h4>
                    @forelse ($totals['overdue'] as $currency => $total)
                        <p class="font-medium text-red-600">{{ $currency }} {{ number_format((float) $total, 2, ",", ".") }}</p>
                    @empty
                        <p class="text-sm text-gray-400">—</p>
                    @endforelse
                </div>
            </div>

            <div class="flex justify-between items-center">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    <div>
                        <x-input-label for="client_id" :value="__('Cliente')" />
                        <select id="client_id" name="client_id" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach ($clients as $client)
                                <option value="{{ $client->id }}" @selected(($filters['client_id'] ?? null) == $client->id)>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="case_id" :value="__('Processo')" />
                        <select id="case_id" name="case_id" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach ($cases as $case)
                                <option value="{{ $case->id }}" @selected(($filters['case_id'] ?? null) == $case->id)>
                                    {{ $case->case_number ?? ('#'.$case->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="type" :value="__('Tipo')" />
                        <select id="type" name="type" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">{{ __('Todos') }}</option>
                            <option value="income" @selected(($filters['type'] ?? null) === 'income')>{{ __('Receita') }}</option>
                            <option value="expense" @selected(($filters['type'] ?? null) === 'expense')>{{ __('Despesa') }}</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach (['pending' => 'Pendente', 'paid' => 'Pago', 'overdue' => 'Atrasado', 'cancelled' => 'Cancelado'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="currency" :value="__('Moeda')" />
                        <select id="currency" name="currency" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">{{ __('Todas') }}</option>
                            <option value="BRL" @selected(($filters['currency'] ?? null) === 'BRL')>BRL</option>
                            <option value="EUR" @selected(($filters['currency'] ?? null) === 'EUR')>EUR</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="date_from" :value="__('De')" />
                        <x-text-input id="date_from" name="date_from" type="date" class="mt-1 text-sm" :value="$filters['date_from'] ?? null" />
                    </div>

                    <div>
                        <x-input-label for="date_to" :value="__('Até')" />
                        <x-text-input id="date_to" name="date_to" type="date" class="mt-1 text-sm" :value="$filters['date_to'] ?? null" />
                    </div>

                    <x-primary-button>{{ __('Filtrar') }}</x-primary-button>
                </form>

                <a href="{{ route('admin.financeiro.create') }}" class="text-sm text-indigo-600 underline">{{ __('+ Novo lançamento') }}</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Cliente') }}</th>
                            <th class="px-4 py-2">{{ __('Processo') }}</th>
                            <th class="px-4 py-2">{{ __('Tipo') }}</th>
                            <th class="px-4 py-2">{{ __('Descrição') }}</th>
                            <th class="px-4 py-2">{{ __('Valor') }}</th>
                            <th class="px-4 py-2">{{ __('Vencimento') }}</th>
                            <th class="px-4 py-2">{{ __('Status') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($entries as $entry)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $entry->client?->name ?? '— cliente removido —' }}</td>
                                <td class="px-4 py-2">{{ $entry->case?->case_number ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $entry->type === 'income' ? 'Receita' : 'Despesa' }}</td>
                                <td class="px-4 py-2">{{ $entry->description }}</td>
                                <td class="px-4 py-2">{{ $entry->currency }} {{ number_format((float) $entry->amount, 2, ",", ".") }}</td>
                                <td class="px-4 py-2">{{ $entry->due_date?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    {{ match ($entry->status) {
                                        'pending' => 'Pendente',
                                        'paid' => 'Pago',
                                        'overdue' => 'Atrasado',
                                        'cancelled' => 'Cancelado',
                                        default => $entry->status,
                                    } }}
                                </td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('admin.financeiro.edit', $entry) }}" class="underline text-indigo-600">{{ __('Editar') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $entries->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
