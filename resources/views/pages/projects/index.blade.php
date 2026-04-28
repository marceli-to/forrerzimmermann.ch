<x-layout.site :description="$seo?->projects_meta_description" title="Projekte">

  <div class="h-full md:px-16 xl:px-32 overflow-auto">

    <x-grid.container class="min-h-full md:pb-22 md:gap-y-20">
      @foreach($projects as $project)
        <x-grid.span class="md:col-span-4 xl:col-span-3">
          <x-cards.project :project="$project" :image="true" />
        </x-grid.span>
      @endforeach
    </x-grid.container>
    
  </div>
  
  <x-slot:footer>
    <x-menu.pages.projects.container />
  </x-slot>

</x-layout.site>
