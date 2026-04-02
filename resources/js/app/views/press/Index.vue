<script setup>
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { usePressStore } from '@/stores/press'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = usePressStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'year', label: 'Jahr', class: 'w-80' },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

const allPress = computed(() => {
	return Object.values(store.press).flat()
})

onMounted(() => {
	store.fetchPress()
})

async function handleDelete(item) {
	const ok = await confirm({
		title: 'Presse löschen',
		message: `"${item.title}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deletePress(item.id)
	toast.success('Presse gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Presse">
			<FormButton @click="router.push({ name: 'press.create' })">
				Neuer Eintrag
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<div v-else-if="allPress.length === 0" class="text-sm text-neutral-400">
			Noch keine Presseeinträge vorhanden.
		</div>

		<template v-else>
			<div v-for="(items, year) in store.press" :key="year" class="mb-32">
				<h2 class="text-sm font-medium text-neutral-500 dark:text-neutral-400 mb-8">{{ year }}</h2>
				<DataTable :columns="columns" :rows="items">
					<template #cell-actions="{ row }">
						<div class="flex items-center justify-end gap-12">
							<button
								class="transition-colors cursor-pointer"
								:class="row.publish ? 'text-neutral-400 hover:text-neutral-900 dark:hover:text-white' : 'text-neutral-300 hover:text-neutral-600 dark:text-neutral-600 dark:hover:text-neutral-400'"
								@click="store.toggle(row.id)"
							>
								<PhEye v-if="row.publish" :size="16" weight="light" />
								<PhEyeSlash v-else :size="16" weight="light" />
							</button>
							<button
								class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors cursor-pointer"
								@click="router.push({ name: 'press.edit', params: { id: row.id } })"
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
	</div>
</template>
