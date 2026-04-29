@props([
  'title' => null,
  'description' => null,
  'ogImage' => null,
  'project' => null,
  'prev' => null,
  'next' => null,
])
<x-layout.partials.html>
  <x-layout.partials.head
    :title="$title"
    :description="$description"
    :og-image="$ogImage"
  />
  <x-layout.partials.body>
    <x-projects.header :project="$project" :prev="$prev" :next="$next" />
    <x-layout.partials.main>
      {{ $slot }}
    </x-layout.partials.main>
    <x-layout.partials.footer>
      {{ $footer ?? '' }}
    </x-layout.partials.footer>
  </x-layout.partials.body>
</x-layout.partials.html>
