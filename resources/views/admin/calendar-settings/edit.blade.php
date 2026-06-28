<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Agenda — Google Calendar') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            @if (session('error'))
                <div class="font-medium text-sm text-red-600">{{ session('error') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    {{ __('Conecte sua conta Google para que os prazos das suas tarefas apareçam automaticamente na sua agenda.') }}
                </p>

                @if ($user->isGoogleCalendarConnected())
                    <div class="text-sm">
                        <span class="text-green-700 font-medium">{{ __('Conectado') }}</span>
                        @if ($user->google_calendar_connected_at)
                            <span class="text-gray-500">
                                ({{ __('desde') }} {{ $user->google_calendar_connected_at->format('d/m/Y H:i') }})
                            </span>
                        @endif
                    </div>

                    @if ($user->google_calendar_last_error)
                        <div class="text-sm text-amber-600">
                            {{ __('Aviso: a última tentativa de sincronização falhou.') }}
                            <p class="text-xs text-gray-500 mt-1">{{ $user->google_calendar_last_error }}</p>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('Suas tarefas continuam funcionando normalmente — apenas a sincronização com o Google está parada. Reconecte para resolver.') }}
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.configuracoes.agenda.disconnect') }}">
                        @csrf
                        <x-secondary-button type="submit">{{ __('Desconectar') }}</x-secondary-button>
                    </form>
                @else
                    <a href="{{ route('admin.configuracoes.agenda.connect') }}">
                        <x-primary-button type="button">{{ __('Conectar com Google Calendar') }}</x-primary-button>
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
