<x-layout.site :description="$seo?->jobs_meta_description">
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
      @foreach($jobs as $job)
        @php echo $job->title @endphp<br>
        @php echo $job->text @endphp<br>
      @endforeach
    </div>
  </div>
</x-layout.site>
