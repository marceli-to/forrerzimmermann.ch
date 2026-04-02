@props(['for' => null])

<label
	@if($for) for="{{ $for }}" @endif
	{{ $attributes->merge(['class' => 'block text-xs font-medium tracking-wide uppercase text-neutral-500 dark:text-neutral-400']) }}
>
	{{ $slot }}
</label>
