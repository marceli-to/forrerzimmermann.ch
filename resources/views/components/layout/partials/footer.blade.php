  <footer class="sticky bottom-0 left-0 z-20 w-full bg-white border-t border-t-black shrink-0 h-[var(--footer-height)] px-16 xl:px-32 flex justify-between items-center">

    @if (!request()->routeIs('page.projects.show'))
      <x-menu.main.mobile.button class="md:hidden" />
    @endif

    {{ $slot }}

  </footer>
