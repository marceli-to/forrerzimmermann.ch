<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAtelierStore } from '@/stores/atelier'
import { PhPencil, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useAtelierStore()

const pageTitles = { profil: 'Profil', team: 'Team', jobs: 'Jobs' }

const columns = [
	{ key: 'name', label: 'Seite', primary: true },
	{ key: 'actions', label: '', class: 'w-140', align: 'right' },
]

onMounted(() => {
	store.fetchPages()
})
</script>

<template>
	<div>
		<PageHeader title="Atelier" />

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<div v-else-if="store.pages.length === 0" class="text-sm text-neutral-400">
			Keine Seiten vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.pages">
			<template #cell-name="{ row }">
				{{ pageTitles[row.slug] || row.slug }}
			</template>
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="transition-colors cursor-pointer"
						:class="row.publish ? 'text-neutral-400 hover:text-neutral-900 dark:hover:text-white' : 'text-neutral-300 dark:text-neutral-600 hover:text-neutral-600 dark:hover:text-neutral-400'"
						:title="row.publish ? 'Veröffentlicht – klicken zum Verstecken' : 'Versteckt – klicken zum Veröffentlichen'"
						@click="store.toggle(row.uuid)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button
						class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white transition-colors cursor-pointer"
						@click="router.push({ name: 'atelier.edit', params: { id: row.uuid } })"
					>
						<PhPencil :size="16" weight="light" />
					</button>
				</div>
			</template>
		</DataTable>
	</div>
</template>
