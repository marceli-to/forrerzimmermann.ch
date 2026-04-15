<picture>
	@foreach($sources as $source)
		<source
			srcset="{{ $source['srcset'] }}"
			type="{{ $source['type'] }}"
			sizes="{{ $source['sizes'] }}"
		>
	@endforeach

	<img
		src="{{ $fallbackUrl }}"
		alt="{{ $alt }}"
		width="{{ $width }}"
		height="{{ $height }}"
		@if($class) class="{{ $class }}" @endif
		loading="{{ $loading }}"
		{{ $attributes }}
	>
</picture>