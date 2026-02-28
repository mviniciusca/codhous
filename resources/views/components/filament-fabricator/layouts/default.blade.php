@props(['page'])
<!DOCTYPE html>
<html lang="en">
<head>
    @vite('resources/css/app.css')
</head>
<body class="antialiased">
  


    
    <x-filament-fabricator::page-blocks :blocks="$page->blocks" />
        
 

    @vite('resources/js/app.js')
    @livewire('notifications')
</body>
</html>

