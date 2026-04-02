<!DOCTYPE html>
<html lang="de" class="h-full bg-white dark:bg-neutral-950 scroll-smooth overflow-y-scroll">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CMS - {{ config('app.name', 'marceli.to') }}</title>
<meta name="robots" content="noindex, nofollow">
<meta name="msapplication-TileColor" content="#ffffff">
<meta name="theme-color" content="#ffffff" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#0a0a0a" media="(prefers-color-scheme: dark)">
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="CMS marceli.to" />
<link rel="manifest" href="/site.webmanifest" />
@vite(['resources/css/app.css'])
</head>
