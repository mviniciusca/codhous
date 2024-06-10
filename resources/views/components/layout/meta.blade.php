<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>{{ $meta->meta_title }}</title>
<meta name="author" content="{{ $meta->meta_author }}">
<meta name="keywords" content="{{ $meta->meta_keywords }}">
<meta name="description" content="{{ $meta->meta_description }}">
<link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $favicon->favicon)}}">
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
