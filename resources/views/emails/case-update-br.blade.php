<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; background-color: #f3f4f6; padding: 24px;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 32px;">
        <h2 style="color: #111827;">Olá, {{ $clientName }}!</h2>

        <p>
            Há uma nova atualização no seu processo
            @if ($caseNumber)
                <strong>{{ $caseNumber }}</strong>
            @endif
            no escritório Advogado Internacional.
        </p>

        <p>{{ $updateSummary }}</p>

        <p>Para ver os detalhes completos, acesse o portal do cliente:</p>

        <p style="text-align: center; margin: 32px 0;">
            <a href="{{ $portalLoginUrl }}"
               style="background-color: #4f46e5; color: #ffffff; padding: 12px 24px; border-radius: 6px; text-decoration: none;">
                Acessar o Portal do Cliente
            </a>
        </p>

        <p style="font-size: 13px; color: #6b7280;">
            Por segurança, não incluímos detalhes sensíveis do processo neste e-mail. Faça
            login no portal para visualizar o andamento completo.
        </p>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

        <p style="font-size: 12px; color: #9ca3af;">
            Este é um e-mail automático do Advogado Internacional. Não é necessário responder.
        </p>
    </div>
</body>
</html>
