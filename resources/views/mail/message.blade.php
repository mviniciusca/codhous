<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['subject'] ?? __('Mail Message') }}</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        .mail-wrapper {
            width: 100%;
            min-width: 540px;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .mail-container {
            border-radius: 8px;
            padding: 20px;
        }

        .mail-message {
            color: #555c66;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        .logo {
            font-size: 16px;
            width: 48px;
            height: 48px;
            margin-x: auto;
            text-align: center;
            color: rgb(33, 139, 238);
        }

        .logo-app {
            max-width: 200px;
            max-height: 48px;
            height: auto;
        }
    </style>
</head>

<body>
    <div class="mail-wrapper">
        <div class="mail-container">
            <p class="mail-message">
                {!! $data['message'] !!}
            </p>
        </div>
    </div>
</body>

</html>