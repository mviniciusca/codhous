<!-- Logo -->
<div class="mb-10 grid justify-center" id="logo">
    <x-application-logo class="block h-16 w-auto fill-current text-lg text-gray-800" />
</div>
<!-- Nav Links -->
<div class="" id="nav-menu">
    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
        <ion-icon class="text-xl" name="planet-outline"></ion-icon> {{ __('Home') }}
    </x-nav-link>

    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index')">
        <ion-icon class="text-xl" name="bookmarks-outline"></ion-icon>{{ __('Tasks') }}

    </x-nav-link>

    <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.index')">
        <ion-icon class="text-xl" name="people-outline"></ion-icon>{{ __('Customers') }}
    </x-nav-link>
</div>
