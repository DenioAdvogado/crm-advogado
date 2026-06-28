<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar área jurídica') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.areas-juridicas.update', $legalArea) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.legal-areas._form')

                    <div class="mt-6 flex gap-3">
                        <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                        <a href="{{ route('admin.areas-juridicas.index') }}" class="text-sm text-gray-600 underline self-center">{{ __('Cancelar') }}</a>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.areas-juridicas.destroy', $legalArea) }}" class="mt-6 pt-6 border-t">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 underline" onclick="return confirm('{{ __('Remover esta área jurídica?') }}')">
                        {{ __('Remover área jurídica') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
