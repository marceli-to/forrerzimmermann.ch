<x-layout.site :description="$seo?->contact_meta_description" title="Kontakt">

  <div class="h-full md:px-16 xl:px-32">

    <x-grid.container class="h-full">
      
      <x-grid.span class="md:col-span-7 xl:col-span-8 md:-ml-16 xl:-ml-32 md:min-h-0">
        {{-- @if($profile?->media->count())
          <x-media.image :media="$profile->media" sizes="(min-width: 768px) 66vw, 100vw" class="aspect-[4/3] md:aspect-auto w-full h-full object-cover" />
        @endif --}}
      </x-grid.span>

      <x-grid.span class="md:col-span-5 xl:col-span-4 px-16 md:pl-0 md:pr-16 xl:pr-32 md:-mr-16 xl:-mr-32 min-h-0 overflow-auto">
        @if($contact)
          <article class="hyphens-auto py-18">

            <h1 class="text-[23px] leading-[1.174] mb-16">
              {{ $contact->name }}
            </h1>

            <div class="text-[18px] leading-[1.33]">
              {!! nl2br($contact->address) !!}
              @if($contact->email)
                <br>
                <a 
                  href="mailto:{{ $contact->email }}"
                  class="hover:text-accent transition-colors !no-underline">
                  {{ $contact->email }}
                </a>
              @endif
              @if($contact->phone)
                <br>
                <a 
                  href="tel:{{ $contact->phone }}"
                  class="hover:text-accent transition-colors !no-underline">
                  {{ $contact->phone }}
                </a>
              @endif
            </div>

          </article>
        @endif
      </x-grid.span>
      
    </x-grid.container>
  </div>
  
  <x-slot:footer>
    <a 
      href="{{ $contact->maps_url }}" 
      class="hover:text-accent transition-colors !no-underline"
      target="_blank"
      rel="noopener noreferrer"
      aria-label="Google Maps">
      Google Maps
    </a>
  </x-slot>

</x-layout.site>
