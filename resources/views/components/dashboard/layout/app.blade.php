<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Codhous / Dashboard </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Tailwind --}}
    @vite('resources/css/app.css')
    {{--  AlpineJS --}}
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.min.js" defer></script>
    <livewire:styles />
</head>

<body class="bg-gray-900 text-gray-400">
    <div class="flex h-screen flex-col">

        <div class="flex-1">
            <div class="flex h-full">
                <div>
                    <x-dashboard.layout.sidebar />
                </div>
                <div class="h-full flex-1 p-4">
                    <header class="mb-10">
                        <x-dashboard.layout.header />
                    </header>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
    {{-- Flash Messages --}}
    <x-dashboard.ui.flash />
    <livewire:scripts />
</body>

</html>
