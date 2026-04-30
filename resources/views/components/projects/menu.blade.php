@props(['project'])
<nav>
  <ul class="flex flex-row gap-x-27">
    <li>
      <a
        href="{{ route('page.project.images', $project->slug) }}"
        aria-label="Bilder"
        @class([
          'text-[18px] leading-none hover:text-accent transition-colors [.is-active]:text-accent',
          'is-active' => request()->routeIs('page.project.images'),
        ])>
        Bilder
      </a>
    </li>
    <li>
      <a
        href="{{ route('page.project.text', $project->slug) }}"
        aria-label="Text"
        @class([
          'text-[18px] leading-none hover:text-accent transition-colors [.is-active]:text-accent',
          'is-active' => request()->routeIs('page.project.text'),
        ])>
        Text
      </a>
    </li>
  </ul>
</nav>
