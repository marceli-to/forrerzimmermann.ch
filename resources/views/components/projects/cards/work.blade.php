@props(['project'])

@if ($project->feature)
  <a 
    href="{{ route('page.projects.show', $project->slug) }}"
    class="hover:text-accent transition-colors group w-full block">

    <div>

      <h2 class="text-[21px] leading-[1.174]">
        {{ $project->title }}@if($project->location), {{ $project->location }}@endif
      </h2>

      <div class="text-[16px] leading-[1.31]">
        {{ $project->subtitle }} {{ $project->year }}
      </div>
      
    </div>

  </a>
@else 
  <div>
    <h2 class="text-[21px] leading-[1.174]">
      {{ $project->title }}@if($project->location), {{ $project->location }}@endif
    </h2>

    <div class="text-[16px] leading-[1.31]">
      {{ $project->subtitle }} {{ $project->year }}
    </div>
  </div>
@endif

