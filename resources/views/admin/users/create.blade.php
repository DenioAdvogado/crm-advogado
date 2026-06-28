<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Novo Usuário Interno') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
                    @csrf
                    @include('admin.users.partials.form', ['user' => null])

                    <x-primary-button>{{ __('Criar usuário') }}</x-primary-button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
