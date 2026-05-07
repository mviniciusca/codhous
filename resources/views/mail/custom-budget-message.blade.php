<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { border-bottom: 2px solid #f4f4f4; padding-bottom: 10px; margin-bottom: 20px; }
        .footer { margin-top: 30px; padding-top: 10px; border-top: 1px solid #eee; font-size: 12px; color: #777; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Olá, {{ $customerName }}!</h2>
        </div>
        
        <div class="content">
            {!! nl2br(e($customMessage)) !!}
        </div>

        <div class="footer">
            <p>Atenciosamente,<br>{{ config('app.name') }}</p>
        </div>
    </div>
</body>
</html>
