@props([
  'href', 
  'label', 
  'active' => false
])

<li>
  <a 
    href="{{ $href }}" 
    aria-label="{{ $label }}"
    class="text-[18px] leading-none hover:text-accent transition-colors {{ $active ? ' text-accent' : '' }}">
    {{ $label }}
  </a>
</li>