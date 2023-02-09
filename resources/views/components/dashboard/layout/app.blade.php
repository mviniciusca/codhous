{{-- Header App --}}
<x-dashboard.layout.partials.header />
{{-- Navbar --}}
<x-dashboard.layout.navbar.app />
{{-- Sidebar --}}
<x-dashboard.layout.sidebar.app />
{{-- Main Content --}}
<div class="m-auto" id="main-content">
    {{ $slot }}
</div>
{{-- Footer --}}
<x-dashboard.layout.partials.footer />
