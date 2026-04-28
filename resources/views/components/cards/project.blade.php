@props(['project', 'image' => false])

<div>
  @if($project->teaser->count())
    <div class="aspect-[3/2] mb-8">
      <x-media.image
        :media="$project->teaser"
        :alt="$project->title"
        sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
        class="w-full h-full object-cover"
      />
    </div>
  @endif

  <div class="flex flex-col gap-y-5">

    <h2 class="text-[21px] leading-[1.17]">
      {{ $project->title }}@if($project->location), {{ $project->location }}@endif
    </h2>

    <div class="text-[16px] leading-[1.31]">
      {{ $project->subtitle }} {{ $project->year }}
    </div>
    
  </div>

</div>
