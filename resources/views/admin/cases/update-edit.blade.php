<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Editar atualização') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('admin.processos.atualizacoes.update', [$case, $caseUpdate]) }}">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-input-label for="description" :value="__('Descrição')" />
                        <textarea id="description" name="description" rows="4" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('description', $caseUpdate->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>

                    <label class="mt-3 flex items-center text-sm text-gray-700">
                        <input type="checkbox" name="notify_client" value="1" class="rounded border-gray-300" @checked(old('notify_client', $caseUpdate->notify_client))>
                        <span class="ml-2">{{ __('Notificar cliente sobre esta atualização') }}</span>
                    </label>

                    <div class="mt-6 flex gap-3">
                        <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                        <a href="{{ route('admin.processos.show', $case) }}" class="text-sm text-gray-600 underline self-center">{{ __('Cancelar') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
