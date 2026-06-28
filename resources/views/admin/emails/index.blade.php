<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Auditoria de E-mails') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('status'))
                <div class="font-medium text-sm text-green-600">{{ session('status') }}</div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-700">
                        <tr>
                            <th class="px-4 py-2">{{ __('Cliente') }}</th>
                            <th class="px-4 py-2">{{ __('Processo') }}</th>
                            <th class="px-4 py-2">{{ __('Atualização') }}</th>
                            <th class="px-4 py-2">{{ __('Enviado em') }}</th>
                            <th class="px-4 py-2">{{ __('Status') }}</th>
                            <th class="px-4 py-2"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($logs as $log)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $log->client->name }}</td>
                                <td class="px-4 py-2">{{ $log->caseUpdate->case->case_number ?? '—' }}</td>
                                <td class="px-4 py-2">{{ \Illuminate\Support\Str::limit($log->caseUpdate->description, 50) }}</td>
                                <td class="px-4 py-2">{{ $log->sent_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="px-4 py-2">
                                    @if ($log->status === 'sent')
                                        <span class="text-green-700">{{ __('Enviado') }}</span>
                                    @elseif ($log->status === 'failed')
                                        <span class="text-red-600">{{ __('Falhou') }}</span>
                                        @if ($log->error_message)
                                            <p class="text-xs text-gray-400">{{ \Illuminate\Support\Str::limit($log->error_message, 60) }}</p>
                                        @endif
                                    @else
                                        <span class="text-gray-500">{{ __('Pendente') }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    @if ($log->status === 'failed')
                                        <form method="POST" action="{{ route('admin.emails.resend', $log) }}">
                                            @csrf
                                            <button type="submit" class="underline text-indigo-600">{{ __('Reenviar') }}</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
