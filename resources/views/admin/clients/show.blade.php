<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $client->name }}</h2>
            @can('manage-clients')
                <a href="{{ route('admin.clientes.edit', $client) }}" class="text-sm text-indigo-600 underline">{{ __('Editar') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2 text-sm">
                <div><span class="text-gray-500">{{ __('País') }}:</span> {{ $client->country === 'Brazil' ? 'Brasil' : 'Portugal' }}</div>
                <div><span class="text-gray-500">{{ __('E-mail') }}:</span> {{ $client->email }}</div>
                <div><span class="text-gray-500">{{ __('Telefone') }}:</span> {{ $client->phone }}</div>
                <div>
                    <span class="text-gray-500">
                        {{ $client->country === 'Brazil' ? ($client->person_type === 'individual' ? 'CPF' : 'CNPJ') : 'NIF' }}:
                    </span>
                    {{ $client->formatted_document_number }}
                </div>
                <div>
                    <span class="text-gray-500">{{ __('Áreas jurídicas') }}:</span>
                    {{ $client->legalAreas->pluck('name')->implode(', ') ?: '—' }}
                </div>
            </div>

            @if ($financialSummary)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-medium text-gray-900">{{ __('Resumo Financeiro') }}</h3>
                        <a href="{{ route('admin.financeiro.index', ['client_id' => $client->id]) }}" class="text-sm text-indigo-600 underline">
                            {{ __('Ver lançamentos') }}
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <h4 class="text-gray-500 mb-1">{{ __('A receber') }}</h4>
                            @forelse ($financialSummary['pending'] as $currency => $total)
                                <p class="font-medium">{{ $currency }} {{ number_format((float) $total, 2, ',', '.') }}</p>
                            @empty
                                <p class="text-gray-400">—</p>
                            @endforelse
                        </div>
                        <div>
                            <h4 class="text-gray-500 mb-1">{{ __('Já pago') }}</h4>
                            @forelse ($financialSummary['paid'] as $currency => $total)
                                <p class="font-medium">{{ $currency }} {{ number_format((float) $total, 2, ',', '.') }}</p>
                            @empty
                                <p class="text-gray-400">—</p>
                            @endforelse
                        </div>
                        <div>
                            <h4 class="text-gray-500 mb-1">{{ __('Em atraso') }}</h4>
                            @forelse ($financialSummary['overdue'] as $currency => $total)
                                <p class="font-medium text-red-600">{{ $currency }} {{ number_format((float) $total, 2, ',', '.') }}</p>
                            @empty
                                <p class="text-gray-400">—</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
