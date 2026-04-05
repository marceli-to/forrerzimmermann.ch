<x-layout.site :description="$seo?->werkliste_meta_description">
  <div class="grid grid-cols-4 gap-x-6 gap-y-10">
    @foreach($projects as $project)
      <x-cards.project :project="$project" />
    @endforeach
  </div>
</x-layout.site>
