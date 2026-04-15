<x-layout.site :description="$seo?->team_meta_description">
  @php
    $desktopMedia = $page?->media->where('variant', 'desktop')->first();
    $mobileMedia = $page?->media->where('variant', 'mobile')->first();
  @endphp
  <div class="grid grid-cols-2 gap-x-6">
    @if($desktopMedia)
      <div>
        <x-media.image :media="$desktopMedia" :mobileMedia="$mobileMedia" sizes="(min-width: 768px) 50vw, 100vw" />
      </div>
    @endif
    <div>
      @foreach($members as $member)
        @php echo $member->firstname @endphp<br>
        @php echo $member->name @endphp<br>
        @php echo $member->title @endphp<br>
        @php echo $member->email @endphp<br>
        @php echo $member->cv @endphp<br>
      @endforeach
    </div>
  </div>
</x-layout.site>
