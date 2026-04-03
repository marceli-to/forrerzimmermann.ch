<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAtelierStore } from '@/stores/atelier'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import SidebarLayout from '@/components/ui/form/SidebarLayout.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormTextarea from '@/components/ui/form/FormTextarea.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useAtelierStore()
const mediaStore = useMediaStore()
const toast = useToast()

const editingMedia = ref(null)

const pageTitles = { profil: 'Profil', team: 'Team', jobs: 'Jobs' }
const pageTitle = computed(() => {
	const slug = store.current?.slug
	return slug ? `${pageTitles[slug]} bearbeiten` : 'Bearbeiten'
})

const form = ref({
	title: '',
	text: '',
	meta_description: '',
	publish: false,
})

onMounted(async () => {
	mediaStore.setItems([])
	await store.fetchPage(route.params.id)
	if (store.current) {
		const p = store.current
		form.value = {
			title: p.title || '',
			text: p.text || '',
			meta_description: p.meta_description || '',
			publish: p.publish,
		}
		mediaStore.setItems(p.media || [])
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

	const success = await store.savePage(
		form.value,
		route.params.id,
		tempMedia
	)

	if (success) {
		toast.success('Seite aktualisiert')
		router.push({ name: 'atelier.index' })
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
		<PageHeader :title="pageTitle" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else-if="store.current" @submit.prevent="handleSubmit">
			<!-- Profil: sidebar layout (has title + editor + media to fill main column) -->
			<SidebarLayout v-if="store.current.slug === 'profil'">
				<div class="flex flex-col gap-24">
					<FormGroup>
						<FormLabel for="title">Titel</FormLabel>
						<FormInput id="title" v-model="form.title" />
						<FormError :message="store.errors.title" />
					</FormGroup>

					<FormGroup>
						<FormLabel>Text</FormLabel>
						<div class="mt-8">
							<Editor v-model="form.text" />
						</div>
						<FormError :message="store.errors.text" />
					</FormGroup>

					<FormGroup>
						<FormLabel>Medien</FormLabel>
						<div class="mt-8 flex flex-col gap-16">
							<MediaUploader v-if="!mediaStore.items.length" @uploaded="onUploaded" />
							<MediaGrid
								v-if="mediaStore.items.length"
								:items="mediaStore.items"
								@edit="onEditMedia"
								@delete="onDeleteMedia"
								@reorder="onReorderMedia"
								@teaser="onSetTeaser"
							/>
						</div>
					</FormGroup>

					<FormActions
						submitLabel="Aktualisieren"
						cancelLabel="Abbrechen"
						@cancel="router.push({ name: 'atelier.index' })"
					/>
				</div>

				<template #sidebar>
					<FormGroup>
						<FormLabel for="meta_description">Meta Description</FormLabel>
						<FormTextarea id="meta_description" v-model="form.meta_description" />
						<FormError :message="store.errors.meta_description" />
					</FormGroup>
				</template>
			</SidebarLayout>

			<!-- Team / Jobs: single column (only media + meta desc, no sidebar needed) -->
			<div v-else class="flex flex-col gap-24">
				<FormGroup>
					<FormLabel>Medien</FormLabel>
					<div class="mt-8 flex flex-col gap-16">
						<MediaUploader v-if="!mediaStore.items.length" @uploaded="onUploaded" />
						<MediaGrid
							v-if="mediaStore.items.length"
							:items="mediaStore.items"
							@edit="onEditMedia"
							@delete="onDeleteMedia"
							@reorder="onReorderMedia"
							@teaser="onSetTeaser"
						/>
					</div>
				</FormGroup>

				<FormGroup>
					<FormLabel for="meta_description">Meta Description</FormLabel>
					<FormTextarea id="meta_description" v-model="form.meta_description" />
					<FormError :message="store.errors.meta_description" />
				</FormGroup>

				<FormActions
					submitLabel="Aktualisieren"
					cancelLabel="Abbrechen"
					@cancel="router.push({ name: 'atelier.index' })"
				/>
			</div>
		</form>

		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="onSaveMedia"
		/>
	</div>
</template>
