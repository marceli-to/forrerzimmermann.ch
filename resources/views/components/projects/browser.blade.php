@props(['prev' => null, 'next' => null])
<nav>
  <ul class="flex items-center gap-x-60">
    <li class="flex items-center gap-x-52">
      @if($prev)
        <a href="{{ route('page.projects.show', $prev->slug) }}" aria-label="Vorheriges Projekt" class="block w-15 h-27 text-black">
          <x-icons.chevron-left />
        </a>
      @endif
      @if($next)
        <a href="{{ route('page.projects.show', $next->slug) }}" aria-label="Nächstes Projekt" class="block w-15 h-27 text-black">
          <x-icons.chevron-right />
        </a>
      @endif
    </li>
    <li>
      <a
        href="{{ route('page.projects') }}"
        aria-label="Zurück"
        class="block w-27 h-27 text-black">
        <x-icons.cross class="w-full h-auto" />
      </a>
    </li>
  </ul>
</nav>
