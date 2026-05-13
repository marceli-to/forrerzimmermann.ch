<x-layout.site :description="$seo?->landing_meta_description">
  <div data-slides="landing" class="h-[calc(100dvh_-_var(--header-height-md)_-_var(--footer-height))] xl:h-[calc(100dvh_-_var(--header-height-lg)_-_var(--footer-height))]">
    <x-swiper.container class="h-full">
      @foreach($slides as $slide)
        @if($slide->type === 'image' && $slide->media->count())
          <x-swiper.image :media="$slide->media" />
        @elseif($slide->type === 'image_text')
          <x-swiper.item class="md:px-16 xl:px-32">
            <div class="h-full overflow-y-auto flex flex-col md:overflow-visible md:grid md:grid-cols-12 md:gap-x-30">
              @if($slide->media->count())
                <div class="shrink-0 md:col-span-8 md:-ml-16 xl:-ml-32 md:h-full">
                  <x-media.image :media="$slide->media" sizes="(min-width: 768px) 50vw, 100vw" class="w-full h-auto md:aspect-auto md:h-full md:object-cover" />
                </div>
              @endif
              @if($slide->text)
                <div class="shrink-0 px-16 md:col-span-4 md:px-0">
                  <article class="hyphens-auto text-[23px] leading-[1.174] py-18">
                    {!! $slide->text !!}
                  </article>
                </div>
              @endif
            </div>
          </x-swiper.item>
        @endif
      @endforeach
    </x-swiper.container>
  </div>

  <x-slot:footer>
    <x-swiper.navigation />
  </x-slot>
</x-layout.site>
