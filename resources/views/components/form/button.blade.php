@props(['type' => 'submit', 'variant' => 'primary'])

@php
$base = 'inline-flex items-center justify-center rounded-md font-medium text-sm transition-colors cursor-pointer focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy';
$variants = [
	'primary' => 'px-16 py-8 bg-navy text-white hover:bg-navy/90 active:bg-navy/80',
	'secondary' => 'px-16 py-8 bg-transparent text-warm-900 border border-warm-400 hover:border-warm-900 active:bg-warm-100',
];
@endphp

<button
	type="{{ $type }}"
	{{ $attributes->merge(['class' => $base . ' ' . ($variants[$variant] ?? $variants['primary'])]) }}
>
	{{ $slot }}
</button>
