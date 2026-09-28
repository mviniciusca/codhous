<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <title>{{ $customSubject ?? 'Mensagem' }}</title>
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        body { height: 100% !important; margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #ffffff; font-family: 'Inter', Helvetica, Arial, sans-serif; color: #111111; }
        table { border-collapse: collapse !important; }
        
        .header { background-color: #111111; padding: 25px 40px; }
        .header-table { width: 100%; }
        .header-logo { color: #ffffff; font-size: 14px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
        .header-label { color: #888888; font-size: 11px; font-weight: 600; letter-spacing: 1px; text-transform: uppercase; text-align: right; }
        
        .main-container { max-width: 650px; margin: 0 auto; width: 100%; }
        .content { padding: 50px 40px; }
        
        .sub-title { font-size: 14px; color: #777777; margin-bottom: 8px; }
        .main-title { font-size: 34px; font-weight: 300; color: #111111; margin: 0 0 30px 0; letter-spacing: -1px; }
        
        .greeting { font-size: 16px; color: #333333; line-height: 1.7; margin-bottom: 40px; }
        
        .table-wrapper { border-left: 2px solid #eaeaea; padding-left: 20px; margin-bottom: 40px; }
        
        .footer { border-top: 1px solid #eaeaea; padding: 40px 40px 60px 40px; }
        .footer-brand { font-size: 12px; font-weight: 700; letter-spacing: 2px; color: #111111; text-transform: uppercase; margin-bottom: 12px; }
        .footer-text { font-size: 11px; color: #999999; line-height: 1.5; margin: 0; }
        
        @media screen and (max-width: 600px) {
            .content, .header, .footer { padding-left: 20px !important; padding-right: 20px !important; }
            .header-label { display: none !important; }
            .main-title { font-size: 28px !important; }
        }
    </style>
</head>
<body>
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center">
                <table class="main-container" border="0" cellpadding="0" cellspacing="0">
                    <!-- Header -->
                    <tr>
                        <td class="header">
                            <table class="header-table" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td class="header-logo">
                                        {{ mb_strtoupper(config('app.name')) }}
                                    </td>
                                    <td class="header-label">
                                        MENSAGEM DIRETA
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td class="content">
                            <div class="sub-title">Para: {{ $customerName }}</div>
                            <h1 class="main-title">{{ $customSubject }}</h1>
                            
                            <div class="table-wrapper">
                                <div class="greeting">
                                    {!! $customMessage !!}
                                </div>
                            </div>
                        </td>
                    </tr>
                    
                    <!-- Footer -->
                    <tr>
                        <td class="footer">
                            <div class="footer-brand">{{ mb_strtoupper(config('app.name')) }}</div>
                            <p class="footer-text">
                                Este é um e-mail oficial. Você pode responder diretamente a esta mensagem.<br>
                                &copy; {{ date('Y') }} {{ config('app.name') }} - Todos os direitos reservados.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
