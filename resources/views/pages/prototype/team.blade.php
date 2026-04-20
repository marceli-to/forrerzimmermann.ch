<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-y-scroll">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="view-transition" content="same-origin">
<title>FZA Prototype Landing</title>
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
<meta name="apple-mobile-web-app-title" content="forrer zimmermann architektur" />
<link rel="manifest" href="/site.webmanifest" />
@vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body 
  class="antialiased font-sans flex flex-col min-h-screen"
  x-data="{ menu: false }">
  <x-debug />

  <header class="bg-orange-200/40 border-b border-b-black w-full md:h-(--header-height-md) px-32 flex flex-col justify-center items-center">
    [HEADER]
  </header>

  <main 
    role="main" 
    class="flex-1 px-32">
    <x-grid.container>
      <x-grid.span class="col-span-8 -ml-32">
        <img src="/img/dummy-content.jpg" alt="Dummy Content" class="w-full h-full object-cover">
      </x-grid.span>
      <x-grid.span class="col-span-4 bg-green-200/40">
        <h2>Praktikumstelle</h2>
        <p>Für Mitarbeit an Bauprojekten suchen wir ab sofort eine Praktikantin/einen Praktikanten. Von Vorteil sind vier Semester Architekturstudium, gute Deutschkenntnisse und Erfahrung mit VectorWorks</p>
      </x-grid.span>
    </x-grid.container>
  </main>

  <footer class="sticky bottom-0 bg-gray-200/30 border-t border-t-black h-(--footer-height-md) px-32 flex justify-between items-center">
    [FOOTER]
  </footer>
</body>
</html>