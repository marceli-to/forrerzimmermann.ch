@props(['project', 'image' => false])

<a 
  href="{{ route('page.projects.show', $project->slug) }}"
  class="hover:text-accent transition-colors group w-full block">

  @if($project->teaser->count())
    <div class="aspect-[3/2] mb-10">
      <x-media.image
        :media="$project->teaser"
        :alt="$project->title"
        loading="lazy"
        sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
        class="w-full h-full object-cover group-hover:brightness-90 transition-all duration-300" />
    </div>
  @endif

  <div>

    <h2 class="text-[21px] leading-[1.17]">
      {{ $project->title }}@if($project->location), {{ $project->location }}@endif
    </h2>

    <div class="text-[16px] leading-[1.31]">
      {{ $project->subtitle }} {{ $project->year }}
    </div>
    
  </div>

</a>
