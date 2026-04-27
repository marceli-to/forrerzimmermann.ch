<header 
  class="_bg-white sticky top-0 z-40 md:relative border-b border-b-black w-full h-[var(--header-height)] xl:h-[var(--header-height-md)] px-16 xl:px-32 pt-16 xl:pt-21 shrink-0 transition-transform duration-600 ease-out will-change-transform [&.is-hidden]:-translate-y-full motion-reduce:transition-none motion-reduce:[&.is-hidden]:translate-y-0" 
  data-shy>
  <x-grid.container>
    <x-grid.span class="xl:col-span-9">
      @if (request()->routeIs('page.landing'))
        <x-logo.animated />
      @else
        <a 
          href="{{ route('page.landing') }}"
          class="block"
          aria-label="Zurück zur Startseite">
          <x-logo.static />
        </a>
      @endif
    </x-grid.span>

    <x-grid.span class="xl:col-span-3 bg-blue-50">
      <x-menu.desktop.container>
        <x-menu.desktop.item href="{{ route('page.projects') }}" label="Projekte" :active="request()->routeIs('page.projects')" />
        <x-menu.desktop.item href="{{ route('page.atelier.profile') }}" label="Atelier" :active="request()->routeIs('page.atelier.*')" />
        <x-menu.desktop.item href="{{ route('page.contact') }}" label="Kontakt" :active="request()->routeIs('page.contact')" />
      </x-menu.desktop.container>
    </x-grid.span>

  </x-grid.container>
</header>