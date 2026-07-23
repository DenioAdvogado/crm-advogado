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
                <div><span class="text-gray-500">{{ __('PaÃ­s') }}:</span> {{ $client->country === 'Brazil' ? 'Brasil' : 'Portugal' }}</div>
                <div><span class="text-gray-500">{{ __('E-mail') }}:</span> {{ $client->email }}</div>
                <div><span class="text-gray-500">{{ __('Telefone') }}:</span> {{ $client->phone }}</div>
                <div>
                    <span class="text-gray-500">
                        {{ $client->country === 'Brazil' ? ($client->person_type === 'individual' ? 'CPF' : 'CNPJ') : 'NIF' }}:
                    </span>
                    {{ $client->formatted_document_number }}
                </div>
                <div>
                    <span class="text-gray-500">{{ __('Ãreas jurÃ­dicas') }}:</span>
                    {{ $client->legalAreas->pluck('name')->implode(', ') ?: 'â' }}
                </div>
                {{-- Qualificação --}}
                @if ($client->nationality)
                    <div><span class="text-gray-500">{{ __('Nacionalidade') }}:</span> {{ $client->nationality }}</div>
                @endif
                @if ($client->marital_status)
                    <div><span class="text-gray-500">{{ __('Estado civil') }}:</span> {{ ucfirst(str_replace('_', ' ', $client->marital_status)) }}{{ $client->stable_union ? ' (união estável)' : '' }}</div>
                @endif
                @if ($client->profession)
                    <div><span class="text-gray-500">{{ __('Profissão') }}:</span> {{ $client->profession }}</div>
                @endif
                @if ($client->birth_date)
                    <div><span class="text-gray-500">{{ __('Data de nascimento') }}:</span> {{ $client->birth_date->format('d/m/Y') }}</div>
                @endif
                @if ($client->document_issuer)
                    <div><span class="text-gray-500">{{ __('RG — Órgão emissor') }}:</span> {{ $client->document_issuer }}</div>
                @endif
                @if ($client->mother_name)
                    <div><span class="text-gray-500">{{ __('Filiação (mãe)') }}:</span> {{ $client->mother_name }}</div>
                @endif
                @if ($client->father_name)
                    <div><span class="text-gray-500">{{ __('Filiação (pai)') }}:</span> {{ $client->father_name }}</div>
                @endif
                @php
                    $enderecoCompleto = collect([
                        $client->address_street,
                        $client->address_number,
                        $client->address_complement,
                        $client->address_neighborhood,
                        $client->address_city,
                        $client->address_state,
                        $client->address_zipcode,
                        $client->address_country,
                    ])->filter()->implode(', ');
                @endphp
                @if ($enderecoCompleto)
                    <div><span class="text-gray-500">{{ __('Endereço completo') }}:</span> {{ $enderecoCompleto }}</div>
                @endif
                {{-- Pessoa Jurídica --}}
                @if ($client->company_legal_name)
                    <div><span class="text-gray-500">{{ __('Razão social') }}:</span> {{ $client->company_legal_name }}</div>
                @endif
                @if ($client->company_trade_name)
                    <div><span class="text-gray-500">{{ __('Nome fantasia') }}:</span> {{ $client->company_trade_name }}</div>
                @endif
                @if ($client->legal_representative)
                    <div><span class="text-gray-500">{{ __('Representante legal') }}:</span> {{ $client->legal_representative }}{{ $client->legal_representative_role ? ' — '.$client->legal_representative_role : '' }}{{ $client->legal_representative_document ? ' (CPF: '.$client->legal_representative_document.')' : '' }}</div>
                @endif
            </div>

            @if ($financialSummary)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-medium text-gray-900">{{ __('Resumo Financeiro') }}</h3>
                        <a href="{{ route('admin.financeiro.index', ['client_id' => $client->id]) }}" class="text-sm text-indigo-600 underline">
                            {{ __('Ver lanÃ§amentos') }}
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                        <div>
                            <h4 class="text-gray-500 mb-1">{{ __('A receber') }}</h4>
                            @forelse ($financialSummary['pending'] as $currency => $total)
                                <p class="font-medium">{{ $currency }} {{ number_format((float) $total, 2, ',', '.') }}</p>
                            @empty
                                <p class="text-gray-400">â</p>
                            @endforelse
                        </div>
                        <div>
                            <h4 class="text-gray-500 mb-1">{{ __('JÃ¡ pago') }}</h4>
                            @forelse ($financialSummary['paid'] as $currency => $total)
                                <p class="font-medium">{{ $currency }} {{ number_format((float) $total, 2, ',', '.') }}</p>
                            @empty
                                <p class="text-gray-400">â</p>
                            @endforelse
                        </div>
                        <div>
                            <h4 class="text-gray-500 mb-1">{{ __('Em atraso') }}</h4>
                            @forelse ($financialSummary['overdue'] as $currency => $total)
                                <p class="font-medium text-red-600">{{ $currency }} {{ number_format((float) $total, 2, ',', '.') }}</p>
                            @empty
                                <p class="text-gray-400">â</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
