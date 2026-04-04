<script setup>
import { computed } from 'vue'
import draggable from 'vuedraggable'
import MediaCard from '@/components/media/MediaCard.vue'

const props = defineProps({
	items: { type: Array, default: () => [] },
	hasOg: { type: Boolean, default: false },
	hasTeaser: { type: Boolean, default: false },
})

const emit = defineEmits(['edit', 'delete', 'reorder', 'teaser', 'og'])

const dragItems = computed({
	get: () => props.items,
	set: (value) => emit('reorder', value),
})
</script>

<template>
	<draggable
		v-model="dragItems"
		item-key="uuid"
		class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-20"
		ghost-class="opacity-30"
		animation="150"
	>
		<template #item="{ element }">
			<div class="cursor-grab active:cursor-grabbing">
				<MediaCard
					:media="element"
					:showInfo="true"
					:badge="element.is_teaser ? 'Teaser' : null"
					:hasTeaser="hasTeaser"
					:isTeaser="element.is_teaser"
					:hasOg="hasOg"
					:isOg="element.is_og"
					@edit="emit('edit', $event)"
					@teaser="emit('teaser', $event)"
					@og="emit('og', $event)"
					@delete="emit('delete', $event)"
				/>
			</div>
		</template>
	</draggable>
</template>
