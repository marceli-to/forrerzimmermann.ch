<script setup>
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAwardStore } from '@/stores/awards'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash } from '@phosphor-icons/vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useAwardStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'year', label: 'Jahr', class: 'w-80' },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

const allAwards = computed(() => {
	return Object.values(store.awards).flat()
})

onMounted(() => {
	store.fetchAwards()
})

async function handleDelete(award) {
	const ok = await confirm({
		title: 'Auszeichnung löschen',
		message: `"${award.title}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteAward(award.id)
	toast.success('Auszeichnung gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Auszeichnungen">
			<FormButton @click="router.push({ name: 'awards.create' })">
				Neue Auszeichnung
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<div v-else-if="allAwards.length === 0" class="text-sm text-neutral-400">
			Noch keine Auszeichnungen vorhanden.
		</div>

		<template v-else>
			<div v-for="(items, year) in store.awards" :key="year" class="mb-32">
				<h2 class="text-sm font-medium text-neutral-500 dark:text-neutral-400 mb-8">{{ year }}</h2>
				<DataTable :columns="columns" :rows="items">
					<template #cell-actions="{ row }">
						<div class="flex items-center justify-end gap-12">
							<button
								class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors cursor-pointer"
								@click="router.push({ name: 'awards.edit', params: { id: row.id } })"
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
