<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar serviço') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.servicos.update', $service) }}">
                    @csrf
                    @method('PUT')
                    @include('admin.services._form')

                    <div class="mt-6 flex gap-3">
                        <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                        <a href="{{ route('admin.servicos.index') }}" class="text-sm text-gray-600 underline self-center">{{ __('Cancelar') }}</a>
                    </div>
                </form>

                <form method="POST" action="{{ route('admin.servicos.destroy', $service) }}" class="mt-6 pt-6 border-t">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 underline" onclick="return confirm('{{ __('Remover este serviço?') }}')">
                        {{ __('Remover serviço') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
