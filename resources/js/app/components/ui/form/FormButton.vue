<script setup>
defineProps({
	type: { type: String, default: 'button' },
	variant: { type: String, default: 'primary' },
})
</script>

<template>
	<button
		:type="type"
		class="inline-flex items-center justify-center font-medium tracking-wide text-sm transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-neutral-900 dark:focus-visible:ring-neutral-400 dark:focus-visible:ring-offset-neutral-950 cursor-pointer"
		:class="{
			'px-20 py-10 bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 border border-neutral-900 dark:border-white hover:bg-neutral-800 dark:hover:bg-neutral-200 hover:border-neutral-800 dark:hover:border-neutral-200 active:bg-neutral-950 dark:active:bg-neutral-300 active:border-neutral-950 dark:active:border-neutral-300': variant === 'primary',
			'px-20 py-10 bg-transparent text-neutral-900 dark:text-neutral-100 border border-neutral-300 dark:border-neutral-700 hover:border-neutral-900 dark:hover:border-neutral-400 active:bg-neutral-100 dark:active:bg-neutral-800': variant === 'secondary',
		}"
	>
		<slot />
	</button>
</template>
