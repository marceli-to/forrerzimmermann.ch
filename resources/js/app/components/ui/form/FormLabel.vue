<script setup>
defineProps({
	for: { type: String, default: null },
})
</script>

<template>
	<label
		:for="$props.for"
		class="block text-xs text-gray-500 mb-6"
	>
		<slot />
	</label>
</template>
