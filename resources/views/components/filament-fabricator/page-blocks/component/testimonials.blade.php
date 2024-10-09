@aware(['page'])
@props(['avatar', 'name', 'job_position', 'opinion'])

<section>
    <div class="mx-auto max-w-screen-xl px-4 py-8 text-center lg:px-6 lg:py-16">
        <figure class="mx-auto max-w-screen-md">
            <svg class="text-gray-400 dark:text-gray-600 mx-auto mb-3 h-12" viewBox="0 0 24 27" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M14.017 18L14.017 10.609C14.017 4.905 17.748 1.039 23 0L23.995 2.151C21.563 3.068 20 5.789 20 8H24V18H14.017ZM0 18V10.609C0 4.905 3.748 1.038 9 0L9.996 2.151C7.563 3.068 6 5.789 6 8H9.983L9.983 18L0 18Z"
                    fill="currentColor" />
            </svg>
            <blockquote>
                <p class="text-gray-900 dark:text-white text-2xl font-medium">"{!! $opinion !!}"</p>
            </blockquote>
            <figcaption class="mt-6 flex items-center justify-center space-x-3">
                <img class="h-6 w-6 rounded-full" src="{{ asset('storage/' . $avatar) }}" alt="profile picture">
                <div class="divide-gray-500 dark:divide-gray-700 flex items-center divide-x-2">
                    <div class="text-gray-900 dark:text-white pr-3 font-medium">
                        {!! $name !!}
                    </div>
                    <div class="text-gray-500 dark:text-gray-400 pl-3 text-sm font-light">
                        {{ $job_position }}
                    </div>
                </div>
            </figcaption>
        </figure>
    </div>
</section>