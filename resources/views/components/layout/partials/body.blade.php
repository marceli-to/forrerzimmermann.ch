<body 
  class="antialiased font-sans h-dvh flex flex-col text-black"
  x-data="{ menu: false }">
  @if(config('app.debug'))
    <x-debug />
  @endif
  {{ $slot }}
</body>