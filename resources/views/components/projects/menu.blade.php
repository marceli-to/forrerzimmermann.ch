@props(['project'])
<nav>
  <ul class="flex flex-row gap-x-27">
    <li>
      <a
        href="{{ route('page.projects.images', $project->slug) }}"
        aria-label="Bilder"
        @class([
          'text-[18px] leading-none hover:text-accent transition-colors [.is-active]:text-accent',
          'is-active' => request()->routeIs('page.projects.images'),
        ])>
        Bilder
      </a>
    </li>
    <li>
      <a
        href="{{ route('page.projects.text', $project->slug) }}"
        aria-label="Text"
        @class([
          'text-[18px] leading-none hover:text-accent transition-colors [.is-active]:text-accent',
          'is-active' => request()->routeIs('page.projects.text'),
        ])>
        Text
      </a>
    </li>
  </ul>
</nav>
