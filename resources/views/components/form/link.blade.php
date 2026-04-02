@props(['href'])

<a
	href="{{ $href }}"
	{{ $attributes->merge(['class' => 'text-sm text-neutral-500 dark:text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors duration-200 underline decoration-neutral-300 dark:decoration-neutral-600 underline-offset-4 hover:decoration-neutral-900 dark:hover:decoration-neutral-400']) }}
>
	{{ $slot }}
</a>
