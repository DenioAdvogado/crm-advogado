<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Produtividade') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Usuário') }}</th>
                            <th class="px-4 py-2">{{ __('Concluídas hoje') }}</th>
                            <th class="px-4 py-2">{{ __('Concluídas na semana anterior') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($report as $row)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $row['user']->name }}</td>
                                <td class="px-4 py-2">{{ $row['completed_today'] }}</td>
                                <td class="px-4 py-2">{{ $row['completed_last_week'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
