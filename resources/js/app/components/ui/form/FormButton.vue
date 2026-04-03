<script setup>
defineProps({
	type: { type: String, default: 'button' },
	variant: { type: String, default: 'primary' },
})
</script>

<template>
	<button
		:type="type"
		class="inline-flex items-center justify-center rounded-md font-medium text-sm transition-colors cursor-pointer focus:outline-none focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-navy"
		:class="{
			'px-16 py-8 bg-navy text-white hover:bg-navy/90 active:bg-navy/80': variant === 'primary',
			'px-16 py-8 bg-transparent text-warm-900 border border-warm-400 hover:border-warm-900 active:bg-warm-100': variant === 'secondary',
		}"
	>
		<slot />
	</button>
</template>
