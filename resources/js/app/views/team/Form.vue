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
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
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
	role: '',
	position: '',
	phone: '',
	email: '',
	cv: '',
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
				role: m.role || '',
				position: m.position || '',
				phone: m.phone || '',
				email: m.email || '',
				cv: m.cv || '',
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
		<PageHeader :title="isEdit ? 'Mitglied bearbeiten' : 'Neues Mitglied'">
			<FormButton variant="secondary" @click="router.push({ name: 'team.index' })">
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
					<FormLabel for="role">Rolle</FormLabel>
					<FormInput id="role" v-model="form.role" />
					<FormError :message="store.errors.role" />
				</FormGroup>
				<FormGroup>
					<FormLabel for="position">Position</FormLabel>
					<FormInput id="position" v-model="form.position" />
					<FormError :message="store.errors.position" />
				</FormGroup>
			</div>

			<div class="grid grid-cols-2 gap-24">
				<FormGroup>
					<FormLabel for="phone">Telefon</FormLabel>
					<FormInput id="phone" v-model="form.phone" />
					<FormError :message="store.errors.phone" />
				</FormGroup>
				<FormGroup>
					<FormLabel for="email">E-Mail *</FormLabel>
					<FormInput id="email" v-model="form.email" />
					<FormError :message="store.errors.email" />
				</FormGroup>
			</div>

			<FormGroup>
				<FormLabel>Lebenslauf</FormLabel>
				<div class="mt-8">
					<Editor v-model="form.cv" />
				</div>
				<FormError :message="store.errors.cv" />
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
