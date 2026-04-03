@props(['href'])

<a
	href="{{ $href }}"
	{{ $attributes->merge(['class' => 'text-sm text-warm-400 hover:text-warm-900 transition-colors underline decoration-warm-300 underline-offset-4 hover:decoration-warm-900']) }}
>
	{{ $slot }}
</a>
