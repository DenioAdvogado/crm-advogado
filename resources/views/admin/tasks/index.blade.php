<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Tarefas') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            <div class="flex justify-between items-center">
                <form method="GET" class="flex flex-wrap gap-3 items-end">
                    @if ($responsibleOptions->isNotEmpty())
                        <div>
                            <x-input-label for="responsible_id" :value="__('Responsável')" />
                            <select id="responsible_id" name="responsible_id" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="">{{ __('Todos') }}</option>
                                @foreach ($responsibleOptions as $option)
                                    <option value="{{ $option->id }}" @selected(($filters['responsible_id'] ?? null) == $option->id)>
                                        {{ $option->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div>
                        <x-input-label for="status" :value="__('Status')" />
                        <select id="status" name="status" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">{{ __('Todos') }}</option>
                            @foreach (['pending' => 'Pendente', 'in_progress' => 'Em andamento', 'completed' => 'Concluída', 'overdue' => 'Atrasada'] as $value => $label)
                                <option value="{{ $value }}" @selected(($filters['status'] ?? null) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <x-input-label for="deadline" :value="__('Prazo')" />
                        <select id="deadline" name="deadline" class="mt-1 border-gray-300 rounded-md shadow-sm text-sm">
                            <option value="">{{ __('Todas') }}</option>
                            <option value="today" @selected(($filters['deadline'] ?? null) === 'today')>{{ __('Hoje') }}</option>
                            <option value="this_week" @selected(($filters['deadline'] ?? null) === 'this_week')>{{ __('Esta semana') }}</option>
                            <option value="overdue" @selected(($filters['deadline'] ?? null) === 'overdue')>{{ __('Atrasadas') }}</option>
                        </select>
                    </div>

                    <x-primary-button>{{ __('Filtrar') }}</x-primary-button>
                </form>

                <a href="{{ route('admin.tarefas.create') }}" class="text-sm text-indigo-600 underline">{{ __('+ Nova tarefa') }}</a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Título') }}</th>
                            <th class="px-4 py-2">{{ __('Cliente/Processo') }}</th>
                            <th class="px-4 py-2">{{ __('Responsável') }}</th>
                            <th class="px-4 py-2">{{ __('Prazo') }}</th>
                            <th class="px-4 py-2">{{ __('Status') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tasks as $task)
                            @php
                                $related = $task->case ?? $task->service;
                            @endphp
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $task->title }}</td>
                                <td class="px-4 py-2">{{ $related?->client?->name ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $task->responsible?->name ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $task->due_date?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    {{ match ($task->status) {
                                        'pending' => 'Pendente',
                                        'in_progress' => 'Em andamento',
                                        'completed' => 'Concluída',
                                        'overdue' => 'Atrasada',
                                        default => $task->status,
                                    } }}
                                </td>
                                <td class="px-4 py-2 space-x-2">
                                    <a href="{{ route('admin.tarefas.edit', $task) }}" class="underline text-indigo-600">{{ __('Editar') }}</a>
                                    @if ($task->status !== 'completed')
                                        <a href="{{ route('admin.tarefas.complete-form', $task) }}" class="underline text-green-700">{{ __('Concluir') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $tasks->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
