<x-layout.site :description="$seo?->landing_meta_description">
  <div class="flex flex-col gap-y-10">
    @foreach($slides as $slide)
      @if($slide->type === 'image' && $slide->media->first())
        <div>
          <x-media.image
            :media="$slide->media->first()"
            :alt="$slide->media->first()->alt ?? ''"
            sizes="100vw"
          />
        </div>
      @elseif($slide->type === 'image_text')
        <div class="grid grid-cols-2 gap-x-6">
          @if($slide->media->first())
            <div>
              <x-media.image
                :media="$slide->media->first()"
                :alt="$slide->media->first()->alt ?? ''"
                sizes="(min-width: 768px) 50vw, 100vw"
              />
            </div>
          @endif
          @if($slide->text)
            <div>
              {!! $slide->text !!}
            </div>
          @endif
        </div>
      @endif
    @endforeach
  </div>
</x-layout.site>
