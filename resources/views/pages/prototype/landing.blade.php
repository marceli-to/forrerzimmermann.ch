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
<meta name="apple-mobile-web-app-title" content="Forrer Zimmermann Architekten" />
<link rel="manifest" href="/site.webmanifest" />
@vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body
  class="antialiased font-sans h-screen flex flex-col text-black"
  x-data="{ menu: false }">
  <x-debug class="hidden" />

  <header 
    class="bg-white sticky top-0 z-40 md:relative border-b border-b-black w-full h-[var(--header-height)] md:h-[var(--header-height-md)] px-16 lg:px-32 pt-16 lg:pt-21 shrink-0 transition-transform duration-600 ease-out will-change-transform [&.is-hidden]:-translate-y-full motion-reduce:transition-none motion-reduce:[&.is-hidden]:translate-y-0" 
    data-header>
    <div class="relative flex flex-col w-full gap-y-[0.28125rem] leading-none">
      <x-icons.logo.fz class="w-212 h-auto" />
      <x-icons.logo.a class="w-180 h-auto relative ml-1 transition-transform duration-900 delay-600 ease-in-out translate-x-[calc(100vw-32px-100%)] will-change-transform [&.is-ready]:translate-x-0 motion-reduce:transition-none motion-reduce:translate-x-0" data-logo />
    </div>
  </header>

  <main
    role="main"
    class="flex-1 md:min-h-0 md:overflow-hidden"
    data-gallery="landing">
    <div class="swiper h-full">
      <div class="swiper-wrapper">

        <div class="swiper-slide md:px-32 !hidden">
          <x-grid.container class="h-full">
            <x-grid.span class="md:col-span-full lg:col-span-10 lg:col-start-2 xl:col-span-8 xl:col-start-3 h-full">
              <img src="/img/dummy-content.jpg" alt="Dummy Content" class="w-full h-full object-cover">
            </x-grid.span>
          </x-grid.container>
        </div>

        <div class="swiper-slide md:px-32">
          <x-grid.container class="min-h-full">
            <x-grid.span class="md:col-span-8 md:-ml-32 md:h-full">
              <img src="/img/dummy-content.jpg" alt="Dummy Content" class="aspect-[4/3] w-full h-full object-cover">
            </x-grid.span>
            <x-grid.span class="bg-green-100 hyphens-auto px-16 py-20 md:col-span-4 text-[22px] leading-[1.18]">
              <p>Umfassende Erfahrung in Projektierung und Ausführung von ganz kleinen bis sehr grossen Bauvorhaben, Sanierungen, Erweiterungen und Neubauten.</p>
            </x-grid.span>
          </x-grid.container>
        </div>

      </div>
    </div>
  </main>

  <footer class="sticky bottom-0 left-0 z-20 w-full bg-white border-t border-t-black shrink-0 h-[var(--footer-height)] px-16 flex justify-between items-center">
    <button 
      type="button" 
      data-gallery-prev="landing" 
      aria-label="Previous slide">

    </button>
    <button 
      type="button" 
      data-gallery-next="landing" 
      aria-label="Next slide">

    </button>
  </footer>
</body>
</html>