<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useCategoryStore } from '@/stores/categories'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useCategoryStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'name', label: 'Name', primary: true },
	{ key: 'types_count', label: 'Typen', class: 'w-80' },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

onMounted(() => {
	store.fetchCategories()
})

async function handleDelete(cat) {
	const ok = await confirm({
		title: 'Kategorie löschen',
		message: `"${cat.name}" und alle zugehörigen Typen wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteCategory(cat.id)
	toast.success('Kategorie gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Einstellungen">
			<FormButton @click="router.push({ name: 'settings.categories.create' })">
				Neue Kategorie
			</FormButton>
		</PageHeader>

		<p class="text-xxs font-medium uppercase tracking-[0.08em] text-neutral-500 dark:text-neutral-400 mb-20">Kategorien</p>

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<div v-else-if="store.categories.length === 0" class="text-sm text-neutral-400">
			Noch keine Kategorien vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.categories">
			<template #cell-types_count="{ row }">
				{{ row.types?.length ?? 0 }}
			</template>
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="transition-colors cursor-pointer"
						:class="row.publish ? 'text-neutral-400 hover:text-neutral-900 dark:hover:text-white' : 'text-neutral-300 dark:text-neutral-600 hover:text-neutral-600 dark:hover:text-neutral-400'"
						:title="row.publish ? 'Aktiv – klicken zum Deaktivieren' : 'Inaktiv – klicken zum Aktivieren'"
						@click="store.toggle(row.id)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button
						class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors cursor-pointer"
						@click="router.push({ name: 'settings.categories.edit', params: { id: row.id } })"
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
