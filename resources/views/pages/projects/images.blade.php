<x-layout.project
  :project="$project"
  :prev="$prev"
  :next="$next"
  :title="$project->full_title"
  :description="$project->meta_description"
  :og-image="$project->og_image">

  <x-projects.images :project="$project" />

  <x-slot:footer>
    <div class="md:hidden">
      <x-projects.browser :prev="$prev" :next="$next" />
    </div>
    <x-projects.menu :project="$project" />
  </x-slot>

</x-layout.project>
