<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useContentStore } from '@/stores/content'
import { useToast } from '@/composables/useToast'
import { PhPencil, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useContentStore()
const toast = useToast()

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'key', label: 'Key', class: 'w-160' },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

onMounted(() => {
	store.fetchItems()
})
</script>

<template>
	<div>
		<PageHeader title="Inhalte">
			<FormButton @click="router.push({ name: 'content.create' })">
				Neuer Inhalt
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<div v-else-if="store.items.length === 0" class="text-sm text-neutral-400">
			Noch keine Inhalte vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.items">
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
						@click="router.push({ name: 'content.edit', params: { id: row.id } })"
					>
						<PhPencil :size="16" weight="light" />
					</button>
				</div>
			</template>
		</DataTable>
	</div>
</template>
