<x-layout.site :description="$seo?->team_meta_description">
  <div class="grid grid-cols-2 gap-x-6">
    @if($page?->desktopMedia->first())
      <div>
        <x-media.image
          :media="$page->desktopMedia->first()"
          :mobileMedia="$page->mobileMedia->first()"
          sizes="(min-width: 768px) 50vw, 100vw"
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
