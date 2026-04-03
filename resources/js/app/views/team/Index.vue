<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useTeamStore } from '@/stores/team'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useTeamStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'name', label: 'Name', primary: true },
	{ key: 'title', label: 'Titel' },
	{ key: 'former', label: '', class: 'w-100' },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

onMounted(() => {
	store.fetchMembers()
})

async function handleDelete(member) {
	const ok = await confirm({
		title: 'Mitglied löschen',
		message: `"${member.firstname} ${member.name}" wirklich löschen?`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteMember(member.uuid)
	toast.success('Mitglied gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Team" />
		<FormActions>
			<button type="button" class="text-sm px-16 py-8 rounded-md bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer" @click="router.push({ name: 'team.create' })">Neues Mitglied</button>
		</FormActions>

		<div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
			Laden...
		</div>

		<div v-else-if="store.members.length === 0" class="text-sm text-gray-400 dark:text-warm-500">
			Noch keine Mitglieder vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.members">
			<template #cell-name="{ row }">
				{{ row.firstname }} {{ row.name }}
			</template>
			<template #cell-former="{ row }">
				<span v-if="row.former" class="text-xs text-gray-400 dark:text-warm-500 bg-neutral-100 dark:bg-warm-800 px-8 py-2 rounded">
					Ehemalig
				</span>
			</template>
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="transition-colors cursor-pointer"
						:class="row.publish ? 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100' : 'text-gray-300 dark:text-warm-700 hover:text-gray-600 dark:hover:text-warm-500'"
						@click="store.toggle(row.uuid)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button
						class="text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer"
						@click="router.push({ name: 'team.edit', params: { id: row.uuid } })"
					>
						<PhPencil :size="16" weight="light" />
					</button>
					<button
						class="text-gray-400 dark:text-warm-500 hover:text-red-600 transition-colors cursor-pointer"
						@click="handleDelete(row)"
					>
						<PhTrash :size="16" weight="light" />
					</button>
				</div>
			</template>
		</DataTable>
	</div>
</template>
