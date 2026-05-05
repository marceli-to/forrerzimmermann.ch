@props(['media', 'fit' => 'crop', 'preset' => 'image_slide', 'showCaption' => false])
@php
  $first = $media instanceof \App\Models\Media ? $media : $media->first();
  $orientation = $first?->orientation ?? 'unknown';
  $variants = config('swiper.' . $preset . '.orientations');
  $variant = $variants[$orientation] ?? $variants['default'];
  $mode = $variant['mode'] ?? 'grid';
  $objectFit = $fit === 'crop' ? 'object-cover' : 'object-contain';
  $caption = $showCaption ? $first?->caption : null;
@endphp
<x-swiper.item class="xl:px-32">
  @if($mode === 'intrinsic' && $first?->width && $first?->height)
    <div class="h-full flex flex-col">
      <div class="flex-1 min-h-0 flex items-center justify-center">
        <div class="h-full max-w-full">
          <x-media.image :media="$media" :fit="$fit" :sizes="$variant['sizes']" class="w-full h-full {{ $objectFit }}" />
        </div>
      </div>
      @if($caption)
        <div class="py-20 px-16 text-[1rem] text-center max-w-xl mx-auto">{{ $caption }}</div>
      @endif
    </div>
  @else
    <div class="h-full flex flex-col">
      <div class="flex-1 min-h-0 flex items-center justify-center">
        <div class="h-full max-w-full">
          <x-media.image :media="$media" :fit="$fit" :sizes="$variant['sizes']" class="w-full h-full {{ $objectFit }}" />
        </div>
      </div>
      @if($caption)
        <div class="py-20 px-16 text-[1rem] text-center max-w-xl mx-auto">{{ $caption }}</div>
      @endif
    </div>
    {{-- <x-grid.container class="h-full">
      <x-grid.span class="{{ $variant['span'] }} h-full flex flex-col">
        <div class="flex-1 min-h-0 relative">
          <x-media.image :media="$media" :fit="$fit" :sizes="$variant['sizes']" class="absolute inset-0 w-full h-full object-cover" />
        </div>
        @if($caption)
          <div class="py-20 px-16 text-[1rem] text-center">{{ $caption }}</div>
        @endif
      </x-grid.span>
    </x-grid.container> --}}
  @endif
</x-swiper.item>
