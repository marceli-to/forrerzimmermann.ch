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
	{ key: 'firstname', label: 'Vorname' },
	{ key: 'role', label: 'Rolle', class: 'w-160' },
	{ key: 'email', label: 'E-Mail', class: 'w-200' },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

onMounted(() => {
	store.fetchMembers()
})

async function handleDelete(member) {
	const ok = await confirm({
		title: 'Mitglied löschen',
		message: `"${member.firstname} ${member.name}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteMember(member.id)
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

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<div v-else-if="store.members.length === 0" class="text-sm text-neutral-400">
			Noch keine Teammitglieder vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.members">
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
						@click="router.push({ name: 'team.edit', params: { id: row.id } })"
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
