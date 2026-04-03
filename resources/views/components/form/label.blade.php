@props(['for' => null])

<label
	@if($for) for="{{ $for }}" @endif
	{{ $attributes->merge(['class' => 'block text-xs text-gray-500 mb-6']) }}
>
	{{ $slot }}
</label>
