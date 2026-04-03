<!DOCTYPE html>
<html lang="de" class="h-full scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name') }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@if (!empty($metaDescription))
<meta name="description" content="{{ $metaDescription }}">
@endif
@if ($seo?->og_title)
<meta property="og:title" content="{{ $ogTitle ?? $seo->og_title }}">
@endif
@if ($seo?->og_description)
<meta property="og:description" content="{{ $ogDescription ?? $seo->og_description }}">
@endif
@php $ogImage = $ogImage ?? $seo?->media->first()?->file @endphp
@if ($ogImage)
<meta property="og:image" content="{{ url('uploads/' . $ogImage) }}">
@endif
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
@vite(['resources/css/app.css'])
</head>
<body class="h-full font-sans antialiased">
    {{ $slot }}
</body>
</html>
