<nav class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center space-x-8">
                <a href="{{ route('portal.dashboard') }}" class="font-semibold text-gray-800">
                    {{ __('Portal do Cliente') }}
                </a>

                <a href="{{ route('portal.dashboard') }}" class="text-sm text-gray-600 hover:text-gray-900 {{ request()->routeIs('portal.dashboard') ? 'underline' : '' }}">
                    {{ __('Meus Processos') }}
                </a>

                <a href="{{ route('portal.meus-dados.edit') }}" class="text-sm text-gray-600 hover:text-gray-900 {{ request()->routeIs('portal.meus-dados.*') ? 'underline' : '' }}">
                    {{ __('Meus Dados') }}
                </a>
            </div>

            <div class="flex items-center">
                <span class="text-sm text-gray-600 mr-4">{{ auth('client')->user()->name }}</span>

                <form method="POST" action="{{ route('portal.logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gray-600 hover:text-gray-900 underline">
                        {{ __('Sair') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
