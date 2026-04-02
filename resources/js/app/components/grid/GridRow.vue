<script setup>
import { PhTrash } from '@phosphor-icons/vue'
import GridSlot from './GridSlot.vue'

const props = defineProps({
	grid: { type: Object, required: true },
	layoutConfig: { type: Object, default: null },
})

const emit = defineEmits(['delete', 'add-media', 'remove-media'])

function getItemAtPosition(position) {
	return props.grid.items?.find(i => i.position === position) || null
}

function onAdd(position) {
	emit('add-media', { gridId: props.grid.id, position })
}

function onRemove(item) {
	emit('remove-media', { gridId: props.grid.id, item })
}
</script>

<template>
	<div class="border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-16">
		<!-- Delete button -->
		<div class="flex justify-end mb-12">
			<button
				type="button"
				class="text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition-colors cursor-pointer"
				title="Zeile löschen"
				@click="emit('delete', grid)"
			>
				<PhTrash :size="14" weight="light" />
			</button>
		</div>

		<!-- 2fr: 2 equal columns -->
		<div v-if="grid.layout_key === '2fr'" class="grid-layout grid-2fr">
			<GridSlot class="grid-slot" :item="getItemAtPosition(0)" :position="0" @add="onAdd" @remove="onRemove" />
			<GridSlot class="grid-slot" :item="getItemAtPosition(1)" :position="1" @add="onAdd" @remove="onRemove" />
		</div>

		<!-- 1fr_stacked-1fr: left stacked, right full -->
		<div v-else-if="grid.layout_key === '1fr_stacked-1fr'" class="grid-layout grid-1fr_stacked-1fr">
			<div class="grid-stacked">
				<GridSlot class="grid-slot" :item="getItemAtPosition(0)" :position="0" @add="onAdd" @remove="onRemove" />
				<GridSlot class="grid-slot" :item="getItemAtPosition(1)" :position="1" @add="onAdd" @remove="onRemove" />
			</div>
			<GridSlot class="grid-slot" :item="getItemAtPosition(2)" :position="2" @add="onAdd" @remove="onRemove" />
		</div>

		<!-- 1fr-1fr_stacked: left full, right stacked -->
		<div v-else-if="grid.layout_key === '1fr-1fr_stacked'" class="grid-layout grid-1fr-1fr_stacked">
			<GridSlot class="grid-slot" :item="getItemAtPosition(0)" :position="0" @add="onAdd" @remove="onRemove" />
			<div class="grid-stacked">
				<GridSlot class="grid-slot" :item="getItemAtPosition(1)" :position="1" @add="onAdd" @remove="onRemove" />
				<GridSlot class="grid-slot" :item="getItemAtPosition(2)" :position="2" @add="onAdd" @remove="onRemove" />
			</div>
		</div>

		<!-- 1fr_sm_lg-1fr_lg_sm: left small/large, right large/small -->
		<div v-else-if="grid.layout_key === '1fr_sm_lg-1fr_lg_sm'" class="grid-layout grid-1fr_sm_lg-1fr_lg_sm">
			<div class="grid-stacked">
				<GridSlot class="grid-slot" :item="getItemAtPosition(0)" :position="0" @add="onAdd" @remove="onRemove" />
				<GridSlot class="grid-slot" :item="getItemAtPosition(1)" :position="1" @add="onAdd" @remove="onRemove" />
			</div>
			<div class="grid-stacked">
				<GridSlot class="grid-slot" :item="getItemAtPosition(2)" :position="2" @add="onAdd" @remove="onRemove" />
				<GridSlot class="grid-slot" :item="getItemAtPosition(3)" :position="3" @add="onAdd" @remove="onRemove" />
			</div>
		</div>

		<!-- 1fr_lg_sm-1fr_sm_lg: left large/small, right small/large -->
		<div v-else-if="grid.layout_key === '1fr_lg_sm-1fr_sm_lg'" class="grid-layout grid-1fr_lg_sm-1fr_sm_lg">
			<div class="grid-stacked">
				<GridSlot class="grid-slot" :item="getItemAtPosition(0)" :position="0" @add="onAdd" @remove="onRemove" />
				<GridSlot class="grid-slot" :item="getItemAtPosition(1)" :position="1" @add="onAdd" @remove="onRemove" />
			</div>
			<div class="grid-stacked">
				<GridSlot class="grid-slot" :item="getItemAtPosition(2)" :position="2" @add="onAdd" @remove="onRemove" />
				<GridSlot class="grid-slot" :item="getItemAtPosition(3)" :position="3" @add="onAdd" @remove="onRemove" />
			</div>
		</div>

		<!-- 1fr_sm_lg-1fr_lg: left small/large, right large -->
		<div v-else-if="grid.layout_key === '1fr_sm_lg-1fr_lg'" class="grid-layout grid-1fr_sm_lg-1fr_lg">
			<div class="grid-stacked">
				<GridSlot class="grid-slot" :item="getItemAtPosition(0)" :position="0" @add="onAdd" @remove="onRemove" />
				<GridSlot class="grid-slot" :item="getItemAtPosition(1)" :position="1" @add="onAdd" @remove="onRemove" />
			</div>
			<GridSlot class="grid-slot" :item="getItemAtPosition(2)" :position="2" @add="onAdd" @remove="onRemove" />
		</div>

		<!-- 1fr_lg-1fr_sm_lg: left large, right small/large -->
		<div v-else-if="grid.layout_key === '1fr_lg-1fr_sm_lg'" class="grid-layout grid-1fr_lg-1fr_sm_lg">
			<GridSlot class="grid-slot" :item="getItemAtPosition(0)" :position="0" @add="onAdd" @remove="onRemove" />
			<div class="grid-stacked">
				<GridSlot class="grid-slot" :item="getItemAtPosition(1)" :position="1" @add="onAdd" @remove="onRemove" />
				<GridSlot class="grid-slot" :item="getItemAtPosition(2)" :position="2" @add="onAdd" @remove="onRemove" />
			</div>
		</div>
	</div>
</template>
