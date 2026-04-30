<x-layout.site :description="$seo?->contact_meta_description" title="Kontakt">

  <div class="h-full md:px-16 xl:px-32">

    <x-grid.container class="h-full">
      
      <x-grid.span class="md:col-span-7 xl:col-span-8 md:-ml-16 xl:-ml-32 md:min-h-0">
        <a
          href="{{ $contact?->maps_url }}"
          target="_blank"
          rel="noopener noreferrer"
          aria-label="Anfahrtsplan auf Google Maps öffnen"
          class="block w-full h-full">
          <picture>
            <source srcset="{{ asset('img/forrerzimmermann-anfahrtsplan.avif') }}" type="image/avif">
            <source srcset="{{ asset('img/forrerzimmermann-anfahrtsplan.webp') }}" type="image/webp">
            <img
              src="{{ asset('img/forrerzimmermann-anfahrtsplan.jpg') }}"
              alt="Anfahrtsplan Forrer Zimmermann"
              width="891"
              height="587"
              loading="eager"
              class="aspect-[4/3] md:aspect-auto w-full h-full object-cover">
          </picture>
        </a>
      </x-grid.span>

      <x-grid.span class="md:col-span-5 xl:col-span-4 px-16 md:pl-0 md:pr-16 xl:pr-32 md:-mr-16 xl:-mr-32 min-h-0 overflow-auto">

        @if($contact)
          <article class="hyphens-auto py-18">

            <h1 class="text-[23px] leading-[1.174] mb-16">
              {{ $contact->name }}
            </h1>

            <div class="flex flex-col gap-y-48 text-[18px] leading-[1.33]">
              <div>
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
              @if ($contact->imprint)
                <div x-data="{ open: false }">
                  <x-buttons.toggle label="Impressum" />
                  <div
                    x-cloak
                    x-show="open"
                    class="mt-18">
                    {!! $contact->imprint !!}
                  </div>
                </div>
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
      class="text-[18px] leading-[1.33] hover:text-accent transition-colors !no-underline"
      target="_blank"
      rel="noopener noreferrer"
      aria-label="Google Maps">
      Google Maps
    </a>
  </x-slot>

</x-layout.site>
