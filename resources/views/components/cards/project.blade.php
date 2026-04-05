@props(['project', 'image' => false])

<div>
  @if($image && $project->teaser->first())
    <div class="aspect-[16/10] overflow-hidden">
      <x-media.image
        :src="'uploads/' . $project->teaser->first()->file"
        :alt="$project->teaser->first()->alt ?? $project->title"
        :width="$project->teaser->first()->width"
        :height="$project->teaser->first()->height"
        :crop="$project->teaser->first()->crop"
        class="w-full h-full object-cover"
      />
    </div>
  @endif
  <div @class(['mt-3' => $image && $project->teaser->first()])>
    <p class="font-bold">{{ $project->title }}@if($project->location), {{ $project->location }}@endif</p>
    <p>{{ $project->subtitle }} {{ $project->year }}</p>
  </div>
</div>
