@if($contact->status)
<x-layout.section>
    <x-layout.content>
        <div class="container mx-auto flex flex-wrap px-5 py-24 sm:flex-nowrap">
            <div
                class="relative flex items-end justify-start overflow-hidden rounded-lg bg-primary-300 p-10 sm:mr-10 md:w-1/2 lg:w-2/3 dark:bg-primary-950">
                <iframe width="100%" height="100%" class="absolute inset-0" frameborder="0" title="map" marginheight="0"
                    marginwidth="0" scrolling="no" src="{{ $contact->map }}"
                    style="filter: grayscale(1) contrast(1) opacity(0.7);"></iframe>
                <div class="relative flex flex-wrap rounded bg-primary-500 py-6 shadow-md">
                    <div class="px-6 lg:w-1/2">
                        <h2 class="title-font text-xs font-semibold tracking-widest">ADDRESS</h2>
                        <p class="mt-1">Photo booth tattooed prism, portland taiyaki hoodie neutra typewriter</p>
                    </div>
                    <div class="mt-4 px-6 lg:mt-0 lg:w-1/2">
                        <h2 class="title-font text-xs font-semibold tracking-widest">EMAIL</h2>
                        <a class="leading-relaxed">example@email.com</a>
                        <h2 class="title-font mt-4 text-xs font-semibold tracking-widest">PHONE</h2>
                        <p class="leading-relaxed">123-456-7890</p>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex w-full flex-col md:ml-auto md:mt-0 md:w-1/2 md:py-2 lg:w-1/3">
                <h2 class="title-font mb-1 text-2xl font-medium leading-tight tracking-tighter">{{ __('Contact') }}
                </h2>
                <p class="mb-5 leading-relaxed">{{ __('Send us a message and our team will able to get you back.') }}
                </p>
                <div class="relative mb-4">
                    <label for="name" class="text-sm leading-7">Name</label>
                    <input type="text" id="name" name="name"
                        class="bg-white w-full rounded border border-gray-300 px-3 py-1 text-base leading-8 outline-none transition-colors duration-200 ease-in-out focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                </div>
                <div class="relative mb-4">
                    <label for="email" class="text-sm leading-7">Email</label>
                    <input type="email" id="email" name="email"
                        class="bg-white w-full rounded border border-gray-300 px-3 py-1 text-base leading-8 outline-none transition-colors duration-200 ease-in-out focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">
                </div>
                <div class="relative mb-4">
                    <label for="message" class="text-sm leading-7">Message</label>
                    <textarea id="message" name="message"
                        class="bg-white h-32 w-full resize-none rounded border border-gray-300 px-3 py-1 text-base leading-6 outline-none transition-colors duration-200 ease-in-out focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200"></textarea>
                </div>
                <x-ui.button :filled='true'>Send Message</x-ui.button>
                <p class="mt-3 text-xs">Chicharrones blog helvetica normcore iceland tousled brook viral
                    artisan.
                </p>
            </div>
        </div>
    </x-layout.content>
</x-layout.section>
@endif
