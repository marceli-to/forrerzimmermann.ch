@props(['project', 'image' => false])

<div>
  @if($image && $project->teaser->first())
    <div class="aspect-[16/10] overflow-hidden">
      <x-media.image
        :media="$project->teaser->first()"
        :alt="$project->title"
        sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
        class="w-full h-full object-cover"
      />
    </div>
  @endif
  <div @class(['mt-3' => $image && $project->teaser->first()])>
    <p class="font-bold">{{ $project->title }}@if($project->location), {{ $project->location }}@endif</p>
    <p>{{ $project->subtitle }} {{ $project->year }}</p>
  </div>
</div>
