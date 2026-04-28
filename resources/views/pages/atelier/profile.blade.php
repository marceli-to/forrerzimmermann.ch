<x-layout.site :description="$seo?->profile_meta_description" title="Profil">
  <div class="h-full md:px-16 xl:px-32">
    <x-grid.container class="h-full">
      
      <x-grid.span class="md:col-span-7 xl:col-span-8 md:-ml-16 xl:-ml-32 md:min-h-0">
        @if($profile?->media->count())
          <x-media.image :media="$profile->media" sizes="(min-width: 768px) 66vw, 100vw" class="aspect-[4/3] md:aspect-auto w-full h-full object-cover" />
        @endif
      </x-grid.span>

      <x-grid.span class="md:col-span-5 xl:col-span-4 px-16 md:pl-0 md:pr-16 xl:pr-32 md:-mr-16 xl:-mr-32 min-h-0 overflow-auto">
        @if($profile)
          <article class="hyphens-auto py-18">
            <h1 class="text-[23px] leading-[1.17] mb-16">
              {!! $profile->title !!}
            </h1>
            <div class="text-[18px] leading-[1.33]">
              {!! $profile->text !!}
            </div>
          </article>
        @endif
      </x-grid.span>
      
    </x-grid.container>
  </div>
  
  <x-slot:footer>
    <x-menu.pages.atelier.container />
  </x-slot>
  
</x-layout.site>
