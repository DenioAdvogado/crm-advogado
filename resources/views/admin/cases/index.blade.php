<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Processos') }}</h2>
            @can('create', \App\Models\LegalCase::class)
                <a href="{{ route('admin.processos.create') }}" class="text-sm text-indigo-600 underline">{{ __('Novo processo') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form method="GET" class="flex flex-wrap gap-3 items-end">
                <div>
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                        <option value="">{{ __('Todos') }}</option>
                        @foreach (['in_progress' => 'Em andamento', 'completed' => 'Concluído', 'suspended' => 'Suspenso', 'archived' => 'Arquivado'] as $value => $label)
                            <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <x-primary-button>{{ __('Filtrar') }}</x-primary-button>
            </form>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Número') }}</th>
                            <th class="px-4 py-2">{{ __('Cliente') }}</th>
                            <th class="px-4 py-2">{{ __('Área') }}</th>
                            <th class="px-4 py-2">{{ __('Responsável') }}</th>
                            <th class="px-4 py-2">{{ __('Status') }}</th>
                            <th class="px-4 py-2">{{ __('Prazo atual') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($cases as $case)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $case->case_number ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $case->client->name }}</td>
                                <td class="px-4 py-2">{{ $case->legalArea->name }}</td>
                                <td class="px-4 py-2">{{ $case->responsibleLawyer?->name ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    {{ match ($case->status) {
                                        'in_progress' => 'Em andamento',
                                        'completed' => 'Concluído',
                                        'suspended' => 'Suspenso',
                                        'archived' => 'Arquivado',
                                        default => $case->status,
                                    } }}
                                </td>
                                <td class="px-4 py-2">{{ $case->current_deadline?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('admin.processos.show', $case) }}" class="underline text-indigo-600">
                                        {{ __('Ver detalhes') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $cases->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
