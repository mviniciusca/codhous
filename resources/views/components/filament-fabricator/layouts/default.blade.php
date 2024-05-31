@props(['page'])
<!DOCTYPE html>
<html lang="en">

<head>
    <x-layout.meta />
</head>

<body class="dark:bg-primary-900 dark:text-primary-300">
    <div class="main-base">
        <div class="px-4 py-4">
            <div class="mx-auto max-w-7xl">
                <x-core.header />
                <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
            </div>
            <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
            <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
            <x-layout.footer />
        </div>
    </div>
</body>

</html>
