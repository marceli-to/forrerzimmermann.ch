@props(['media'])
@php
  $orientation = $media->first()?->orientation ?? 'unknown';
  $variants = config('swiper.image_slide.orientations');
  $variant = $variants[$orientation] ?? $variants['default'];
@endphp
<x-swiper.item class="xl:px-32">
  <x-grid.container class="h-full">
    <x-grid.span class="{{ $variant['span'] }} h-full">
      <x-media.image :media="$media" :sizes="$variant['sizes']" class="w-full h-full object-cover" />
    </x-grid.span>
  </x-grid.container>
</x-swiper.item>
