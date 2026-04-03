@props(['href'])

<a
	href="{{ $href }}"
	{{ $attributes->merge(['class' => 'text-sm text-gray-400 hover:text-gray-900 transition-colors underline decoration-gray-300 underline-offset-4 hover:decoration-gray-900']) }}
>
	{{ $slot }}
</a>
