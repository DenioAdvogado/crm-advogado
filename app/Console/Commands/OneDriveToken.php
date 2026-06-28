<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Passo 2 do fluxo manual de autorização do OneDrive (Bloco 10, execução única): troca o
 * "code" obtido no passo 1 por um refresh token, que deve ser colado em ONEDRIVE_REFRESH_TOKEN
 * no .env. A partir daí, o Command de backup diário usa esse refresh token sozinho, sem
 * precisar de login manual de novo (a Microsoft renova o refresh token automaticamente a
 * cada uso, mas o token enviado aqui já basta para o backup funcionar indefinidamente).
 */
#[Signature('app:onedrive-token {code}')]
#[Description('Troca o código de autorização do OneDrive por um refresh token (passo 2/2 — execução única no deploy).')]
class OneDriveToken extends Command
{
    public function handle(): int
    {
        $config = config('services.onedrive');
        $code = $this->argument('code');

        $response = Http::asForm()->post(
            sprintf('https://login.microsoftonline.com/%s/oauth2/v2.0/token', $config['tenant_id']),
            [
                'client_id' => $config['client_id'],
                'client_secret' => $config['client_secret'],
                'grant_type' => 'authorization_code',
                'code' => $code,
                'redirect_uri' => $config['redirect_uri'],
                'scope' => 'Files.ReadWrite offline_access',
            ]
        );

        if ($response->failed()) {
            $this->error('Falha ao obter o token: '.$response->body());

            return self::FAILURE;
        }

        $refreshToken = $response->json('refresh_token');

        $this->line('Copie a linha abaixo e cole no .env de produção, substituindo o valor de ONEDRIVE_REFRESH_TOKEN:');
        $this->line('');
        $this->line('ONEDRIVE_REFRESH_TOKEN='.$refreshToken);

        return self::SUCCESS;
    }
}
