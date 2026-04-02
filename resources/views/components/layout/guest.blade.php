<!DOCTYPE html>
<html lang="de" class="h-full scroll-smooth dark:bg-neutral-950">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name', 'CMS') }}</title>
<meta name="robots" content="noindex, nofollow">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
@vite(['resources/css/site.css'])
</head>
<body class="h-full font-sans text-neutral-900 dark:text-neutral-100 antialiased bg-white dark:bg-neutral-950">
	{{ $slot }}
</body>
</html>
