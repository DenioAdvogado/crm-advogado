<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

/**
 * Upload do backup diário do banco para o OneDrive (Bloco 10), via chamadas REST diretas
 * à Microsoft Graph API com o Http facade do Laravel — sem precisar de um pacote/SDK
 * adicional (diferente da integração com Google Calendar do Bloco 7, que usava o SDK
 * google/apiclient porque a API de eventos é mais complexa que um upload simples de
 * arquivo).
 *
 * O fluxo de autorização (obter o refresh token inicial) é feito uma única vez, manualmente,
 * via os Commands "app:onedrive-authorize" e "app:onedrive-token" — não há tela no painel
 * para isso, porque é uma credencial de aplicação/administração do sistema, não uma conexão
 * pessoal por usuário (diferente do Google Calendar, que é por usuário).
 */
class OneDriveService
{
    private const TOKEN_URL_TEMPLATE = 'https://login.microsoftonline.com/%s/oauth2/v2.0/token';

    private const UPLOAD_URL_TEMPLATE = 'https://graph.microsoft.com/v1.0/me/drive/root:/%s/%s:/content';

    /**
     * Troca o refresh token salvo no .env por um access token válido (expira em ~1h).
     * Não persistimos o access token — é gerado a cada execução do backup diário.
     */
    public function getAccessToken(): string
    {
        $config = config('services.onedrive');

        if (empty($config['refresh_token']) || empty($config['client_id']) || empty($config['client_secret'])) {
            throw new RuntimeException('Credenciais do OneDrive não configuradas no .env (ver DEPLOY.md).');
        }

        $response = Http::asForm()->post(sprintf(self::TOKEN_URL_TEMPLATE, $config['tenant_id']), [
            'client_id' => $config['client_id'],
            'client_secret' => $config['client_secret'],
            'grant_type' => 'refresh_token',
            'refresh_token' => $config['refresh_token'],
            'scope' => 'Files.ReadWrite offline_access',
        ]);

        if ($response->failed()) {
            throw new RuntimeException('Falha ao renovar o token do OneDrive: '.$response->body());
        }

        return $response->json('access_token');
    }

    /**
     * Upload simples (até 4 MB — suficiente para o dump deste banco por bastante tempo;
     * se um dia o backup passar disso, a Microsoft Graph exige "upload session" em pedaços,
     * o que não foi implementado aqui por desproporcional ao tamanho atual do projeto).
     */
    public function upload(string $localPath, string $remoteFileName): bool
    {
        $accessToken = $this->getAccessToken();
        $folder = trim(config('services.onedrive.folder'), '/');

        $url = sprintf(self::UPLOAD_URL_TEMPLATE, $folder, $remoteFileName);

        $response = Http::withToken($accessToken)
            ->withBody(file_get_contents($localPath), 'application/octet-stream')
            ->put($url);

        if ($response->failed()) {
            Log::error('Falha ao enviar backup para o OneDrive: '.$response->body());

            return false;
        }

        return true;
    }
}
