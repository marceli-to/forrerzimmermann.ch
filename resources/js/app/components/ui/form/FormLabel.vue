<script setup>
defineProps({
	for: { type: String, default: null },
})
</script>

<template>
	<label
		:for="$props.for"
		class="block text-xs text-gray-500 dark:text-warm-400 mb-8"
	>
		<slot />
	</label>
</template>
