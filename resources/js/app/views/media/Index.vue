<script setup>
import { ref, onMounted, computed } from 'vue'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import mediaApi from '@/api/media'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import { PhMagnifyingGlass } from '@phosphor-icons/vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import MediaCard from '@/components/media/MediaCard.vue'

const toast = useToast()
const { confirm } = useConfirm()

const items = ref([])
const loading = ref(true)
const editingMedia = ref(null)
const search = ref('')

const filteredItems = computed(() => {
	if (!search.value.trim()) return items.value
	const q = search.value.toLowerCase()
	return items.value.filter(item =>
		(item.original_name || '').toLowerCase().includes(q) ||
		(item.alt || '').toLowerCase().includes(q) ||
		(item.caption || '').toLowerCase().includes(q)
	)
})

onMounted(async () => {
	await fetchMedia()
})

async function fetchMedia() {
	loading.value = true
	try {
		const { data } = await mediaApi.index()
		items.value = data.data
	} finally {
		loading.value = false
	}
}

function onUploaded(mediaData) {
	items.value.unshift(mediaData)
	toast.success('Bild hochgeladen')
}

function openEdit(media) {
	editingMedia.value = media
}

async function handleSave({ uuid, data }) {
	try {
		const { data: response } = await mediaApi.update(uuid, data)
		const index = items.value.findIndex(i => i.uuid === uuid)
		if (index !== -1) {
			items.value[index] = response.data
		}
		editingMedia.value = null
		toast.success('Gespeichert')
	} catch {
		toast.error('Fehler beim Speichern')
	}
}

async function handleDelete(media) {
	const ok = await confirm({
		title: 'Bild löschen',
		message: `"${media.original_name}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	try {
		await mediaApi.destroy(media.uuid)
		items.value = items.value.filter(i => i.uuid !== media.uuid)
		toast.success('Bild gelöscht')
	} catch {
		toast.error('Fehler beim Löschen')
	}
}

</script>

<template>
	<div>
		<PageHeader title="Media">
			<span v-if="!loading" class="text-sm text-neutral-400">
				{{ items.length }} {{ items.length === 1 ? 'Datei' : 'Dateien' }}
			</span>
		</PageHeader>

		<!-- Upload -->
		<div class="mb-24">
			<MediaUploader compact @uploaded="onUploaded" />
		</div>

		<!-- Search -->
		<div class="relative mb-24" v-if="items.length > 0">
			<PhMagnifyingGlass :size="14" class="absolute left-12 top-1/2 -translate-y-1/2 text-neutral-400" />
			<input
				v-model="search"
				type="text"
				placeholder="Suchen..."
				class="w-full border border-neutral-200 pl-32 pr-12 py-10 text-sm text-neutral-900 focus:outline-none focus:border-neutral-400 bg-white"
			/>
		</div>

		<!-- Loading -->
		<div v-if="loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<!-- Empty -->
		<div v-else-if="items.length === 0" class="text-sm text-neutral-400">
			Noch keine Medien vorhanden.
		</div>

		<!-- Grid -->
		<div v-else-if="filteredItems.length > 0" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
			<MediaCard
				v-for="media in filteredItems"
				:key="media.uuid"
				:media="media"
				:badge="media.in_use ? 'Verwendet' : null"
				:deletable="!media.in_use"
				@edit="openEdit"
				@delete="handleDelete"
			/>
		</div>

		<!-- No search results -->
		<div v-else class="text-sm text-neutral-400">
			Keine Ergebnisse für "{{ search }}".
		</div>

		<!-- Edit Modal -->
		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="handleSave"
		/>
	</div>
</template>
