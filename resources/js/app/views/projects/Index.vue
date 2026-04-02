<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useProjectStore } from '@/stores/projects'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useProjectStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'name', label: 'Name' },
	{ key: 'location', label: 'Ort' },
	{ key: 'year', label: 'Jahr', class: 'w-80' },
	{ key: 'status', label: 'Status', class: 'w-120' },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

onMounted(() => {
	store.fetchProjects()
})

async function handleDelete(project) {
	const ok = await confirm({
		title: 'Projekt löschen',
		message: `"${project.title}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteProject(project.id)
	toast.success('Projekt gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Projekte">
			<FormButton @click="router.push({ name: 'projects.create' })">
				Neues Projekt
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<div v-else-if="store.projects.length === 0" class="text-sm text-neutral-400">
			Noch keine Projekte vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.projects">
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="transition-colors cursor-pointer"
						:class="row.publish ? 'text-neutral-400 hover:text-neutral-900 dark:hover:text-white' : 'text-neutral-300 dark:text-neutral-600 hover:text-neutral-600 dark:hover:text-neutral-400'"
						:title="row.publish ? 'Veröffentlicht – klicken zum Verstecken' : 'Versteckt – klicken zum Veröffentlichen'"
						@click="store.toggle(row.id)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button
						class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors cursor-pointer"
						@click="router.push({ name: 'projects.edit', params: { id: row.id } })"
					>
						<PhPencil :size="16" weight="light" />
					</button>
					<button
						class="text-neutral-400 hover:text-red-600 dark:hover:text-red-400 transition-colors cursor-pointer"
						@click="handleDelete(row)"
					>
						<PhTrash :size="16" weight="light" />
					</button>
				</div>
			</template>
		</DataTable>
	</div>
</template>
