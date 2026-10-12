{{--
    Comunicado da newsletter. Layout em tabelas com estilos inline (Gmail, Outlook, Apple Mail),
    coluna de até 600px que vira largura total no celular. Também é a prévia do painel ($preview).
    No envio, logo e imagem de topo vão embutidos (CID) via $message->embed().
--}}
@php
    $embed = isset($message) && $message instanceof \Illuminate\Mail\Message;
    $src = fn (?array $image) => $image ? ($embed && $image['path'] ? $message->embed($image['path']) : $image['url']) : null;
    $logoSrc = $src($logo);
    $bannerSrc = $src($banner);
@endphp
<!DOCTYPE html>
<html lang="pt-BR" xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="color-scheme" content="light">
    <meta name="supported-color-schemes" content="light">
    <title>{{ $subject }}</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    <style>
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }
        @media screen and (max-width: 620px) {
            .nl-outer { padding: 0 !important; }
            .nl-card { border-radius: 0 !important; border-left: 0 !important; border-right: 0 !important; }
            .nl-pad { padding-left: 20px !important; padding-right: 20px !important; }
            .nl-title { font-size: 21px !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;width:100%;background-color:#f3f4f6;">
    <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;font-size:1px;line-height:1px;color:#f3f4f6;opacity:0;">
        {{ $preheader }}&#8199;&#847;&#8199;&#847;&#8199;&#847;&#8199;&#847;&#8199;&#847;&#8199;&#847;
    </div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#f3f4f6;">
        <tr>
            <td class="nl-outer" align="center" style="padding:32px 16px;">
                <!--[if mso]><table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"><tr><td><![endif]-->
                <table role="presentation" class="nl-card" width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width:600px;background-color:#ffffff;border:1px solid #e5e7eb;border-radius:12px;">
                    {{-- Marca --}}
                    <tr>
                        <td class="nl-pad" align="center" style="padding:28px 32px 20px;">
                            @if ($logoSrc)
                                <img src="{{ $logoSrc }}" alt="{{ $brand }}" height="44" style="display:block;height:44px;width:auto;max-width:220px;border:0;outline:none;text-decoration:none;">
                            @else
                                <span style="font-family:Helvetica,Arial,sans-serif;font-size:20px;font-weight:700;color:#111827;">{{ $brand }}</span>
                            @endif
                        </td>
                    </tr>

                    @if ($bannerSrc)
                        <tr>
                            <td class="nl-pad" style="padding:0 32px;">
                                <img src="{{ $bannerSrc }}" alt="" width="536" style="display:block;width:100%;max-width:536px;height:auto;border:0;border-radius:8px;outline:none;text-decoration:none;">
                            </td>
                        </tr>
                    @endif

                    {{-- Conteúdo --}}
                    <tr>
                        <td class="nl-pad" style="padding:28px 32px 8px;font-family:Helvetica,Arial,sans-serif;">
                            <h1 class="nl-title" style="margin:0 0 20px;font-size:24px;line-height:1.3;font-weight:700;color:#111827;">{{ $subject }}</h1>
                            <div style="font-size:15px;line-height:1.6;color:#374151;word-break:break-word;">
                                {!! $bodyHtml !!}
                            </div>
                        </td>
                    </tr>

                    {{-- Rodapé --}}
                    <tr>
                        <td class="nl-pad" style="padding:24px 32px 28px;font-family:Helvetica,Arial,sans-serif;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                <tr>
                                    <td style="border-top:1px solid #e5e7eb;padding-top:20px;font-size:12px;line-height:1.6;color:#6b7280;">
                                        <strong style="color:#374151;">{{ $brand }}</strong>
                                        @if ($address)
                                            <br>{{ $address }}
                                        @endif
                                        <br>Você recebeu este e-mail porque se inscreveu na nossa newsletter.
                                        @if ($unsubscribeUrl)
                                            <br><a href="{{ $unsubscribeUrl }}" target="_blank" rel="noopener noreferrer" style="color:#6b7280;text-decoration:underline;">Cancelar inscrição</a>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
                <!--[if mso]></td></tr></table><![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
