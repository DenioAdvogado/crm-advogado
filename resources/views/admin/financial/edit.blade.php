<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar Lançamento Financeiro') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.financeiro.update', $entry) }}">
                    @csrf
                    @method('put')
                    @include('admin.financial._form', ['entry' => $entry])

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>{{ __('Salvar alterações') }}</x-primary-button>
                        <a href="{{ route('admin.financeiro.index') }}" class="text-sm text-gray-600 underline">{{ __('Cancelar') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
