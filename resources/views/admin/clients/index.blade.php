<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Clientes') }}</h2>
            @can('manage-clients')
                <a href="{{ route('admin.clientes.create') }}" class="text-sm text-indigo-600 underline">{{ __('Novo cliente') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Nome') }}</th>
                            <th class="px-4 py-2">{{ __('País') }}</th>
                            <th class="px-4 py-2">{{ __('E-mail') }}</th>
                            <th class="px-4 py-2">{{ __('Status') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clients as $client)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $client->name }}</td>
                                <td class="px-4 py-2">{{ $client->country === 'Brazil' ? 'Brasil' : 'Portugal' }}</td>
                                <td class="px-4 py-2">{{ $client->email }}</td>
                                <td class="px-4 py-2">{{ $client->active ? 'Ativo' : 'Inativo' }}</td>
                                <td class="px-4 py-2">
                                    <a href="{{ route('admin.clientes.show', $client) }}" class="underline text-indigo-600">
                                        {{ __('Ver detalhes') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $clients->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
