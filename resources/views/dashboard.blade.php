<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p>{{ __("Você está logado como :name (:level).", ['name' => auth('web')->user()->name, 'level' => auth('web')->user()->access_level]) }}</p>

                    <p class="mt-4">
                        <a href="{{ route('admin.test-financial-access') }}" class="underline text-indigo-600">
                            {{ __('Testar acesso ao módulo financeiro (rota temporária do Bloco 2)') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
