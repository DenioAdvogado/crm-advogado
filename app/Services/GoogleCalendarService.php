<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event as GoogleEvent;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Integração com Google Calendar (Bloco 7). Cada usuário interno conecta a própria conta —
 * não existe conta centralizada. Qualquer falha aqui (token expirado, API fora, etc.) é
 * capturada e registrada (log + `users.google_calendar_last_error`), nunca deixa subir para
 * quebrar a criação/edição de tarefas, que é a funcionalidade principal do sistema.
 */
class GoogleCalendarService
{
    private function scopes(): array
    {
        return [GoogleCalendar::CALENDAR_EVENTS];
    }

    public function buildAuthClient(): GoogleClient
    {
        $client = new GoogleClient();
        $client->setClientId(config('services.google.client_id'));
        $client->setClientSecret(config('services.google.client_secret'));
        $client->setRedirectUri(config('services.google.redirect_uri'));
        $client->setScopes($this->scopes());
        $client->setAccessType('offline');
        $client->setPrompt('consent');

        return $client;
    }

    public function getAuthUrl(): string
    {
        return $this->buildAuthClient()->createAuthUrl();
    }

    /**
     * Troca o código de autorização do Google por tokens e salva no usuário. Chamado pelo
     * callback OAuth (`/admin/configuracoes/agenda/callback`).
     */
    public function handleCallback(User $user, string $code): void
    {
        $client = $this->buildAuthClient();
        $token = $client->fetchAccessTokenWithAuthCode($code);

        if (isset($token['error'])) {
            throw new \RuntimeException($token['error_description'] ?? $token['error']);
        }

        $user->forceFill([
            'google_access_token' => $token['access_token'],
            'google_refresh_token' => $token['refresh_token'] ?? $user->google_refresh_token,
            'google_token_expires_at' => now()->addSeconds($token['expires_in'] ?? 3600),
            'google_calendar_connected_at' => now(),
            'google_calendar_last_error' => null,
        ])->save();
    }

    public function disconnect(User $user): void
    {
        $user->forceFill([
            'google_access_token' => null,
            'google_refresh_token' => null,
            'google_token_expires_at' => null,
            'google_calendar_connected_at' => null,
            'google_calendar_last_error' => null,
        ])->save();
    }

    /**
     * Monta um client autenticado para este usuário, renovando o access_token via
     * refresh_token se necessário. Retorna null (sem lançar exceção) se o usuário nunca
     * conectou ou se a renovação falhar — quem chama decide o que fazer (normalmente: só
     * pular a sincronização).
     */
    private function clientFor(User $user): ?GoogleClient
    {
        if (! $user->google_refresh_token) {
            return null;
        }

        $client = $this->buildAuthClient();
        $client->setAccessToken([
            'access_token' => $user->google_access_token,
            'refresh_token' => $user->google_refresh_token,
        ]);

        if ($client->isAccessTokenExpired()) {
            try {
                $newToken = $client->fetchAccessTokenWithRefreshToken($user->google_refresh_token);

                if (isset($newToken['error'])) {
                    throw new \RuntimeException($newToken['error_description'] ?? $newToken['error']);
                }

                $user->forceFill([
                    'google_access_token' => $newToken['access_token'],
                    'google_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                    'google_calendar_last_error' => null,
                ])->save();
            } catch (Throwable $exception) {
                $this->recordFailure($user, $exception);

                return null;
            }
        }

        return $client;
    }

    /**
     * Cria ou atualiza o evento no Google Calendar do responsável da tarefa. Não faz nada
     * (silenciosamente) se o responsável não tiver conectado a agenda.
     */
    public function syncTask(Task $task): void
    {
        $task->loadMissing('responsible');
        $user = $task->responsible;

        if (! $user || ! $task->due_date) {
            return;
        }

        $client = $this->clientFor($user);

        if (! $client) {
            return;
        }

        $service = new GoogleCalendar($client);

        $event = new GoogleEvent([
            'summary' => $task->title,
            'description' => $this->describeTask($task),
            'start' => ['dateTime' => $task->due_date->toAtomString()],
            'end' => ['dateTime' => $task->due_date->copy()->addHour()->toAtomString()],
        ]);

        try {
            if ($task->google_event_id) {
                $service->events->update('primary', $task->google_event_id, $event);
            } else {
                $created = $service->events->insert('primary', $event);
                $task->forceFill(['google_event_id' => $created->getId()])->save();
            }

            if ($user->google_calendar_last_error) {
                $user->forceFill(['google_calendar_last_error' => null])->save();
            }
        } catch (Throwable $exception) {
            $this->recordFailure($user, $exception);
        }
    }

    /**
     * Remove o evento do Google Calendar (tarefa concluída ou excluída).
     */
    public function deleteTaskEvent(Task $task): void
    {
        if (! $task->google_event_id) {
            return;
        }

        $task->loadMissing('responsible');
        $user = $task->responsible;

        $client = $user ? $this->clientFor($user) : null;

        if (! $client) {
            return;
        }

        $service = new GoogleCalendar($client);

        try {
            $service->events->delete('primary', $task->google_event_id);
        } catch (Throwable $exception) {
            // Evento já pode ter sido removido manualmente no Google — não é uma falha
            // que precise travar nada nem ficar registrada como erro de conexão.
            Log::info('Falha ao remover evento do Google Calendar (provavelmente já não existe): '.$exception->getMessage());
        }

        $task->forceFill(['google_event_id' => null])->save();
    }

    private function describeTask(Task $task): string
    {
        $relatedCase = $task->relatedCase();

        $lines = [];

        if ($relatedCase) {
            $lines[] = 'Cliente: '.($relatedCase->client?->name ?? '— cliente removido —');
            $lines[] = 'Processo: '.($relatedCase->case_number ?? '#'.$relatedCase->id);
        }

        return implode("\n", $lines);
    }

    private function recordFailure(User $user, Throwable $exception): void
    {
        Log::warning('Falha na sincronização com Google Calendar para o usuário '.$user->id.': '.$exception->getMessage());

        $user->forceFill(['google_calendar_last_error' => $exception->getMessage()])->save();
    }
}
