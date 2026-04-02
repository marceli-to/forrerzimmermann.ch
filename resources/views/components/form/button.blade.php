@props(['type' => 'submit', 'variant' => 'primary'])

@php
$base = 'inline-flex items-center justify-center font-medium tracking-wide text-sm transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-neutral-900 dark:focus-visible:ring-neutral-400 dark:focus-visible:ring-offset-neutral-950';
$variants = [
	'primary' => 'px-5 py-2.5 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 hover:bg-neutral-800 dark:hover:bg-neutral-200 active:bg-neutral-950 dark:active:bg-neutral-300',
	'secondary' => 'px-5 py-2.5 bg-transparent text-neutral-900 dark:text-neutral-100 border border-neutral-300 dark:border-neutral-700 hover:border-neutral-900 dark:hover:border-neutral-400 active:bg-neutral-100 dark:active:bg-neutral-800',
];
@endphp

<button
	type="{{ $type }}"
	{{ $attributes->merge(['class' => $base . ' ' . ($variants[$variant] ?? $variants['primary'])]) }}
>
	{{ $slot }}
</button>
