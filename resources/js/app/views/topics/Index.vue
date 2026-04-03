<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useTopicStore } from '@/stores/topics'
import { useToast } from '@/composables/useToast'
import { useConfirm } from '@/composables/useConfirm'
import { PhPencil, PhTrash, PhEye, PhEyeSlash } from '@phosphor-icons/vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import DataTable from '@/components/ui/table/DataTable.vue'

const router = useRouter()
const store = useTopicStore()
const toast = useToast()
const { confirm } = useConfirm()

const columns = [
	{ key: 'title', label: 'Titel', primary: true },
	{ key: 'actions', label: '', class: 'w-100', align: 'right' },
]

onMounted(() => {
	store.fetchTopics()
})

async function handleDelete(topic) {
	const ok = await confirm({
		title: 'Thema löschen',
		message: `"${topic.title}" wirklich löschen? Dies kann nicht rückgängig gemacht werden.`,
		confirmLabel: 'Löschen',
		destructive: true,
	})
	if (!ok) return
	await store.deleteTopic(topic.uuid)
	toast.success('Thema gelöscht')
}
</script>

<template>
	<div>
		<PageHeader title="Themen">
			<FormButton @click="router.push({ name: 'topics.create' })">
				Neues Thema
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-gray-400">
			Laden...
		</div>

		<div v-else-if="store.topics.length === 0" class="text-sm text-gray-400">
			Noch keine Themen vorhanden.
		</div>

		<DataTable v-else :columns="columns" :rows="store.topics">
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
						@click="router.push({ name: 'topics.edit', params: { id: row.uuid } })"
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
