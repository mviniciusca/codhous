<!DOCTYPE html>
<html>

<head>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
</head>

<body style="background-color: #F3F4F6; margin: 0; padding: 20px;">
    <div
        style="font-family: 'Inter', sans-serif; max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="text-align: center; margin-bottom: 32px;">
            <h2 style="color: #111827; font-weight: 600; font-size: 22px; margin: 0;">{{ $company->trade_name }}</h2>
            <h3 style="color: #6B7280; font-weight: 500; font-size: 16px; margin-top: 8px;">Orçamento #{{
                $budget['code'] ?? 'N/A' }}</h3>
        </div>

        <div style="margin-bottom: 28px; color: #374151; line-height: 1.5; font-size: 15px;">
            <p>Olá <strong>{{ $budget['content'][0]['customer_name'] ?? 'Cliente' }}</strong>,</p>
            <p style="color: #6B7280;">Segue o orçamento solicitado com as informações principais:</p>
        </div>

        <div style="background-color: #F9FAFB; border-radius: 8px; padding: 20px; margin-bottom: 28px;">
            <div style="margin-bottom: 14px;">
                <p style="color: #6B7280; font-size: 13px; margin: 0 0 3px 0;">Código do Orçamento</p>
                <p style="color: #111827; font-weight: 500; margin: 0; font-size: 15px;">{{ $budget['code'] ?? 'N/A' }}
                </p>
            </div>
            <div style="margin-bottom: 14px;">
                <p style="color: #6B7280; font-size: 13px; margin: 0 0 3px 0;">Data</p>
                <p style="color: #111827; font-weight: 500; margin: 0; font-size: 15px;">{{ isset($budget['created_at'])
                    ? date('d/m/Y', strtotime($budget['created_at'])) : date('d/m/Y') }}</p>
            </div>
            <div>
                <p style="color: #6B7280; font-size: 13px; margin: 0 0 3px 0;">Valor Total</p>
                <p style="color: #111827; font-weight: 600; margin: 0; font-size: 15px;">{{ env('CURRENCY_SUFFIX') }} {{
                    number_format(($budget['content'][0]['total'] ?? 0), 2, ',', '.') }}</p>
            </div>
        </div>

        <p style="color: #374151; line-height: 1.5; margin-bottom: 28px; font-size: 15px;">Para mais detalhes, consulte
            o PDF em anexo. Em caso de dúvidas, estamos à disposição:</p>

        <div style="margin-bottom: 32px; font-size: 15px;">
            <p style="color: #374151; margin: 0 0 8px 0;"><strong style="color: #6B7280;">Telefone:</strong> {{
                $company->phone }}</p>
            <p style="color: #374151; margin: 0;"><strong style="color: #6B7280;">E-mail:</strong> {{ $company->email }}
            </p>
        </div>

        <div style="border-top: 1px solid #E5E7EB; padding-top: 20px; text-align: center;">
            <p style="color: #9CA3AF; font-size: 11px; margin: 0 0 3px 0;">Este é um e-mail automático. Por favor, não
                responda.</p>
            <p style="color: #9CA3AF; font-size: 11px; margin: 0;">{{ $company->trade_name }} © {{ date('Y') }} - Todos
                os direitos reservados</p>
        </div>
    </div>
</body>

</html>
