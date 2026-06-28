<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Relatórios') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 space-y-4">
                <p class="text-sm text-gray-600">
                    {{ __('Planilha .xlsx com prazos pendentes (serviços e tarefas), financeiro (BRL e EUR) e produtividade da equipe.') }}
                </p>

                <a href="{{ route('admin.relatorios.download') }}">
                    <x-primary-button type="button">{{ __('Gerar e baixar planilha agora') }}</x-primary-button>
                </a>

                <div class="border-t pt-4 text-sm text-gray-600">
                    <p>
                        {{ __('Envio automático diário às :time para:', ['time' => $sendTime]) }}
                        <strong>{{ $adminEmail ?? '— (ADMIN_REPORT_EMAIL não configurado no .env)' }}</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
