<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Serviços') }}</h2>
            @can('manage-services')
                <a href="{{ route('admin.servicos.create') }}" class="text-sm text-indigo-600 underline">{{ __('Novo serviço') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Descrição') }}</th>
                            <th class="px-4 py-2">{{ __('Cliente') }}</th>
                            <th class="px-4 py-2">{{ __('Processo') }}</th>
                            <th class="px-4 py-2">{{ __('Responsável') }}</th>
                            <th class="px-4 py-2">{{ __('Prazo') }}</th>
                            <th class="px-4 py-2">{{ __('Status') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($services as $service)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $service->description }}</td>
                                <td class="px-4 py-2">{{ $service->client->name }}</td>
                                <td class="px-4 py-2">{{ $service->case?->case_number ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $service->responsible?->name ?? '—' }}</td>
                                <td class="px-4 py-2">{{ $service->execution_deadline?->format('d/m/Y') ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    {{ match ($service->status) {
                                        'pending' => 'Pendente',
                                        'in_progress' => 'Em andamento',
                                        'completed' => 'Concluído',
                                        default => $service->status,
                                    } }}
                                </td>
                                <td class="px-4 py-2">
                                    @can('manage-services')
                                        <a href="{{ route('admin.servicos.edit', $service) }}" class="underline text-indigo-600">{{ __('Editar') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $services->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
