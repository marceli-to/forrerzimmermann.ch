<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-y-auto">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="view-transition" content="same-origin">
<title>FZA Prototype Landing</title>
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="Forrer Zimmermann Architekten" />
<link rel="manifest" href="/site.webmanifest" />
@vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body
  class="antialiased font-sans h-screen flex flex-col"
  x-data="{ menu: false }">
  <x-debug />

  <x-layout.partials.header />

  <main role="main" class="flex-1 min-h-0 md:px-32 overflow-hidden">

    <x-grid.container class="h-full">
      <x-grid.span class="md:col-span-8 md:-ml-32 md:min-h-0">
        <img src="/img/dummy-content.jpg" alt="Dummy Content" class="w-full h-full object-cover">
      </x-grid.span>

      <x-grid.span class="col-span-4 px-16 py-16 md:px-0 min-h-0 overflow-auto">
        <h2 class="text-[23px] leading-[1.17] mb-0">Katrin Zimmermann</h2>
        <div class="text-[18px] leading-[1.33]">
          Architektin MSc ETH<br>kzi@forrerzimmermann.ch
        </div>
      </x-grid.span>
    </x-grid.container>
    
  </main>

  <x-layout.partials.footer />
</body>
</html>