<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Integração com Google Calendar (Bloco 7) — ver CLAUDE.md para o roteiro de criação
    // do projeto/credenciais no Google Cloud Console.
    'google' => [
        'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI'),
    ],

    // Envio do backup diário do banco para o OneDrive (Bloco 10) — ver DEPLOY.md para o
    // roteiro completo de registro do app no Azure AD e obtenção do refresh token.
    'onedrive' => [
        'tenant_id' => env('ONEDRIVE_TENANT_ID', 'common'),
        'client_id' => env('ONEDRIVE_CLIENT_ID'),
        'client_secret' => env('ONEDRIVE_CLIENT_SECRET'),
        'redirect_uri' => env('ONEDRIVE_REDIRECT_URI', 'http://localhost'),
        'refresh_token' => env('ONEDRIVE_REFRESH_TOKEN'),
        'folder' => env('ONEDRIVE_BACKUP_FOLDER', 'Backups/CRM'),
    ],

];
