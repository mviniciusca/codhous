@props(['title' => 'Convite'])
{{-- Páginas públicas do convite (CSS próprio, sem depender do build do painel) --}}
@php
    $logo = \App\Models\Setting::get('website.logo');
    $brand = \App\Models\Setting::get('website.name') ?: config('app.name');
@endphp
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Convite' }} · {{ $brand }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #f6f7f9; --surface: #fff; --text: #0d0f13; --muted: #6b7484; --border: rgba(13, 15, 19, .1);
            --primary: #2350f5; --primary-hover: #1c3fe0; --danger: #dc2626; --ring: rgba(54, 104, 255, .35);
        }
        @media (prefers-color-scheme: dark) {
            :root { --bg: #0d0f13; --surface: #171a20; --text: #f6f7f9; --muted: #8a93a3; --border: rgba(255, 255, 255, .08); --primary: #3668ff; --primary-hover: #608cff; --danger: #f87171; }
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; padding: 24px 16px; font-family: Jost, system-ui, sans-serif; background: var(--bg); color: var(--text); -webkit-font-smoothing: antialiased; }
        .card { width: 100%; max-width: 420px; padding: 32px; border-radius: 16px; background: var(--surface); border: 1px solid var(--border); box-shadow: 0 24px 48px -24px rgba(0, 0, 0, .35); }
        .brand { display: flex; justify-content: center; margin-bottom: 24px; }
        .brand img { max-height: 44px; max-width: 220px; object-fit: contain; }
        .brand span { font-size: 20px; font-weight: 700; }
        h1 { margin: 0; font-size: 22px; font-weight: 600; letter-spacing: -.01em; text-align: center; }
        .lead { margin: 8px 0 24px; font-size: 14px; line-height: 1.6; color: var(--muted); text-align: center; }
        .lead strong { color: var(--text); font-weight: 600; }
        .field { display: grid; gap: 6px; margin-bottom: 16px; }
        label { font-size: 13px; font-weight: 500; }
        input { width: 100%; padding: 10px 12px; border-radius: 10px; border: 1px solid var(--border); background: transparent; color: var(--text); font: inherit; font-size: 14px; }
        input:focus { outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px var(--ring); }
        input[readonly] { color: var(--muted); background: rgba(127, 127, 127, .06); }
        .hint { font-size: 12px; color: var(--muted); }
        .error { font-size: 12px; color: var(--danger); }
        button, .btn { display: inline-flex; align-items: center; justify-content: center; width: 100%; padding: 11px 16px; border: 0; border-radius: 10px; font: inherit; font-size: 14px; font-weight: 600; color: #fff; background: var(--primary); cursor: pointer; text-decoration: none; transition: background-color .15s ease; }
        button:hover, .btn:hover { background: var(--primary-hover); }
        button:focus-visible, .btn:focus-visible { outline: 2px solid var(--primary); outline-offset: 3px; }
        .foot { margin-top: 20px; font-size: 12px; color: var(--muted); text-align: center; }
    </style>
</head>
<body>
    <main class="card">
        <div class="brand">
            @if ($logo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($logo) }}" alt="{{ $brand }}">
            @else
                <span>{{ $brand }}</span>
            @endif
        </div>
        {{ $slot }}
    </main>
</body>
</html>
