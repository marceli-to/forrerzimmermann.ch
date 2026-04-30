<x-layout.site :description="$seo?->landing_meta_description">
  <div data-slides="landing" class="h-full">
    <x-swiper.container class="h-full">
      @foreach($slides as $slide)
        @if($slide->type === 'image' && $slide->media->count())
          <x-swiper.image :media="$slide->media" />
        @elseif($slide->type === 'image_text')
          <x-swiper.item class="md:px-16 xl:px-32">
            <x-grid.container class="h-full">
              @if($slide->media->count())
                <x-grid.span class="md:col-span-8 md:-ml-16 xl:-ml-32 md:h-full">
                  <x-media.image :media="$slide->media" sizes="(min-width: 768px) 50vw, 100vw" class="aspect-[4/3] md:aspect-auto w-full h-full object-cover" />
                </x-grid.span>
              @endif
              @if($slide->text)
                <x-grid.span class="md:col-span-4 px-16 md:px-0">
                  <article class="hyphens-auto text-[23px] leading-[1.174] py-18">
                    {!! $slide->text !!}
                  </article>
                </x-grid.span>
              @endif
            </x-grid.container>
          </x-swiper.item>
        @endif
      @endforeach
    </x-swiper.container>
  </div>

  <x-slot:footer>
    <x-swiper.navigation />
  </x-slot>
</x-layout.site>
