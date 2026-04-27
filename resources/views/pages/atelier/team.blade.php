<x-layout.site :description="$seo?->team_meta_description">
  <div class="h-full md:px-16 xl:px-32">
    <x-grid.container class="h-full">
      <x-grid.span class="md:col-span-7 xl:col-span-8 md:-ml-16 xl:-ml-32 md:min-h-0">
        @if($page?->media->count())
          <x-media.image :media="$page->media" sizes="(min-width: 768px) 66vw, 100vw" class="aspect-[4/3] md:aspect-auto w-full h-full object-cover" />
        @endif
      </x-grid.span>
      <x-grid.span class="md:col-span-5 xl:col-span-4 px-16 py-16 md:px-0 min-h-0 overflow-auto">
        @foreach($members as $member)
          <div @class(['mb-32' => !$loop->last])>
            <h2 class="text-[23px] leading-[1.18] mb-0">
              {{ $member->firstname }} {{ $member->name }}
            </h2>
            <div class="text-[18px] leading-[1.33]">
              {{ $member->title }}<br>{{ $member->email }}
            </div>
          </div>
        @endforeach
      </x-grid.span>
    </x-grid.container>
  </div>
</x-layout.site>
