@props(['project', 'image' => false])

@php
  $desktopTeaser = $project->teaser->where('variant', 'desktop')->first();
  $mobileTeaser = $project->teaser->where('variant', 'mobile')->first();
@endphp

<div>
  @if($image && $desktopTeaser)
    <div class="aspect-[16/10] overflow-hidden">
      <x-media.image
        :media="$desktopTeaser"
        :mobileMedia="$mobileTeaser"
        :alt="$project->title"
        sizes="(min-width: 1024px) 25vw, (min-width: 640px) 50vw, 100vw"
        class="w-full h-full object-cover"
      />
    </div>
  @endif
  <div @class(['mt-3' => $image && $desktopTeaser])>
    <p class="font-bold">{{ $project->title }}@if($project->location), {{ $project->location }}@endif</p>
    <p>{{ $project->subtitle }} {{ $project->year }}</p>
  </div>
</div>
