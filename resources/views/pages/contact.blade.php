<x-layout.site :description="$seo?->contact_meta_description">
  <div class="grid grid-cols-2 gap-x-6">
    <div>
      [Map]
    </div>
    <div>
      @if($contact)
        @php echo $contact->name @endphp<br>
        @php echo $contact->address @endphp<br>
        @php echo $contact->email @endphp<br>
        @php echo $contact->phone @endphp<br>
        @php echo $contact->maps_url @endphp<br>
        @php echo $contact->imprint @endphp
      @endif
    </div>
  </div>
</x-layout.site>
