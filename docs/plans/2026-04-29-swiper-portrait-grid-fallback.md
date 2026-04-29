# Swiper portrait layout — grid fallback

The current `<x-swiper.image>` component sizes portrait slides intrinsically (height-driven, width follows the image's natural aspect ratio). If the design feedback prefers fixed column widths instead, this is how to roll back to the grid-based variant.

## Files to change

- `config/swiper.php`
- `resources/views/components/swiper/image.blade.php`

## `config/swiper.php`

Replace the `portrait` entry's `mode` and bring back a `span`. Pick the col-span that matched the design call (the last grid version used 6/12 centered):

```php
'portrait' => [
    'span' => 'md:col-span-6 md:col-start-4 xl:col-span-6 xl:col-start-4',
    'sizes' => '(min-width: 1280px) 50vw, (min-width: 768px) 50vw, 100vw',
],
```

Earlier iteration used `xl:col-span-4 xl:col-start-5` (4/12 centered) — switch to whichever the designers settle on.

## `resources/views/components/swiper/image.blade.php`

Drop the mode branch entirely so both orientations go through the grid path:

```blade
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
```

## After changing

```sh
php artisan config:clear
```

## Reference commits

- `338b555` — first grid version (`xl:col-span-4 xl:col-start-5`, 4/12 centered)
- The interim 6/12 variant was edited into `config/swiper.php` between `338b555` and the intrinsic switch — see git history of that file
