<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTeamStore } from '@/stores/team'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import Editor from '@/components/ui/editor/Editor.vue'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useTeamStore()
const mediaStore = useMediaStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)
const editingMedia = ref(null)

const form = ref({
	firstname: '',
	name: '',
	title: '',
	email: '',
	cv: '',
	former: false,
})

onMounted(async () => {
	mediaStore.setItems([])

	if (isEdit.value) {
		await store.fetchMember(route.params.id)
		if (store.current) {
			const m = store.current
			form.value = {
				firstname: m.firstname || '',
				name: m.name || '',
				title: m.title || '',
				email: m.email || '',
				cv: m.cv || '',
				former: m.former || false,
			}
			mediaStore.setItems(m.media || [])
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

	const success = await store.saveMember(
		form.value,
		isEdit.value ? route.params.id : null,
		tempMedia
	)

	if (success) {
		toast.success(isEdit.value ? 'Mitglied aktualisiert' : 'Mitglied erstellt')
		router.push({ name: 'team.index' })
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
		<PageHeader :title="isEdit ? 'Mitglied bearbeiten' : 'Neues Mitglied'" />

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<form v-else class="flex flex-col gap-24" @submit.prevent="handleSubmit">
			<div class="grid grid-cols-2 gap-24">
				<FormGroup>
					<FormLabel for="firstname">Vorname *</FormLabel>
					<FormInput id="firstname" v-model="form.firstname" />
					<FormError :message="store.errors.firstname" />
				</FormGroup>
				<FormGroup>
					<FormLabel for="name">Name *</FormLabel>
					<FormInput id="name" v-model="form.name" />
					<FormError :message="store.errors.name" />
				</FormGroup>
			</div>

			<div class="grid grid-cols-2 gap-24">
				<FormGroup>
					<FormLabel for="title">Titel</FormLabel>
					<FormInput id="title" v-model="form.title" />
					<FormError :message="store.errors.title" />
				</FormGroup>
				<FormGroup>
					<FormLabel for="email">E-Mail</FormLabel>
					<FormInput id="email" v-model="form.email" />
					<FormError :message="store.errors.email" />
				</FormGroup>
			</div>

			<FormGroup>
				<FormCheckbox v-model="form.former">Ehemalige/r Mitarbeiter/in</FormCheckbox>
			</FormGroup>

			<FormGroup>
				<FormLabel>Lebenslauf</FormLabel>
				<div class="mt-8">
					<Editor v-model="form.cv" />
				</div>
				<FormError :message="store.errors.cv" />
			</FormGroup>

			<FormGroup>
				<FormLabel>Portrait</FormLabel>
				<div class="mt-8 flex flex-col gap-16">
					<MediaUploader v-if="!mediaStore.items.length" @uploaded="onUploaded" />
					<MediaGrid
						v-if="mediaStore.items.length"
						:items="mediaStore.items"
						@edit="onEditMedia"
						@delete="onDeleteMedia"
						@reorder="onReorderMedia"
					/>
				</div>
			</FormGroup>

			<FormActions
				:submitLabel="isEdit ? 'Aktualisieren' : 'Erstellen'"
				cancelLabel="Abbrechen"
				@cancel="router.push({ name: 'team.index' })"
			/>
		</form>

		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="onSaveMedia"
		/>
	</div>
</template>
