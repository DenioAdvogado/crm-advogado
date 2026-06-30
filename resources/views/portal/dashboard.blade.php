<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Meus Processos') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Processos') }}</h3>

                    @if ($cases->isEmpty())
                        <p class="text-sm text-gray-600">{{ __('Você não possui processos cadastrados.') }}</p>
                    @else
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-2">{{ __('Número') }}</th>
                                    <th class="px-4 py-2">{{ __('Área') }}</th>
                                    <th class="px-4 py-2">{{ __('Status') }}</th>
                                    <th class="px-4 py-2">{{ __('Prazo mais próximo') }}</th>
                                    <th class="px-4 py-2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cases as $case)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $case->case_number ?? '—' }}</td>
                                        <td class="px-4 py-2">{{ $case->legalArea?->name ?? '—' }}</td>
                                        <td class="px-4 py-2">
                                            {{ match ($case->status) {
                                                'in_progress' => 'Em andamento',
                                                'completed' => 'Concluído',
                                                'suspended' => 'Suspenso',
                                                'archived' => 'Arquivado',
                                                default => $case->status,
                                            } }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $case->current_deadline?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-2">
                                            <a href="{{ route('portal.processos.show', $case) }}" class="underline text-indigo-600">
                                                {{ __('Ver detalhes') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="font-medium text-gray-900 mb-4">{{ __('Serviços em andamento ou pendentes') }}</h3>

                    @if ($services->isEmpty())
                        <p class="text-sm text-gray-600">{{ __('Nenhum serviço em andamento ou pendente.') }}</p>
                    @else
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-700">
                                <tr>
                                    <th class="px-4 py-2">{{ __('Descrição') }}</th>
                                    <th class="px-4 py-2">{{ __('Status') }}</th>
                                    <th class="px-4 py-2">{{ __('Prazo de execução') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($services as $service)
                                    <tr class="border-t">
                                        <td class="px-4 py-2">{{ $service->description }}</td>
                                        <td class="px-4 py-2">
                                            {{ match ($service->status) {
                                                'pending' => 'Pendente',
                                                'in_progress' => 'Em andamento',
                                                'completed' => 'Concluído',
                                                default => $service->status,
                                            } }}
                                        </td>
                                        <td class="px-4 py-2">
                                            {{ $service->execution_deadline?->format('d/m/Y') ?? '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-portal-layout>
