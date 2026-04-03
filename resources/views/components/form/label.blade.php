@props(['for' => null])

<label
	@if($for) for="{{ $for }}" @endif
	{{ $attributes->merge(['class' => 'block text-xxs font-medium text-warm-400 mb-4']) }}
>
	{{ $slot }}
</label>
