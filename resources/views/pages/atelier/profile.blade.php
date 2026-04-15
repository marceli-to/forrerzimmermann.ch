<x-layout.site :description="$seo?->profile_meta_description">
  <div class="grid grid-cols-2 gap-x-6">
    @if($profile->desktopMedia->first())
      <div>
        <x-media.image
          :media="$profile->desktopMedia->first()"
          :mobileMedia="$profile->mobileMedia->first()"
          sizes="(min-width: 768px) 50vw, 100vw"
        />
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
