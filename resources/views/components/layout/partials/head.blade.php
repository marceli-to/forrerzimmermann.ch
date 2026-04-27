@props([
  'title' => null,
  'description' => null,
  'ogImage' => null,
])
@php
  $appName = config('app.name');
  $defaultDescription = config('app.meta_description');
  $metaTitle = $title ?? $appName;
  $metaTitle = $metaTitle !== $appName ? "{$metaTitle} – {$appName}" : $appName;
  $metaDescription = $description ?? $seo?->og_description ?? $defaultDescription;
  $resolvedOgImage = $ogImage ?? $seo?->media->firstWhere('is_og', true)?->file ?? $seo?->media->first()?->file ?? null;
  $ogImageUrl = $resolvedOgImage ? url('uploads/' . $resolvedOgImage) : asset('opengraph.jpg');
@endphp
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="view-transition" content="same-origin">
<title>{{ $metaTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="forrer zimmermann architektur" />
<link rel="manifest" href="/site.webmanifest" />
<meta property="og:title" content="{{ $metaTitle }}" />
<meta property="og:type" content="website" />
<meta property="og:url" content="{{ url()->current() }}" />
<meta property="og:image" content="{{ $ogImageUrl }}" />
<meta property="og:description" content="{{ $metaDescription }}" />
<meta property="og:site_name" content="{{ $appName }}" />
<meta property="og:locale" content="de_CH" />
<meta name="twitter:card" content="summary_large_image" />
<meta name="twitter:title" content="{{ $metaTitle }}" />
<meta name="twitter:description" content="{{ $metaDescription }}" />
<meta name="twitter:image" content="{{ $ogImageUrl }}" />
@vite(['resources/css/site.css', 'resources/js/site.js', 'resources/js/debug.js'])
</head>
