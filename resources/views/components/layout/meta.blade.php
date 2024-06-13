<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>{{ $meta->meta_title ?? __('Codhous Software') }}</title>
<meta name="author" content="{{ $meta->meta_author ?? __('Marcos Coelho Dev')}}">
<meta name="keywords"
    content="{{ $meta->meta_keywords ?? __('laravel, tailwind, filament, php, fullstack application')}}">
<meta name="description"
    content="{{ $meta->meta_description ?? __('This is Codhous Software for your bisness. Open Source and powered by Filament + Laravel. Build for Marcos Coelho.')}}">
<link rel="icon" type="image/x-icon"
    href="{{ $favicon->favicon ? asset('storage/' . $favicon->favicon) : asset('favicon.png')}}">
@if($meta->header_scripts)
{{-- Header Scripts --}}
{{ $meta->header_scripts }}
@endif
{{-- Google Scripts --}}
@if($meta->google_analytics)
{{ $meta->google_analytics }}
@endif
@if($meta->google_tag)
{{ $meta->google_tag }}
@endif
