<x-layout.project
  :project="$project"
  :prev="$prev"
  :next="$next"
  :title="$project->full_title"
  :description="$project->meta_description"
  :og-image="$project->og_image">

  <x-slot:footer>
    <x-projects.menu />
  </x-slot>

</x-layout.project>
