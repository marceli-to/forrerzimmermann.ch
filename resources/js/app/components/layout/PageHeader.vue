<script setup>
defineProps({
	title: { type: String, required: true },
})
</script>

<template>
	<div class="flex items-center justify-between mb-40">
		<h1 class="text-lg font-medium text-neutral-900">{{ title }}</h1>
		<div v-if="$slots.default" class="flex items-center gap-12">
			<slot />
		</div>
	</div>
</template>
