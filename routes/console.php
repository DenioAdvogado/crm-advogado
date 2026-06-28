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

// Bloco 5: mesma ideia, para lançamentos financeiros vencidos. Mantido como Command
// separado (em vez de reaproveitar app:mark-overdue-tasks) porque são modelos/domínios
// diferentes (Task x FinancialEntry) sem nada em comum a não ser o conceito de "atrasado" —
// só a infraestrutura de agendamento é reaproveitada (mesmo Scheduler, mesmo cron único no
// servidor rodando `php artisan schedule:run`), não a lógica.
Schedule::command('app:mark-overdue-financial-entries')->daily();

// Bloco 8: relatório diário (prazos, financeiro, produtividade) por e-mail ao
// administrador. Horário configurável via ADMIN_REPORT_SEND_TIME no .env (padrão 07:00) —
// não fica hardcoded para não exigir alterar código se o usuário quiser mudar o horário.
Schedule::command('app:send-admin-report')->dailyAt(config('reports.send_time'));
