<x-layout.site :description="$seo?->jobs_meta_description">
  <div class="grid grid-cols-2 gap-x-6">
    @if($page?->media)
      <div>
        <x-media.image :media="$page->media" sizes="(min-width: 768px) 50vw, 100vw" />
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
