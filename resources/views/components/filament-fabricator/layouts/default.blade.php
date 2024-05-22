@props(['page'])
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $page->title }}</title>
    @vite('resources/css/app.css')
</head>

<body class="dark:bg-primary-900 dark:text-primary-300">
    <div class="container">
        <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
    </div>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <x-layout.footer />
</body>

</html>
