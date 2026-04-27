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
  class="antialiased font-sans md:h-screen flex flex-col text-black"
  x-data="{ menu: false }">
  <x-debug class="hidden" />

  <x-layout.partials.header />

  <main
    role="main"
    class="md:flex-1 md:min-h-0 md:overflow-auto scrollarea"> 

    <div class="flex flex-col gap-y-22 md:grid md:grid-cols-12 md:gap-30 p-16 md:p-32">
      <div class="md:col-span-4 xl:col-span-3">
        <a href="#" class="hover:text-accent transition-colors">
          <div class="aspect-[3/2] w-full bg-yellow-50"></div>
          <h2 class="text-[21px] leading-[1.17] mb-0 mt-12">Nietengasse, Zürich</h2>
          <span class="text-[16px] leading-[1.3]">Instandsetzung 2024</span>
        </a>
      </div>
      <div class="md:col-span-4 xl:col-span-3">
        <a href="#" class="hover:text-accent transition-colors">
          <div class="aspect-[3/2] w-full bg-orange-50"></div>
          <h2 class="text-[21px] leading-[1.17] mb-0 mt-12">Nietengasse, Zürich</h2>
          <span class="text-[16px] leading-[1.3]">Instandsetzung 2024</span>
        </a>
      </div>
      <div class="md:col-span-4 xl:col-span-3">
        <a href="#" class="hover:text-accent transition-colors">
          <div class="aspect-[3/2] w-full bg-green-50"></div>
          <h2 class="text-[21px] leading-[1.17] mb-0 mt-12">Nietengasse, Zürich</h2>
          <span class="text-[16px] leading-[1.3]">Instandsetzung 2024</span>
        </a>
      </div>
      <div class="md:col-span-4 xl:col-span-3">
        <a href="#" class="hover:text-accent transition-colors">
          <div class="aspect-[3/2] w-full bg-blue-50"></div>
          <h2 class="text-[21px] leading-[1.17] mb-0 mt-12">Nietengasse, Zürich</h2>
          <span class="text-[16px] leading-[1.3]">Instandsetzung 2024</span>
        </a>
      </div>
      <div class="md:col-span-4 xl:col-span-3">
        <a href="#" class="hover:text-accent transition-colors">
          <div class="aspect-[3/2] w-full bg-purple-50"></div>
          <h2 class="text-[21px] leading-[1.17] mb-0 mt-12">Nietengasse, Zürich</h2>
          <span class="text-[16px] leading-[1.3]">Instandsetzung 2024</span>
        </a>
      </div>
      <div class="md:col-span-4 xl:col-span-3">
        <a href="#" class="hover:text-accent transition-colors">
          <div class="aspect-[3/2] w-full bg-fuchsia-50"></div>
          <h2 class="text-[21px] leading-[1.17] mb-0 mt-12">Nietengasse, Zürich</h2>
          <span class="text-[16px] leading-[1.3]">Instandsetzung 2024</span>
        </a>
      </div>
      <div class="md:col-span-4 xl:col-span-3">
        <a href="#" class="hover:text-accent transition-colors">
          <div class="aspect-[3/2] w-full bg-lime-50"></div>
          <h2 class="text-[21px] leading-[1.17] mb-0 mt-12">Nietengasse, Zürich</h2>
          <span class="text-[16px] leading-[1.3]">Instandsetzung 2024</span>
        </a>
      </div>
      <div class="md:col-span-4 xl:col-span-3">
        <a href="#" class="hover:text-accent transition-colors">
          <div class="aspect-[3/2] w-full bg-rose-50"></div>
          <h2 class="text-[21px] leading-[1.17] mb-0 mt-12">Nietengasse, Zürich</h2>
          <span class="text-[16px] leading-[1.3]">Instandsetzung 2024</span>
        </a>
      </div>
    </div>


  </main>

  <x-layout.partials.footer />
</body>
</html>