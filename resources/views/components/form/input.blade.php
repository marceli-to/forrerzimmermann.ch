@props(['type' => 'text', 'name', 'id' => null, 'value' => null, 'placeholder' => null])

<input
	type="{{ $type }}"
	name="{{ $name }}"
	id="{{ $id ?? $name }}"
	@if($value) value="{{ $value }}" @endif
	@if($placeholder) placeholder="{{ $placeholder }}" @endif
	{{ $attributes->merge([
		'class' => 'block w-full px-0 py-3 text-base bg-transparent border-0 border-b border-neutral-300 dark:border-neutral-700 text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500 focus:border-neutral-900 dark:focus:border-neutral-400 focus:ring-0 transition-colors duration-200 outline-none'
	]) }}
/>
