<x-guest-layout>
    <div class="text-gray-900">
        <p>{{ __('Bem-vindo(a) ao Portal do Cliente, :name.', ['name' => auth('client')->user()->name]) }}</p>

        <p class="mt-4 text-sm text-gray-600">
            {{ __('Este é um painel mínimo só para validar o login do portal (Bloco 2). As telas reais do Portal do Cliente vêm no Bloco 3.') }}
        </p>

        <form method="POST" action="{{ route('portal.logout') }}" class="mt-6">
            @csrf
            <x-primary-button>{{ __('Sair') }}</x-primary-button>
        </form>
    </div>
</x-guest-layout>
