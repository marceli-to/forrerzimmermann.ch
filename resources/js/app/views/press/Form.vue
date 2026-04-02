<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { usePressStore } from '@/stores/press'
import { useMediaStore } from '@/stores/media'
import { useProjectStore } from '@/stores/projects'
import { useToast } from '@/composables/useToast'
import Editor from '@/components/ui/editor/Editor.vue'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import FormWithSidebar from '@/components/layout/FormWithSidebar.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormSelect from '@/components/ui/form/FormSelect.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = usePressStore()
const mediaStore = useMediaStore()
const projectStore = useProjectStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)
const editingMedia = ref(null)

const currentYear = new Date().getFullYear()
const yearOptions = Array.from(
	{ length: currentYear - 1989 },
	(_, i) => ({ value: String(currentYear - i), label: String(currentYear - i) })
)

const projectOptions = computed(() =>
	projectStore.projects.map(p => ({ value: p.id, label: p.title }))
)

const form = ref({
	title: '',
	description: '',
	year: String(currentYear),
	url: '',
	project_id: null,
})

onMounted(async () => {
	mediaStore.setItems([])
	await projectStore.fetchProjects()

	if (isEdit.value) {
		await store.fetchPressItem(route.params.id)
		if (store.current) {
			const p = store.current
			form.value = {
				title: p.title || '',
				description: p.description || '',
				year: p.year || String(currentYear),
				url: p.url || '',
				project_id: p.project_id || null,
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

	const success = await store.savePress(
		form.value,
		isEdit.value ? route.params.id : null,
		tempMedia
	)

	if (success) {
		toast.success(isEdit.value ? 'Presseeintrag aktualisiert' : 'Presseeintrag erstellt')
		router.push({ name: 'press.index' })
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
		<PageHeader :title="isEdit ? 'Presseeintrag bearbeiten' : 'Neuer Presseeintrag'">
			<FormButton variant="secondary" @click="router.push({ name: 'press.index' })">
				Abbrechen
			</FormButton>
			<FormButton @click="handleSubmit">
				{{ isEdit ? 'Aktualisieren' : 'Erstellen' }}
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<form v-else @submit.prevent="handleSubmit">
			<FormWithSidebar>
				<div>
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
							/>
						</div>
					</FormGroup>
				</div>

				<template #sidebar>
					<FormGroup>
						<FormLabel for="year">Jahr *</FormLabel>
						<FormSelect id="year" v-model="form.year" :options="yearOptions" />
						<FormError :message="store.errors.year" />
					</FormGroup>

					<FormGroup>
						<FormLabel for="project">Projekt</FormLabel>
						<FormSelect id="project" v-model="form.project_id" :options="projectOptions" />
					</FormGroup>

					<FormGroup>
						<FormLabel for="url">URL</FormLabel>
						<FormInput id="url" v-model="form.url" />
						<FormError :message="store.errors.url" />
					</FormGroup>
				</template>
			</FormWithSidebar>
		</form>

		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="onSaveMedia"
		/>
	</div>
</template>
