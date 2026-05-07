<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Playground</title>
  @vite(['resources/css/site.css', 'resources/js/site.js'])
</head>
<body class="flex flex-col min-h-dvh">
  <header class="bg-green-100 sticky top-0 z-40 md:relative border-b border-b-black w-full h-60 shrink-0">

  </header>
  <main class="flex-1 min-h-0 overflow-hidden relative">
    <div data-slides="playground" class="h-[calc(100dvh-120px)] relative px-100">
      <div class="swiper h-full">
        <div class="swiper-wrapper">
          <div class="swiper-slide">
            <div class="h-full flex items-center justify-center overflow-hidden">
              <img src="/img/landscape.jpg" class="h-full w-auto max-w-none">
            </div>
          </div>
          <div class="swiper-slide">
            <div class="h-full flex flex-col">
              <div class="flex-1 min-h-0 flex items-center justify-center overflow-hidden">
                <img src="/img/portrait.jpg" class="h-full w-auto max-w-none">
              </div>
              <div class="py-20 px-16 text-[1rem] text-center max-w-xl mx-auto">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</div>
            </div>
          </div>
          <div class="swiper-slide">
            <div class="h-full flex flex-col">
              <div class="flex-1 min-h-0 flex items-center justify-center overflow-hidden">
                <img src="/img/landscape-2.jpg" class="h-full w-auto max-w-none">
              </div>
              <div class="py-20 px-16 text-[1rem] text-center max-w-xl mx-auto">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</div>
            </div>
          </div>
        </div>
      </div>
      <button type="button" data-slides-prev="playground" class="absolute left-16 top-1/2 -translate-y-1/2 z-10 px-12 py-8 bg-white/80 border border-black">‹</button>
      <button type="button" data-slides-next="playground" class="absolute right-16 top-1/2 -translate-y-1/2 z-10 px-12 py-8 bg-white/80 border border-black">›</button>
    </div>
  </main>
  <footer class="sticky bottom-0 left-0 z-20 w-full bg-blue-100 border-t border-t-black shrink-0 h-60 px-16 xl:px-32 flex justify-between items-center">

  </footer>
</body>
</html>
