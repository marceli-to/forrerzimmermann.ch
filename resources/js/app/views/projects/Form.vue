<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectStore } from '@/stores/projects'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import topicsApi from '@/api/topics'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormSelect from '@/components/ui/form/FormSelect.vue'
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useProjectStore()
const mediaStore = useMediaStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)
const editingMedia = ref(null)
const topicOptions = ref([])

const form = ref({
	title: '',
	location: '',
	subtitle: '',
	year: null,
	description: '',
	info: '',
	meta_description: '',
	publish: false,
	feature: false,
	topic_id: null,
})

onMounted(async () => {
	mediaStore.setItems([])

	// Load topics for select
	try {
		const { data } = await topicsApi.index()
		topicOptions.value = data.data.map(t => ({ value: t.uuid, label: t.title }))
	} catch {}

	if (isEdit.value) {
		await store.fetchProject(route.params.id)
		if (store.current) {
			const p = store.current
			form.value = {
				title: p.title || '',
				location: p.location || '',
				subtitle: p.subtitle || '',
				year: p.year || null,
				description: p.description || '',
				info: p.info || '',
				meta_description: p.meta_description || '',
				publish: p.publish,
				feature: p.feature,
				topic_id: p.topic?.uuid || null,
			}
			mediaStore.setItems(p.media || [])
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

	const success = await store.saveProject(
		form.value,
		isEdit.value ? route.params.id : null,
		tempMedia
	)

	if (success) {
		toast.success(isEdit.value ? 'Projekt aktualisiert' : 'Projekt erstellt')
		router.push({ name: 'projects.index' })
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
function onSetTeaser(media) { mediaStore.setTeaser(media.uuid) }
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Projekt bearbeiten' : 'Neues Projekt'">
			<FormButton variant="secondary" @click="router.push({ name: 'projects.index' })">
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

			<div class="grid grid-cols-2 gap-24">
				<FormGroup>
					<FormLabel for="location">Ort</FormLabel>
					<FormInput id="location" v-model="form.location" />
					<FormError :message="store.errors.location" />
				</FormGroup>
				<FormGroup>
					<FormLabel for="subtitle">Untertitel</FormLabel>
					<FormInput id="subtitle" v-model="form.subtitle" />
					<FormError :message="store.errors.subtitle" />
				</FormGroup>
			</div>

			<div class="grid grid-cols-2 gap-24">
				<FormGroup>
					<FormLabel for="year">Jahr *</FormLabel>
					<FormInput id="year" v-model="form.year" type="number" />
					<FormError :message="store.errors.year" />
				</FormGroup>
				<FormGroup>
					<FormLabel for="topic_id">Thema</FormLabel>
					<FormSelect id="topic_id" v-model="form.topic_id" :options="topicOptions" />
					<FormError :message="store.errors.topic_id" />
				</FormGroup>
			</div>

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
				<FormLabel for="meta_description">Meta Description</FormLabel>
				<FormInput id="meta_description" v-model="form.meta_description" />
				<FormError :message="store.errors.meta_description" />
			</FormGroup>

			<div class="flex gap-24">
				<FormGroup>
					<FormCheckbox v-model="form.publish">Veröffentlichen</FormCheckbox>
				</FormGroup>
				<FormGroup>
					<FormCheckbox v-model="form.feature">In Auswahl anzeigen</FormCheckbox>
				</FormGroup>
			</div>

			<FormGroup>
				<FormLabel>Medien</FormLabel>
				<div class="mt-8">
					<MediaUploader :compact="mediaStore.items.length > 0" @uploaded="onUploaded" />
					<MediaGrid
						v-if="mediaStore.items.length"
						:items="mediaStore.items"
						class="mt-16"
						@edit="onEditMedia"
						@delete="onDeleteMedia"
						@reorder="onReorderMedia"
						@teaser="onSetTeaser"
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
