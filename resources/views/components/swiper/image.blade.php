@props(['media', 'fit' => 'crop'])
@php
  $first = $media instanceof \App\Models\Media ? $media : $media->first();
  $orientation = $first?->orientation ?? 'unknown';
  $variants = config('swiper.image_slide.orientations');
  $variant = $variants[$orientation] ?? $variants['default'];
  $mode = $variant['mode'] ?? 'grid';
  $objectFit = $fit === 'crop' ? 'object-cover' : 'object-contain';
@endphp
<x-swiper.item class="xl:px-32">
  @if($mode === 'intrinsic' && $first?->width && $first?->height)
    <div class="h-full flex items-center justify-center">
      <div class="h-full max-w-full">
        <x-media.image :media="$media" :fit="$fit" :sizes="$variant['sizes']" class="w-full h-full {{ $objectFit }}" />
      </div>
    </div>
  @else
    <x-grid.container class="h-full">
      <x-grid.span class="{{ $variant['span'] }} h-full">
        <x-media.image :media="$media" :fit="$fit" :sizes="$variant['sizes']" class="w-full h-full {{ $objectFit }}" />
      </x-grid.span>
    </x-grid.container>
  @endif
</x-swiper.item>
