<script setup>
import { PhPlus, PhTrash } from '@phosphor-icons/vue'

const props = defineProps({
	item: { type: Object, default: null },
	position: { type: Number, required: true },
})

const emit = defineEmits(['add', 'remove'])

const hasMedia = () => props.item?.media
</script>

<template>
	<div class="relative group border border-dashed border-neutral-300 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-900 overflow-hidden">
		<!-- Filled slot -->
		<template v-if="hasMedia()">
			<img
				:src="item.media.preview_url"
				:alt="item.media.alt || ''"
				class="block w-full h-full object-cover"
			/>
			<!-- Remove overlay -->
			<div class="absolute inset-0 bg-black/70 opacity-0 group-hover:opacity-100 transition-opacity duration-150 flex items-center justify-center">
				<button
					type="button"
					class="text-white hover:text-white/80 transition-colors cursor-pointer"
					title="Entfernen"
					@click="emit('remove', item)"
				>
					<PhTrash :size="20" weight="light" />
				</button>
			</div>
		</template>

		<!-- Empty slot -->
		<template v-else>
			<button
				type="button"
				class="w-full h-full flex items-center justify-center text-neutral-400 hover:text-neutral-600 dark:hover:text-neutral-300 hover:bg-neutral-100 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
				@click="emit('add', position)"
			>
				<PhPlus :size="20" weight="light" />
			</button>
		</template>
	</div>
</template>
