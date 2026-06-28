<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <p class="text-sm text-gray-600">
                {{ __("Você está logado como :name (:level).", ['name' => auth('web')->user()->name, 'level' => auth('web')->user()->access_level]) }}
            </p>

            {{-- Cartões de resumo --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @if ($role !== 'staff')
                    <div class="bg-white shadow-sm sm:rounded-lg p-4">
                        <h4 class="text-sm text-gray-500 mb-1">{{ __('Clientes ativos') }}</h4>
                        <p class="text-2xl font-semibold">{{ $activeClientsCount }}</p>
                    </div>

                    <div class="bg-white shadow-sm sm:rounded-lg p-4">
                        <h4 class="text-sm text-gray-500 mb-1">{{ __('Processos em andamento') }}</h4>
                        <p class="text-2xl font-semibold">{{ $casesInProgressCount }}</p>
                    </div>
                @endif

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-sm text-gray-500 mb-1">{{ __('Tarefas pendentes') }}</h4>
                    <p class="text-2xl font-semibold">{{ $tasksPendingCount }}</p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-4">
                    <h4 class="text-sm text-gray-500 mb-1">{{ __('Tarefas atrasadas') }}</h4>
                    <p class="text-2xl font-semibold text-red-600">{{ $tasksOverdueCount }}</p>
                </div>

                @if ($canViewFinancial)
                    <div class="bg-white shadow-sm sm:rounded-lg p-4">
                        <h4 class="text-sm text-gray-500 mb-1">{{ __('Total a receber') }}</h4>
                        @forelse ($financialTotals as $currency => $total)
                            <p class="font-semibold">{{ $currency }} {{ number_format((float) $total, 2, ',', '.') }}</p>
                        @empty
                            <p class="text-gray-400">—</p>
                        @endforelse
                    </div>
                @endif
            </div>

            {{-- Atalhos de cadastro — cada botão só aparece para quem tem permissão
                 (a rota por trás também é protegida pela mesma Gate/Policy, não só o
                 botão). --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-4">
                <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('Ações rápidas') }}</h3>
                <div class="flex flex-wrap gap-3">
                    @can('manage-clients')
                        <a href="{{ route('admin.clientes.create') }}" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-100">
                            {{ __('+ Cliente') }}
                        </a>
                    @endcan

                    @can('create', \App\Models\LegalCase::class)
                        <a href="{{ route('admin.processos.create') }}" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-100">
                            {{ __('+ Processo') }}
                        </a>
                    @endcan

                    @can('manage-services')
                        <a href="{{ route('admin.servicos.create') }}" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-100">
                            {{ __('+ Serviço') }}
                        </a>
                    @endcan

                    @can('create', \App\Models\Task::class)
                        <a href="{{ route('admin.tarefas.create') }}" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-100">
                            {{ __('+ Tarefa') }}
                        </a>
                    @endcan

                    @can('view-financial')
                        <a href="{{ route('admin.financeiro.create') }}" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-100">
                            {{ __('+ Lançamento financeiro') }}
                        </a>
                    @endcan

                    @can('manage-legal-areas')
                        <a href="{{ route('admin.areas-juridicas.create') }}" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-100">
                            {{ __('+ Área jurídica') }}
                        </a>
                    @endcan

                    @can('manage-users')
                        <a href="{{ route('admin.users.create') }}" class="text-sm bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-md hover:bg-indigo-100">
                            {{ __('+ Usuário') }}
                        </a>
                    @endcan

                    @can('view-reports')
                        <a href="{{ route('admin.relatorios.index') }}" class="text-sm text-indigo-600 underline self-center">
                            {{ __('Gerar relatório') }}
                        </a>
                    @endcan
                </div>
            </div>

            {{-- Prazos mais urgentes --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="font-medium text-gray-900 mb-4">{{ __('Prazos nos próximos 7 dias') }}</h3>

                @if ($upcomingDeadlines->isEmpty())
                    <p class="text-sm text-gray-600">{{ __('Nenhum prazo nos próximos 7 dias.') }}</p>
                @else
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700">
                            <tr>
                                <th class="px-4 py-2">{{ __('Tipo') }}</th>
                                <th class="px-4 py-2">{{ __('Descrição') }}</th>
                                <th class="px-4 py-2">{{ __('Data') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($upcomingDeadlines as $deadline)
                                <tr class="border-t">
                                    <td class="px-4 py-2">{{ $deadline['type'] }}</td>
                                    <td class="px-4 py-2">{{ $deadline['label'] }}</td>
                                    <td class="px-4 py-2">{{ $deadline['date']?->format('d/m/Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
