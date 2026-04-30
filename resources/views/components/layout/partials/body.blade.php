<body 
  class="antialiased font-sans h-screen flex flex-col text-black"
  x-data="{ menu: false }">
  @if(config('app.debug'))
    <x-debug />
  @endif
  {{ $slot }}
</body>