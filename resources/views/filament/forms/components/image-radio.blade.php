<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div x-data="{ state: $wire.$entangle('{{ $getStatePath() }}') }">
        <div class="flex flex-wrap gap-4 mt-2">
            @php
                $directory = public_path('img/templates');
                $files = \Illuminate\Support\Facades\File::isDirectory($directory) ? \Illuminate\Support\Facades\File::files($directory) : [];
            @endphp
            
            @foreach($files as $file)
                @php
                    $filename = $file->getFilename();
                    $url = asset('img/templates/' . $filename);
                @endphp
                <label class="cursor-pointer relative flex flex-col items-center group" style="width: 160px;">
                    <input type="radio" value="{{ $filename }}" x-model="state" class="sr-only" name="template_selection" />
                    
                    <div :class="{
                            'border-primary-500 ring-2 ring-primary-500': state === '{{ $filename }}',
                            'border-gray-200 dark:border-gray-700 hover:border-primary-400': state !== '{{ $filename }}'
                         }" 
                         class="rounded-xl overflow-hidden border-2 transition-all duration-200 bg-white dark:bg-gray-800 shadow-sm relative" style="width: 160px; height: 160px;">
                        <img src="{{ $url }}" style="width: 100%; height: 100%; object-fit: cover;" alt="{{ $filename }}">
                        
                        <!-- Check icon overlay -->
                        <div x-show="state === '{{ $filename }}'" x-transition class="absolute inset-0 bg-primary-500/20"></div>
                    </div>
                    
                    <div :class="{ 'text-primary-600 font-bold': state === '{{ $filename }}', 'text-gray-600 dark:text-gray-400': state !== '{{ $filename }}' }" class="mt-2 text-xs text-center font-medium truncate w-full px-1 transition-colors">
                        {{ $filename }}
                    </div>
                    
                    <!-- Check icon -->
                    <div x-show="state === '{{ $filename }}'" x-transition class="absolute top-2 right-2 bg-primary-600 text-white rounded-full p-1 shadow-sm">
                        <x-heroicon-s-check-circle class="w-5 h-5" />
                    </div>
                </label>
            @endforeach
        </div>
        
        @if(count($files) === 0)
            <div class="text-sm text-gray-500 italic p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-dashed border-gray-300 dark:border-gray-700">
                Nenhum template encontrado na pasta public/img/templates.
            </div>
        @endif
    </div>
</x-dynamic-component>
