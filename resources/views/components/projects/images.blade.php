@php
  $images = $project->media->filter(fn ($m) => $m->isImage() && $m->variant !== 'mobile');
@endphp

@if($images->count())

  <div
    data-slides="project-{{ $project->id }}"
    class="relative h-full hidden md:block">
    <x-swiper.container class="h-full">
      @foreach($images as $image)
        <x-swiper.image :media="$image" fit="max" />
      @endforeach
    </x-swiper.container>
    <x-swiper.navigation :id="'project-' . $project->id" layout="sides" />
  </div>

  <div class="md:hidden flex flex-col gap-y-18">
    @foreach($images as $image)
      <x-media.image :media="$image" fit="max" sizes="100vw" class="w-full h-auto" />
    @endforeach
  </div>

@endif