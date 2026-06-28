<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

/**
 * Passo 1 do fluxo manual de autorização do OneDrive (Bloco 10, só roda uma vez durante o
 * deploy): imprime a URL que o administrador deve abrir no navegador e logar com a conta
 * Microsoft que vai guardar os backups.
 */
#[Signature('app:onedrive-authorize')]
#[Description('Gera a URL de autorização do OneDrive (passo 1/2 — execução única no deploy).')]
class OneDriveAuthorize extends Command
{
    public function handle(): int
    {
        $config = config('services.onedrive');

        if (empty($config['client_id'])) {
            $this->error('Configure ONEDRIVE_CLIENT_ID e ONEDRIVE_CLIENT_SECRET no .env antes de continuar.');

            return self::FAILURE;
        }

        $url = sprintf(
            'https://login.microsoftonline.com/%s/oauth2/v2.0/authorize?%s',
            $config['tenant_id'],
            http_build_query([
                'client_id' => $config['client_id'],
                'response_type' => 'code',
                'redirect_uri' => $config['redirect_uri'],
                'response_mode' => 'query',
                'scope' => 'Files.ReadWrite offline_access',
            ])
        );

        $this->line('Abra esta URL no navegador, faça login com a conta Microsoft escolhida para guardar os backups, e autorize o acesso:');
        $this->line('');
        $this->line($url);
        $this->line('');
        $this->line('Depois do login, você será redirecionado para a redirect_uri configurada (ex: http://localhost/?code=...).');
        $this->line('Copie o valor de "code" da URL e rode: php artisan app:onedrive-token {code}');

        return self::SUCCESS;
    }
}
