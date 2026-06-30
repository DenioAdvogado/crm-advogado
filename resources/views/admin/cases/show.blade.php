<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Processo') }} {{ $case->case_number }}</h2>
            @can('update', $case)
                <a href="{{ route('admin.processos.edit', $case) }}" class="text-sm text-indigo-600 underline">{{ __('Editar') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2 text-sm">
                <div>
                    <span class="text-gray-500">{{ __('Cliente') }}:</span>
                    @if ($case->client)
                        <a href="{{ route('admin.clientes.show', $case->client) }}" class="underline text-indigo-600">{{ $case->client->name }}</a>
                    @else
                        <span class="text-gray-400">— cliente removido —</span>
                    @endif
                </div>
                <div><span class="text-gray-500">{{ __('Área jurídica') }}:</span> {{ $case->legalArea?->name ?? '—' }}</div>
                <div><span class="text-gray-500">{{ __('Responsável') }}:</span> {{ $case->responsibleLawyer?->name ?? '—' }}</div>
                <div><span class="text-gray-500">{{ __('Status') }}:</span> {{ $case->status }}</div>
                <div><span class="text-gray-500">{{ __('Data de abertura') }}:</span> {{ $case->opened_at?->format('d/m/Y') }}</div>
                <div><span class="text-gray-500">{{ __('Prazo atual') }}:</span> {{ $case->current_deadline?->format('d/m/Y') ?? '—' }}</div>
                <div><span class="text-gray-500">{{ __('Descrição') }}:</span> {{ $case->description ?? '—' }}</div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-3">{{ __('Serviços') }}</h3>
                @forelse ($case->services as $service)
                    <p class="text-sm border-t py-2">{{ $service->description }} — {{ $service->status }}</p>
                @empty
                    <p class="text-sm text-gray-500">{{ __('Nenhum serviço vinculado.') }}</p>
                @endforelse
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-3">{{ __('Tarefas') }}</h3>
                @forelse ($case->tasks as $task)
                    <p class="text-sm border-t py-2">{{ $task->title }} — {{ $task->status }}</p>
                @empty
                    <p class="text-sm text-gray-500">{{ __('Nenhuma tarefa vinculada.') }}</p>
                @endforelse
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-3">{{ __('Atualizações do processo') }}</h3>

                @forelse ($case->updates as $update)
                    <div class="border-t py-3 text-sm flex justify-between items-start gap-4">
                        <div>
                            <p>{{ $update->description }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $update->created_at->format('d/m/Y H:i') }}
                                @if ($update->author) — {{ $update->author->name }} @endif
                                @if ($update->notify_client) · {{ __('cliente notificado') }} @endif
                            </p>
                        </div>
                        @can('update', $case)
                            <div class="flex gap-2 shrink-0">
                                <a href="{{ route('admin.processos.atualizacoes.edit', [$case, $update]) }}" class="text-xs text-indigo-600 underline">{{ __('Editar') }}</a>
                                <form method="POST" action="{{ route('admin.processos.atualizacoes.destroy', [$case, $update]) }}" onsubmit="return confirm('{{ __('Remover esta atualização?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 underline">{{ __('Remover') }}</button>
                                </form>
                            </div>
                        @endcan
                    </div>
                @empty
                    <p class="text-sm text-gray-500">{{ __('Nenhuma atualização registrada.') }}</p>
                @endforelse

                @can('update', $case)
                    <form method="POST" action="{{ route('admin.processos.atualizacoes.store', $case) }}" class="mt-4 pt-4 border-t space-y-3">
                        @csrf
                        <div>
                            <x-input-label for="description" :value="__('Nova atualização')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"></textarea>
                        </div>
                        <label class="flex items-center text-sm text-gray-700">
                            <input type="checkbox" name="notify_client" value="1" class="rounded border-gray-300">
                            <span class="ml-2">{{ __('Notificar cliente sobre esta atualização') }}</span>
                        </label>
                        <x-primary-button>{{ __('Registrar atualização') }}</x-primary-button>
                    </form>
                @endcan
            </div>

            <a href="{{ route('admin.processos.index') }}" class="text-sm text-gray-600 underline">{{ __('Voltar') }}</a>
        </div>
    </div>
</x-app-layout>
