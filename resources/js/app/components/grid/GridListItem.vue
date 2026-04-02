<script setup>
import { PhTrash, PhDotsSixVertical } from '@phosphor-icons/vue'
import { layoutIcons } from './gridLayoutIcons'

defineProps({
	grid: { type: Object, required: true },
})

const emit = defineEmits(['delete'])
</script>

<template>
	<div class="flex items-center gap-12 border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 px-8 py-12 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors">
		<!-- Drag handle -->
		<div class="grid-drag-handle cursor-grab active:cursor-grabbing text-neutral-900 dark:text-neutral-100 shrink-0">
			<PhDotsSixVertical :size="20" weight="light" />
		</div>

		<!-- Layout icon -->
		<svg width="30" height="24" viewBox="0 0 30 24" class="shrink-0">
			<rect
				v-for="(rect, i) in (layoutIcons[grid.layout_key] || [])"
				:key="i"
				:x="rect.x"
				:y="rect.y"
				:width="rect.w"
				:height="rect.h"
				fill="currentColor"
				class="text-neutral-300 dark:text-neutral-600"
			/>
		</svg>

		<!-- Spacer -->
		<div class="flex-1" />

		<!-- Delete button -->
		<button
			type="button"
			class="text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition-colors cursor-pointer shrink-0"
			title="Zeile löschen"
			@click="emit('delete', grid)"
		>
			<PhTrash :size="14" weight="light" />
		</button>
	</div>
</template>
