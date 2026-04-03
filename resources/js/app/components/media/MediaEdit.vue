<script setup>
import { ref, watch } from 'vue'
import Drawer from '@/components/ui/drawer/Drawer.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'
import FormTextarea from '@/components/ui/form/FormTextarea.vue'

const props = defineProps({
	media: { type: Object, default: null },
})

const emit = defineEmits(['close', 'save'])

const isOpen = ref(false)

const form = ref({
	alt: '',
	caption: '',
})

watch(() => props.media, (val) => {
	if (val) {
		form.value.alt = val.alt || ''
		form.value.caption = val.caption || ''
		isOpen.value = true
	} else {
		isOpen.value = false
	}
}, { immediate: true })

function close() {
	isOpen.value = false
	emit('close')
}

function handleSave() {
	emit('save', {
		uuid: props.media.uuid,
		data: { ...form.value },
	})
	isOpen.value = false
}
</script>

<template>
	<Drawer
		:open="isOpen"
		:title="media?.type === 'video' ? 'Video bearbeiten' : 'Bild bearbeiten'"
		@close="close"
	>
		<!-- Preview -->
		<div class="bg-neutral-50 border-b border-neutral-200">
			<video
				v-if="media?.type === 'video'"
				:src="media.original_url"
				class="w-full max-h-[320px] object-contain"
				controls
				muted
				preload="metadata"
			/>
			<img
				v-else-if="media"
				:src="media.preview_url"
				:alt="media.alt || ''"
				class="w-full max-h-[320px] object-contain"
			/>
		</div>

		<!-- File info -->
		<div v-if="media" class="px-24 py-16 border-b border-neutral-100 text-xs text-gray-400 space-y-2">
			<div class="text-gray-900 font-medium">{{ media.original_name }}</div>
			<div>
				<template v-if="media.width && media.height">{{ media.width }} &times; {{ media.height }} px · </template>
				{{ media.mime_type }}
			</div>
		</div>

		<!-- Fields -->
		<div class="px-24 py-20 flex flex-col gap-24">
			<FormGroup>
				<FormLabel for="alt">Alt-Text</FormLabel>
				<FormInput id="alt" v-model="form.alt" placeholder="Bildbeschreibung für Screenreader..." />
			</FormGroup>

			<FormGroup>
				<FormLabel for="caption">Bildunterschrift</FormLabel>
				<FormTextarea id="caption" v-model="form.caption" placeholder="Optionale Bildunterschrift..." />
			</FormGroup>
		</div>

		<!-- Footer -->
		<template #footer>
			<div class="flex gap-12">
				<button
					type="button"
					class="text-sm inline-flex items-center justify-center rounded-md px-16 py-8 bg-gray-900 text-white hover:bg-gray-800 transition-colors cursor-pointer"
					@click="handleSave"
				>
					Speichern
				</button>
				<button
					type="button"
					class="text-sm inline-flex items-center justify-center rounded-md px-16 py-8 bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors cursor-pointer"
					@click="close"
				>
					Abbrechen
				</button>
			</div>
		</template>
	</Drawer>
</template>
