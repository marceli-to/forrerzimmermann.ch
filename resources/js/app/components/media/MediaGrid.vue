<script setup>
import { computed } from 'vue'
import draggable from 'vuedraggable'
import MediaCard from '@/components/media/MediaCard.vue'

const props = defineProps({
	items: { type: Array, default: () => [] },
})

const emit = defineEmits(['edit', 'delete', 'reorder', 'teaser'])

const dragItems = computed({
	get: () => props.items,
	set: (value) => emit('reorder', value),
})
</script>

<template>
	<draggable
		v-model="dragItems"
		item-key="uuid"
		class="grid grid-cols-3 sm:grid-cols-4 lg:grid-cols-6 gap-8"
		ghost-class="opacity-30"
		animation="150"
	>
		<template #item="{ element }">
			<div class="cursor-grab active:cursor-grabbing">
				<MediaCard
					:media="element"
					:showInfo="true"
					:badge="element.is_teaser ? 'Teaser' : null"
					:showTeaser="true"
					:isTeaser="element.is_teaser"
					@edit="emit('edit', $event)"
					@teaser="emit('teaser', $event)"
					@delete="emit('delete', $event)"
				/>
			</div>
		</template>
	</draggable>
</template>
