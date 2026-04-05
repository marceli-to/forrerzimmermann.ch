<x-layout.site :description="$seo?->team_meta_description">
  <div class="grid grid-cols-2 gap-x-6">
    @if($page?->media)
      <div>
        <x-media.image
          :src="'uploads/' . $page->media->file"
          :alt="$page->media->alt ?? ''"
          :width="$page->media->width"
          :height="$page->media->height"
          :crop="$page->media->crop"
        />
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
