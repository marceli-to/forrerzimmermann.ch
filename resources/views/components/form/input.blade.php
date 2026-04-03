@props(['type' => 'text', 'name', 'id' => null, 'value' => null, 'placeholder' => null])

<input
	type="{{ $type }}"
	name="{{ $name }}"
	id="{{ $id ?? $name }}"
	@if($value) value="{{ $value }}" @endif
	@if($placeholder) placeholder="{{ $placeholder }}" @endif
	{{ $attributes->merge([
		'class' => 'block w-full px-0 py-10 text-sm bg-transparent border-0 border-b border-warm-300 text-warm-900 placeholder:text-warm-300 focus:border-warm-900 focus:ring-0 transition-colors outline-none'
	]) }}
/>
