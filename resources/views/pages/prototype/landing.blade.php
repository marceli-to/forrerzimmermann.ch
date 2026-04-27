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

<body class="antialiased font-sans h-screen flex flex-col text-black" x-data="{ menu: false }">
  <x-debug />

  <x-layout.partials.header />

  <main role="main"class="flex-1 md:min-h-0 md:overflow-hidden">

    <div data-slides="landing" class="h-full">
      <div class="swiper h-full">
        <div class="swiper-wrapper">

          <div class="swiper-slide md:px-32">
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
              <x-grid.span class="hyphens-auto px-16 md:px-0 py-20 md:col-span-4 text-[22px] leading-[1.18]">
                <p>Umfassende Erfahrung in Projektierung und Ausführung von ganz kleinen bis sehr grossen Bauvorhaben, Sanierungen, Erweiterungen und Neubauten.</p>
              </x-grid.span>
            </x-grid.container>
          </div>

        </div>
      </div>
    </div>
  </main>

  <x-layout.partials.footer>
    <x-swiper.navigation />
  </x-layout.partials.footer>

</body>
</html>