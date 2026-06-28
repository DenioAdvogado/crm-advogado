<x-portal-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detalhes do Processo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <span class="text-sm text-gray-500">{{ __('Número do processo') }}</span>
                    <p class="font-medium">{{ $case->case_number ?? '—' }}</p>
                </div>

                <div>
                    <span class="text-sm text-gray-500">{{ __('Área jurídica') }}</span>
                    <p class="font-medium">{{ $case->legalArea->name }}</p>
                </div>

                <div>
                    <span class="text-sm text-gray-500">{{ __('Status') }}</span>
                    <p class="font-medium">
                        {{ match ($case->status) {
                            'in_progress' => 'Em andamento',
                            'completed' => 'Concluído',
                            'suspended' => 'Suspenso',
                            'archived' => 'Arquivado',
                            default => $case->status,
                        } }}
                    </p>
                </div>

                <div>
                    <span class="text-sm text-gray-500">{{ __('Data de abertura') }}</span>
                    <p class="font-medium">{{ $case->opened_at?->format('d/m/Y') ?? '—' }}</p>
                </div>

                <div>
                    <span class="text-sm text-gray-500">{{ __('Prazo atual') }}</span>
                    <p class="font-medium">{{ $case->current_deadline?->format('d/m/Y') ?? '—' }}</p>
                </div>

                <div>
                    <span class="text-sm text-gray-500">{{ __('Descrição') }}</span>
                    <p class="font-medium">{{ $case->description ?? '—' }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Histórico de atualizações') }}</h3>

                @if ($case->updates->isEmpty())
                    <p class="text-sm text-gray-600">{{ __('Nenhuma atualização registrada ainda.') }}</p>
                @else
                    <ul class="space-y-4">
                        @foreach ($case->updates as $update)
                            <li class="border-t pt-4">
                                <p class="text-sm text-gray-500">
                                    {{ $update->created_at->format('d/m/Y H:i') }}
                                    @if ($update->author)
                                        — {{ $update->author->name }}
                                    @endif
                                </p>
                                <p>{{ $update->description }}</p>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <a href="{{ route('portal.dashboard') }}" class="underline text-sm text-gray-600">
                {{ __('Voltar para Meus Processos') }}
            </a>
        </div>
    </div>
</x-portal-layout>
