<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useProjectStore } from '@/stores/projects'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import Editor from '@/components/ui/editor/Editor.vue'
import FormWithSidebar from '@/components/layout/FormWithSidebar.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormSelect from '@/components/ui/form/FormSelect.vue'
import FormCheckbox from '@/components/ui/form/FormCheckbox.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'
import Tabs from '@/components/ui/tabs/Tabs.vue'
import Tab from '@/components/ui/tabs/Tab.vue'
import GridBuilder from '@/components/grid/GridBuilder.vue'

const route = useRoute()
const router = useRouter()
const store = useProjectStore()
const mediaStore = useMediaStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)
const editingMedia = ref(null)
const mounted = ref(false)
const activeTab = ref('data')

const formTabs = computed(() => {
	const tabs = [
		{ key: 'data', label: 'Daten' },
		{ key: 'images', label: 'Bilder' },
		{ key: 'videos', label: 'Videos' },
	]
	if (isEdit.value) {
		tabs.push({ key: 'layout', label: 'Layout' })
	}
	return tabs
})

const imageItems = computed(() => mediaStore.items.filter(i => i.type === 'image'))
const videoItems = computed(() => mediaStore.items.filter(i => i.type === 'video'))

// Year options: current year down to 1990
const currentYear = new Date().getFullYear()
const yearOptions = Array.from(
	{ length: currentYear - 1989 },
	(_, i) => ({ value: currentYear - i, label: String(currentYear - i) })
)

const statusOptions = [
	{ value: 'Ausgeführt', label: 'Ausgeführt' },
	{ value: 'In Planung', label: 'In Planung' },
	{ value: 'Studie', label: 'Studie' },
]

const competitionOptions = [
	{ value: '1. Preis', label: '1. Preis' },
	{ value: '2. Preis', label: '2. Preis' },
	{ value: '3. Preis', label: '3. Preis' },
	{ value: 'Ankauf', label: 'Ankauf' },
	{ value: 'Anerkennung', label: 'Anerkennung' },
	{ value: 'Andere', label: 'Andere' },
]

const categoryOptions = computed(() =>
	store.categories.map(c => ({ value: c.id, label: c.name }))
)

const typeOptions = computed(() => {
	if (!form.value.category_id) return []
	const cat = store.categories.find(c => c.id === form.value.category_id)
	return (cat?.types || []).map(t => ({ value: t.id, label: t.name }))
})

const form = ref({
	title: '',
	name: '',
	location: '',
	year: null,
	description: '',
	info: '',
	status: null,
	competition: null,
	category_id: null,
	category_type_id: null,
	has_detail: true,
	publish: false,
})

watch(() => form.value.category_id, () => {
	if (mounted.value) {
		form.value.category_type_id = null
	}
})

onMounted(async () => {
	mediaStore.setItems([])
	await store.fetchCategories()

	if (isEdit.value) {
		await store.fetchProject(route.params.id)
		if (store.current) {
			const p = store.current
			form.value = {
				title: p.title,
				name: p.name || '',
				location: p.location || '',
				year: p.year || null,
				description: p.description || '',
				info: p.info || '',
				status: p.status || null,
				competition: p.competition || null,
				category_id: p.category_id,
				category_type_id: p.category_type_id,
				has_detail: p.has_detail,
				publish: p.publish,
			}
			mediaStore.setItems(p.media || [])
		}
	}
	mounted.value = true
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

		<form v-else @submit.prevent="handleSubmit">
			<Tabs v-model="activeTab" :tabs="formTabs">
				<Tab name="data">
					<FormWithSidebar>
						<div>
							<FormGroup>
								<FormLabel for="title">Titel *</FormLabel>
								<FormInput id="title" v-model="form.title" />
								<FormError :message="store.errors.title" />
							</FormGroup>

							<div class="grid grid-cols-2 gap-24">
								<FormGroup>
									<FormLabel for="name">Name</FormLabel>
									<FormInput id="name" v-model="form.name" />
									<FormError :message="store.errors.name" />
								</FormGroup>
								<FormGroup>
									<FormLabel for="location">Ort</FormLabel>
									<FormInput id="location" v-model="form.location" />
									<FormError :message="store.errors.location" />
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
						</div>

						<template #sidebar>
							<FormGroup>
								<FormLabel for="category">Kategorie</FormLabel>
								<FormSelect
									id="category"
									v-model="form.category_id"
									:options="categoryOptions"
								/>
							</FormGroup>

							<FormGroup>
								<FormLabel for="type">Typ</FormLabel>
								<FormSelect
									id="type"
									v-model="form.category_type_id"
									:options="typeOptions"
									:disabled="!typeOptions.length"
								/>
							</FormGroup>

							<FormGroup>
								<FormLabel for="year">Jahr</FormLabel>
								<FormSelect
									id="year"
									v-model="form.year"
									:options="yearOptions"
								/>
							</FormGroup>

							<FormGroup>
								<FormLabel for="status">Status</FormLabel>
								<FormSelect
									id="status"
									v-model="form.status"
									:options="statusOptions"
								/>
							</FormGroup>

							<FormGroup>
								<FormLabel for="competition">Wettbewerb</FormLabel>
								<FormSelect
									id="competition"
									v-model="form.competition"
									:options="competitionOptions"
								/>
							</FormGroup>

							<div class="flex flex-col gap-14">
								<FormCheckbox v-model="form.has_detail">Detailseite</FormCheckbox>
								<FormCheckbox v-model="form.publish">Veröffentlichen</FormCheckbox>
							</div>
						</template>
					</FormWithSidebar>
				</Tab>

				<Tab name="images">
					<MediaUploader :compact="imageItems.length > 0" accept="image/*" @uploaded="onUploaded" />
					<MediaGrid
						v-if="imageItems.length"
						:items="imageItems"
						class="mt-16"
						@edit="onEditMedia"
						@delete="onDeleteMedia"
						@reorder="onReorderMedia"
						@teaser="onSetTeaser"
					/>
				</Tab>

				<Tab name="videos">
					<MediaUploader :compact="videoItems.length > 0" accept="video/*" @uploaded="onUploaded" />
					<MediaGrid
						v-if="videoItems.length"
						:items="videoItems"
						class="mt-16"
						@edit="onEditMedia"
						@delete="onDeleteMedia"
						@reorder="onReorderMedia"
						@teaser="onSetTeaser"
					/>
				</Tab>

				<Tab v-if="isEdit" name="layout">
					<GridBuilder :project-id="route.params.id" />
				</Tab>
			</Tabs>
		</form>

		<MediaEdit
			:media="editingMedia"
			@close="editingMedia = null"
			@save="onSaveMedia"
		/>
	</div>
</template>
