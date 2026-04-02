<script setup>
import Drawer from '@/components/ui/drawer/Drawer.vue'
import MediaCard from '@/components/media/MediaCard.vue'

const props = defineProps({
	media: { type: Array, default: () => [] },
	visible: { type: Boolean, default: false },
})

const emit = defineEmits(['select', 'close'])

function selectMedia(media) {
	emit('select', media)
	emit('close')
}
</script>

<template>
	<Drawer :open="visible" title="Bild auswählen" size="md" @close="emit('close')">
		<div class="p-24">
			<div v-if="media.length" class="grid grid-cols-3 gap-8">
				<MediaCard
					v-for="item in media"
					:key="item.id"
					:media="item"
					:showInfo="false"
					:showOverlay="false"
					:realFormat="true"
					class="cursor-pointer"
					@click="selectMedia(item)"
				/>
			</div>
			<p v-else class="text-sm text-neutral-400">
				Keine Bilder vorhanden. Laden Sie zuerst Bilder im «Bilder»-Tab hoch.
			</p>
		</div>
	</Drawer>
</template>
