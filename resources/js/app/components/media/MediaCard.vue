<script setup>
import { PhTrash, PhPencil, PhStar } from '@phosphor-icons/vue'

defineProps({
	media: { type: Object, required: true },
	realFormat: { type: Boolean, default: false },
	showInfo: { type: Boolean, default: true },
	badge: { type: String, default: null },
	showTeaser: { type: Boolean, default: false },
	isTeaser: { type: Boolean, default: false },
	showOverlay: { type: Boolean, default: true },
	deletable: { type: Boolean, default: true },
})

const emit = defineEmits(['edit', 'delete', 'teaser', 'click'])

function formatSize(bytes) {
	if (!bytes) return '–'
	if (bytes < 1024) return bytes + ' B'
	if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
	return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
}
</script>

<template>
	<div
		class="group relative bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 overflow-hidden hover:border-neutral-900 dark:hover:border-neutral-500 transition-colors"
		@click="emit('click', media)"
	>
		<!-- Image -->
		<div class="aspect-square overflow-hidden flex items-center justify-center" :class="realFormat ? 'p-16' : ''">
			<video
				v-if="media.type === 'video'"
				:src="media.original_url"
				class="block max-w-full max-h-full object-contain"
				muted
				preload="metadata"
			/>
			<img
				v-else
				:src="realFormat ? media.preview_url : media.thumbnail_url"
				:alt="media.alt || media.original_name"
				:class="realFormat ? 'max-w-full max-h-full object-contain' : 'w-full h-full object-cover'"
			/>
		</div>

		<!-- Info -->
		<div v-if="showInfo" class="px-10 py-8 border-t border-neutral-100 dark:border-neutral-800">
			<div class="text-xs text-neutral-900 dark:text-neutral-100 truncate">{{ media.original_name }}</div>
			<div class="text-xxs text-neutral-400 dark:text-neutral-500 mt-2">
				<template v-if="media.type !== 'video'">{{ media.width }}&times;{{ media.height }} · </template>{{ formatSize(media.size) }}
			</div>
		</div>

		<!-- Badge -->
		<div
			v-if="badge"
			class="absolute top-0 left-0 bg-neutral-900 dark:bg-neutral-100 text-white dark:text-neutral-900 text-[9px] font-medium tracking-wide uppercase px-6 py-3 leading-none"
		>
			{{ badge }}
		</div>

		<!-- Overlay actions -->
		<div
			v-if="showOverlay"
			class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity duration-150 flex items-center justify-center gap-16"
		>
			<button
				type="button"
				class="text-white/70 hover:text-white transition-colors cursor-pointer"
				title="Bearbeiten"
				@click.stop="emit('edit', media)"
			>
				<PhPencil :size="18" weight="light" />
			</button>
			<button
				v-if="showTeaser"
				type="button"
				class="text-white/70 hover:text-white transition-colors cursor-pointer"
				title="Als Teaser setzen"
				@click.stop="emit('teaser', media)"
			>
				<PhStar :size="18" :weight="isTeaser ? 'fill' : 'light'" />
			</button>
			<button
				v-if="deletable"
				type="button"
				class="text-white/70 hover:text-white transition-colors cursor-pointer"
				title="Löschen"
				@click.stop="emit('delete', media)"
			>
				<PhTrash :size="18" weight="light" />
			</button>
		</div>
	</div>
</template>
