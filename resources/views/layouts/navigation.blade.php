   <nav></nav>
   <!-- Primary Navigation Menu -->
   <div class="grid h-full w-auto">
       <div class="flex-col">
           <div class="grid">
               <!-- Logo -->
               <div class="grid justify-center p-4">
                   <a href="{{ route('dashboard') }}">
                       <x-application-logo class="block h-16 w-auto fill-current text-lg text-gray-800" />
                   </a>
               </div>

               <!-- Navigation Links -->
               <div class="items mt-20 grid gap-2 p-4">
                   <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                       <ion-icon class="text-xl" name="planet-outline"></ion-icon> {{ __('Home') }}
                   </x-nav-link>

                   <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index')">
                       <ion-icon class="text-xl" name="bookmarks-outline"></ion-icon>{{ __('Tasks') }}

                   </x-nav-link>

                   {{--
                   <x-nav-link :href="route('projects.index')" :active="request()->routeIs('projects.index')">
                       <ion-icon class="text-xl" name="rocket-outline"></ion-icon>{{ __('Projects') }}
                   </x-nav-link>  --}}

                   <x-nav-link :href="route('customers.index')" :active="request()->routeIs('customers.index')">
                       <ion-icon class="text-xl" name="people-outline"></ion-icon>{{ __('Customers') }}
                   </x-nav-link>

               </div>
           </div>

       </div>

       <div class="flex h-auto w-auto items-end justify-center">
           <span class="flex items-center justify-center gap-2 p-4 pb-8 text-sm">
               <!-- Avatar -->
               <span class="rounded-full bg-gray-50 p-7"></span>
               <!-- Username -->
               <span class="grid">
                   <strong>{{ ucfirst(strtolower(Auth::user()->name)) }}</strong>
                   {{ Auth::user()->email }}
               </span>
           </span>
       </div>
   </div>
   </nav>
