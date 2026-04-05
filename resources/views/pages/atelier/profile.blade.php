<x-layout.site :description="$seo?->profile_meta_description">
  <div class="grid grid-cols-2 gap-x-6">
    @if($profile->media)
      <div>
        <x-media.image
          :src="'uploads/' . $profile->media->file"
          :alt="$profile->media->alt ?? ''"
          :width="$profile->media->width"
          :height="$profile->media->height"
          :crop="$profile->media->crop"
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
