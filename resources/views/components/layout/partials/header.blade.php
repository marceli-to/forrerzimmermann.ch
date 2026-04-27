<header 
  class="bg-white sticky top-0 z-40 md:relative border-b border-b-black w-full h-[var(--header-height)] xl:h-[var(--header-height-md)] px-16 xl:px-32 pt-16 xl:pt-21 shrink-0 transition-transform duration-600 ease-out will-change-transform [&.is-hidden]:-translate-y-full motion-reduce:transition-none motion-reduce:[&.is-hidden]:translate-y-0" 
  data-shy>
  @if (request()->routeIs('page.landing'))
    <x-logo.animated />
  @else
  <a 
    href="{{ route('page.landing') }}"
    class="block"
    aria-label="Zurück zur Startseite">
    <x-logo.default />
  </a>
  @endif
</header>