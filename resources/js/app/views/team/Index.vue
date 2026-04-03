<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useTeamStore } from '@/stores/team'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormButton from '@/components/ui/form/FormButton.vue'
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
		<PageHeader title="Team">
			<FormButton @click="router.push({ name: 'team.create' })">
				Neues Mitglied
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-gray-400">
			Laden...
		</div>

		<div v-else-if="store.members.length === 0" class="text-sm text-gray-400">
			Noch keine Mitglieder vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.members">
			<template #cell-name="{ row }">
				{{ row.firstname }} {{ row.name }}
			</template>
			<template #cell-former="{ row }">
				<span v-if="row.former" class="text-xs text-gray-400 bg-neutral-100 px-8 py-2 rounded">
					Ehemalig
				</span>
			</template>
			<template #cell-actions="{ row }">
				<div class="flex items-center justify-end gap-12">
					<button
						class="transition-colors cursor-pointer"
						:class="row.publish ? 'text-gray-400 hover:text-gray-900' : 'text-gray-300 hover:text-gray-600'"
						@click="store.toggle(row.uuid)"
					>
						<PhEye v-if="row.publish" :size="16" weight="light" />
						<PhEyeSlash v-else :size="16" weight="light" />
					</button>
					<button
						class="text-gray-400 hover:text-gray-900 transition-colors cursor-pointer"
						@click="router.push({ name: 'team.edit', params: { id: row.uuid } })"
					>
						<PhPencil :size="16" weight="light" />
					</button>
					<button
						class="text-gray-400 hover:text-red-600 transition-colors cursor-pointer"
						@click="handleDelete(row)"
					>
						<PhTrash :size="16" weight="light" />
					</button>
				</div>
			</template>
		</DataTable>
	</div>
</template>
