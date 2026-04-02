<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { PhPlus, PhTrash, PhPencil } from '@phosphor-icons/vue'
import { useCategoryStore } from '@/stores/categories'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormError from '@/components/ui/form/FormError.vue'
import DataTable from '@/components/ui/table/DataTable.vue'
import Drawer from '@/components/ui/drawer/Drawer.vue'

const route = useRoute()
const router = useRouter()
const store = useCategoryStore()
const toast = useToast()
const { confirm } = useConfirm()

const isEdit = computed(() => !!route.params.id)

const form = ref({ name: '' })
const errors = ref({})

const typeColumns = [
	{ key: 'name', label: 'Name – Plural', primary: true },
	{ key: 'name_singular', label: 'Name – Singular' },
	{ key: 'actions', label: '', class: 'w-80', align: 'right' },
]

// Types
const types = ref([])

const dragTypes = computed({
	get: () => types.value,
	set: (val) => {
		types.value = val
		store.reorderTypes(Number(route.params.id), val)
	},
})

// Drawer
const drawerOpen = ref(false)
const drawerType = ref({ name: '', name_singular: '' })
const drawerEditingId = ref(null)
const drawerErrors = ref({})

const drawerTitle = computed(() => drawerEditingId.value ? 'Typ bearbeiten' : 'Neuer Typ')

onMounted(async () => {
	if (isEdit.value) {
		await store.fetchCategories()
		const cat = store.categories.find(c => c.id === Number(route.params.id))
		if (cat) {
			form.value = { name: cat.name }
			types.value = [...(cat.types || [])]
		}
	}
})

async function handleSubmit() {
	errors.value = {}
	if (!form.value.name.trim()) {
		errors.value.name = ['Name ist erforderlich.']
		return
	}
	if (isEdit.value) {
		await store.updateCategory(Number(route.params.id), form.value)
		toast.success('Kategorie aktualisiert')
	} else {
		const cat = await store.addCategory(form.value.name)
		toast.success('Kategorie erstellt')
		router.replace({ name: 'settings.categories.edit', params: { id: cat.id } })
	}
}

// ─── Type drawer ─────────────────────────────────────────────────────────────

function openAddDrawer() {
	drawerEditingId.value = null
	drawerType.value = { name: '', name_singular: '' }
	drawerErrors.value = {}
	drawerOpen.value = true
}

function openEditDrawer(type) {
	drawerEditingId.value = type.id
	drawerType.value = { name: type.name, name_singular: type.name_singular || '' }
	drawerErrors.value = {}
	drawerOpen.value = true
}

function closeDrawer() {
	drawerOpen.value = false
}

async function handleDrawerSave() {
	drawerErrors.value = {}
	const name = drawerType.value.name.trim()
	if (!name) {
		drawerErrors.value.name = 'Name ist erforderlich.'
		return
	}

	const payload = {
		name,
		name_singular: drawerType.value.name_singular.trim() || null,
	}

	if (drawerEditingId.value) {
		await store.updateType(Number(route.params.id), drawerEditingId.value, payload)
		const idx = types.value.findIndex(t => t.id === drawerEditingId.value)
		if (idx !== -1) types.value[idx] = { ...types.value[idx], ...payload }
		toast.success('Typ aktualisiert')
	} else {
		const type = await store.addType(Number(route.params.id), { ...payload, publish: true })
		types.value.push(type)
		toast.success('Typ erstellt')
	}

	drawerOpen.value = false
}

async function handleDeleteType(type) {
	const ok = await confirm({
		title: 'Typ löschen',
		message: `"${type.name}" wirklich löschen?`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteType(Number(route.params.id), type.id)
	types.value = types.value.filter(t => t.id !== type.id)
}
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Kategorie bearbeiten' : 'Neue Kategorie'">
			<FormButton variant="secondary" @click="router.push({ name: 'settings.index' })">
				Abbrechen
			</FormButton>
			<FormButton @click="handleSubmit">
				{{ isEdit ? 'Aktualisieren' : 'Erstellen' }}
			</FormButton>
		</PageHeader>

		<FormGroup>
			<FormLabel for="name">Name *</FormLabel>
			<FormInput id="name" v-model="form.name" />
			<FormError :message="errors.name?.[0]" />
		</FormGroup>

		<!-- Types — only shown in edit mode -->
		<template v-if="isEdit">
			<div class="mt-40">
				<div class="flex items-center justify-between mb-16">
					<p class="text-xxs font-medium uppercase tracking-[0.08em] text-neutral-500 dark:text-neutral-400">Typen</p>
					<button
						type="button"
						class="flex items-center gap-6 text-xs text-neutral-400 hover:text-neutral-800 dark:hover:text-neutral-200 transition-colors cursor-pointer"
						@click="openAddDrawer"
					>
						<PhPlus :size="13" weight="light" />
						Typ hinzufügen
					</button>
				</div>

				<DataTable
					v-if="types.length"
					v-model="dragTypes"
					:columns="typeColumns"
					:rows="types"
					:draggable-rows="true"
				>
					<template #cell-name_singular="{ row }">
						{{ row.name_singular || '–' }}
					</template>
					<template #cell-actions="{ row }">
						<div class="flex items-center justify-end gap-12">
							<button
								type="button"
								class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors cursor-pointer"
								@click="openEditDrawer(row)"
							>
								<PhPencil :size="14" weight="light" />
							</button>
							<button
								type="button"
								class="text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition-colors cursor-pointer"
								@click="handleDeleteType(row)"
							>
								<PhTrash :size="14" weight="light" />
							</button>
						</div>
					</template>
				</DataTable>

				<p v-else class="text-sm text-neutral-400">
					Noch keine Typen vorhanden.
				</p>
			</div>
		</template>

		<p v-else class="mt-16 text-xs text-neutral-400">
			Typen können nach dem Erstellen der Kategorie hinzugefügt werden.
		</p>

		<!-- Type drawer -->
		<Drawer :open="drawerOpen" :title="drawerTitle" @close="closeDrawer">
			<div class="px-24 py-20 flex flex-col gap-20">
				<FormGroup>
					<FormLabel for="type-name">Name – Plural *</FormLabel>
					<FormInput id="type-name" v-model="drawerType.name" @keydown.enter="handleDrawerSave" />
					<FormError :message="drawerErrors.name" />
				</FormGroup>
				<FormGroup>
					<FormLabel for="type-name-singular">Name – Singular</FormLabel>
					<FormInput id="type-name-singular" v-model="drawerType.name_singular" @keydown.enter="handleDrawerSave" />
				</FormGroup>
			</div>
			<template #footer>
				<div class="flex justify-between">
					<FormButton variant="secondary" @click="closeDrawer">Abbrechen</FormButton>
					<FormButton @click="handleDrawerSave">
						{{ drawerEditingId ? 'Aktualisieren' : 'Erstellen' }}
					</FormButton>
				</div>
			</template>
		</Drawer>
	</div>
</template>
