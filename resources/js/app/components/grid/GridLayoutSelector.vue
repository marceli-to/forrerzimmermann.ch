<script setup>
import { layoutIcons } from './gridLayoutIcons'

const props = defineProps({
	layouts: { type: Array, default: () => [] },
})

const emit = defineEmits(['add'])
</script>

<template>
	<div class="flex flex-wrap gap-8">
		<button
			v-for="layout in layouts"
			:key="layout.key"
			type="button"
			class="border border-neutral-200 dark:border-neutral-700 p-10 hover:border-neutral-900 dark:hover:border-neutral-400 transition-colors cursor-pointer bg-white dark:bg-neutral-900"
			:title="layout.label"
			@click="emit('add', layout.key)"
		>
			<svg width="30" height="24" viewBox="0 0 30 24">
				<rect
					v-for="(rect, i) in (layoutIcons[layout.key] || [])"
					:key="i"
					:x="rect.x"
					:y="rect.y"
					:width="rect.w"
					:height="rect.h"
					fill="currentColor"
					class="text-neutral-300 dark:text-neutral-600"
				/>
			</svg>
		</button>
	</div>
</template>
