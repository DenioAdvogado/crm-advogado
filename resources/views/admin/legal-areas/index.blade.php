<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Áreas jurídicas') }}</h2>
            @can('manage-legal-areas')
                <a href="{{ route('admin.areas-juridicas.create') }}" class="text-sm text-indigo-600 underline">{{ __('Nova área jurídica') }}</a>
            @endcan
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="text-sm text-green-700 bg-green-50 border border-green-200 rounded-md p-3">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="text-sm text-red-700 bg-red-50 border border-red-200 rounded-md p-3">{{ $errors->first() }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Nome') }}</th>
                            <th class="px-4 py-2">{{ __('País aplicável') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($legalAreas as $area)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $area->name }}</td>
                                <td class="px-4 py-2">
                                    {{ match ($area->applicable_country) {
                                        'Brazil' => 'Brasil',
                                        'Portugal' => 'Portugal',
                                        default => 'Ambos',
                                    } }}
                                </td>
                                <td class="px-4 py-2">
                                    @can('manage-legal-areas')
                                        <a href="{{ route('admin.areas-juridicas.edit', $area) }}" class="underline text-indigo-600">{{ __('Editar') }}</a>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $legalAreas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
