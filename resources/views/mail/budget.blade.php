<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee;">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2 style="color: #333;">{{ env('APP_NAME') }}</h2>
        <h3 style="color: #555;">Orçamento #{{ $budget['code'] ?? 'N/A' }}</h3>
    </div>

    <div style="margin-bottom: 25px;">
        <p>Prezado(a) <strong>{{ $budget['content']['customer_name'] ?? 'Cliente' }}</strong>,</p>
        <p>Segue em anexo o orçamento solicitado. Abaixo estão as informações principais:</p>
    </div>

    <div style="background-color: #f7f7f7; border-left: 4px solid #333; padding: 15px; margin-bottom: 20px;">
        <p><strong>Código do Orçamento:</strong> {{ $budget['code'] ?? 'N/A' }}</p>
        <p><strong>Data:</strong> {{ isset($budget['created_at']) ? date('d/m/Y', strtotime($budget['created_at'])) :
            date('d/m/Y') }}</p>
        <p><strong>Valor Total:</strong> {{ env('CURRENCY_SUFFIX') }} {{ $budget['content']['total'] ?? '0,00' }}</p>
    </div>

    <p>Para mais detalhes, consulte o documento PDF anexado a este e-mail.</p>
    <p>Caso tenha alguma dúvida ou precise de mais informações, entre em contato conosco:</p>

    <div style="margin-top: 15px;">
        <p><strong>Telefone:</strong> (21) 966134366</p>
        <p><strong>E-mail:</strong> sac@codhous.app</p>
    </div>

    <div
        style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 12px; color: #777; text-align: center;">
        <p>Este é um e-mail automático. Por favor, não responda.</p>
        <p>{{ env('APP_NAME') }} © {{ date('Y') }} - Todos os direitos reservados</p>
    </div>
</div>
