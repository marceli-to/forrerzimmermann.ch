<x-layout.site :description="$seo?->profile_meta_description">
  @php
    $desktopMedia = $profile->media->where('variant', 'desktop')->first();
    $mobileMedia = $profile->media->where('variant', 'mobile')->first();
  @endphp
  <div class="grid grid-cols-2 gap-x-6">
    @if($desktopMedia)
      <div>
        <x-media.image :media="$desktopMedia" :mobileMedia="$mobileMedia" sizes="(min-width: 768px) 50vw, 100vw" />
      </div>
    @endif
    @if($profile)
      <div>
        @php echo $profile->title @endphp<br>
        @php echo $profile->text @endphp
      </div>
    @endif
  </div>
</x-layout.site>
