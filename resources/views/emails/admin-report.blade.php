<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; background-color: #f3f4f6; padding: 24px;">
    <div style="max-width: 480px; margin: 0 auto; background: #ffffff; border-radius: 8px; padding: 32px;">
        <h2 style="color: #111827;">Relatório diário — {{ now()->format('d/m/Y') }}</h2>

        <p>Segue em anexo a planilha com:</p>

        <ul>
            <li>Prazos pendentes (serviços e tarefas)</li>
            <li>Financeiro (BRL e EUR)</li>
            <li>Produtividade da equipe</li>
        </ul>

        <p style="font-size: 12px; color: #9ca3af;">
            Este é um e-mail automático do CRM Advogado Internacional.
        </p>
    </div>
</body>
</html>
