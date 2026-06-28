<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Processo') }} {{ $case->case_number }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-2 text-sm">
                <div><span class="text-gray-500">{{ __('Cliente') }}:</span> <a href="{{ route('admin.clientes.show', $case->client) }}" class="underline text-indigo-600">{{ $case->client->name }}</a></div>
                <div><span class="text-gray-500">{{ __('Área jurídica') }}:</span> {{ $case->legalArea->name }}</div>
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

            <a href="{{ route('admin.processos.index') }}" class="text-sm text-gray-600 underline">{{ __('Voltar') }}</a>
        </div>
    </div>
</x-app-layout>
