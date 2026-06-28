<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Concluir Tarefa') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <p class="text-sm text-gray-600 mb-4">{{ $task->title }}</p>

                <form method="POST" action="{{ route('admin.tarefas.complete', $task) }}">
                    @csrf

                    <div>
                        <x-input-label for="update_description" :value="__('Descrição da atualização (registrada no histórico do processo, se houver processo vinculado)')" />
                        <textarea id="update_description" name="update_description" rows="3" class="block mt-1 w-full border-gray-300 rounded-md shadow-sm">{{ old('update_description') }}</textarea>
                        <x-input-error :messages="$errors->get('update_description')" class="mt-2" />
                    </div>

                    <div class="mt-4 flex items-center">
                        <input type="checkbox" id="notify_client" name="notify_client" value="1" class="rounded border-gray-300">
                        <label for="notify_client" class="ml-2 text-sm text-gray-700">
                            {{ __('Notificar cliente sobre esta atualização') }}
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ __('Apenas registra a intenção por agora — o envio de e-mail será implementado no Bloco 6.') }}
                    </p>

                    <div class="mt-6 flex items-center gap-4">
                        <x-primary-button>{{ __('Concluir tarefa') }}</x-primary-button>
                        <a href="{{ route('admin.tarefas.index') }}" class="text-sm text-gray-600 underline">{{ __('Cancelar') }}</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
