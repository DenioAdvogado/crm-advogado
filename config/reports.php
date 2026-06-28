<?php

return [

    // E-mail do administrador que recebe o relatório diário (Bloco 8).
    'admin_email' => env('ADMIN_REPORT_EMAIL'),

    // Horário do envio automático diário, formato "HH:MM" (24h). Usado em routes/console.php.
    'send_time' => env('ADMIN_REPORT_SEND_TIME', '07:00'),

];
