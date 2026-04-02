<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useBookStore } from '@/stores/books'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import Editor from '@/components/ui/editor/Editor.vue'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useBookStore()
const mediaStore = useMediaStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)
const editingMedia = ref(null)

const form = ref({
	title: '',
	description: '',
	info: '',
	url: '',
})

onMounted(async () => {
	mediaStore.setItems([])

	if (isEdit.value) {
		await store.fetchBook(route.params.id)
		if (store.current) {
			const b = store.current
			form.value = {
				title: b.title || '',
				description: b.description || '',
				info: b.info || '',
				url: b.url || '',
			}
			mediaStore.setItems(b.media || [])
		}
	}
})

async function handleSubmit() {
	const tempMedia = mediaStore.tempItems.map(item => ({
		uuid: item.uuid,
		file: item.file,
		original_name: item.original_name,
		mime_type: item.mime_type,
		size: item.size,
		width: item.width,
		height: item.height,
		alt: item.alt || null,
		caption: item.caption || null,
	}))

	const success = await store.saveBook(
		form.value,
		isEdit.value ? route.params.id : null,
		tempMedia
	)

	if (success) {
		toast.success(isEdit.value ? 'Buch aktualisiert' : 'Buch erstellt')
		router.push({ name: 'books.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}

function onUploaded(media) { mediaStore.addItem(media) }
function onEditMedia(media) { editingMedia.value = media }
async function onSaveMedia({ uuid, data }) {
	const success = await mediaStore.updateItem(uuid, data)
	if (success) editingMedia.value = null
}
async function onDeleteMedia(media) { await mediaStore.deleteItem(media.uuid) }
function onReorderMedia(items) { mediaStore.reorder(items) }
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Buch bearbeiten' : 'Neues Buch'">
			<FormButton variant="secondary" @click="router.push({ name: 'books.index' })">
				Abbrechen
			</FormButton>
			<FormButton @click="handleSubmit">
				{{ isEdit ? 'Aktualisieren' : 'Erstellen' }}
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<form v-else class="max-w-4xl" @submit.prevent="handleSubmit">
			<FormGroup>
				<FormLabel for="title">Titel *</FormLabel>
				<FormInput id="title" v-model="form.title" />
				<FormError :message="store.errors.title" />
			</FormGroup>

			<FormGroup>
				<FormLabel>Beschreibung</FormLabel>
				<div class="mt-8">
					<Editor v-model="form.description" />
				</div>
				<FormError :message="store.errors.description" />
			</FormGroup>

			<FormGroup>
				<FormLabel>Info</FormLabel>
				<div class="mt-8">
					<Editor v-model="form.info" />
				</div>
				<FormError :message="store.errors.info" />
			</FormGroup>

			<FormGroup>
				<FormLabel for="url">URL</FormLabel>
				<FormInput id="url" v-model="form.url" />
				<FormError :message="store.errors.url" />
			</FormGroup>

			<FormGroup>
				<FormLabel>Bilder</FormLabel>
				<div class="mt-8">
					<MediaUploader :compact="mediaStore.items.length > 0" @uploaded="onUploaded" />
					<MediaGrid
						v-if="mediaStore.items.length"
						:items="mediaStore.items"
						class="mt-16"
						@edit="onEditMedia"
						@delete="onDeleteMedia"
						@reorder="onReorderMedia"
					/>
				</div>
			</FormGroup>
		</form>

		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="onSaveMedia"
		/>
	</div>
</template>
