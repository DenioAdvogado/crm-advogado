<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Bloco 4: marca tarefas vencidas como "atrasada", uma vez por dia. Em produção (VPS
// Hostinger, Bloco 10) isso só funciona se houver uma entrada de cron rodando
// `php artisan schedule:run` a cada minuto — é o scheduler do Laravel, não o comando em si,
// que precisa do cron do servidor configurado.
Schedule::command('app:mark-overdue-tasks')->daily();
